@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'معاينة الإيصال' : 'Receipt Preview')
@section('page-title', app()->getLocale() === 'ar' ? 'معاينة الإيصال' : 'Receipt Preview')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-eye"></i> {{ app()->getLocale() === 'ar' ? 'معاينة الإيصال' : 'Receipt Preview' }}</span>
                <button onclick="printReceipt()" class="btn btn-sm btn-primary">
                    <i class="bi bi-printer"></i> {{ app()->getLocale() === 'ar' ? 'طباعة' : 'Print' }}
                </button>
            </div>
            <div class="card-body">
                @include('tickets.receipt', ['ticket' => $ticket, 'settings' => $settings])
            </div>
        </div>
        
        <div class="mt-3 text-center">
            <a href="{{ route('settings.printing.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> {{ app()->getLocale() === 'ar' ? 'رجوع للإعدادات' : 'Back to Settings' }}
            </a>
            <a href="{{ route('settings.printing.designer') }}" class="btn btn-warning">
                <i class="bi bi-palette"></i> {{ app()->getLocale() === 'ar' ? 'مصمم الإيصال' : 'Receipt Designer' }}
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const printMode = '{{ $settings['print_mode'] ?? 'browser' }}';
const customWidth = {{ $settings['paper_width'] ?? 80 }};
const customHeight = {{ $settings['paper_height'] ?? 200 }};

function printReceipt() {
    if (printMode === 'system') {
        // System print dialog
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Receipt</title>
                <style>
                    @page { size: auto; margin: 5mm; }
                    body { font-family: Arial; margin: 0; padding: 5mm; }
                    @media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }
                </style>
            </head>
            <body>
                {!! $ticket->department->name !!}<br>
                <strong>{{ $ticket->ticket_number }}</strong><br>
                <hr style="border: 1px dashed #000;">
                {{ app()->getLocale() === 'ar' ? 'المريض' : 'Patient' }}: {{ $ticket->patient->full_name }}<br>
                {{ app()->getLocale() === 'ar' ? 'الخدمة' : 'Service' }}: {{ app()->getLocale() === 'ar' ? $ticket->service->name_ar : $ticket->service->name }}<br>
                {{ app()->getLocale() === 'ar' ? 'السعر' : 'Price' }}: {{ number_format($ticket->amount_paid, 2) }} JD<br>
                <script>
                    setTimeout(function() { window.print(); }, 500);
                <\/script>
            </body>
            </html>
        `);
        printWindow.document.close();
    } else if (printMode === 'custom') {
        // Custom size print
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Receipt</title>
                <style>
                    @page {
                        size: ${customWidth}mm ${customHeight}mm;
                        margin: 2mm;
                    }
                    body {
                        font-family: Arial;
                        margin: 0;
                        padding: 2mm;
                        width: ${customWidth - 4}mm;
                    }
                    @media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }
                </style>
            </head>
            <body>
                {!! $ticket->department->name !!}<br>
                <strong>{{ $ticket->ticket_number }}</strong><br>
                <hr style="border: 1px dashed #000;">
                {{ app()->getLocale() === 'ar' ? 'المريض' : 'Patient' }}: {{ $ticket->patient->full_name }}<br>
                {{ app()->getLocale() === 'ar' ? 'الخدمة' : 'Service' }}: {{ app()->getLocale() === 'ar' ? $ticket->service->name_ar : $ticket->service->name }}<br>
                {{ app()->getLocale() === 'ar' ? 'السعر' : 'Price' }}: {{ number_format($ticket->amount_paid, 2) }} JD<br>
                <script>
                    setTimeout(function() { window.print(); }, 500);
                <\/script>
            </body>
            </html>
        `);
        printWindow.document.close();
    } else {
        // Browser default print
        window.print();
    }
}
</script>
@endpush
