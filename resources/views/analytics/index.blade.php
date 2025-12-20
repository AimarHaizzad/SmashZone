@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Analytics & Reports</h1>
                <p class="text-gray-600 mt-2">Comprehensive business insights and performance metrics</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('analytics.export-pdf', absolute: false) }}" 
                   class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export PDF
                </a>
                <a href="{{ route('analytics.export-excel', absolute: false) }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Excel
                </a>
            </div>
        </div>
    </div>

    <!-- Performance Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-600 text-sm font-medium">Total Revenue</p>
                    <p class="text-2xl font-bold text-blue-900">RM {{ number_format($revenueData['total_revenue'] ?? 0, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 0V4m0 7v7" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-600 text-sm font-medium">Conversion Rate</p>
                    <p class="text-2xl font-bold text-green-900">{{ number_format($performanceData['conversion_rate'] ?? 0, 1) }}%</p>
                </div>
                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-600 text-sm font-medium">Avg Booking Value</p>
                    <p class="text-2xl font-bold text-purple-900">RM {{ number_format($performanceData['avg_booking_value'] ?? 0, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-6 border border-orange-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-600 text-sm font-medium">Monthly Growth</p>
                    <p class="text-2xl font-bold text-orange-900">{{ number_format($performanceData['monthly_growth'] ?? 0, 1) }}%</p>
                </div>
                <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Booking Predictions -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-2xl shadow-sm border border-purple-100 p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        AI-Powered Booking Predictions
                    </h2>
                    <p class="text-gray-600 mt-2">Predictive analytics for the next 7 days to maximize your income</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Confidence Score</div>
                    <div class="text-2xl font-bold text-purple-600">{{ $predictionData['confidence']['overall'] ?? 0 }}%</div>
                </div>
            </div>

            <!-- Weekly Predictions Chart -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">7-Day Booking Forecast</h3>
                    <div class="h-64">
                        <canvas id="predictionChart"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Daily Predictions</h3>
                    <div class="space-y-3">
                        @foreach(($predictionData['predictions'] ?? []) as $prediction)
                        <div class="flex items-center justify-between p-4 rounded-lg {{ $prediction['is_peak_day'] ? 'bg-green-50 border border-green-200' : ($prediction['is_low_day'] ? 'bg-yellow-50 border border-yellow-200' : 'bg-gray-50 border border-gray-200') }}">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $prediction['is_peak_day'] ? 'bg-green-500' : ($prediction['is_low_day'] ? 'bg-yellow-500' : 'bg-gray-500') }} text-white font-bold">
                                    {{ $prediction['predicted_bookings'] }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $prediction['day_name'] }}</div>
                                    <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($prediction['date'])->format('M d') }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-semibold text-gray-900">RM {{ number_format($prediction['revenue_estimate'], 0) }}</div>
                                <div class="text-sm text-gray-500">{{ $prediction['confidence'] }}% confidence</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- AI Recommendations -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                    AI Recommendations
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach(($predictionData['recommendations'] ?? []) as $recommendation)
                    <div class="p-4 rounded-lg border {{ $recommendation['type'] === 'peak_days' ? 'bg-green-50 border-green-200' : ($recommendation['type'] === 'low_days' ? 'bg-yellow-50 border-yellow-200' : 'bg-blue-50 border-blue-200') }}">
                        <h4 class="font-semibold text-gray-900 mb-2">{{ $recommendation['title'] }}</h4>
                        <p class="text-sm text-gray-600 mb-3">{{ $recommendation['description'] }}</p>
                        <ul class="space-y-1">
                            @foreach($recommendation['actions'] as $action)
                            <li class="text-sm text-gray-700 flex items-start gap-2">
                                <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ $action }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Monthly Revenue Chart -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Revenue Trend</h3>
            <div class="h-64">
                <canvas id="monthlyRevenueChart"></canvas>
            </div>
        </div>

        <!-- Revenue by Court -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue by Court</h3>
            <div class="space-y-4">
                @foreach(($revenueData['by_court'] ?? []) as $court)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">{{ $court->name ?? 'Unknown Court' }}</p>
                        <p class="text-sm text-gray-600">{{ $court->booking_count ?? 0 }} bookings</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-green-600">RM {{ number_format($court->total_revenue ?? 0, 2) }}</p>
                        <p class="text-xs text-gray-500">{{ ($revenueData['total_revenue'] ?? 0) > 0 ? number_format((($court->total_revenue ?? 0) / $revenueData['total_revenue']) * 100, 1) : 0 }}%</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Court Utilization -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Peak Hours -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Peak Hours Analysis</h3>
            <div class="h-64">
                <canvas id="peakHoursChart"></canvas>
            </div>
        </div>

        <!-- Court Utilization -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Court Utilization Rate</h3>
            <div class="space-y-4">
                @foreach(($utilizationData['utilization_rate'] ?? []) as $court)
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">{{ $court->name ?? 'Unknown Court' }}</p>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min($court->utilization_rate ?? 0, 100) }}%"></div>
                        </div>
                    </div>
                    <div class="ml-4 text-right">
                        <p class="font-semibold text-blue-600">{{ number_format($court->utilization_rate ?? 0, 1) }}%</p>
                        <p class="text-xs text-gray-500">{{ $court->bookings_count ?? 0 }} bookings</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Customer Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Top Customers -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Customers</h3>
            <div class="space-y-3">
                @forelse(($customerData['top_customers'] ?? collect()) as $customer)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex items-center flex-1">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-bold mr-3 shadow-sm">
                            {{ $customer->rank ?? ($loop->iteration) }}
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ $customer->name ?? 'Unknown Customer' }}</p>
                            <p class="text-sm text-gray-600">{{ $customer->email ?? 'No email' }}</p>
                        </div>
                    </div>
                    <div class="text-right ml-4">
                        <p class="font-semibold text-green-600 text-lg">RM {{ number_format($customer->total_spent ?? 0, 2) }}</p>
                        <p class="text-xs text-gray-500">{{ $customer->booking_count ?? 0 }} {{ ($customer->booking_count ?? 0) == 1 ? 'booking' : 'bookings' }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <p class="text-gray-500 text-sm">No customer data available yet</p>
                    <p class="text-gray-400 text-xs mt-1">Customer rankings will appear here once bookings are made</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Customer Metrics -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Insights</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <p class="text-2xl font-bold text-blue-600">{{ $customerData['new_customers'] ?? 0 }}</p>
                    <p class="text-sm text-blue-600">New Customers</p>
                    <p class="text-xs text-gray-500">This Month</p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <p class="text-2xl font-bold text-green-600">{{ $customerData['returning_customers'] ?? 0 }}</p>
                    <p class="text-sm text-green-600">Returning</p>
                    <p class="text-xs text-gray-500">Loyal Customers</p>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($customerData['avg_bookings_per_customer'] ?? 0, 1) }}</p>
                    <p class="text-sm text-purple-600">Avg Bookings</p>
                    <p class="text-xs text-gray-500">Per Customer</p>
                </div>
                <div class="text-center p-4 bg-orange-50 rounded-lg">
                    <p class="text-2xl font-bold text-orange-600">{{ ($customerData['top_customers'] ?? collect())->count() }}</p>
                    <p class="text-sm text-orange-600">VIP Customers</p>
                    <p class="text-xs text-gray-500">Top Spenders</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Metrics</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 0V4m0 7v7" />
                    </svg>
                </div>
                <p class="text-2xl font-bold text-gray-900">RM {{ number_format($performanceData['current_month_revenue'] ?? 0, 2) }}</p>
                <p class="text-sm text-gray-600">Current Month Revenue</p>
            </div>
            <div class="text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <p class="text-2xl font-bold text-gray-900">RM {{ number_format($performanceData['last_month_revenue'] ?? 0, 2) }}</p>
                <p class="text-sm text-gray-600">Last Month Revenue</p>
            </div>
            <div class="text-center">
                <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($performanceData['monthly_growth'], 1) }}%</p>
                <p class="text-sm text-gray-600">Monthly Growth Rate</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Monthly Revenue Chart
const monthlyCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
const monthlyChart = new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: @json($revenueData['monthly_labels'] ?? []),
        datasets: [{
            label: 'Revenue (RM)',
            data: @json($revenueData['monthly_data'] ?? []),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'RM ' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Peak Hours Chart
const peakCtx = document.getElementById('peakHoursChart').getContext('2d');
const peakChart = new Chart(peakCtx, {
    type: 'bar',
    data: {
        labels: @json($utilizationData['peak_hours_labels'] ?? []),
        datasets: [{
            label: 'Bookings',
            data: @json($utilizationData['peak_hours_data'] ?? []),
            backgroundColor: 'rgba(34, 197, 94, 0.8)',
            borderColor: 'rgb(34, 197, 94)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// AI Prediction Chart
const predictionCtx = document.getElementById('predictionChart').getContext('2d');

// Prepare prediction data
const predictionData = @json($predictionData['predictions'] ?? []);
const labels = predictionData.map(p => p.day_name || '');
const data = predictionData.map(p => p.predicted_bookings || 0);
const pointColors = predictionData.map(p => {
    if (p.is_peak_day) return 'rgb(34, 197, 94)';
    if (p.is_low_day) return 'rgb(245, 158, 11)';
    return 'rgb(107, 114, 128)';
});

try {
const predictionChart = new Chart(predictionCtx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Predicted Bookings',
            data: data,
            borderColor: 'rgb(147, 51, 234)',
            backgroundColor: 'rgba(147, 51, 234, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: pointColors,
            pointBorderColor: pointColors,
            pointRadius: 8,
            pointHoverRadius: 10
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    afterLabel: function(context) {
                        const prediction = predictionData[context.dataIndex] || {};
                        return [
                            'Revenue: RM ' + (prediction.revenue_estimate || 0).toLocaleString(),
                            'Confidence: ' + (prediction.confidence || 0) + '%',
                            prediction.is_peak_day ? '🔥 Peak Day' : 
                            prediction.is_low_day ? '📉 Low Day' : '📊 Normal Day'
                        ];
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Number of Bookings'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Days of Week'
                }
            }
        },
        elements: {
            point: {
                hoverBackgroundColor: 'rgb(147, 51, 234)'
            }
        }
    }
});

} catch (error) {
    console.error('Error creating prediction chart:', error);
}
</script>
@endpush
@endsection 
@endsection 