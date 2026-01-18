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
use Illuminate\Support\Collection;

class CountryReportExport implements WithMultipleSheets
{
    protected $countryId;
    protected $startDate;
    protected $endDate;

    public function __construct($countryId, $startDate, $endDate)
    {
        $this->countryId = $countryId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {
        return [
            new CountryVendorsSheet($this->countryId, $this->startDate, $this->endDate),
            new CountryProductsSheet($this->countryId, $this->startDate, $this->endDate),
            new CountryShowroomsSheet($this->countryId, $this->startDate, $this->endDate),
            new CountryOrdersSheet($this->countryId, $this->startDate, $this->endDate),
            new CountryLoadsSheet($this->countryId, $this->startDate, $this->endDate),
            new CountryTransportersSheet($this->countryId, $this->startDate, $this->endDate),
            new CountrySummarySheet($this->countryId, $this->startDate, $this->endDate),
        ];
    }
}

class CountryVendorsSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $countryId;
    protected $startDate;
    protected $endDate;

    public function __construct($countryId, $startDate, $endDate)
    {
        $this->countryId = $countryId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
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
            'Phone',
            'City',
            'Verification Status',
            'Account Status',
            'Email Verified',
            'Created At',
        ];
    }

    public function collection()
    {
        return Vendor::with(['user', 'businessProfile'])
            ->whereHas('businessProfile', function($q) {
                $q->where('country_id', $this->countryId);
            })
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get()
            ->map(function($vendor) {
                return [
                    'id' => $vendor->id,
                    'name' => $vendor->user->name ?? 'N/A',
                    'email' => $vendor->user->email ?? 'N/A',
                    'business_name' => $vendor->businessProfile->business_name ?? 'N/A',
                    'phone' => $vendor->businessProfile->phone ?? 'N/A',
                    'city' => $vendor->businessProfile->city ?? 'N/A',
                    'verification_status' => $vendor->verification_status,
                    'account_status' => $vendor->account_status,
                    'email_verified' => $vendor->email_verified ? 'Yes' : 'No',
                    'created_at' => $vendor->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }
}

class CountryProductsSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $countryId;
    protected $startDate;
    protected $endDate;

    public function __construct($countryId, $startDate, $endDate)
    {
        $this->countryId = $countryId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
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
            'Vendor',
            'Status',
            'Admin Verified',
            'Views',
            'Min Order Quantity',
            'Is Negotiable',
            'Created At',
        ];
    }

    public function collection()
    {
        return Product::with(['productCategory', 'user'])
            ->where('country_id', $this->countryId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->productCategory->name ?? 'N/A',
                    'vendor' => $product->user->name ?? 'N/A',
                    'status' => $product->status,
                    'admin_verified' => $product->is_admin_verified ? 'Yes' : 'No',
                    'views' => $product->views,
                    'min_order_qty' => $product->min_order_quantity,
                    'is_negotiable' => $product->is_negotiable ? 'Yes' : 'No',
                    'created_at' => $product->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }
}

class CountryShowroomsSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $countryId;
    protected $startDate;
    protected $endDate;

    public function __construct($countryId, $startDate, $endDate)
    {
        $this->countryId = $countryId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function title(): string
    {
        return 'Showrooms';
    }

    public function headings(): array
    {
        return [
            'Showroom Number',
            'Name',
            'City',
            'Status',
            'Is Verified',
            'Is Featured',
            'Is Authorized Dealer',
            'Views Count',
            'Inquiries Count',
            'Rating',
            'Created At',
        ];
    }

    public function collection()
    {
        return Showroom::where('country_id', $this->countryId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get()
            ->map(function($showroom) {
                return [
                    'showroom_number' => $showroom->showroom_number,
                    'name' => $showroom->name,
                    'city' => $showroom->city,
                    'status' => $showroom->status,
                    'is_verified' => $showroom->is_verified ? 'Yes' : 'No',
                    'is_featured' => $showroom->is_featured ? 'Yes' : 'No',
                    'is_authorized_dealer' => $showroom->is_authorized_dealer ? 'Yes' : 'No',
                    'views_count' => $showroom->views_count,
                    'inquiries_count' => $showroom->inquiries_count,
                    'rating' => $showroom->rating,
                    'created_at' => $showroom->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }
}

class CountryOrdersSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $countryId;
    protected $startDate;
    protected $endDate;

    public function __construct($countryId, $startDate, $endDate)
    {
        $this->countryId = $countryId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function title(): string
    {
        return 'Orders';
    }

    public function headings(): array
    {
        return [
            'Order Number',
            'Buyer Name',
            'Buyer Email',
            'Vendor Name',
            'Status',
            'Payment Status',
            'Subtotal',
            'Tax',
            'Shipping Fee',
            'Total',
            'Currency',
            'Created At',
            'Delivered At',
        ];
    }

    public function collection()
    {
        return Order::with(['buyer', 'vendor'])
            ->whereHas('vendor.businessProfile', function($q) {
                $q->where('country_id', $this->countryId);
            })
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get()
            ->map(function($order) {
                return [
                    'order_number' => $order->order_number,
                    'buyer_name' => $order->buyer->name ?? 'N/A',
                    'buyer_email' => $order->buyer->email ?? 'N/A',
                    'vendor_name' => $order->vendor->name ?? 'N/A',
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'subtotal' => $order->subtotal,
                    'tax' => $order->tax,
                    'shipping_fee' => $order->shipping_fee,
                    'total' => $order->total,
                    'currency' => $order->currency,
                    'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                    'delivered_at' => $order->delivered_at ? $order->delivered_at->format('Y-m-d H:i:s') : 'N/A',
                ];
            });
    }
}

class CountryLoadsSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $countryId;
    protected $startDate;
    protected $endDate;

    public function __construct($countryId, $startDate, $endDate)
    {
        $this->countryId = $countryId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
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
            'Destination City',
            'Cargo Type',
            'Weight',
            'Weight Unit',
            'Status',
            'Budget',
            'Currency',
            'Assigned Transporter',
            'Pickup Date',
            'Delivery Date',
            'Created At',
        ];
    }

    public function collection()
    {
        return Load::with(['assignedTransporter'])
            ->where(function($q) {
                $q->where('origin_country_id', $this->countryId)
                  ->orWhere('destination_country_id', $this->countryId);
            })
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get()
            ->map(function($load) {
                return [
                    'load_number' => $load->load_number,
                    'origin_city' => $load->origin_city,
                    'destination_city' => $load->destination_city,
                    'cargo_type' => $load->cargo_type,
                    'weight' => $load->weight,
                    'weight_unit' => $load->weight_unit,
                    'status' => $load->status,
                    'budget' => $load->budget,
                    'currency' => $load->currency,
                    'assigned_transporter' => $load->assignedTransporter->company_name ?? 'N/A',
                    'pickup_date' => $load->pickup_date ? $load->pickup_date->format('Y-m-d') : 'N/A',
                    'delivery_date' => $load->delivery_date ? $load->delivery_date->format('Y-m-d') : 'N/A',
                    'created_at' => $load->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }
}

class CountryTransportersSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $countryId;
    protected $startDate;
    protected $endDate;

    public function __construct($countryId, $startDate, $endDate)
    {
        $this->countryId = $countryId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
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
            'Registration Number',
            'Email',
            'Phone',
            'Status',
            'Is Verified',
            'Fleet Size',
            'Average Rating',
            'Total Deliveries',
            'Successful Deliveries',
            'Created At',
        ];
    }

    public function collection()
    {
        return Transporter::where('country_id', $this->countryId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get()
            ->map(function($transporter) {
                return [
                    'id' => $transporter->id,
                    'company_name' => $transporter->company_name,
                    'registration_number' => $transporter->registration_number,
                    'email' => $transporter->email,
                    'phone' => $transporter->phone,
                    'status' => $transporter->status,
                    'is_verified' => $transporter->is_verified ? 'Yes' : 'No',
                    'fleet_size' => $transporter->fleet_size,
                    'average_rating' => $transporter->average_rating,
                    'total_deliveries' => $transporter->total_deliveries,
                    'successful_deliveries' => $transporter->successful_deliveries,
                    'created_at' => $transporter->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }
}

class CountrySummarySheet implements FromCollection, WithHeadings, WithTitle
{
    protected $countryId;
    protected $startDate;
    protected $endDate;

    public function __construct($countryId, $startDate, $endDate)
    {
        $this->countryId = $countryId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
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
        $vendorsTotal = Vendor::whereHas('businessProfile', function($q) {
            $q->where('country_id', $this->countryId);
        })->count();

        $productsTotal = Product::where('country_id', $this->countryId)->count();

        $showroomsTotal = Showroom::where('country_id', $this->countryId)->count();

        $ordersTotal = Order::whereHas('vendor.businessProfile', function($q) {
            $q->where('country_id', $this->countryId);
        })->count();

        $ordersValue = Order::whereHas('vendor.businessProfile', function($q) {
            $q->where('country_id', $this->countryId);
        })->sum('total');

        $transportersTotal = Transporter::where('country_id', $this->countryId)->count();

        $loadsTotal = Load::where(function($q) {
            $q->where('origin_country_id', $this->countryId)
              ->orWhere('destination_country_id', $this->countryId);
        })->count();

        return collect([
            ['Total Vendors', $vendorsTotal],
            ['Total Products', $productsTotal],
            ['Total Showrooms', $showroomsTotal],
            ['Total Orders', $ordersTotal],
            ['Total Orders Value', '$' . number_format($ordersValue, 2)],
            ['Total Transporters', $transportersTotal],
            ['Total Loads', $loadsTotal],
            ['Report Period', $this->startDate . ' to ' . $this->endDate],
        ]);
    }
}
