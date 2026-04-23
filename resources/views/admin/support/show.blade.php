@extends('layouts.home')

@section('page-content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.support.index') }}"
               class="w-8 h-8 bg-white border border-gray-200 rounded-lg flex items-center justify-center text-gray-500 hover:bg-gray-50 shadow-sm transition-colors">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $ticket->subject }}</h1>
                <p class="mt-0.5 text-xs text-gray-500 font-mono">{{ $ticket->ticket_number }}</p>
            </div>
        </div>

        {{-- Quick Status Change --}}
        <form action="{{ route('admin.support.status', $ticket) }}" method="POST" class="flex items-center gap-2">
            @csrf @method('PATCH')
            <select name="status"
                    onchange="this.form.submit()"
                    class="px-3 py-2 rounded-lg border border-gray-200 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[#ff0808]/20 focus:border-[#ff0808] bg-white">
                @foreach(['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'closed' => 'Closed'] as $val => $label)
                    <option value="{{ $val }}" {{ $ticket->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </form>
    </div>

        {{-- Attention Banner --}}
    @if($ticket->requires_attention)
    <div class="p-4 bg-red-50 rounded-xl border border-red-200 flex items-center gap-3">
        <i class="fas fa-exclamation-triangle text-red-500 text-lg flex-shrink-0"></i>
        <div class="flex-1">
            <p class="text-sm font-bold text-red-800">This ticket requires admin attention</p>
            <p class="text-xs text-red-600 mt-0.5">The user flagged this as urgent. Please review and respond promptly.</p>
        </div>
    </div>
    @endif
    {{-- Alerts --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm text-green-900 font-medium flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 bg-red-50 rounded-lg border border-red-200">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li class="text-sm text-red-700">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ── Left: Conversation ── --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Conversation Thread --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-800">Conversation</h2>
                </div>

                <div class="p-5 space-y-5 max-h-[520px] overflow-y-auto" id="conversation">

                    {{-- Opening message --}}
                    <div class="flex gap-3">
                        <div class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0 font-bold text-gray-500 text-sm">
                            {{ strtoupper(substr($ticket->user?->name ?? '?', 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="text-sm font-semibold text-gray-800">{{ $ticket->user?->name ?? 'User' }}</span>
                                <span class="text-[10px] text-gray-400">{{ $ticket->created_at->format('M d, Y \a\t H:i') }}</span>
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-500 text-[10px] font-semibold rounded-full">Original</span>
                            </div>
                            <div class="bg-gray-50 rounded-xl rounded-tl-none p-4 text-sm text-gray-700 leading-relaxed border border-gray-100">
                                {!! nl2br(e($ticket->description)) !!}
                            </div>

                            {{-- Opening attachments --}}
                            @if(!empty($ticket->attachments))
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach($ticket->attachments as $att)
                                        <a href="{{ Storage::url($att['path']) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs text-gray-600 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-paperclip text-gray-400"></i>
                                            {{ $att['original_name'] ?? 'Attachment' }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Replies --}}
                    @foreach($ticket->replies as $reply)
                    @php
                        $isStaff = $reply->is_staff_reply;
                        $name    = $reply->user?->name ?? ($isStaff ? 'Support Team' : 'User');
                        $initial = strtoupper(substr($name, 0, 1));
                    @endphp
                    <div class="flex gap-3 {{ $isStaff ? 'flex-row-reverse' : '' }}">
                        <div class="w-9 h-9 rounded-full {{ $isStaff ? 'bg-[#ff0808]' : 'bg-gray-200' }} flex items-center justify-center flex-shrink-0 font-bold {{ $isStaff ? 'text-white' : 'text-gray-500' }} text-sm">
                            {{ $isStaff ? 'A' : $initial }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1.5 {{ $isStaff ? 'justify-end' : '' }}">
                                @if($isStaff)
                                    <span class="text-[10px] text-gray-400">{{ $reply->created_at->format('M d, Y \a\t H:i') }}</span>
                                    <span class="px-2 py-0.5 bg-[#ff0808]/10 text-[#ff0808] text-[10px] font-semibold rounded-full">Staff</span>
                                    <span class="text-sm font-semibold text-gray-800">{{ $name }}</span>
                                @else
                                    <span class="text-sm font-semibold text-gray-800">{{ $name }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $reply->created_at->format('M d, Y \a\t H:i') }}</span>
                                @endif
                            </div>
                            <div class="{{ $isStaff ? 'bg-[#ff0808]/5 border-[#ff0808]/20 rounded-tr-none' : 'bg-gray-50 border-gray-100 rounded-tl-none' }} rounded-xl p-4 text-sm text-gray-700 leading-relaxed border">
                                {!! nl2br(e($reply->message)) !!}
                            </div>

                            {{-- Reply attachments --}}
                            @if(!empty($reply->attachments))
                                <div class="mt-2 flex flex-wrap gap-2 {{ $isStaff ? 'justify-end' : '' }}">
                                    @foreach($reply->attachments as $att)
                                        <a href="{{ Storage::url($att['path']) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs text-gray-600 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-paperclip text-gray-400"></i>
                                            {{ $att['original_name'] ?? 'Attachment' }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach

                </div>
            </div>

            {{-- Reply Form --}}
            @if(!$ticket->isClosed())
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-800">Reply as Staff</h2>
                </div>
                <div class="p-5">
                    <form action="{{ route('admin.support.reply', $ticket) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <textarea
                            name="message"
                            rows="5"
                            placeholder="Type your reply here..."
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#ff0808]/20 focus:border-[#ff0808] resize-none placeholder-gray-400"
                            required>{{ old('message') }}</textarea>

                        <div class="mt-3 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">

                            {{-- Attachment upload --}}
                            <div>
                                <label class="flex items-center gap-2 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-600 font-medium cursor-pointer hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-paperclip text-gray-400"></i>
                                    Attach files
                                    <input type="file" name="attachments[]" multiple class="hidden"
                                           accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.zip"
                                           onchange="updateFileLabel(this)">
                                </label>
                                <p class="text-[10px] text-gray-400 mt-1" id="file-label">JPG, PNG, PDF, DOC, ZIP — max 5MB each</p>
                            </div>

                            {{-- Status + Send --}}
                            <div class="flex items-center gap-2">
                                <select name="status"
                                        class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#ff0808]/20 focus:border-[#ff0808]">
                                    <option value="">Keep status</option>
                                    @foreach(['open' => 'Set Open', 'in_progress' => 'Set In Progress', 'resolved' => 'Mark Resolved', 'closed' => 'Close Ticket'] as $val => $label)
                                        <option value="{{ $val }}">{{ $label }}</option>
                                    @endforeach
                                </select>

                                <button type="submit"
                                        class="px-5 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-semibold hover:bg-red-700 transition-colors flex items-center gap-2">
                                    <i class="fas fa-paper-plane"></i> Send Reply
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @else
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-5 text-center">
                <i class="fas fa-lock text-gray-300 text-2xl mb-2"></i>
                <p class="text-sm text-gray-500 font-medium">This ticket is {{ $ticket->status }}.</p>
                <form action="{{ route('admin.support.status', $ticket) }}" method="POST" class="mt-3">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="open">
                    <button type="submit"
                            class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-colors">
                        <i class="fas fa-undo mr-1"></i> Reopen Ticket
                    </button>
                </form>
            </div>
            @endif

        </div>

        {{-- ── Right: Sidebar Info ── --}}
        <div class="space-y-4">

            {{-- Ticket Details --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wider">Ticket Details</h3>
                <dl class="space-y-3">

                    <div>
                        <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status</dt>
                        <dd>
                            @php
                                $statusMap = [
                                    'open'        => ['bg-blue-100 text-blue-700',   'Open'],
                                    'in_progress' => ['bg-amber-100 text-amber-700', 'In Progress'],
                                    'resolved'    => ['bg-green-100 text-green-700', 'Resolved'],
                                    'closed'      => ['bg-gray-100 text-gray-500',   'Closed'],
                                ];
                                [$sCls, $sLabel] = $statusMap[$ticket->status] ?? ['bg-gray-100 text-gray-500', 'Unknown'];
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $sCls }}">
                                <i class="fas fa-circle text-[5px]"></i> {{ $sLabel }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Priority</dt>
                        <dd>
                            @php
                                $prioMap = [
                                    'low'    => ['bg-gray-100 text-gray-500',       'Low'],
                                    'medium' => ['bg-blue-100 text-blue-600',       'Medium'],
                                    'high'   => ['bg-orange-100 text-orange-600',   'High'],
                                    'urgent' => ['bg-red-100 text-red-700 font-bold','Urgent'],
                                ];
                                [$pCls, $pLabel] = $prioMap[$ticket->priority] ?? ['bg-gray-100 text-gray-500', ucfirst($ticket->priority)];
                            @endphp
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded text-xs font-semibold {{ $pCls }}">{{ $pLabel }}</span>
                                <button type="button"
                                        onclick="document.getElementById('priority-form').classList.toggle('hidden')"
                                        class="text-[10px] text-gray-400 hover:text-[#ff0808] transition-colors underline">
                                    change
                                </button>
                            </div>
                            {{-- Priority change form (hidden by default) --}}
                            <form id="priority-form"
                                  action="{{ route('admin.support.priority', $ticket) }}"
                                  method="POST"
                                  class="hidden mt-2 flex items-center gap-2">
                                @csrf @method('PATCH')
                                <select name="priority"
                                        class="flex-1 px-2 py-1.5 rounded-lg border border-gray-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#ff0808]/20 focus:border-[#ff0808]">
                                    @foreach(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent'] as $val => $label)
                                        <option value="{{ $val }}" {{ $ticket->priority === $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit"
                                        class="px-2 py-1.5 bg-[#ff0808] text-white rounded-lg text-xs font-semibold hover:bg-red-700 transition-colors">
                                    Save
                                </button>
                            </form>
                        </dd>
                    </div>

                    @if($ticket->category)
                    <div>
                        <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Category</dt>
                        <dd class="text-sm text-gray-700">{{ ucwords(str_replace('_', ' ', $ticket->category)) }}</dd>
                    </div>
                    @endif

                    <div>
                        <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Opened</dt>
                        <dd class="text-sm text-gray-700">{{ $ticket->created_at->format('M d, Y H:i') }}</dd>
                    </div>

                    @if($ticket->last_replied_at)
                    <div>
                        <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Last Reply</dt>
                        <dd class="text-sm text-gray-700">{{ $ticket->last_replied_at->format('M d, Y H:i') }}</dd>
                    </div>
                    @endif

                    @if($ticket->resolved_at)
                    <div>
                        <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Resolved At</dt>
                        <dd class="text-sm text-gray-700">{{ $ticket->resolved_at->format('M d, Y H:i') }}</dd>
                    </div>
                    @endif

                    <div>
                        <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Replies</dt>
                        <dd class="text-sm font-bold text-gray-900">{{ $ticket->replies->count() }}</dd>
                    </div>

                </dl>
            </div>

            {{-- User Details --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wider">User</h3>

                <div class="flex items-center gap-3 mb-4">
                    <div class="w-11 h-11 bg-gray-200 rounded-full flex items-center justify-center font-bold text-gray-500 text-lg">
                        {{ strtoupper(substr($ticket->user?->name ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $ticket->user?->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $ticket->user?->email ?? '' }}</p>
                    </div>
                </div>

                @if($ticket->user)
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-xs text-gray-500">Member since</dt>
                        <dd class="text-xs font-semibold text-gray-700">{{ $ticket->user->created_at->format('M Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-xs text-gray-500">Total tickets</dt>
                        <dd class="text-xs font-semibold text-gray-700">
                            {{ \App\Models\SupportTicket::where('user_id', $ticket->user_id)->count() }}
                        </dd>
                    </div>
                </dl>
                @endif
            </div>

                        {{-- Attention Toggle --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider">Admin Flags</h3>
                <form action="{{ route('admin.support.attention', $ticket) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="w-full flex items-center gap-3 px-4 py-3 rounded-lg border transition-colors
                                   {{ $ticket->requires_attention
                                        ? 'bg-red-50 border-red-200 text-red-700 hover:bg-red-100'
                                        : 'bg-gray-50 border-gray-200 text-gray-600 hover:bg-amber-50 hover:border-amber-200 hover:text-amber-700' }}">
                        <i class="fas fa-exclamation-triangle {{ $ticket->requires_attention ? 'text-red-500' : 'text-gray-400' }}"></i>
                        <span class="text-sm font-semibold flex-1 text-left">
                            {{ $ticket->requires_attention ? 'Flagged: Needs Attention' : 'Flag: Needs Attention' }}
                        </span>
                        @if($ticket->requires_attention)
                            <span class="text-[10px] text-red-500 underline">remove</span>
                        @endif
                    </button>
                </form>
            </div>

            {{-- Danger Zone --}}
            <div class="bg-white rounded-xl border border-red-100 shadow-sm p-5">
                <h3 class="text-sm font-bold text-red-600 mb-4 uppercase tracking-wider">Danger Zone</h3>
                <form action="{{ route('admin.support.destroy', $ticket) }}" method="POST"
                      onsubmit="return confirm('Permanently delete this ticket and all its replies? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="w-full px-4 py-2 bg-red-50 text-red-700 rounded-lg text-sm font-semibold hover:bg-red-100 transition-colors border border-red-200">
                        <i class="fas fa-trash mr-2"></i> Delete Ticket
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    // Auto-scroll conversation to bottom
    document.addEventListener('DOMContentLoaded', function () {
        const conv = document.getElementById('conversation');
        if (conv) conv.scrollTop = conv.scrollHeight;
    });

    // File label update
    function updateFileLabel(input) {
        const label = document.getElementById('file-label');
        if (input.files.length > 0) {
            label.textContent = input.files.length + ' file(s) selected';
            label.classList.add('text-[#ff0808]');
        } else {
            label.textContent = 'JPG, PNG, PDF, DOC, ZIP — max 5MB each';
            label.classList.remove('text-[#ff0808]');
        }
    }
</script>
@endsection
