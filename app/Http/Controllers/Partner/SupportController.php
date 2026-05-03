<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('user_id', auth()->id())
                      ->latest()
                      ->take(5)
                      ->get();

        $stats = [
            'open'       => SupportTicket::where('user_id', auth()->id())->where('status', 'open')->count(),
            'in_progress'=> SupportTicket::where('user_id', auth()->id())->where('status', 'in_progress')->count(),
            'closed'     => SupportTicket::where('user_id', auth()->id())->where('status', 'closed')->count(),
        ];

        return view('partner.support.index', compact('tickets', 'stats'));
    }

    public function tickets()
    {
        $tickets = SupportTicket::where('user_id', auth()->id())
                      ->latest()
                      ->paginate(15);

        return view('partner.support.tickets', compact('tickets'));
    }

    public function createTicket()
    {
        return view('partner.support.create');
    }

    public function storeTicket(Request $request)
    {
        $request->validate([
            'subject'  => 'required|string|max:255',
            'category' => 'required|in:general,billing,technical,partnership,other',
            'priority' => 'required|in:low,medium,high',
            'message'  => 'required|string|max:5000',
        ]);

        SupportTicket::create([
            'user_id'  => auth()->id(),
            'subject'  => $request->subject,
            'ticket_number' => 'TCKT-' . strtoupper(uniqid()),
            'category' => $request->category,
            'priority' => $request->priority,
            'message'  => $request->message,
            'description' => $request->message,
            'status'   => 'open',
        ]);

        return redirect()->route('partner.support.tickets')
                         ->with('success', 'Support ticket created. We\'ll get back to you shortly.');
    }

    public function showTicket(SupportTicket $ticket)
    {
        abort_if($ticket->user_id !== auth()->id(), 403);
        $ticket->load('replies.user');
        return view('partner.support.show', compact('ticket'));
    }

    public function replyTicket(Request $request, SupportTicket $ticket)
    {
        abort_if($ticket->user_id !== auth()->id(), 403);

        $request->validate(['message' => 'required|string|max:5000']);

        // Use replies relationship if it exists, otherwise store in ticket notes
        if (method_exists($ticket, 'replies')) {
            $ticket->replies()->create([
                'user_id' => auth()->id(),
                'message' => $request->message,
            ]);
        }

        if ($ticket->status === 'closed') {
            $ticket->update(['status' => 'open']);
        }

        return back()->with('success', 'Reply sent.');
    }

    public function closeTicket(SupportTicket $ticket)
    {
        abort_if($ticket->user_id !== auth()->id(), 403);
        $ticket->update(['status' => 'closed']);
        return back()->with('success', 'Ticket closed.');
    }

    public function faq()
    {
        return view('partner.support.faq');
    }

    public function contact()
    {
        return view('partner.support.contact');
    }

    public function sendContact(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // Create a general support ticket from the contact form
        SupportTicket::create([
            'user_id'  => auth()->id(),
            'subject'  => $request->subject,
            'category' => 'general',
            'priority' => 'medium',
            'message'  => $request->message,
            'status'   => 'open',
        ]);

        return back()->with('success', 'Your message has been sent. We\'ll respond within 24 hours.');
    }
}
