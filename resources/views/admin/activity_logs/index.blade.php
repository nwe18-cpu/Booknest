@extends('admin.layouts.app')

@section('title', 'Activity History - Booknest Admin')

@section('content')
<div class="container-fluid">
    <div class="page-header-flex">
        <div>
            <h1 class="page-title">Activity History</h1>
            <p class="page-subtitle-mute">Track updates and changes made by administrators and staff members across the system.</p>
        </div>
    </div>

    <!-- Read-only Table of Activity Logs -->
    <div class="data-table-card">
        <div class="card-header-flex">
            <div class="header-title-group">
                <h3><i class="fa-solid fa-clock-rotate-left"></i> System Audit Trail</h3>
                <p>A read-only log of modifications to books, authors, orders, customer accounts, and settings.</p>
            </div>
        </div>

        <div class="table-responsive" style="margin-top: 15px;">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th style="width: 180px;">Date & Time</th>
                        <th style="width: 220px;">Staff Member</th>
                        <th>Modification Description</th>
                        <th style="width: 140px;">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="font-size-0-78-text-muted">
                                <div><strong>{{ $log->created_at->format('Y-m-d') }}</strong></div>
                                <div style="font-size:0.75rem; color:#718096;">{{ $log->created_at->format('h:i:s A') }}</div>
                                <div style="font-size:0.7rem; color:#a0aec0;">{{ $log->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                @if($log->staff)
                                    <div><strong>{{ $log->staff->name }}</strong></div>
                                    <div class="font-size-0-78-text-muted">{{ $log->staff->email }}</div>
                                    <span style="font-size:0.7rem; font-weight:700; text-transform:uppercase; color:#718096; background:rgba(0,0,0,0.05); padding:2px 6px; border-radius:4px;">{{ $log->staff->role?->name ?? 'Staff' }}</span>
                                @else
                                    <div style="color: #a0aec0; font-style: italic;">System / Automated</div>
                                @endif
                            </td>
                            <td style="font-size: 0.85rem; line-height: 1.4; color: #2d3748; word-break: break-word;">
                                {{ $log->description }}
                            </td>
                            <td class="font-size-0-78-text-muted" style="font-family: monospace;">
                                {{ $log->ip_address ?? 'N/A' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 40px; color: #a0aec0;">
                                <i class="fa-solid fa-clock-rotate-left" style="font-size: 2.5rem; margin-bottom: 12px; display: block; opacity: 0.4;"></i>
                                No activity modifications recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper" style="margin-top: 20px; padding: 0 15px 15px 15px;">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
