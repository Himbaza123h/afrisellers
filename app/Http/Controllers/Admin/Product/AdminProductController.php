<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductCategory;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AdminProductController extends Controller
{
    // ─────────────────────────────────────────────
    //  INDEX
    // ─────────────────────────────────────────────
    public function index(Request $request)
{
        $query = Product::with(['user', 'productCategory', 'country', 'images'])
            ->latest();

        // Filters
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('product_category_id', $request->category);
        }
        if ($request->filled('country')) {
            $query->where('country_id', $request->country);
        }
        if ($request->filled('verified')) {
            $query->where('is_admin_verified', $request->verified === 'yes');
        }
        if ($request->filled('vendor')) {
            $query->where('user_id', $request->vendor);
        }

        $products   = $query->paginate(20)->withQueryString();
        $categories = ProductCategory::orderBy('name')->get();
        $countries  = Country::orderBy('name')->get();
        $vendors    = User::whereHas('roles', fn($q) => $q->where('slug', 'vendor'))
                          ->orderBy('name')->get();

        // Stats
$statsQuery = Product::query();

// Apply same vendor filter to stats
if ($request->filled('vendor')) {
    $statsQuery->where('user_id', $request->vendor);
}

$stats = [
    'total'    => (clone $statsQuery)->count(),
    'draft'    => (clone $statsQuery)->where('status', 'draft')->count(),
    'active'   => (clone $statsQuery)->where('status', 'active')->count(),
    'inactive' => (clone $statsQuery)->where('status', 'inactive')->count(),
];

        return view('admin.products.index', compact('products', 'categories', 'countries', 'vendors', 'stats'));
    }

    // ─────────────────────────────────────────────
    //  CREATE
    // ─────────────────────────────────────────────
public function create(Request $request)
{
    $categories = ProductCategory::orderBy('name')->get();
    $countries  = Country::orderBy('name')->get();
    $vendors    = User::whereHas('roles', fn($q) => $q->where('slug', 'vendor'))
                      ->orderBy('name')->get();

    // Pre-select vendor from query string (?user_id=X)
    $lockedUserId = $request->query('user_id');

    return view('admin.products.create', compact('categories', 'countries', 'vendors', 'lockedUserId'));
}

    // ─────────────────────────────────────────────
    //  STORE
    // ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'user_id'               => 'required|exists:users,id',
            'product_category_id'   => 'required|exists:product_categories,id',
            'country_id'            => 'required|exists:countries,id',
            'description'           => 'required|string',
            'short_description'     => 'nullable|string|max:500',
            'min_order_quantity'    => 'nullable|integer|min:1',
            'is_negotiable'         => 'boolean',
            'status'                => 'required|in:active,inactive,draft',
            'is_admin_verified'     => 'boolean',
            'images'   => 'nullable|array|max:4',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'video'    => 'nullable|file|mimes:mp4,webm,mov|max:51200',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(6);

        $product = Product::create($validated);

        // Handle image uploads
        if ($request->hasFile('images')) {
            $this->handleImageUploads($request->file('images'), $product);
        }

        // Handle video upload
    if ($request->hasFile('video')) {
        $videoPath = $request->file('video')->store('products/' . $product->id . '/videos', 'public');
        $product->update(['video_url' => $videoPath]);
    }

return redirect()->route('admin.vendor.product.show', $product)
                 ->with('success', 'Product created successfully.');
    }

    // ─────────────────────────────────────────────
    //  SHOW
    // ─────────────────────────────────────────────
    public function show(Product $product)
    {
        $product->load(['user.vendor', 'productCategory', 'country', 'images' => fn($q) => $q->orderBy('sort_order')]);

        return view('admin.products.show', compact('product'));
    }

    // ─────────────────────────────────────────────
    //  EDIT
    // ─────────────────────────────────────────────
    public function edit(Product $product)
    {
        $product->load(['images' => fn($q) => $q->orderBy('sort_order')]);
        $categories = ProductCategory::orderBy('name')->get();
        $countries  = Country::orderBy('name')->get();
        $vendors    = User::whereHas('roles', fn($q) => $q->where('slug', 'vendor'))
                          ->orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories', 'countries', 'vendors'));
    }

    // ─────────────────────────────────────────────
    //  UPDATE
    // ─────────────────────────────────────────────
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'user_id'               => 'required|exists:users,id',
            'product_category_id'   => 'required|exists:product_categories,id',
            'country_id'            => 'required|exists:countries,id',
            'description'           => 'required|string',
            'short_description'     => 'nullable|string|max:500',
            'min_order_quantity'    => 'nullable|integer|min:1',
            'is_negotiable'         => 'boolean',
            'status'                => 'required|in:active,inactive,draft',
            'is_admin_verified'     => 'boolean',
            'images'        => 'nullable|array|max:4',
            'images.*'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'video'         => 'nullable|file|mimes:mp4,webm,mov|max:51200',
            'remove_video'  => 'nullable|boolean',
        ]);

        $product->update($validated);

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $this->handleImageUploads($request->file('images'), $product);
        }

        // Handle video removal
if ($request->input('remove_video') == '1' && $product->video_url) {
    Storage::disk('public')->delete($product->video_url);
    $product->update(['video_url' => null]);
}

// Handle new video upload (replaces existing)
if ($request->hasFile('video')) {
    if ($product->video_url) {
        Storage::disk('public')->delete($product->video_url);
    }
    $videoPath = $request->file('video')->store('products/' . $product->id . '/videos', 'public');
    $product->update(['video_url' => $videoPath]);
}

return redirect()->route('admin.vendor.product.show', $product)
                 ->with('success', 'Product updated successfully.');
    }

    // ─────────────────────────────────────────────
    //  DESTROY
    // ─────────────────────────────────────────────
    public function destroy(Product $product)
    {
        // Delete images from storage
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_url);
            if ($image->thumbnail_url) {
                Storage::disk('public')->delete($image->thumbnail_url);
            }
            $image->delete();
        }

        $product->delete();

return redirect()->route('admin.vendor.product.index',
                    array_filter(['vendor' => request('vendor')]))
                 ->with('success', 'Product deleted successfully.');
    }

    // ─────────────────────────────────────────────
    //  APPROVE / REJECT
    // ─────────────────────────────────────────────
    public function approve(Product $product)
    {
        $product->update(['status' => 'active', 'is_admin_verified' => true]);
        return redirect()->back()->with('success', 'Product approved successfully.');
    }

    public function reject(Product $product)
    {
        $product->update(['status' => 'inactive', 'is_admin_verified' => false]);
        return redirect()->back()->with('success', 'Product rejected.');
    }

    public function toggleVerification(Product $product)
    {
        $product->update(['is_admin_verified' => !$product->is_admin_verified]);

        return redirect()->back()->with('success', 'Verification status updated.');
    }

    // ─────────────────────────────────────────────
    //  BULK DELETE
    // ─────────────────────────────────────────────
    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:products,id']);

        $products = Product::whereIn('id', $request->ids)->get();

        foreach ($products as $product) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_url);
                $image->delete();
            }
            $product->delete();
        }

        return redirect()->back()->with('success', count($request->ids) . ' products deleted.');
    }

    // ─────────────────────────────────────────────
    //  BULK STATUS UPDATE
    // ─────────────────────────────────────────────
    public function bulkStatus(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array',
            'ids.*'  => 'exists:products,id',
            'status' => 'required|in:active,inactive,draft',
        ]);

        Product::whereIn('id', $request->ids)->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Products updated successfully.');
    }

    // ─────────────────────────────────────────────
    //  DOWNLOAD EXCEL TEMPLATE
    // ─────────────────────────────────────────────
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Products');

        // Headers
        $headers = [
            'A1' => 'name',
            'B1' => 'short_description',
            'C1' => 'description',
            'D1' => 'min_order_quantity',
            'E1' => 'is_negotiable (yes/no)',
            'F1' => 'status (active/inactive/draft)',
            'G1' => 'category_name',
            'H1' => 'country_name',
            'I1' => 'vendor_email',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getFill()
                  ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                  ->getStartColor()->setARGB('FFFF0808');
            $sheet->getStyle($cell)->getFont()->getColor()->setARGB('FFFFFFFF');
        }

        // Sample row
        $sheet->setCellValue('A2', 'Sample Product Name');
        $sheet->setCellValue('B2', 'Short description here');
        $sheet->setCellValue('C2', 'Full detailed description');
        $sheet->setCellValue('D2', 10);
        $sheet->setCellValue('E2', 'yes');
        $sheet->setCellValue('F2', 'draft');
        $sheet->setCellValue('G2', 'Electronics');
        $sheet->setCellValue('H2', 'Nigeria');
        $sheet->setCellValue('I2', 'vendor@example.com');

        // Auto-width
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Instruction sheet
        $infoSheet = $spreadsheet->createSheet();
        $infoSheet->setTitle('Instructions');
        $infoSheet->setCellValue('A1', 'BULK PRODUCT UPLOAD INSTRUCTIONS');
        $infoSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $infoSheet->setCellValue('A3', '1. Fill in the Products sheet with your product data.');
        $infoSheet->setCellValue('A4', '2. category_name must match an existing category exactly.');
        $infoSheet->setCellValue('A5', '3. country_name must match an existing country exactly.');
        $infoSheet->setCellValue('A6', '4. vendor_email must match a registered vendor account.');
        $infoSheet->setCellValue('A7', '5. is_negotiable: use "yes" or "no".');
        $infoSheet->setCellValue('A8', '6. status: use "active", "inactive", or "draft".');
        $infoSheet->setCellValue('A9', '7. Do not modify the header row.');
        $infoSheet->getColumnDimension('A')->setWidth(70);

        $spreadsheet->setActiveSheetIndex(0);

        $writer   = new Xlsx($spreadsheet);
        $filename = 'product_bulk_upload_template.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    // ─────────────────────────────────────────────
    //  BULK UPLOAD (EXCEL IMPORT)
    // ─────────────────────────────────────────────
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        try {
            $path        = $request->file('file')->getRealPath();
            $spreadsheet = IOFactory::load($path);
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray(null, true, true, true);

            $errors   = [];
            $imported = 0;

            foreach ($rows as $index => $row) {
                if ($index === 1) continue; // skip header

                $name = trim($row['A'] ?? '');
                if (empty($name)) continue;

                // Resolve category
                $category = ProductCategory::whereRaw('LOWER(name) = ?', [strtolower(trim($row['G'] ?? ''))])->first();
                if (!$category) {
                    $errors[] = "Row $index: Category '{$row['G']}' not found.";
                    continue;
                }

                // Resolve country
                $country = Country::whereRaw('LOWER(name) = ?', [strtolower(trim($row['H'] ?? ''))])->first();
                if (!$country) {
                    $errors[] = "Row $index: Country '{$row['H']}' not found.";
                    continue;
                }

                // Resolve vendor
                $vendor = User::where('email', trim($row['I'] ?? ''))->first();
                if (!$vendor) {
                    $errors[] = "Row $index: Vendor email '{$row['I']}' not found.";
                    continue;
                }

                $status = in_array(trim($row['F'] ?? ''), ['active', 'inactive', 'draft'])
                        ? trim($row['F'])
                        : 'draft';

                Product::create([
                    'name'                => $name,
                    'slug'                => Str::slug($name) . '-' . Str::random(6),
                    'short_description'   => trim($row['B'] ?? ''),
                    'description'         => trim($row['C'] ?? ''),
                    'min_order_quantity'  => (int) ($row['D'] ?? 1),
                    'is_negotiable'       => strtolower(trim($row['E'] ?? 'no')) === 'yes',
                    'status'              => $status,
                    'product_category_id' => $category->id,
                    'country_id'          => $country->id,
                    'user_id'             => $vendor->id,
                ]);

                $imported++;
            }

$message = "$imported products imported successfully.";

$redirectParams = request()->filled('redirect_vendor')
    ? ['vendor' => request('redirect_vendor')]
    : [];

if (!empty($errors)) {
    $message .= ' ' . count($errors) . ' rows had errors.';
    return redirect()->route('admin.vendor.product.index', $redirectParams)
                     ->with('success', $message)
                     ->with('import_errors', $errors);
}

return redirect()->route('admin.vendor.product.index', $redirectParams)
                 ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    //  IMAGE MANAGEMENT
    // ─────────────────────────────────────────────
    public function deleteImage(Product $product, ProductImage $image)
    {
        Storage::disk('public')->delete($image->image_url);
        if ($image->thumbnail_url) {
            Storage::disk('public')->delete($image->thumbnail_url);
        }
        $image->delete();

        // If deleted image was primary, set first remaining as primary
        if ($image->is_primary) {
            $first = $product->images()->first();
            if ($first) $first->update(['is_primary' => true]);
        }

        return response()->json(['success' => true]);
    }

    public function setPrimaryImage(Product $product, ProductImage $image)
    {
        $product->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return response()->json(['success' => true]);
    }

    public function reorderImages(Request $request, Product $product)
    {
        $request->validate(['order' => 'required|array', 'order.*' => 'integer']);

        foreach ($request->order as $sortOrder => $imageId) {
            ProductImage::where('id', $imageId)
                        ->where('product_id', $product->id)
                        ->update(['sort_order' => $sortOrder]);
        }

        return response()->json(['success' => true]);
    }

    public function uploadImages(Request $request, Product $product)
    {
        $request->validate(['images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120']);

        $uploaded = [];
        if ($request->hasFile('images')) {
            $uploaded = $this->handleImageUploads($request->file('images'), $product);
        }

        return response()->json(['success' => true, 'images' => $uploaded]);
    }

    // ─────────────────────────────────────────────
    //  PRINT
    // ─────────────────────────────────────────────
    public function print(Request $request)
    {
        $query = Product::with(['user', 'productCategory', 'country'])->latest();

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('category')) $query->where('product_category_id', $request->category);

        $products = $query->get();

        return view('admin.products.print', compact('products'));
    }

    // ─────────────────────────────────────────────
    //  HELPER
    // ─────────────────────────────────────────────
    private function handleImageUploads(array $files, Product $product): array
    {
        $uploaded   = [];
        $sortOffset = $product->images()->max('sort_order') ?? -1;
        $hasPrimary = $product->images()->where('is_primary', true)->exists();

        foreach ($files as $i => $file) {
            $path = $file->store('products/' . $product->id, 'public');

            $image = ProductImage::create([
                'product_id' => $product->id,
                'image_url'  => $path,
                'alt_text'   => $product->name,
                'sort_order' => $sortOffset + $i + 1,
                'is_primary' => (!$hasPrimary && $i === 0),
            ]);

            if (!$hasPrimary && $i === 0) $hasPrimary = true;

            $uploaded[] = [
                'id'        => $image->id,
                'url'       => Storage::url($path),
                'is_primary'=> $image->is_primary,
            ];
        }

        return $uploaded;
    }
}
