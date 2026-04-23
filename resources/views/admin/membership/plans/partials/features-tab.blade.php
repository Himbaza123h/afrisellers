<div class="space-y-4">
    <p class="text-xs text-gray-500">Feature flags and limits per plan. Use <span class="font-medium text-gray-700">Manage</span> to add or edit keys.</p>

    @forelse($plans as $plan)
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-4 py-3 bg-gray-50 border-b border-gray-200">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">{{ $plan->name }}</h3>
                    <p class="text-xs text-gray-500">{{ $plan->features_count }} feature{{ $plan->features_count === 1 ? '' : 's' }} · ${{ number_format($plan->price, 2) }} / {{ $plan->duration_days }} days</p>
                </div>
                <a href="{{ route('admin.memberships.features.index', $plan) }}"
                   class="inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-all text-xs font-medium shrink-0">
                    <i class="fas fa-sliders-h"></i>
                    <span>Manage features</span>
                </a>
            </div>
            @if($plan->features->isEmpty())
                <div class="px-4 py-8 text-center text-sm text-gray-500">
                    No features configured for this plan yet.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50/80 border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Key</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($plan->features as $planFeature)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-4 py-2">
                                        <div>
                                            <span class="text-xs font-medium text-gray-800">{{ $planFeature->feature?->name ?? '—' }}</span>
                                            <code class="text-xs font-mono text-blue-700 bg-blue-50 px-2 py-0.5 rounded ml-1">{{ $planFeature->feature_key }}</code>
                                        </div>
                                        @if($planFeature->feature?->description)
                                            <p class="mt-1 text-xs text-gray-500 leading-snug">{{ \Illuminate\Support\Str::limit($planFeature->feature->description, 90) }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-gray-800 font-medium">{{ $planFeature->feature_value }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @empty
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm py-12 text-center">
            <p class="text-sm text-gray-600 mb-3">No plans to show features for.</p>
            <a href="{{ route('admin.memberships.plans.create') }}" class="inline-flex items-center gap-1 px-4 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-[#e60707] text-sm font-medium">
                <i class="fas fa-plus"></i>
                <span>Create a plan</span>
            </a>
        </div>
    @endforelse

    @if(method_exists($plans, 'hasPages') && $plans->hasPages())
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm px-4 py-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-700">Showing {{ $plans->firstItem() }}-{{ $plans->lastItem() }} of {{ $plans->total() }}</span>
                <div class="text-sm">{{ $plans->links() }}</div>
            </div>
        </div>
    @endif
</div>
