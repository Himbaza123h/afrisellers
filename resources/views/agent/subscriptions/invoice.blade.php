<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $sub->generateInvoiceNumber() }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 13px; color: #111; padding: 40px; background: #f9fafb; }
        .invoice { max-width: 720px; margin: 0 auto; background: white; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.08); overflow: hidden; }
        .header { background: linear-gradient(135deg, #f59e0b, #ef4444); padding: 32px 40px; color: white; }
        .header h1 { font-size: 24px; font-weight: 700; }
        .header p { font-size: 12px; opacity: .85; margin-top: 4px; }
        .body { padding: 32px 40px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 24px; }
        .col h3 { font-size: 10px; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; margin-bottom: 6px; }
        .col p { font-size: 13px; font-weight: 600; color: #111; line-height: 1.6; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; padding: 10px 12px; font-size: 10px; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; background: #f9fafb; border-bottom: 2px solid #e5e7eb; }
        td { padding: 12px; border-bottom: 1px solid #f3f4f6; font-size: 13px; }
        .total-row td { font-weight: 700; font-size: 15px; background: #f9fafb; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 9999px; font-size: 11px; font-weight: 600; }
        .badge-paid { background: #d1fae5; color: #065f46; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .footer { padding: 20px 40px; border-top: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between; }
        .footer p { font-size: 11px; color: #9ca3af; }
        .no-print { margin-bottom: 20px; text-align: right; }
        @media print {
            body { background: white; padding: 0; }
            .no-print { display: none; }
            .invoice { box-shadow: none; border-radius: 0; }
            @page { margin: 15mm; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()"
            style="padding: 8px 20px; background: #f59e0b; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
            🖨️ Print / Save PDF
        </button>
        <a href="{{ route('agent.subscriptions.index') }}"
            style="margin-left: 10px; padding: 8px 20px; background: #f3f4f6; color: #374151; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; display: inline-block;">
            ← Back
        </a>
    </div>

    <div class="invoice">
        <div class="header">
            <h1>Invoice</h1>
            <p>#{{ $sub->generateInvoiceNumber() }} &nbsp;·&nbsp; {{ $sub->created_at->format('M d, Y') }}</p>
        </div>

        <div class="body">
            <div class="row">
                <div class="col">
                    <h3>Billed To</h3>
                    <p>{{ auth()->user()->name }}<br>{{ auth()->user()->email }}</p>
                </div>
                <div class="col" style="text-align: right;">
                    <h3>Invoice Number</h3>
                    <p style="font-family: monospace;">{{ $sub->generateInvoiceNumber() }}</p>
                    <h3 style="margin-top: 10px;">Transaction ID</h3>
                    <p style="font-family: monospace; font-size: 11px;">{{ $sub->transaction_id ?? '—' }}</p>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Period</th>
                        <th>Payment Method</th>
                        <th style="text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>{{ $sub->package?->name }}</strong> Agent Plan<br>
                            <span style="font-size: 11px; color: #6b7280;">{{ ucfirst($sub->package?->billing_cycle) }} subscription</span>
                        </td>
                        <td style="font-size: 11px;">
                            {{ $sub->starts_at?->format('M d, Y') }}<br>→ {{ $sub->expires_at?->format('M d, Y') }}
                        </td>
                        <td>{{ ucfirst(str_replace('_', ' ', $sub->payment_method ?? '—')) }}</td>
                        <td style="text-align: right; font-weight: 700;">${{ number_format($sub->amount_paid, 2) }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="3" style="text-align: right;">Total Paid</td>
                        <td style="text-align: right; color: #059669;">${{ number_format($sub->amount_paid, 2) }}</td>
                    </tr>
                </tfoot>
            </table>

            <div style="margin-top: 20px; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <p style="font-size: 11px; color: #6b7280;">Payment Status</p>
                    <span class="badge badge-{{ $sub->payment_status === 'paid' ? 'paid' : 'pending' }}">
                        {{ ucfirst($sub->payment_status ?? 'pending') }}
                    </span>
                </div>
                <div style="text-align: right;">
                    <p style="font-size: 11px; color: #6b7280;">Subscription Status</p>
                    <span class="badge badge-{{ $sub->isActive() ? 'paid' : 'pending' }}">
                        {{ ucfirst($sub->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>{{ config('app.name') }} &nbsp;·&nbsp; Agent Portal</p>
            <p>Generated {{ now()->format('M d, Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
