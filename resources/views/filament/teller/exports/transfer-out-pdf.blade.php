<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>{{ __('message.Transfer-OUT Requests') }} — {{ $date }}</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: 'DejaVu Sans', Arial, sans-serif;
    font-size: 9px;
    color: #1e293b;
    background: #fff;
    line-height: 1.4;
  }

  /* ── Page Header ── */
  .page-header {
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 60%, #4338ca 100%);
    color: #fff;
    padding: 18px 22px 14px;
    border-radius: 0 0 8px 8px;
    margin-bottom: 14px;
  }
  .page-header .brand { font-size: 13px; font-weight: 700; letter-spacing: 1px; opacity: 0.85; }
  .page-header h1 { font-size: 20px; font-weight: 800; margin: 3px 0 2px; }
  .page-header .meta { font-size: 8px; opacity: 0.7; }
  .page-header .meta span { margin-right: 14px; }

  /* ── Summary Cards ── */
  .summary-row {
    display: flex;
    gap: 10px;
    margin-bottom: 14px;
  }
  .summary-card {
    flex: 1;
    border-radius: 8px;
    padding: 10px 12px;
    border: 1.5px solid;
  }
  .summary-card .label { font-size: 7.5px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.7; }
  .summary-card .value { font-size: 20px; font-weight: 800; margin-top: 2px; }
  .summary-card .sub { font-size: 7px; opacity: 0.65; margin-top: 1px; }

  .card-pending  { background: #fffbeb; border-color: #f59e0b; color: #92400e; }
  .card-accepted { background: #eff6ff; border-color: #3b82f6; color: #1e3a8a; }
  .card-done     { background: #f0fdf4; border-color: #22c55e; color: #14532d; }
  .card-rejected { background: #fff1f2; border-color: #f43f5e; color: #881337; }
  .card-total    { background: #f5f3ff; border-color: #8b5cf6; color: #4c1d95; }

  /* ── Table ── */
  table {
    width: 100%;
    border-collapse: collapse;
    font-size: 8px;
  }
  thead tr {
    background: #312e81;
    color: #fff;
  }
  thead th {
    padding: 7px 6px;
    text-align: left;
    font-weight: 700;
    font-size: 7.5px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    white-space: nowrap;
  }
  thead th:last-child, thead th.num { text-align: right; }

  tbody tr:nth-child(even) { background: #f8fafc; }
  tbody tr:nth-child(odd)  { background: #ffffff; }
  tbody tr:hover { background: #f1f5f9; }

  tbody td {
    padding: 6px 6px;
    border-bottom: 1px solid #e2e8f0;
    vertical-align: middle;
  }
  tbody td.num { text-align: right; font-weight: 600; }
  tbody td.net { text-align: right; font-weight: 700; color: #15803d; }

  /* ── Status Badges ── */
  .badge {
    display: inline-block;
    padding: 2px 7px;
    border-radius: 999px;
    font-size: 7px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
  }
  .badge-pending  { background: #fef3c7; color: #92400e; border: 1px solid #f59e0b; }
  .badge-accepted { background: #dbeafe; color: #1e3a8a; border: 1px solid #3b82f6; }
  .badge-done     { background: #dcfce7; color: #14532d; border: 1px solid #22c55e; }
  .badge-rejected { background: #ffe4e6; color: #881337; border: 1px solid #f43f5e; }
  .badge-cancelled{ background: #f1f5f9; color: #475569; border: 1px solid #94a3b8; }

  /* ── Footer ── */
  .page-footer {
    margin-top: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 7px;
    color: #94a3b8;
    border-top: 1px solid #e2e8f0;
    padding-top: 8px;
  }
  .page-footer strong { color: #475569; }

  .no-data {
    text-align: center;
    padding: 40px;
    color: #94a3b8;
    font-size: 12px;
  }
</style>
</head>
<body>

{{-- ── Page Header ── --}}
<div class="page-header">
  <div class="brand">G+ SERVICES — TELLER @ POIPET</div>
  <h1>{{ __('message.Transfer-OUT Requests') }}</h1>
  <div class="meta">
    <span>📅 {{ __('message.Today') }}: {{ $date }}</span>
    <span>📊 {{ __('message.All') }}: {{ $records->count() }} {{ __('message.Transfer-OUT') }}</span>
    <span>🕐 {{ __('message.Auto-refreshing') }}</span>
  </div>
</div>

{{-- ── Summary Cards ── --}}
<div class="summary-row">
  <div class="summary-card card-pending">
    <div class="label">⏳ {{ __('message.Pending') }}</div>
    <div class="value">{{ $pending }}</div>
    <div class="sub">{{ __('message.Awaiting your action') }}</div>
  </div>
  <div class="summary-card card-accepted">
    <div class="label">✅ {{ __('message.Accepted') }}</div>
    <div class="value">{{ $accepted }}</div>
    <div class="sub">{{ __('message.Processing in progress') }}</div>
  </div>
  <div class="summary-card card-done">
    <div class="label">✔ {{ __('message.Completed') }}</div>
    <div class="value">{{ $completed }}</div>
    <div class="sub">{{ __('message.Completed') }}</div>
  </div>
  <div class="summary-card card-rejected">
    <div class="label">✖ {{ __('message.Rejected') }}</div>
    <div class="value">{{ $rejected }}</div>
    <div class="sub">{{ __('message.Review if needed') }}</div>
  </div>
  <div class="summary-card card-total">
    <div class="label">💰 {{ __('message.Net Amount') }} Total</div>
    <div class="value">{{ number_format($total, 2) }}</div>
    <div class="sub">{{ $currency }}</div>
  </div>
</div>

{{-- ── Records Table ── --}}
@if ($records->isEmpty())
  <div class="no-data">{{ __('message.Today Transfer-OUT') }} — No records found.</div>
@else
<table>
  <thead>
    <tr>
      <th>#</th>
      <th>{{ __('message.Invoice Number') }}</th>
      <th>{{ __('message.Customer Name') }}</th>
      <th>{{ __('message.Bank Name') }}</th>
      <th>{{ __('message.Account Name') }}</th>
      <th>{{ __('message.Account Number') }}</th>
      <th>{{ __('message.Currency') }}</th>
      <th class="num">{{ __('message.Entered Amount') }}</th>
      <th class="num">{{ __('message.Transfer Fee') }}</th>
      <th class="num">{{ __('message.Net Amount') }}</th>
      <th>{{ __('message.Status') }}</th>
      <th>{{ __('message.Reject Reason') }}</th>
      <th>{{ __('message.Created At') }}</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($records as $i => $r)
    <tr>
      <td>{{ $i + 1 }}</td>
      <td><strong>{{ $r->invoice_number }}</strong></td>
      <td>{{ $r->customer_name }}</td>
      <td>{{ $r->bank_name }}</td>
      <td>{{ $r->acc_name }}</td>
      <td style="font-family: monospace;">{{ $r->acc_number }}</td>
      <td>{{ $r->currency }}</td>
      <td class="num">{{ number_format($r->entered_amount, 2) }}</td>
      <td class="num">{{ number_format($r->trf_fee, 2) }}</td>
      <td class="net">{{ number_format($r->net_amount, 2) }}</td>
      <td>
        @php
          $badgeClass = match($r->status) {
            'pending_bkk_approval' => 'badge-pending',
            'accepted_bkk'         => 'badge-accepted',
            'completed'            => 'badge-done',
            'Rejected'             => 'badge-rejected',
            'cancelled'            => 'badge-cancelled',
            default                => 'badge-cancelled',
          };
          $statusLabel = match($r->status) {
            'pending_bkk_approval' => __('message.Pending'),
            'accepted_bkk'         => __('message.Accepted'),
            'completed'            => __('message.Completed'),
            'Rejected'             => __('message.Rejected'),
            'cancelled'            => __('message.Cancelled'),
            default                => $r->status,
          };
        @endphp
        <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
      </td>
      <td style="color: #be123c; font-size: 7.5px;">{{ $r->reject_reason ?? '—' }}</td>
      <td>{{ $r->created_at?->format('d M Y H:i') }}</td>
    </tr>
    @endforeach

    {{-- Totals Row --}}
    <tr style="background: #f5f3ff; border-top: 2px solid #8b5cf6;">
      <td colspan="9" style="text-align: right; font-weight: 700; color: #4c1d95; padding: 7px 6px;">
        TOTAL ({{ $records->count() }} records)
      </td>
      <td class="net" style="color: #4c1d95; font-size: 10px;">
        {{ number_format($total, 2) }}
      </td>
      <td colspan="3"></td>
    </tr>
  </tbody>
</table>
@endif

{{-- ── Footer ── --}}
<div class="page-footer">
  <div>
    <strong>G+ Services</strong> — Teller @ Poipet |
    {{ __('message.Transfer-OUT Requests') }}
  </div>
  <div>
    Generated: {{ now()->format('d M Y H:i:s') }} |
    Exported by: {{ auth('teller')->user()?->name ?? 'Teller' }}
  </div>
</div>

</body>
</html>