<!-- User Hierarchy Table -->
<div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-bold text-gray-900">User Hierarchy Overview</h3>
        <a href="{{ route('admin.users.index') }}" class="text-[#ff0808] font-bold hover:underline text-xs">Manage All</a>
    </div>
    <div class="overflow-x-auto -mx-4 sm:mx-0">
        <table class="min-w-full">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="text-left py-2 px-3 text-[10px] font-bold text-gray-600 uppercase">Role</th>
                    <th class="text-left py-2 px-3 text-[10px] font-bold text-gray-600 uppercase">Total</th>
                    <th class="text-left py-2 px-3 text-[10px] font-bold text-gray-600 uppercase">Active</th>
                    <th class="text-left py-2 px-3 text-[10px] font-bold text-gray-600 uppercase">Pending</th>
                    <th class="text-left py-2 px-3 text-[10px] font-bold text-gray-600 uppercase">Suspended</th>
                    <th class="text-left py-2 px-3 text-[10px] font-bold text-gray-600 uppercase">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($userHierarchy as $hierarchy)
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                    <td class="py-2.5 px-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-{{ $hierarchy['color'] }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-{{ $hierarchy['icon'] }} text-{{ $hierarchy['color'] }}-600 text-xs"></i>
                            </div>
                            <span class="font-semibold text-gray-900 text-xs">{{ $hierarchy['role'] }}</span>
                        </div>
                    </td>
                    <td class="py-2.5 px-3 font-bold text-gray-900 text-xs">{{ number_format($hierarchy['total']) }}</td>
                    <td class="py-2.5 px-3">
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-[10px] font-bold">{{ number_format($hierarchy['active']) }}</span>
                    </td>
                    <td class="py-2.5 px-3">
                        <span class="px-2 py-0.5 bg-orange-100 text-orange-700 rounded-full text-[10px] font-bold">{{ number_format($hierarchy['pending']) }}</span>
                    </td>
                    <td class="py-2.5 px-3">
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded-full text-[10px] font-bold">{{ number_format($hierarchy['suspended']) }}</span>
                    </td>
                    <td class="py-2.5 px-3">
                        <a href="{{ $hierarchy['manage_route'] }}" class="text-[#ff0808] hover:underline font-bold text-xs">Manage</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
