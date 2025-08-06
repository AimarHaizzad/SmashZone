<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SmashZone Analytics Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #3B82F6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #3B82F6;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #666;
            margin: 5px 0 0 0;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            color: #1F2937;
            border-bottom: 1px solid #E5E7EB;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }
        .metric-card {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        .metric-value {
            font-size: 24px;
            font-weight: bold;
            color: #3B82F6;
            margin-bottom: 5px;
        }
        .metric-label {
            color: #64748B;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #E5E7EB;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #F3F4F6;
            font-weight: bold;
            color: #374151;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .positive {
            color: #059669;
        }
        .negative {
            color: #DC2626;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SmashZone Analytics Report</h1>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p>Business Performance Analysis</p>
    </div>

    <!-- Performance Overview -->
    <div class="section">
        <h2>Performance Overview</h2>
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-value">RM {{ number_format($revenueData['total_revenue'], 2) }}</div>
                <div class="metric-label">Total Revenue</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">{{ number_format($performanceData['conversion_rate'], 1) }}%</div>
                <div class="metric-label">Conversion Rate</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">RM {{ number_format($performanceData['avg_booking_value'], 2) }}</div>
                <div class="metric-label">Average Booking Value</div>
            </div>
            <div class="metric-card">
                <div class="metric-value {{ $performanceData['monthly_growth'] >= 0 ? 'positive' : 'negative' }}">
                    {{ number_format($performanceData['monthly_growth'], 1) }}%
                </div>
                <div class="metric-label">Monthly Growth</div>
            </div>
        </div>
    </div>

    <!-- Revenue Analysis -->
    <div class="section">
        <h2>Revenue Analysis</h2>
        
        <h3>Revenue by Court</h3>
        <table>
            <thead>
                <tr>
                    <th>Court Name</th>
                    <th class="text-right">Total Revenue</th>
                    <th class="text-center">Bookings</th>
                    <th class="text-center">Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($revenueData['by_court'] as $court)
                <tr>
                    <td>{{ $court->name }}</td>
                    <td class="text-right">RM {{ number_format($court->total_revenue, 2) }}</td>
                    <td class="text-center">{{ $court->booking_count }}</td>
                    <td class="text-center">{{ number_format(($court->total_revenue / $revenueData['total_revenue']) * 100, 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <h3>Monthly Revenue Trend (Last 12 Months)</h3>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th class="text-right">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($revenueData['monthly'] as $month)
                <tr>
                    <td>{{ date('F Y', mktime(0, 0, 0, $month->month, 1, $month->year)) }}</td>
                    <td class="text-right">RM {{ number_format($month->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Court Utilization -->
    <div class="section">
        <h2>Court Utilization Analysis</h2>
        
        <h3>Court Utilization Rates</h3>
        <table>
            <thead>
                <tr>
                    <th>Court Name</th>
                    <th class="text-center">Bookings (Last Month)</th>
                    <th class="text-center">Utilization Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($utilizationData['utilization_rate'] as $court)
                <tr>
                    <td>{{ $court->name }}</td>
                    <td class="text-center">{{ $court->bookings_count }}</td>
                    <td class="text-center">{{ number_format($court->utilization_rate, 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <h3>Peak Hours Analysis</h3>
        <table>
            <thead>
                <tr>
                    <th>Hour</th>
                    <th class="text-center">Number of Bookings</th>
                </tr>
            </thead>
            <tbody>
                @foreach($utilizationData['peak_hours'] as $hour)
                <tr>
                    <td>{{ $hour->hour }}:00</td>
                    <td class="text-center">{{ $hour->booking_count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Customer Analytics -->
    <div class="section">
        <h2>Customer Analytics</h2>
        
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-value">{{ $customerData['new_customers'] }}</div>
                <div class="metric-label">New Customers (This Month)</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">{{ $customerData['returning_customers'] }}</div>
                <div class="metric-label">Returning Customers</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">{{ number_format($customerData['avg_bookings_per_customer'], 1) }}</div>
                <div class="metric-label">Avg Bookings per Customer</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">{{ $customerData['top_customers']->count() }}</div>
                <div class="metric-label">VIP Customers</div>
            </div>
        </div>

        <h3>Top Customers by Spending</h3>
        <table>
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th class="text-center">Bookings</th>
                    <th class="text-right">Total Spent</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customerData['top_customers'] as $customer)
                <tr>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td class="text-center">{{ $customer->booking_count }}</td>
                    <td class="text-right">RM {{ number_format($customer->total_spent, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Performance Metrics -->
    <div class="section">
        <h2>Performance Metrics</h2>
        
        <table>
            <thead>
                <tr>
                    <th>Metric</th>
                    <th class="text-right">Value</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Current Month Revenue</td>
                    <td class="text-right">RM {{ number_format($performanceData['current_month_revenue'], 2) }}</td>
                </tr>
                <tr>
                    <td>Last Month Revenue</td>
                    <td class="text-right">RM {{ number_format($performanceData['last_month_revenue'], 2) }}</td>
                </tr>
                <tr>
                    <td>Monthly Growth Rate</td>
                    <td class="text-right {{ $performanceData['monthly_growth'] >= 0 ? 'positive' : 'negative' }}">
                        {{ number_format($performanceData['monthly_growth'], 1) }}%
                    </td>
                </tr>
                <tr>
                    <td>Average Revenue per Booking</td>
                    <td class="text-right">RM {{ number_format($revenueData['avg_revenue_per_booking'], 2) }}</td>
                </tr>
            </tbody>
        </table>

        <h3>Court Efficiency Analysis</h3>
        <table>
            <thead>
                <tr>
                    <th>Court Name</th>
                    <th class="text-center">Bookings (Last Month)</th>
                    <th class="text-right">Revenue (Last Month)</th>
                    <th class="text-right">Revenue per Booking</th>
                </tr>
            </thead>
            <tbody>
                @foreach($performanceData['court_efficiency'] as $court)
                <tr>
                    <td>{{ $court->name }}</td>
                    <td class="text-center">{{ $court->bookings_count }}</td>
                    <td class="text-right">RM {{ number_format($court->bookings_sum_total_price, 2) }}</td>
                    <td class="text-right">RM {{ number_format($court->revenue_per_booking, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Summary -->
    <div class="section">
        <h2>Executive Summary</h2>
        <p>This report provides a comprehensive analysis of SmashZone's business performance, including revenue trends, court utilization, customer behavior, and key performance indicators. The data shows:</p>
        
        <ul>
            <li><strong>Total Revenue:</strong> RM {{ number_format($revenueData['total_revenue'], 2) }} across all courts</li>
            <li><strong>Conversion Rate:</strong> {{ number_format($performanceData['conversion_rate'], 1) }}% of bookings result in payments</li>
            <li><strong>Customer Growth:</strong> {{ $customerData['new_customers'] }} new customers this month</li>
            <li><strong>Monthly Performance:</strong> {{ $performanceData['monthly_growth'] >= 0 ? 'Positive' : 'Negative' }} growth of {{ number_format($performanceData['monthly_growth'], 1) }}%</li>
        </ul>
        
        <p><strong>Report generated on:</strong> {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>
</body>
</html> 