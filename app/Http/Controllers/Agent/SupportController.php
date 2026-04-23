<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\SupportFaq;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    // ─── INDEX (hub) ──────────────────────────────────────────────────
    public function index()
    {
        $userId = auth()->id();

        $stats = [
            'open'        => SupportTicket::where('user_id', $userId)->open()->count(),
            'in_progress' => SupportTicket::where('user_id', $userId)->inProgress()->count(),
            'resolved'    => SupportTicket::where('user_id', $userId)->resolved()->count(),
            'closed'      => SupportTicket::where('user_id', $userId)->closed()->count(),
            'total'       => SupportTicket::where('user_id', $userId)->count(),
        ];

        $recentTickets = SupportTicket::where('user_id', $userId)
            ->with('latestReply.user')
            ->latest()
            ->take(5)
            ->get();

        $faqs = SupportFaq::active()->take(6)->get();

        return view('agent.support.index', compact('stats', 'recentTickets', 'faqs'));
    }

    // ─── TICKETS LIST ─────────────────────────────────────────────────
    public function tickets(Request $request)
    {
        $userId = auth()->id();

        $tickets = SupportTicket::where('user_id', $userId)
            ->with('latestReply.user')
            ->when($request->search, fn($q) =>
                $q->where('subject', 'like', "%{$request->search}%")
                  ->orWhere('ticket_number', 'like', "%{$request->search}%")
            )
            ->when($request->status, fn($q) =>
                $q->where('status', $request->status)
            )
            ->when($request->category, fn($q) =>
                $q->where('category', $request->category)
            )
            ->when($request->priority, fn($q) =>
                $q->where('priority', $request->priority)
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'open'        => SupportTicket::where('user_id', $userId)->open()->count(),
            'in_progress' => SupportTicket::where('user_id', $userId)->inProgress()->count(),
            'resolved'    => SupportTicket::where('user_id', $userId)->resolved()->count(),
            'total'       => SupportTicket::where('user_id', $userId)->count(),
        ];

        return view('agent.support.tickets', compact('tickets', 'stats'));
    }

    // ─── CREATE TICKET ────────────────────────────────────────────────
    public function createTicket()
    {
        return view('agent.support.create');
    }

    // ─── STORE TICKET ─────────────────────────────────────────────────
    public function storeTicket(Request $request)
    {

    $validated = $request->validate([
        'subject'     => 'required|string|max:255',
        'category'    => 'required|in:general,technical,billing,vendor,account,other',
        'description' => 'required|string|max:10000',
    ]);

        $ticket = SupportTicket::create([
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'user_id'       => auth()->id(),
            'subject'       => $validated['subject'],
            'category'      => $validated['category'],
            'priority'      => 'low',
            'description'   => $validated['description'],
            'status'        => 'open',
            'requires_attention' => $request->boolean('requires_attention'),
        ]);

        return redirect()
            ->route('agent.support.ticket.show', $ticket->id)
            ->with('success', "Ticket {$ticket->ticket_number} created. Our team will respond shortly.");
    }

    // ─── SHOW TICKET ──────────────────────────────────────────────────
    public function showTicket($ticket)
    {
        $ticket = SupportTicket::where('user_id', auth()->id())
            ->with(['replies.user'])
            ->findOrFail($ticket);

        return view('agent.support.show', compact('ticket'));
    }

    // ─── REPLY TICKET ─────────────────────────────────────────────────
    public function replyTicket(Request $request, $ticket)
    {
        $ticket = SupportTicket::where('user_id', auth()->id())
            ->findOrFail($ticket);

        if ($ticket->isClosed()) {
            return back()->with('error', 'This ticket is closed. Please open a new ticket.');
        }

        $request->validate([
            'message' => 'required|string|max:10000',
        ]);

        SupportTicketReply::create([
            'ticket_id'      => $ticket->id,
            'user_id'        => auth()->id(),
            'message'        => $request->message,
            'is_staff_reply' => false,
        ]);

        $ticket->update([
            'status'          => 'open',
            'last_replied_at' => now(),
        ]);

        return back()->with('success', 'Reply sent successfully.');
    }

    // ─── CLOSE TICKET ─────────────────────────────────────────────────
    public function closeTicket($ticket)
    {
        $ticket = SupportTicket::where('user_id', auth()->id())
            ->findOrFail($ticket);

        $ticket->close();

        return back()->with('success', 'Ticket has been closed.');
    }

    // ─── FAQ ──────────────────────────────────────────────────────────
    public function faq(Request $request)
    {
        $faqs = SupportFaq::active()
            ->when($request->category, fn($q) =>
                $q->where('category', $request->category)
            )
            ->when($request->search, fn($q) =>
                $q->where('question', 'like', "%{$request->search}%")
                  ->orWhere('answer', 'like', "%{$request->search}%")
            )
            ->get()
            ->groupBy('category');

        $categories = SupportFaq::active()
            ->distinct()
            ->pluck('category');

        return view('agent.support.faq', compact('faqs', 'categories'));
    }

    // ─── CONTACT ──────────────────────────────────────────────────────
    public function contact()
    {
        return view('agent.support.contact');
    }

    // ─── SEND CONTACT ─────────────────────────────────────────────────
    public function sendContact(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // Convert the contact form into a ticket for tracking
        $ticket = SupportTicket::create([
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'user_id'       => auth()->id(),
            'subject'       => $validated['subject'],
            'category'      => 'general',
            'priority'      => 'medium',
            'description'   => "**Contact Form Submission**\n\nName: {$validated['name']}\nEmail: {$validated['email']}\n\n{$validated['message']}",
            'status'        => 'open',
        ]);

        // TODO: Mail::to('support@yourapp.com')->send(new ContactFormMail($validated, $ticket));

        return redirect()
            ->route('agent.support.index')
            ->with('success', "Message sent! Your reference is {$ticket->ticket_number}.");
    }
}
