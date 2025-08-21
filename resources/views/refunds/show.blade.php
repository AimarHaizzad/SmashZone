@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Refund Details</h1>
                    <p class="mt-2 text-gray-600">Refund #{{ $refund->id }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('refunds.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        Back to Refunds
                    </a>
                </div>
            </div>
        </div>

        <!-- Refund Details Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
            <div class="p-8">
                <!-- Status Header -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Refund Status</h2>
                        <p class="text-gray-600">Last updated: {{ $refund->updated_at->format('M j, Y g:i A') }}</p>
                    </div>
                    <span class="inline-flex px-4 py-2 text-sm font-semibold rounded-full {{ $refund->status_badge_class }}">
                        {{ ucfirst($refund->status) }}
                    </span>
                </div>

                <!-- Refund Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Refund Information</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Amount:</span>
                                <span class="font-semibold text-gray-900">{{ $refund->formatted_amount }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Method:</span>
                                <span class="font-semibold text-gray-900">{{ $refund->refund_method }}</span>
                            </div>
                            @if($refund->stripe_refund_id)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Stripe Refund ID:</span>
                                    <span class="font-semibold text-gray-900">{{ $refund->stripe_refund_id }}</span>
                                </div>
                            @endif
                            @if($refund->refunded_at)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Refunded At:</span>
                                    <span class="font-semibold text-gray-900">{{ $refund->refunded_at->format('M j, Y g:i A') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Booking Information</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Court:</span>
                                <span class="font-semibold text-gray-900">{{ $refund->booking->court->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Date:</span>
                                <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($refund->booking->date)->format('M j, Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Time:</span>
                                <span class="font-semibold text-gray-900">
                                    {{ \Carbon\Carbon::parse($refund->booking->start_time)->format('g:i A') }} - 
                                    {{ \Carbon\Carbon::parse($refund->booking->end_time)->format('g:i A') }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Customer:</span>
                                <span class="font-semibold text-gray-900">{{ $refund->user->name }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reason -->
                @if($refund->reason)
                    <div class="bg-gray-50 rounded-xl p-6 mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Reason for Refund</h3>
                        <p class="text-gray-700">{{ $refund->reason }}</p>
                    </div>
                @endif

                <!-- Staff Actions -->
                @if(auth()->user()->isOwner() || auth()->user()->isStaff())
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Staff Actions</h3>
                        
                        @if($refund->status === 'failed')
                            <form method="POST" action="{{ route('refunds.retry', $refund) }}" class="inline-block mr-4">
                                @csrf
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    Retry Refund
                                </button>
                            </form>
                        @endif

                        @if($refund->status === 'pending' || $refund->status === 'failed')
                            <button onclick="showManualRefundModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                Process Manual Refund
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Manual Refund Modal -->
@if(auth()->user()->isOwner() || auth()->user()->isStaff())
    <div id="manual-refund-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Process Manual Refund</h3>
                <form method="POST" action="{{ route('refunds.manual', $refund) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="manual_refund_notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Refund Notes
                        </label>
                        <textarea 
                            id="manual_refund_notes" 
                            name="manual_refund_notes" 
                            rows="4" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter notes about the manual refund process..."
                            required
                        ></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hideManualRefundModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            Process Refund
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<script>
function showManualRefundModal() {
    document.getElementById('manual-refund-modal').classList.remove('hidden');
}

function hideManualRefundModal() {
    document.getElementById('manual-refund-modal').classList.add('hidden');
}
</script>
@endsection
