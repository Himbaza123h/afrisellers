<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Subscription Report - {{ now()->format('M d, Y') }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }

        .header h1 {
            font-size: 18px;
            margin: 5px 0;
            font-weight: bold;
        }

        .header p {
            margin: 3px 0;
            font-size: 10px;
            color: #666;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #000;
        }

        .info-box {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 15px;
            background-color: #f9f9f9;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin: 15px 0;
        }

        .info-item {
            padding: 8px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .info-label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .info-value {
            font-size: 12px;
            font-weight: bold;
            color: #000;
        }

        .data-table {
            margin-top: 10px;
            page-break-inside: avoid;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        .data-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-active { background-color: #d1fae5; color: #065f46; }
        .badge-expired { background-color: #fee2e2; color: #991b1b; }
        .badge-cancelled { background-color: #e5e7eb; color: #374151; }
        .badge-pending { background-color: #fed7aa; color: #9a3412; }

        .feature-box {
            border: 1px solid #ddd;
            padding: 8px;
            margin-bottom: 8px;
            background-color: #f9f9f9;
        }

        .feature-check {
            color: #22c55e;
            margin-right: 5px;
        }

        .feature-cross {
            color: #ccc;
            margin-right: 5px;
        }

        .footer {
            text-align: center;
            font-size: 9px;
            color: #666;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }

        .progress-bar {
            width: 100%;
            height: 15px;
            background-color: #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
            margin: 5px 0;
        }

        .progress-fill {
            height: 100%;
            background-color: #3b82f6;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>AGENT SUBSCRIPTION REPORT</h1>
        <p>Package Subscription & Payment History</p>
        <p>
            Generated on: {{ now()->format('d/m/Y H:i:s') }} |
            Agent: {{ auth()->user()->name }} ({{ auth()->user()->email }})
        </p>
    </div>

    @if($currentSubscription)
        <!-- Current Subscription -->
        <div class="section-title">Current Active Subscription</div>

        <div class="info-box" style="background-color: #f0fdf4; border-color: #22c55e;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <div>
                    <h2 style="font-size: 16px; font-weight: bold; margin: 0;">{{ $currentSubscription->package->name }}</h2>
                    <p style="font-size: 10px; color: #666; margin: 3px 0;">{{ $currentSubscription->package->description }}</p>
                </div>
                <span class="badge badge-{{ strtolower($currentSubscription->status) }}">{{ ucfirst($currentSubscription->status) }}</span>
            </div>
        </div>

        <!-- Subscription Details -->
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Started On</div>
                <div class="info-value">{{ $currentSubscription->starts_at->format('M d, Y') }}</div>
            </div>

            <div class="info-item">
                <div class="info-label">Expires On</div>
                <div class="info-value">{{ $currentSubscription->expires_at->format('M d, Y') }}</div>
            </div>

            <div class="info-item">
                <div class="info-label">Days Remaining</div>
                <div class="info-value">{{ $currentSubscription->daysRemaining() }} days</div>
            </div>

            <div class="info-item">
                <div class="info-label">Amount Paid</div>
                <div class="info-value">${{ number_format($currentSubscription->amount_paid, 2) }}</div>
            </div>

            <div class="info-item">
                <div class="info-label">Payment Method</div>
                <div class="info-value">{{ ucfirst(str_replace('_', ' ', $currentSubscription->payment_method ?? 'N/A')) }}</div>
            </div>

            <div class="info-item">
                <div class="info-label">Transaction ID</div>
                <div class="info-value" style="font-size: 9px; font-family: monospace;">{{ $currentSubscription->transaction_id ?? 'N/A' }}</div>
            </div>
        </div>

        <!-- Usage Statistics -->
        <div class="section-title">Usage Statistics</div>

        <div style="margin-bottom: 15px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                <span style="font-size: 10px; font-weight: bold;">Referrals Usage:</span>
                <span style="font-size: 10px; font-weight: bold;">{{ $currentSubscription->referrals_used }} / {{ $currentSubscription->package->max_referrals }}</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $currentSubscription->getReferralUsagePercentage() }}%;"></div>
            </div>
            <p style="font-size: 9px; color: #666; margin-top: 3px;">{{ $currentSubscription->package->max_referrals - $currentSubscription->referrals_used }} referral(s) remaining</p>
        </div>

        <div style="margin-bottom: 15px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                <span style="font-size: 10px; font-weight: bold;">Payouts This Month:</span>
                <span style="font-size: 10px; font-weight: bold;">{{ $currentSubscription->payouts_used }} / {{ $currentSubscription->package->max_payouts_per_month }}</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $currentSubscription->package->max_payouts_per_month > 0 ? min(100, ($currentSubscription->payouts_used / $currentSubscription->package->max_payouts_per_month) * 100) : 0 }}%; background-color: #22c55e;"></div>
            </div>
            <p style="font-size: 9px; color: #666; margin-top: 3px;">{{ $currentSubscription->package->max_payouts_per_month - $currentSubscription->payouts_used }} payout(s) remaining</p>
        </div>

        <!-- Package Features -->
        <div class="section-title">Active Package Features</div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px;">
            <div class="feature-box">
                <span class="feature-check">✓</span>
                <strong>{{ $currentSubscription->package->max_referrals }} Referrals</strong>
                <p style="font-size: 9px; color: #666; margin: 3px 0 0 15px;">Maximum allowed</p>
            </div>

            <div class="feature-box">
                @if($currentSubscription->package->allow_rfqs)
                    <span class="feature-check">✓</span>
                    <strong>RFQ Access</strong>
                    <p style="font-size: 9px; color: #666; margin: 3px 0 0 15px;">Enabled</p>
                @else
                    <span class="feature-cross">✗</span>
                    <strong style="color: #999;">RFQ Access</strong>
                    <p style="font-size: 9px; color: #999; margin: 3px 0 0 15px;">Not available</p>
                @endif
            </div>

            <div class="feature-box">
                @if($currentSubscription->package->priority_support)
                    <span class="feature-check">✓</span>
                    <strong>Priority Support</strong>
                    <p style="font-size: 9px; color: #666; margin: 3px 0 0 15px;">Enabled</p>
                @else
                    <span class="feature-cross">✗</span>
                    <strong style="color: #999;">Priority Support</strong>
                    <p style="font-size: 9px; color: #999; margin: 3px 0 0 15px;">Not available</p>
                @endif
            </div>

            <div class="feature-box">
                @if($currentSubscription->package->advanced_analytics)
                    <span class="feature-check">✓</span>
                    <strong>Advanced Analytics</strong>
                    <p style="font-size: 9px; color: #666; margin: 3px 0 0 15px;">Enabled</p>
                @else
                    <span class="feature-cross">✗</span>
                    <strong style="color: #999;">Advanced Analytics</strong>
                    <p style="font-size: 9px; color: #999; margin: 3px 0 0 15px;">Not available</p>
                @endif
            </div>

            <div class="feature-box">
                <span class="feature-check">✓</span>
                <strong>{{ $currentSubscription->package->commission_rate }}% Commission</strong>
                <p style="font-size: 9px; color: #666; margin: 3px 0 0 15px;">Earn rate</p>
            </div>

            <div class="feature-box">
                @if($currentSubscription->package->featured_profile)
                    <span class="feature-check">✓</span>
                    <strong>Featured Profile</strong>
                    <p style="font-size: 9px; color: #666; margin: 3px 0 0 15px;">Enabled</p>
                @else
                    <span class="feature-cross">✗</span>
                    <strong style="color: #999;">Featured Profile</strong>
                    <p style="font-size: 9px; color: #999; margin: 3px 0 0 15px;">Not available</p>
                @endif
            </div>
        </div>
    @else
        <div class="info-box" style="text-align: center; padding: 30px;">
            <p style="font-size: 14px; font-weight: bold; margin: 0 0 10px 0;">No Active Subscription</p>
            <p style="font-size: 11px; color: #666; margin: 0;">Agent does not have an active subscription at this time.</p>
        </div>
    @endif

    <!-- Subscription History -->
    <div class="section-title" style="page-break-before: always;">Subscription History</div>

    @if($subscriptionHistory->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>Package</th>
                    <th>Status</th>
                    <th class="text-right">Amount</th>
                    <th>Started</th>
                    <th>Expired</th>
                    <th>Duration</th>
                    <th>Payment</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subscriptionHistory as $subscription)
                    <tr>
                        <td style="font-weight: bold;">{{ $subscription->package->name }}</td>
                        <td><span class="badge badge-{{ strtolower($subscription->status) }}">{{ ucfirst($subscription->status) }}</span></td>
                        <td class="text-right" style="font-weight: bold;">${{ number_format($subscription->amount_paid, 2) }}</td>
                        <td style="font-size: 9px;">{{ $subscription->starts_at->format('M d, Y') }}</td>
                        <td style="font-size: 9px;">{{ $subscription->expires_at->format('M d, Y') }}</td>
                        <td style="font-size: 9px;">{{ $subscription->starts_at->diffInDays($subscription->expires_at) }} days</td>
                        <td style="font-size: 9px;">{{ ucfirst(str_replace('_', ' ', $subscription->payment_method ?? 'N/A')) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot style="background-color: #f2f2f2; font-weight: bold;">
                <tr>
                    <td colspan="2">Total</td>
                    <td class="text-right">${{ number_format($subscriptionHistory->sum('amount_paid'), 2) }}</td>
                    <td colspan="4">{{ $subscriptionHistory->count() }} subscription(s)</td>
                </tr>
            </tfoot>
        </table>
    @else
        <div class="info-box" style="text-align: center; padding: 20px;">
            <p style="font-size: 11px; color: #666; margin: 0;">No subscription history available.</p>
        </div>
    @endif

    <!-- Summary -->
    <div class="section-title">Summary</div>
    <table class="data-table">
        <tbody>
            <tr>
                <td style="width: 40%; font-weight: bold;">Agent Name</td>
                <td>{{ auth()->user()->name }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Agent Email</td>
                <td>{{ auth()->user()->email }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Total Subscriptions</td>
                <td>{{ $stats['total_subscriptions'] }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Total Amount Spent</td>
                <td>${{ number_format($stats['total_spent'], 2) }}</td>
            </tr>
            @if($stats['active_since'])
                <tr>
                    <td style="font-weight: bold;">Member Since</td>
                    <td>{{ $stats['active_since']->format('M d, Y') }}</td>
                </tr>
            @endif
            @if($currentSubscription)
                <tr>
                    <td style="font-weight: bold;">Current Package</td>
                    <td>{{ $currentSubscription->package->name }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Subscription Status</td>
                    <td>{{ ucfirst($currentSubscription->status) }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Days Remaining</td>
                    <td>{{ $currentSubscription->daysRemaining() }} days</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Agent Package Subscription System - Detailed Report</p>
        <p>Report ID: SUB-{{ now()->format('Ymd-His') }} | Generated by: {{ auth()->user()->email }}</p>
        <p class="no-print">
            <button onclick="window.print()" style="padding: 5px 15px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 3px;">
                Print Report
            </button>
            <button onclick="window.close()" style="padding: 5px 15px; background: #6c757d; color: white; border: none; cursor: pointer; border-radius: 3px; margin-left: 10px;">
                Close
            </button>
        </p>
    </div>
</body>
</html>
