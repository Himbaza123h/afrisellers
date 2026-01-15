<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\RFQs;
use App\Models\RFQMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RFQController extends Controller
{
    /**
     * Display a listing of the buyer's RFQs.
     */
/**
 * Display a listing of the buyer's RFQs.
 */
public function index()
{
    try {
        $userId = auth()->id();
        $status = request('status', 'all');

        // Start the query
        $query = RFQs::where('user_id', $userId)
            ->with(['product', 'country', 'messages.user'])
            ->withCount('messages');

        // Apply status filter if not 'all'
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Order and paginate
        $rfqs = $query->latest()->paginate(15);

        // Keep the filter parameter in pagination links
        $rfqs->appends(['status' => $status]);

        return view('buyer.rfq.index', compact('rfqs'));
    } catch (\Exception $e) {
        Log::error('Buyer RFQ Index Error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('buyer.dashboard.home')
            ->with('error', 'An error occurred while loading your RFQs.');
    }
}

    /**
     * Show vendors who responded to a specific RFQ.
     */
    public function showVendors(RFQs $rfq)
    {
        try {
            // Ensure the RFQ belongs to the authenticated buyer
            if ($rfq->user_id !== auth()->id()) {
                abort(403, 'You do not have permission to view this RFQ.');
            }

            $rfq->load(['product', 'country', 'messages.user']);

            // Get all messages as collection
            $allMessages = $rfq->messages()->with('user')->get();

            // Get unique vendors who responded (users with vendor messages)
            $vendors = $allMessages
                ->where('sender_type', 'vendor')
                ->pluck('user')
                ->filter()
                ->unique('id')
                ->map(function ($vendor) use ($allMessages) {
                    $vendor->messages_count = $allMessages->where('user_id', $vendor->id)->count();
                    return $vendor;
                })
                ->values();

            return view('buyer.rfq.vendors', compact('rfq', 'vendors', 'allMessages'));
        } catch (\Exception $e) {
            Log::error('Buyer RFQ Vendors Error: ' . $e->getMessage(), [
                'rfq_id' => $rfq->id,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('buyer.rfqs.index')
                ->with('error', 'An error occurred while loading vendors.');
        }
    }

    /**
     * Show messages from a specific vendor for an RFQ.
     */
    public function showMessages(RFQs $rfq, $vendorId)
    {
        try {
            // Ensure the RFQ belongs to the authenticated buyer
            if ($rfq->user_id !== auth()->id()) {
                abort(403, 'You do not have permission to view this RFQ.');
            }

            $rfq->load(['product', 'country', 'messages.user']);

            // Get all messages as collection
            $allMessages = $rfq->messages()->with('user')->get();

            // Get the vendor
            $vendor = \App\Models\User::find($vendorId);
            if (!$vendor) {
                return redirect()->route('buyer.rfqs.vendors', $rfq)
                    ->with('error', 'Vendor not found.');
            }

            // Filter messages: show messages from vendor AND messages from buyer (conversation thread)
            $buyerId = auth()->id();
            $messages = $allMessages->filter(function ($message) use ($vendorId, $buyerId) {
                // Include messages from the vendor
                if ($message->user_id == $vendorId && $message->sender_type === 'vendor') {
                    return true;
                }
                // Include messages from the buyer (in this conversation thread)
                if ($message->user_id == $buyerId && $message->sender_type === 'buyer') {
                    return true;
                }
                return false;
            })->sortBy('created_at')->values();

            // Ensure messages is a collection
            if (!$messages instanceof \Illuminate\Support\Collection) {
                $messages = collect($messages);
            }

            return view('buyer.rfq.messages', compact('rfq', 'vendor', 'messages', 'allMessages'));
        } catch (\Exception $e) {
            Log::error('Buyer RFQ Messages Error: ' . $e->getMessage(), [
                'rfq_id' => $rfq->id,
                'vendor_id' => $vendorId,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('buyer.rfqs.vendors', $rfq)
                ->with('error', 'An error occurred while loading messages.');
        }
    }

    /**
     * Close an RFQ.
     */
    public function close(RFQs $rfq)
    {
        try {
            // Ensure the RFQ belongs to the authenticated buyer
            if ($rfq->user_id !== auth()->id()) {
                abort(403, 'You do not have permission to close this RFQ.');
            }

            // Check if already closed
            if ($rfq->isClosed()) {
                return back()->with('error', 'This RFQ is already closed.');
            }

            // Close the RFQ
            $rfq->update(['status' => 'closed']);

            Log::info('RFQ closed', [
                'rfq_id' => $rfq->id,
                'user_id' => auth()->id(),
            ]);

            return back()->with('success', 'RFQ closed successfully. No further messages can be sent.');
        } catch (\Exception $e) {
            Log::error('Buyer RFQ Close Error: ' . $e->getMessage(), [
                'rfq_id' => $rfq->id,
                'user_id' => auth()->id(),
            ]);

            return back()->with('error', 'Failed to close RFQ. Please try again.');
        }
    }

    /**
     * Store a message for an RFQ.
     */
    public function storeMessage(Request $request, RFQs $rfq)
    {
        try {
            // Ensure the RFQ belongs to the authenticated buyer
            if ($rfq->user_id !== auth()->id()) {
                abort(403, 'You do not have permission to send messages for this RFQ.');
            }

            // Check if RFQ is closed
            if ($rfq->isClosed()) {
                return back()->with('error', 'This RFQ is closed. No further messages can be sent.');
            }

            $validated = $request->validate([
                'message' => 'required|string|max:5000',
            ]);

            RFQMessage::create([
                'rfq_id' => $rfq->id,
                'user_id' => auth()->id(),
                'message' => $validated['message'],
                'sender_type' => 'buyer',
            ]);

            // Get vendor_id from request if present (to redirect back to vendor messages)
            $vendorId = $request->get('vendor_id');
            if ($vendorId) {
                return redirect()->route('buyer.rfqs.messages', ['rfq' => $rfq, 'vendor' => $vendorId])
                    ->with('success', 'Message sent successfully.');
            }

            // Otherwise redirect to vendors list
            return redirect()->route('buyer.rfqs.vendors', $rfq)
                ->with('success', 'Message sent successfully.');
        } catch (\Exception $e) {
            Log::error('Buyer RFQ Message Store Error: ' . $e->getMessage(), [
                'rfq_id' => $rfq->id,
                'user_id' => auth()->id(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to send message. Please try again.');
        }
    }
}
