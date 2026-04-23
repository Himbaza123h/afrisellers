<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ServiceDeliveredMail;
use App\Models\ServiceDelivery;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ServiceDeliveryController extends Controller
{

public function index(Request $request)
{
    $query = ServiceDelivery::with(['user', 'plan', 'deliveredBy'])
        ->latest();

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('search')) {
        $query->whereHas('user', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('email', 'like', '%' . $request->search . '%');
        })->orWhere('service_name', 'like', '%' . $request->search . '%');
    }

    // Group by user
    $deliveries = $query->get()->groupBy('user_id');

    $stats = [
        'total'       => ServiceDelivery::count(),
        'pending'     => ServiceDelivery::where('status', 'pending')->count(),
        'in_progress' => ServiceDelivery::where('status', 'in_progress')->count(),
        'delivered'   => ServiceDelivery::where('status', 'delivered')->count(),
    ];

    return view('admin.service-deliveries.index', compact('deliveries', 'stats'));
}

    public function bulkUpdateStatus(Request $request, $userId)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,delivered,rejected',
            'notes'  => 'nullable|string|max:1000',
        ]);

        $deliveries = ServiceDelivery::where('user_id', $userId)->get();

        foreach ($deliveries as $delivery) {
            $wasDelivered = $delivery->status !== 'delivered' && $request->status === 'delivered';

            $delivery->update([
                'status'       => $request->status,
                'notes'        => $request->notes,
                'delivered_by' => $request->status === 'delivered' ? auth()->id() : $delivery->delivered_by,
                'delivered_at' => $request->status === 'delivered' ? now() : $delivery->delivered_at,
            ]);

            if ($wasDelivered && $request->boolean('notify_user', true)) {
                Mail::to($delivery->user->email)
                    ->send(new ServiceDeliveredMail($delivery));
            }
        }

        return back()->with('success', 'All services for this vendor updated successfully!');
    }

    public function show(ServiceDelivery $serviceDelivery)
    {
        $serviceDelivery->load(['user', 'plan', 'subscription', 'deliveredBy']);
        return view('admin.service-deliveries.show', compact('serviceDelivery'));
    }

    public function updateStatus(Request $request, ServiceDelivery $serviceDelivery)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,delivered,rejected',
            'notes'  => 'nullable|string|max:1000',
        ]);

        $wasDelivered = $serviceDelivery->status !== 'delivered' && $request->status === 'delivered';

        $serviceDelivery->update([
            'status'       => $request->status,
            'notes'        => $request->notes,
            'delivered_by' => $request->status === 'delivered' ? auth()->id() : $serviceDelivery->delivered_by,
            'delivered_at' => $request->status === 'delivered' ? now() : $serviceDelivery->delivered_at,
        ]);

        // Send email notification when marked as delivered
        if ($wasDelivered && $request->boolean('notify_user', true)) {
            Mail::to($serviceDelivery->user->email)
                ->send(new ServiceDeliveredMail($serviceDelivery));
        }

        return back()->with('success', 'Service status updated successfully!');
    }
}
