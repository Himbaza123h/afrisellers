<!-- Release Modal -->
<div id="releaseModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6 border-b">
            <h3 class="text-xl font-bold text-gray-900">Release Funds</h3>
        </div>
        <form id="releaseForm" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600">Are you sure you want to release the funds to the vendor? This action cannot be undone.</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Release Notes (Optional)</label>
                    <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Add any notes about this release..."></textarea>
                </div>
            </div>
            <div class="p-6 bg-gray-50 rounded-b-xl flex gap-3 justify-end">
                <button type="button" onclick="closeModal('releaseModal')" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                    Release Funds
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Refund Modal -->
<div id="refundModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6 border-b">
            <h3 class="text-xl font-bold text-gray-900">Refund Escrow</h3>
        </div>
        <form id="refundForm" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600">Are you sure you want to refund this escrow to the buyer? This action cannot be undone.</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Refund Reason <span class="text-red-500">*</span></label>
                    <textarea name="reason" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Explain why this escrow is being refunded..."></textarea>
                </div>
            </div>
            <div class="p-6 bg-gray-50 rounded-b-xl flex gap-3 justify-end">
                <button type="button" onclick="closeModal('refundModal')" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-medium">
                    Process Refund
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Dispute Modal -->
<div id="disputeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6 border-b">
            <h3 class="text-xl font-bold text-gray-900">Mark as Disputed</h3>
        </div>
        <form id="disputeForm" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600">Mark this escrow as disputed. This will freeze the funds until the dispute is resolved.</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dispute Reason <span class="text-red-500">*</span></label>
                    <textarea name="reason" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Describe the dispute..."></textarea>
                </div>
            </div>
            <div class="p-6 bg-gray-50 rounded-b-xl flex gap-3 justify-end">
                <button type="button" onclick="closeModal('disputeModal')" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                    Mark as Disputed
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Resolve Dispute Modal -->
<div id="resolveDisputeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6 border-b">
            <h3 class="text-xl font-bold text-gray-900">Resolve Dispute</h3>
        </div>
        <form id="resolveDisputeForm" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600">Choose how to resolve this dispute.</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Resolution Action <span class="text-red-500">*</span></label>
                    <select name="resolution_action" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select action...</option>
                        <option value="release">Release to Vendor</option>
                        <option value="refund">Refund to Buyer</option>
                        <option value="partial">Partial Split</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Resolution Notes <span class="text-red-500">*</span></label>
                    <textarea name="resolution_notes" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Explain the resolution decision..."></textarea>
                </div>
            </div>
            <div class="p-6 bg-gray-50 rounded-b-xl flex gap-3 justify-end">
                <button type="button" onclick="closeModal('resolveDisputeModal')" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    Resolve Dispute
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Cancel Modal -->
<div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6 border-b">
            <h3 class="text-xl font-bold text-gray-900">Cancel Escrow</h3>
        </div>
        <form id="cancelForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600">Are you sure you want to cancel this escrow? This action cannot be undone.</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cancellation Reason <span class="text-red-500">*</span></label>
                    <textarea name="reason" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Explain why this escrow is being cancelled..."></textarea>
                </div>
            </div>
            <div class="p-6 bg-gray-50 rounded-b-xl flex gap-3 justify-end">
                <button type="button" onclick="closeModal('cancelModal')" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-medium">
                    Cancel Escrow
                </button>
            </div>
        </form>
    </div>
</div>
