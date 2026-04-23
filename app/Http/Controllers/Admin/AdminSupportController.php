<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminSupportController extends Controller
{
    // ─── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        // ── "By User" grouped view ─────────────────────────────────────
        if ($request->view === 'users') {
            $users = \App\Models\User::whereHas('supportTickets')
                ->withCount([
                    'supportTickets',
                    'supportTickets as open_tickets_count'        => fn($q) => $q->where('status', 'open'),
                    'supportTickets as attention_tickets_count'   => fn($q) => $q->where('requires_attention', true),
                ])
                ->when($request->filled('search'), function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                      ->orWhere('email', 'like', "%{$request->search}%");
                })
                ->orderByDesc('attention_tickets_count')
                ->orderByDesc('open_tickets_count')
                ->paginate(20)
                ->withQueryString();

            $stats = [
                'total'       => SupportTicket::count(),
                'open'        => SupportTicket::where('status', 'open')->count(),
                'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
                'resolved'    => SupportTicket::where('status', 'resolved')->count(),
                'closed'      => SupportTicket::where('status', 'closed')->count(),
                'attention'   => SupportTicket::where('requires_attention', true)->count(),
            ];

            return view('admin.support.index', compact('stats'))->with([
                'viewMode' => 'users',
                'users'    => $users,
            ]);
        }

        // ── Normal list view ───────────────────────────────────────────
        $query = SupportTicket::with(['user', 'latestReply'])
            ->orderByDesc('requires_attention') // attention tickets float to top
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('attention')) {
            $query->where('requires_attention', true);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $tickets = $query->paginate(20)->withQueryString();

        $stats = [
            'total'       => SupportTicket::count(),
            'open'        => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved'    => SupportTicket::where('status', 'resolved')->count(),
            'closed'      => SupportTicket::where('status', 'closed')->count(),
            'attention'   => SupportTicket::where('requires_attention', true)->count(),
        ];

        $categories = SupportTicket::select('category')
            ->distinct()->pluck('category')->filter()->sort()->values();

        // For the user filter dropdown — only users who have tickets
        $filterUser = null;
        if ($request->filled('user_id')) {
            $filterUser = \App\Models\User::find($request->user_id);
        }

        return view('admin.support.index', compact('tickets', 'stats', 'categories', 'filterUser'))
            ->with('viewMode', 'list');
    }

    // ─── Show ─────────────────────────────────────────────────────────────────

    public function show(SupportTicket $ticket)
    {
        $ticket->load(['user', 'replies.user']);

        return view('admin.support.show', compact('ticket'));
    }

    // ─── Reply ────────────────────────────────────────────────────────────────

    public function reply(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'message'       => 'required|string|min:2|max:5000',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,zip|max:5120',
            'status'        => 'nullable|in:open,in_progress,resolved,closed',
        ]);

        $attachments = [];

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('support/attachments', 'public');
                $attachments[] = [
                    'path'          => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime'          => $file->getMimeType(),
                    'size'          => $file->getSize(),
                ];
            }
        }

        // Create the reply
        SupportTicketReply::create([
            'ticket_id'      => $ticket->id,
            'user_id'        => Auth::id(),
            'message'        => $request->message,
            'attachments'    => $attachments ?: null,
            'is_staff_reply' => true,
        ]);

        // Update ticket timestamps and optionally status
        $ticketData = ['last_replied_at' => now()];

        if ($request->filled('status') && $request->status !== $ticket->status) {
            $ticketData['status'] = $request->status;

            if ($request->status === 'resolved') {
                $ticketData['resolved_at'] = now();
            } elseif ($request->status === 'closed') {
                $ticketData['closed_at'] = now();
            } elseif ($request->status === 'open') {
                $ticketData['resolved_at'] = null;
                $ticketData['closed_at']   = null;
            }
        }

        // Auto-set in_progress if still open
        if ($ticket->status === 'open' && !isset($ticketData['status'])) {
            $ticketData['status'] = 'in_progress';
        }

        $ticket->update($ticketData);

        return redirect()
            ->route('admin.support.show', $ticket)
            ->with('success', 'Reply sent successfully.');
    }

    // ─── Update Status ────────────────────────────────────────────────────────

    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $data = ['status' => $request->status];

        match ($request->status) {
            'resolved' => $data['resolved_at'] = now(),
            'closed'   => $data['closed_at'] = now(),
            'open'     => $data += ['resolved_at' => null, 'closed_at' => null],
            default    => null,
        };

        $ticket->update($data);

        return back()->with('success', 'Ticket status updated to ' . ucfirst(str_replace('_', ' ', $request->status)) . '.');
    }

    // ─── Delete ───────────────────────────────────────────────────────────────

    public function destroy(SupportTicket $ticket)
    {
        // Remove stored attachments
        foreach ($ticket->attachments ?? [] as $att) {
            Storage::disk('public')->delete($att['path'] ?? '');
        }

        foreach ($ticket->replies as $reply) {
            foreach ($reply->attachments ?? [] as $att) {
                Storage::disk('public')->delete($att['path'] ?? '');
            }
        }

        $ticket->delete();

        return redirect()
            ->route('admin.support.index')
            ->with('success', 'Ticket deleted successfully.');
    }

    // ─── Update Priority ──────────────────────────────────────────────────────

public function updatePriority(Request $request, SupportTicket $ticket)
{
    $request->validate([
        'priority' => 'required|in:low,medium,high,urgent',
    ]);

    $ticket->update(['priority' => $request->priority]);

    return back()->with('success', 'Priority updated to ' . ucfirst($request->priority) . '.');
}

// ─── Toggle Requires Attention ────────────────────────────────────────────

    public function updateAttention(Request $request, SupportTicket $ticket)
    {
        $ticket->update(['requires_attention' => !$ticket->requires_attention]);

        $msg = $ticket->requires_attention
            ? 'Ticket flagged as requiring attention.'
            : 'Attention flag removed.';

        return back()->with('success', $msg);
    }

    // ─── Print ────────────────────────────────────────────────────────────────

    public function print(Request $request)
    {
        $tickets = SupportTicket::with(['user', 'latestReply'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->get();

        return view('admin.support.print', compact('tickets'));
    }
}
