<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\Models\Vendor\Vendor;
use App\Models\Product;
use App\Models\Showroom;
use App\Models\Order;
use App\Models\Load;
use App\Models\Transporter;
use App\Models\Country;
use Illuminate\Support\Collection;

class RegionalReportExport implements WithMultipleSheets
{
    protected $region;
    protected $startDate;
    protected $endDate;
    protected $countryFilter;

    public function __construct($region, $startDate, $endDate, $countryFilter = null)
    {
        $this->region = $region;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->countryFilter = $countryFilter;
    }

    public function sheets(): array
    {
        return [
            new VendorsSheet($this->region, $this->startDate, $this->endDate, $this->countryFilter),
            new ProductsSheet($this->region, $this->startDate, $this->endDate, $this->countryFilter),
            new OrdersSheet($this->region, $this->startDate, $this->endDate, $this->countryFilter),
            new LoadsSheet($this->region, $this->startDate, $this->endDate, $this->countryFilter),
            new TransportersSheet($this->region, $this->startDate, $this->endDate, $this->countryFilter),
            new SummarySheet($this->region, $this->startDate, $this->endDate, $this->countryFilter),
        ];
    }
}

class VendorsSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $region;
    protected $startDate;
    protected $endDate;
    protected $countryFilter;

    public function __construct($region, $startDate, $endDate, $countryFilter)
    {
        $this->region = $region;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->countryFilter = $countryFilter;
    }

    public function title(): string
    {
        return 'Vendors';
    }

    public function headings(): array
    {
        return [
            'Vendor ID',
            'Name',
            'Email',
            'Business Name',
            'Country',
            'Verification Status',
            'Account Status',
            'Email Verified',
            'Created At',
        ];
    }

    public function collection()
    {
        $countries = Country::where('region_id', $this->region)->pluck('id')->toArray();

        if ($this->countryFilter) {
            $countries = [$this->countryFilter];
        }

        return Vendor::with(['user', 'businessProfile.country'])
            ->whereHas('businessProfile', function($q) use ($countries) {
                $q->whereIn('country_id', $countries);
            })
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get()
            ->map(function($vendor) {
                return [
                    'id' => $vendor->id,
                    'name' => $vendor->user->name ?? 'N/A',
                    'email' => $vendor->user->email ?? 'N/A',
                    'business_name' => $vendor->businessProfile->business_name ?? 'N/A',
                    'country' => $vendor->businessProfile->country->name ?? 'N/A',
                    'verification_status' => $vendor->verification_status,
                    'account_status' => $vendor->account_status,
                    'email_verified' => $vendor->email_verified ? 'Yes' : 'No',
                    'created_at' => $vendor->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }
}

class ProductsSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $region;
    protected $startDate;
    protected $endDate;
    protected $countryFilter;

    public function __construct($region, $startDate, $endDate, $countryFilter)
    {
        $this->region = $region;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->countryFilter = $countryFilter;
    }

    public function title(): string
    {
        return 'Products';
    }

    public function headings(): array
    {
        return [
            'Product ID',
            'Name',
            'Category',
            'Country',
            'Status',
            'Admin Verified',
            'Views',
            'Min Order Quantity',
            'Created At',
        ];
    }

    public function collection()
    {
        $countries = Country::where('region_id', $this->region)->pluck('id')->toArray();

        if ($this->countryFilter) {
            $countries = [$this->countryFilter];
        }

        return Product::with(['productCategory', 'country'])
            ->whereIn('country_id', $countries)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->productCategory->name ?? 'N/A',
                    'country' => $product->country->name ?? 'N/A',
                    'status' => $product->status,
                    'admin_verified' => $product->is_admin_verified ? 'Yes' : 'No',
                    'views' => $product->views,
                    'min_order_qty' => $product->min_order_quantity,
                    'created_at' => $product->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }
}

class OrdersSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $region;
    protected $startDate;
    protected $endDate;
    protected $countryFilter;

    public function __construct($region, $startDate, $endDate, $countryFilter)
    {
        $this->region = $region;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->countryFilter = $countryFilter;
    }

    public function title(): string
    {
        return 'Orders';
    }

    public function headings(): array
    {
        return [
            'Order Number',
            'Buyer',
            'Vendor',
            'Country',
            'Status',
            'Payment Status',
            'Subtotal',
            'Tax',
            'Shipping',
            'Total',
            'Currency',
            'Created At',
        ];
    }

    public function collection()
    {
        $countries = Country::where('region_id', $this->region)->pluck('id')->toArray();

        if ($this->countryFilter) {
            $countries = [$this->countryFilter];
        }

        return Order::with(['buyer', 'vendor.businessProfile.country'])
            ->whereHas('vendor.businessProfile', function($q) use ($countries) {
                $q->whereIn('country_id', $countries);
            })
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get()
            ->map(function($order) {
                return [
                    'order_number' => $order->order_number,
                    'buyer' => $order->buyer->name ?? 'N/A',
                    'vendor' => $order->vendor->name ?? 'N/A',
                    'country' => $order->vendor->businessProfile->country->name ?? 'N/A',
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'subtotal' => $order->subtotal,
                    'tax' => $order->tax,
                    'shipping' => $order->shipping_fee,
                    'total' => $order->total,
                    'currency' => $order->currency,
                    'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }
}

class LoadsSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $region;
    protected $startDate;
    protected $endDate;
    protected $countryFilter;

    public function __construct($region, $startDate, $endDate, $countryFilter)
    {
        $this->region = $region;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->countryFilter = $countryFilter;
    }

    public function title(): string
    {
        return 'Loads';
    }

    public function headings(): array
    {
        return [
            'Load Number',
            'Origin City',
            'Origin Country',
            'Destination City',
            'Destination Country',
            'Cargo Type',
            'Weight',
            'Status',
            'Budget',
            'Currency',
            'Created At',
        ];
    }

    public function collection()
    {
        $countries = Country::where('region_id', $this->region)->pluck('id')->toArray();

        if ($this->countryFilter) {
            $countries = [$this->countryFilter];
        }

        return Load::with(['originCountry', 'destinationCountry'])
            ->where(function($q) use ($countries) {
                $q->whereIn('origin_country_id', $countries)
                  ->orWhereIn('destination_country_id', $countries);
            })
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get()
            ->map(function($load) {
                return [
                    'load_number' => $load->load_number,
                    'origin_city' => $load->origin_city,
                    'origin_country' => $load->originCountry->name ?? 'N/A',
                    'destination_city' => $load->destination_city,
                    'destination_country' => $load->destinationCountry->name ?? 'N/A',
                    'cargo_type' => $load->cargo_type,
                    'weight' => $load->weight . ' ' . $load->weight_unit,
                    'status' => $load->status,
                    'budget' => $load->budget,
                    'currency' => $load->currency,
                    'created_at' => $load->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }
}

class TransportersSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $region;
    protected $startDate;
    protected $endDate;
    protected $countryFilter;

    public function __construct($region, $startDate, $endDate, $countryFilter)
    {
        $this->region = $region;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->countryFilter = $countryFilter;
    }

    public function title(): string
    {
        return 'Transporters';
    }

    public function headings(): array
    {
        return [
            'Transporter ID',
            'Company Name',
            'Email',
            'Phone',
            'Country',
            'Fleet Size',
            'Status',
            'Verified',
            'Average Rating',
            'Total Deliveries',
            'Created At',
        ];
    }

    public function collection()
    {
        $countries = Country::where('region_id', $this->region)->pluck('id')->toArray();

        if ($this->countryFilter) {
            $countries = [$this->countryFilter];
        }

        return Transporter::with(['country', 'user'])
            ->whereIn('country_id', $countries)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get()
            ->map(function($transporter) {
                return [
                    'id' => $transporter->id,
                    'company_name' => $transporter->company_name,
                    'email' => $transporter->email,
                    'phone' => $transporter->phone,
                    'country' => $transporter->country->name ?? 'N/A',
                    'fleet_size' => $transporter->fleet_size,
                    'status' => $transporter->status,
                    'verified' => $transporter->is_verified ? 'Yes' : 'No',
                    'average_rating' => $transporter->average_rating,
                    'total_deliveries' => $transporter->total_deliveries,
                    'created_at' => $transporter->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }
}

class SummarySheet implements FromCollection, WithHeadings, WithTitle
{
    protected $region;
    protected $startDate;
    protected $endDate;
    protected $countryFilter;

    public function __construct($region, $startDate, $endDate, $countryFilter)
    {
        $this->region = $region;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->countryFilter = $countryFilter;
    }

    public function title(): string
    {
        return 'Summary';
    }

    public function headings(): array
    {
        return ['Metric', 'Value'];
    }

    public function collection()
    {
        $countries = Country::where('region_id', $this->region)->pluck('id')->toArray();

        if ($this->countryFilter) {
            $countries = [$this->countryFilter];
        }

        $vendorsTotal = Vendor::whereHas('businessProfile', function($q) use ($countries) {
            $q->whereIn('country_id', $countries);
        })->count();

        $productsTotal = Product::whereIn('country_id', $countries)->count();

        $ordersTotal = Order::whereHas('vendor.businessProfile', function($q) use ($countries) {
            $q->whereIn('country_id', $countries);
        })->count();

        $ordersValue = Order::whereHas('vendor.businessProfile', function($q) use ($countries) {
            $q->whereIn('country_id', $countries);
        })->sum('total');

        return collect([
            ['Total Vendors', $vendorsTotal],
            ['Total Products', $productsTotal],
            ['Total Orders', $ordersTotal],
            ['Total Orders Value', '$' . number_format($ordersValue, 2)],
            ['Report Period', $this->startDate . ' to ' . $this->endDate],
            ['Region', $this->region],
        ]);
    }
}
