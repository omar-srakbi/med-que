@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard')
@section('page-title', app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard')

@section('content')
<!-- Enhanced Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h6 class="text-muted mb-0">{{ app()->getLocale() === 'ar' ? 'إجمالي المرضى' : 'Total Patients' }}</h6>
                        <h3 class="mb-0 mt-1">{{ number_format($stats['total_patients']) }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-people text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    @if($stats['patients_trend'] >= 0)
                        <span class="text-success small"><i class="bi bi-arrow-up"></i> {{ $stats['patients_trend'] }}%</span>
                    @else
                        <span class="text-danger small"><i class="bi bi-arrow-down"></i> {{ abs($stats['patients_trend']) }}%</span>
                    @endif
                    <span class="text-muted small ms-2">{{ app()->getLocale() === 'ar' ? 'منذ الأمس' : 'vs yesterday' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card success border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h6 class="text-muted mb-0">{{ app()->getLocale() === 'ar' ? 'مرضى اليوم' : "Today's Patients" }}</h6>
                        <h3 class="mb-0 mt-1">{{ $stats['today_patients'] }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-person-check text-success" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    @if($stats['patients_trend'] >= 0)
                        <span class="text-success small"><i class="bi bi-arrow-up"></i> {{ $stats['patients_trend'] }}%</span>
                    @else
                        <span class="text-danger small"><i class="bi bi-arrow-down"></i> {{ abs($stats['patients_trend']) }}%</span>
                    @endif
                    <span class="text-muted small ms-2">{{ app()->getLocale() === 'ar' ? 'منذ الأمس' : 'vs yesterday' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card warning border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h6 class="text-muted mb-0">{{ app()->getLocale() === 'ar' ? 'تذاكر اليوم' : "Today's Tickets" }}</h6>
                        <h3 class="mb-0 mt-1">{{ $stats['today_tickets'] }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-ticket-perforated text-warning" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    @if($stats['tickets_trend'] >= 0)
                        <span class="text-success small"><i class="bi bi-arrow-up"></i> {{ $stats['tickets_trend'] }}%</span>
                    @else
                        <span class="text-danger small"><i class="bi bi-arrow-down"></i> {{ abs($stats['tickets_trend']) }}%</span>
                    @endif
                    <span class="text-muted small ms-2">{{ app()->getLocale() === 'ar' ? 'منذ الأمس' : 'vs yesterday' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card danger border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h6 class="text-muted mb-0">{{ app()->getLocale() === 'ar' ? 'إيرادات اليوم' : "Today's Revenue" }}</h6>
                        <h3 class="mb-0 mt-1">{{ number_format($stats['today_revenue'], 2) }} <small class="text-muted">JD</small></h3>
                    </div>
                    <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-cash-stack text-danger" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    @if($stats['revenue_trend'] >= 0)
                        <span class="text-success small"><i class="bi bi-arrow-up"></i> {{ $stats['revenue_trend'] }}%</span>
                    @else
                        <span class="text-danger small"><i class="bi bi-arrow-down"></i> {{ abs($stats['revenue_trend']) }}%</span>
                    @endif
                    <span class="text-muted small ms-2">{{ app()->getLocale() === 'ar' ? 'منذ الأمس' : 'vs yesterday' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Widgets Row -->
<div class="row mb-4">
    <!-- Charts Column -->
    <div class="col-lg-8">
        <div class="row">
            <!-- Patients Trend Chart -->
            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0"><i class="bi bi-graph-up text-primary"></i> {{ app()->getLocale() === 'ar' ? 'حركة المرضى' : 'Patients Trend' }}</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="patientsTrendChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Revenue Trend Chart -->
            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0"><i class="bi bi-cash-stack text-success"></i> {{ app()->getLocale() === 'ar' ? 'حركة الإيرادات' : 'Revenue Trend' }}</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueTrendChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Department Distribution -->
            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0"><i class="bi bi-pie-chart text-info"></i> {{ app()->getLocale() === 'ar' ? 'توزيع الأقسام' : 'Department Distribution' }}</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="departmentChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Ticket Status -->
            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0"><i class="bi bi-clock-history text-warning"></i> {{ app()->getLocale() === 'ar' ? 'حالة التذاكر' : 'Ticket Status' }}</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="ticketStatusChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0"><i class="bi bi-lightning-charge text-warning"></i> {{ app()->getLocale() === 'ar' ? 'إجراءات سريعة' : 'Quick Actions' }}</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @can('create_patients')
                    <a href="{{ route('patients.create') }}" class="btn btn-outline-primary">
                        <i class="bi bi-person-plus"></i> {{ app()->getLocale() === 'ar' ? 'إضافة مريض' : 'Add Patient' }}
                    </a>
                    @endcan
                    @can('create_tickets')
                    <a href="{{ route('tickets.create') }}" class="btn btn-outline-success">
                        <i class="bi bi-ticket-perforated"></i> {{ app()->getLocale() === 'ar' ? 'إنشاء تذكرة' : 'Create Ticket' }}
                    </a>
                    @endcan
                    @can('create_medical_records')
                    <a href="{{ route('medical-records.create') }}" class="btn btn-outline-warning">
                        <i class="bi bi-file-earmark-medical"></i> {{ app()->getLocale() === 'ar' ? 'سجل طبي' : 'Medical Record' }}
                    </a>
                    @endcan
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-graph-up"></i> {{ app()->getLocale() === 'ar' ? 'التقارير' : 'Reports' }}
                    </a>
                    <a href="{{ route('settings.index') }}" class="btn btn-outline-info" @if(!auth()->user()->hasPermission('manage_settings')) style="display:none;" @endif>
                        <i class="bi bi-gear"></i> {{ app()->getLocale() === 'ar' ? 'الإعدادات' : 'Settings' }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Queue Status -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0"><i class="bi bi-display text-primary"></i> {{ app()->getLocale() === 'ar' ? 'حالة الطابور' : 'Queue Status' }}</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" id="queueStatusList">
                    @foreach($queueStatus as $queue)
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h6 class="mb-0">{{ $queue['name'] }}</h6>
                            <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'في الانتظار' : 'Waiting' }}: {{ $queue['waiting'] }}</small>
                        </div>
                        <div class="text-end">
                            @if($queue['current_serving'])
                                <div class="badge bg-success fs-6">{{ app()->getLocale() === 'ar' ? 'الآن' : 'Now' }}: {{ $queue['current_serving'] }}</div>
                            @endif
                            @if($queue['next_queue'])
                                <div class="badge bg-warning text-dark mt-1">{{ app()->getLocale() === 'ar' ? 'التالي' : 'Next' }}: {{ $queue['next_queue'] }}</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0"><i class="bi bi-activity text-danger"></i> {{ app()->getLocale() === 'ar' ? 'النشاط الأخير' : 'Recent Activity' }}</h6>
            </div>
            <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                <div class="list-group list-group-flush" id="activityFeed">
                    @foreach($recentActivity as $activity)
                    <div class="list-group-item py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                @if($activity->action === 'created')
                                    <span class="badge bg-success bg-opacity-10 text-success p-2"><i class="bi bi-plus-circle"></i></span>
                                @elseif($activity->action === 'updated')
                                    <span class="badge bg-warning bg-opacity-10 text-warning p-2"><i class="bi bi-pencil"></i></span>
                                @elseif($activity->action === 'deleted')
                                    <span class="badge bg-danger bg-opacity-10 text-danger p-2"><i class="bi bi-trash"></i></span>
                                @else
                                    <span class="badge bg-info bg-opacity-10 text-info p-2"><i class="bi bi-info-circle"></i></span>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 small">{{ $activity->description }}</h6>
                                <small class="text-muted">{{ $activity->user ? $activity->user->full_name : 'System' }} • {{ $activity->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Tickets Table -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-clock-history"></i> {{ app()->getLocale() === 'ar' ? 'آخر التذاكر اليوم' : "Today's Recent Tickets" }}</h6>
                @can('create_tickets')
                <a href="{{ route('tickets.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus"></i> {{ app()->getLocale() === 'ar' ? 'تذكرة جديدة' : 'New Ticket' }}
                </a>
                @endcan
            </div>
            <div class="card-body p-0">
                @if($recentTickets->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3">{{ app()->getLocale() === 'ar' ? 'رقم التذكرة' : 'Ticket #' }}</th>
                                <th class="py-3">{{ app()->getLocale() === 'ar' ? 'المريض' : 'Patient' }}</th>
                                <th class="py-3">{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }}</th>
                                <th class="py-3">{{ app()->getLocale() === 'ar' ? 'الخدمة' : 'Service' }}</th>
                                <th class="py-3">{{ app()->getLocale() === 'ar' ? 'الطابور' : 'Queue #' }}</th>
                                <th class="py-3">{{ app()->getLocale() === 'ar' ? 'المبلغ' : 'Amount' }}</th>
                                <th class="py-3">{{ app()->getLocale() === 'ar' ? 'الوقت' : 'Time' }}</th>
                                <th class="py-3">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTickets as $ticket)
                            <tr>
                                <td class="py-3"><span class="badge bg-primary">{{ $ticket->ticket_number }}</span></td>
                                <td class="py-3">{{ $ticket->patient->full_name }}</td>
                                <td class="py-3">{{ app()->getLocale() === 'ar' ? $ticket->department->name_ar : $ticket->department->name }}</td>
                                <td class="py-3">{{ app()->getLocale() === 'ar' ? $ticket->service->name_ar : $ticket->service->name }}</td>
                                <td class="py-3"><span class="badge bg-info">{{ $ticket->queue_number }}</span></td>
                                <td class="py-3">{{ number_format($ticket->amount_paid, 2) }} JD</td>
                                <td class="py-3">{{ $ticket->created_at_time?->format('H:i') }}</td>
                                <td class="py-3">
                                    @if($ticket->completed_at)
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> {{ app()->getLocale() === 'ar' ? 'مكتمل' : 'Completed' }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> {{ app()->getLocale() === 'ar' ? 'قيد الانتظار' : 'Pending' }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">{{ app()->getLocale() === 'ar' ? 'لا توجد تذاكر اليوم' : 'No tickets today' }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Chart.js default config
Chart.defaults.font.family = "'Segoe UI', 'Helvetica', 'Arial', sans-serif";
Chart.defaults.color = '#666';

// Color scheme
const colors = {
    primary: '#0d6efd',
    success: '#198754',
    warning: '#ffc107',
    danger: '#dc3545',
    info: '#0dcaf0',
    secondary: '#6c757d'
};

// Store chart instances to prevent recreation
let patientsChart = null;
let revenueChart = null;
let deptChart = null;
let ticketChart = null;

// Patients Trend Chart
const patientsCtx = document.getElementById('patientsTrendChart').getContext('2d');
if (patientsCtx) {
    patientsChart = new Chart(patientsCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($patientsTrend, 'date')) !!},
            datasets: [{
                label: '{{ app()->getLocale() === 'ar' ? 'المرضى' : 'Patients' }}',
                data: {!! json_encode(array_column($patientsTrend, 'count')) !!},
                borderColor: colors.primary,
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointBackgroundColor: colors.primary
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 2,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });
}

// Revenue Trend Chart
const revenueCtx = document.getElementById('revenueTrendChart').getContext('2d');
if (revenueCtx) {
    revenueChart = new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($revenueTrend, 'date')) !!},
            datasets: [{
                label: '{{ app()->getLocale() === 'ar' ? 'الإيرادات' : 'Revenue' }} (JD)',
                data: {!! json_encode(array_column($revenueTrend, 'revenue')) !!},
                backgroundColor: 'rgba(25, 135, 84, 0.8)',
                borderColor: colors.success,
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 2,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });
}

// Department Distribution Chart
const deptCtx = document.getElementById('departmentChart').getContext('2d');
if (deptCtx) {
    deptChart = new Chart(deptCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_column($departmentDistribution->toArray(), 'name')) !!},
            datasets: [{
                data: {!! json_encode(array_column($departmentDistribution->toArray(), 'count')) !!},
                backgroundColor: [
                    colors.primary,
                    colors.success,
                    colors.warning,
                    colors.danger,
                    colors.info,
                    colors.secondary
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 1.5,
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: { boxWidth: 12, padding: 10 }
                }
            }
        }
    });
}

// Ticket Status Chart
const ticketCtx = document.getElementById('ticketStatusChart').getContext('2d');
if (ticketCtx) {
    ticketChart = new Chart(ticketCtx, {
        type: 'pie',
        data: {
            labels: ['{{ app()->getLocale() === 'ar' ? 'مكتمل' : 'Completed' }}', '{{ app()->getLocale() === 'ar' ? 'قيد الانتظار' : 'Pending' }}'],
            datasets: [{
                data: [{{ $ticketStatus['completed'] }}, {{ $ticketStatus['pending'] }}],
                backgroundColor: [colors.success, colors.warning],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 1.5,
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: { boxWidth: 12, padding: 10 }
                }
            }
        }
    });
}

// Auto-refresh dashboard data every 30 seconds
function refreshDashboardData() {
    fetch('{{ route('dashboard.api-data') }}')
        .then(response => response.json())
        .then(data => {
            // Update queue status
            const queueList = document.getElementById('queueStatusList');
            if (queueList && data.queue_status) {
                let queueHtml = '';
                data.queue_status.forEach(queue => {
                    queueHtml += `
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div>
                                <h6 class="mb-0">${queue.name}</h6>
                                <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'في الانتظار' : 'Waiting' }}: ${queue.waiting}</small>
                            </div>
                            <div class="text-end">
                                ${queue.current_serving ? `<div class="badge bg-success fs-6">{{ app()->getLocale() === 'ar' ? 'الآن' : 'Now' }}: ${queue.current_serving}</div>` : ''}
                                ${queue.next_queue ? `<div class="badge bg-warning text-dark mt-1">{{ app()->getLocale() === 'ar' ? 'التالي' : 'Next' }}: ${queue.next_queue}</div>` : ''}
                            </div>
                        </div>
                    `;
                });
                queueList.innerHTML = queueHtml;
            }

            // Update activity feed
            const activityFeed = document.getElementById('activityFeed');
            if (activityFeed && data.activity) {
                let activityHtml = '';
                data.activity.forEach(item => {
                    let icon = 'info-circle';
                    let color = 'info';
                    if (item.action === 'created') { icon = 'plus-circle'; color = 'success'; }
                    else if (item.action === 'updated') { icon = 'pencil'; color = 'warning'; }
                    else if (item.action === 'deleted') { icon = 'trash'; color = 'danger'; }
                    
                    activityHtml += `
                        <div class="list-group-item py-3 px-3">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <span class="badge bg-${color} bg-opacity-10 text-${color} p-2">
                                        <i class="bi bi-${icon}"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 small">${item.description}</h6>
                                    <small class="text-muted">${item.user} • ${item.time}</small>
                                </div>
                            </div>
                        </div>
                    `;
                });
                activityFeed.innerHTML = activityHtml;
            }
        })
        .catch(error => console.error('Error refreshing data:', error));
}

// Refresh every 60 seconds (disabled by default to prevent layout issues)
// Uncomment the line below to enable auto-refresh
// let refreshInterval = setInterval(refreshDashboardData, 60000);
</script>

<style>
.stat-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
.card {
    transition: box-shadow 0.2s;
}
.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
}
/* Prevent layout shift and scaling */
body {
    overflow-x: hidden;
}
.row {
    margin-right: 0 !important;
    margin-left: 0 !important;
}
.col-md-3, .col-md-6, .col-lg-4, .col-lg-8, .col-12 {
    padding-right: 0.75rem !important;
    padding-left: 0.75rem !important;
}
.list-group-item {
    overflow: hidden;
}
#activityFeed {
    max-height: 400px !important;
    overflow-y: auto !important;
}
/* Fix chart container heights */
#patientsTrendChart,
#revenueTrendChart,
#departmentChart,
#ticketStatusChart {
    max-height: 200px !important;
}
.card-body canvas {
    max-height: 200px !important;
}
</style>
@endpush
@endsection
