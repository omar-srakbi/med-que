@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'إيصال التذكرة' : 'Ticket Receipt')
@section('page-title', app()->getLocale() === 'ar' ? 'إيصال التذكرة' : 'Ticket Receipt')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card" id="receipt-card">
            @if(($printSettings['receipt_show_header'] ?? false))
            <div class="card-header text-center">
                <button onclick="window.print()" class="btn btn-sm btn-primary float-end no-print">
                    <i class="bi bi-printer"></i> {{ app()->getLocale() === 'ar' ? 'طباعة' : 'Print' }}
                </button>
                <h4><i class="bi bi-hospital"></i> {{ \App\Models\Setting::getClinicName() }}</h4>
                <p class="mb-0 text-muted">{{ app()->getLocale() === 'ar' ? 'إيصال تذكرة' : 'Ticket Receipt' }}</p>
            </div>
            @endif
            <div class="card-body" style="padding: 0; margin: 0;">
                @php
                    // Get print settings
                    $printSettings = [];
                    foreach (\App\Models\PrintSetting::where('category', 'receipt')->get() as $setting) {
                        $printSettings[$setting->setting_key] = $setting->setting_value;
                    }
                    
                    // Also get general settings
                    foreach (\App\Models\PrintSetting::where('category', 'general')->get() as $setting) {
                        $printSettings[$setting->setting_key] = $setting->setting_value;
                    }

                    // Get saved layout
                    $layout = null;
                    if (isset($printSettings['receipt_layout'])) {
                        $layout = json_decode($printSettings['receipt_layout'], true);
                    }

                    // Get paper size (from designer or print settings)
                    $paperWidth = $printSettings['paper_width'] ?? $printSettings['print_custom_width'] ?? 80;
                    $paperHeight = $printSettings['paper_height'] ?? $printSettings['print_custom_height'] ?? 200;
                @endphp
                
                @if($layout)
                    <!-- Use Custom Layout from Designer -->
                    <div id="custom_receipt" style="position: relative; width: {{ $paperWidth }}mm; height: {{ $paperHeight }}mm; margin: 0 auto; background: white; overflow: hidden;">
                        @foreach($layout as $element)
                            @php
                                $text = $element['text'] ?? '';
                                // Replace placeholders with actual values
                                $text = str_replace('{ticket_number}', $ticket->ticket_number, $text);
                                $text = str_replace('{patient_name}', $ticket->patient->full_name, $text);
                                $text = str_replace('{department_name}', app()->getLocale() === 'ar' ? $ticket->department->name_ar : $ticket->department->name, $text);
                                $text = str_replace('{service_name}', app()->getLocale() === 'ar' ? $ticket->service->name_ar : $ticket->service->name, $text);
                                $text = str_replace('{price}', \App\Models\Setting::formatCurrency($ticket->amount_paid), $text);
                                $text = str_replace('{queue_number}', $ticket->queue_number, $text);
                                $text = str_replace('{cashier_name}', $ticket->cashier->full_name, $text);
                                $text = str_replace('{created_at}', $ticket->created_at->format('Y-m-d H:i'), $text);
                                $text = str_replace('{visit_date}', $ticket->visit_date->format('Y-m-d H:i'), $text);
                                $text = str_replace('{clinic_name}', \App\Models\Setting::getClinicName(), $text);
                            @endphp
                            <div style="position: absolute; left: {{ $element['x'] }}mm; top: {{ $element['y'] }}mm; font-size: {{ $element['size'] }}pt; text-align: {{ $element['align'] }}; font-weight: {{ $element['bold'] ? 'bold' : 'normal' }};">
                                {!! $text !!}
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Use Default Layout with Custom Paper Size -->
                    <div id="default_receipt" style="width: {{ $paperWidth }}mm; min-height: {{ $paperHeight }}mm; margin: 0; background: white; padding: 2mm;">
                        <div style="text-align: center; margin-bottom: 5mm;">
                            <h2 style="font-size: 16pt; margin: 0; font-weight: bold;">{{ $ticket->ticket_number }}</h2>
                            <p style="font-size: 9pt; margin: 1mm 0; color: #666;">{{ $ticket->visit_date->format('Y-m-d H:i') }}</p>
                        </div>

                    <table class="table table-borderless" style="margin: 0; width: 100%;">
                        @if(($printSettings['receipt_show_patient'] ?? true))
                        <tr>
                            <td style="padding: 1mm 0;"><strong>{{ app()->getLocale() === 'ar' ? 'المريض' : 'Patient' }}:</strong></td>
                            <td class="text-end" style="padding: 1mm 0;">{{ $ticket->patient->full_name }}</td>
                        </tr>
                        @endif

                        @if(($printSettings['receipt_show_clinic_name'] ?? true))
                        <tr>
                            <td style="padding: 1mm 0;"><strong>{{ app()->getLocale() === 'ar' ? 'العيادة' : 'Clinic' }}:</strong></td>
                            <td class="text-end" style="padding: 1mm 0;">{{ \App\Models\Setting::getClinicName() }}</td>
                        </tr>
                        @endif

                        @if(($printSettings['receipt_show_phone'] ?? true))
                        <tr>
                            <td style="padding: 1mm 0;"><strong>{{ app()->getLocale() === 'ar' ? 'الهاتف' : 'Phone' }}:</strong></td>
                            <td class="text-end" style="padding: 1mm 0;">{{ config('app.phone', 'N/A') }}</td>
                        </tr>
                        @endif

                        @if(($printSettings['receipt_show_department'] ?? true))
                        <tr>
                            <td style="padding: 1mm 0;"><strong>{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }}:</strong></td>
                            <td class="text-end" style="padding: 1mm 0;">{{ app()->getLocale() === 'ar' ? $ticket->department->name_ar : $ticket->department->name }}</td>
                        </tr>
                        @endif

                        @if(($printSettings['receipt_show_service'] ?? true))
                        <tr>
                            <td style="padding: 1mm 0;"><strong>{{ app()->getLocale() === 'ar' ? 'الخدمة' : 'Service' }}:</strong></td>
                            <td class="text-end" style="padding: 1mm 0;">{{ app()->getLocale() === 'ar' ? $ticket->service->name_ar : $ticket->service->name }}</td>
                        </tr>
                        @endif

                        @if(($printSettings['receipt_show_queue_number'] ?? true))
                        <tr>
                            <td style="padding: 1mm 0;"><strong>{{ app()->getLocale() === 'ar' ? 'رقم الطابور' : 'Queue Number' }}:</strong></td>
                            <td class="text-end" style="padding: 1mm 0;"><span class="badge bg-info fs-6">{{ $ticket->queue_number }}</span></td>
                        </tr>
                        @endif

                        @if(($printSettings['receipt_show_price'] ?? true))
                        <tr>
                            <td style="padding: 1mm 0;"><strong>{{ app()->getLocale() === 'ar' ? 'المبلغ المدفوع' : 'Amount Paid' }}:</strong></td>
                            <td class="text-end" style="padding: 1mm 0;"><span class="text-success fw-bold">{{ \App\Models\Setting::formatCurrency($ticket->amount_paid) }}</span></td>
                        </tr>
                        @endif

                        @if(($printSettings['receipt_show_cashier'] ?? true))
                        <tr>
                            <td style="padding: 1mm 0;"><strong>{{ app()->getLocale() === 'ar' ? 'الأمين' : 'Cashier' }}:</strong></td>
                            <td class="text-end" style="padding: 1mm 0;">{{ $ticket->cashier->full_name }}</td>
                        </tr>
                        @endif

                        @if(($printSettings['receipt_show_visit_date'] ?? true))
                        <tr>
                            <td style="padding: 1mm 0;"><strong>{{ app()->getLocale() === 'ar' ? 'تاريخ الزيارة' : 'Visit Date' }}:</strong></td>
                            <td class="text-end" style="padding: 1mm 0;">{{ $ticket->visit_date->format('Y-m-d H:i') }}</td>
                        </tr>
                        @endif

                        @if(($printSettings['receipt_show_ticket_number'] ?? true))
                        <tr>
                            <td style="padding: 1mm 0;"><strong>{{ app()->getLocale() === 'ar' ? 'رقم التذكرة' : 'Ticket Number' }}:</strong></td>
                            <td class="text-end" style="padding: 1mm 0;">{{ $ticket->ticket_number }}</td>
                        </tr>
                        @endif
                    </table>

                    @if(($printSettings['receipt_show_thank_you'] ?? true))
                    <div style="text-align: center; margin-top: 5mm;">
                        <p style="font-size: 10pt; margin: 0; color: #666;">{{ app()->getLocale() === 'ar' ? ($printSettings['receipt_thank_you_ar'] ?? 'شكراً لزيارتكم') : ($printSettings['receipt_thank_you_en'] ?? 'Thank you for your visit') }}</p>
                    </div>
                    @endif

                    @if(($printSettings['qr_code_enabled'] ?? true))
                    <div style="text-align: center; margin-top: 3mm;">
                        <div style="width: {{ $printSettings['qr_code_size'] ?? 100 }}px; height: {{ $printSettings['qr_code_size'] ?? 100 }}px; margin: 0 auto; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                            <small style="color: #666;">QR Code</small>
                        </div>
                    </div>
                    @endif
                @endif
                </div>
            </div>
            <div class="card-footer text-center">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="bi bi-printer"></i> {{ app()->getLocale() === 'ar' ? 'طباعة' : 'Print' }}
                </button>
                <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> {{ app()->getLocale() === 'ar' ? 'إغلاق' : 'Close' }}
                </a>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .sidebar, .btn, .main-content > div:first-child, .card-header .btn, .card-footer, .no-print {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .card-body {
        padding: 0 !important;
        margin: 0 !important;
    }
    #custom_receipt, #default_receipt {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
        margin: 0 !important;
        padding: 0 !important;
    }
    table {
        margin: 0 !important;
    }
    /* Hide notifications/alerts */
    .alert, .alert-dismissible, #search-results, .dropdown-menu {
        display: none !important;
    }
}
</style>
@endsection
