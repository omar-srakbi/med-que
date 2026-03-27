<div style="font-family: 'Courier New', monospace; font-size: 11px; line-height: 1.4; max-width: 320px; margin: 0 auto;">
    {{-- Header --}}
    @if(($settings['receipt_show_header'] ?? false))
    <div style="text-align: center; margin-bottom: 10px; border-bottom: 1px dashed #000; padding-bottom: 8px;">
        @if(($settings['receipt_show_logo'] ?? true) && isset($settings['receipt_logo_path']))
        <img src="{{ asset('storage/' . $settings['receipt_logo_path']) }}" style="max-height: 40px; margin-bottom: 5px;">
        @endif
        @if(($settings['receipt_show_clinic_name'] ?? true))
        <div style="font-weight: bold; font-size: 13px;">{{ \App\Models\Setting::getClinicName() }}</div>
        @endif
        @if(($settings['receipt_show_phone'] ?? true))
        <div style="font-size: 10px; color: #666;">{{ \App\Models\Setting::getPhone() }}</div>
        @endif
        @if(isset($settings['receipt_custom_header']) && !empty($settings['receipt_custom_header']))
        <div style="font-size: 9px; margin-top: 3px;">{!! nl2br(e($settings['receipt_custom_header'])) !!}</div>
        @endif
    </div>
    @endif

    {{-- Ticket Info --}}
    <div style="text-align: center; margin-bottom: 10px;">
        <div style="font-weight: bold; font-size: 14px;">{{ $ticket->ticket_number }}</div>
        <div style="font-size: 9px; color: #666;">{{ $ticket->visit_date->format('Y-m-d H:i') }}</div>
    </div>

    {{-- Content --}}
    <div style="border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 8px 0; margin: 8px 0;">
        @if(($settings['receipt_show_queue_number'] ?? true))
        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
            <span>{{ app()->getLocale() === 'ar' ? 'الطابور' : 'Queue' }}:</span>
            <span style="font-weight: bold;">{{ $ticket->queue_number }}</span>
        </div>
        @endif

        @if(($settings['receipt_show_patient'] ?? true))
        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
            <span>{{ app()->getLocale() === 'ar' ? 'المريض' : 'Patient' }}:</span>
            <span>{{ $ticket->patient->full_name }}</span>
        </div>
        @endif

        @if(($settings['receipt_show_department'] ?? true))
        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
            <span>{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }}:</span>
            <span>{{ app()->getLocale() === 'ar' ? $ticket->department->name_ar : $ticket->department->name }}</span>
        </div>
        @endif

        @if(($settings['receipt_show_service'] ?? true))
        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
            <span>{{ app()->getLocale() === 'ar' ? 'الخدمة' : 'Service' }}:</span>
            <span>{{ app()->getLocale() === 'ar' ? $ticket->service->name_ar : $ticket->service->name }}</span>
        </div>
        @endif

        @if(($settings['receipt_show_price'] ?? true))
        <div style="display: flex; justify-content: space-between; margin-bottom: 4px; font-weight: bold;">
            <span>{{ app()->getLocale() === 'ar' ? 'السعر' : 'Price' }}:</span>
            <span>{{ \App\Models\Setting::formatCurrency($ticket->amount_paid) }}</span>
        </div>
        @endif

        @if(($settings['receipt_show_cashier'] ?? true))
        <div style="display: flex; justify-content: space-between; margin-bottom: 4px; font-size: 9px; color: #666;">
            <span>{{ app()->getLocale() === 'ar' ? 'الأمين' : 'Cashier' }}:</span>
            <span>{{ $ticket->cashier->full_name }}</span>
        </div>
        @endif

        @if(($settings['receipt_show_visit_date'] ?? true))
        <div style="display: flex; justify-content: space-between; margin-bottom: 4px; font-size: 9px; color: #666;">
            <span>{{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}:</span>
            <span>{{ $ticket->created_at->format('Y-m-d H:i') }}</span>
        </div>
        @endif
    </div>

    {{-- QR Code Top --}}
    @if(($settings['qr_code_enabled'] ?? true) && ($settings['qr_code_position'] ?? 'bottom') === 'top')
    <div style="text-align: center; margin: 8px 0;">
        <div style="width: 80px; height: 80px; margin: 0 auto; display: inline-block; background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Crect fill=\'black\' x=\'10\' y=\'10\' width=\'30\' height=\'30\'/%3E%3Crect fill=\'black\' x=\'60\' y=\'10\' width=\'30\' height=\'30\'/%3E%3Crect fill=\'black\' x=\'10\' y=\'60\' width=\'30\' height=\'30\'/%3E%3Crect fill=\'black\' x=\'50\' y=\'50\' width=\'10\' height=\'10\'/%3E%3Crect fill=\'black\' x=\'70\' y=\'70\' width=\'20\' height=\'20\'/%3E%3C/svg%3E'); background-size: contain; background-repeat: no-repeat;"></div>
        <div style="font-size: 8px; color: #666; margin-top: 2px;">QR Code</div>
    </div>
    @endif

    {{-- Barcode --}}
    @if(($settings['barcode_enabled'] ?? false))
    <div style="text-align: center; margin: 8px 0;">
        <div style="background: repeating-linear-gradient(90deg, #000 0px, #000 2px, #fff 2px, #fff 4px); height: 30px; width: 80%; margin: 0 auto;"></div>
        <div style="font-size: 9px; margin-top: 2px;">{{ $ticket->ticket_number }}</div>
    </div>
    @endif

    {{-- QR Code Bottom --}}
    @if(($settings['qr_code_enabled'] ?? true) && ($settings['qr_code_position'] ?? 'bottom') === 'bottom')
    <div style="text-align: center; margin: 8px 0;">
        <div style="width: 80px; height: 80px; margin: 0 auto; display: inline-block; background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Crect fill=\'black\' x=\'10\' y=\'10\' width=\'30\' height=\'30\'/%3E%3Crect fill=\'black\' x=\'60\' y=\'10\' width=\'30\' height=\'30\'/%3E%3Crect fill=\'black\' x=\'10\' y=\'60\' width=\'30\' height=\'30\'/%3E%3Crect fill=\'black\' x=\'50\' y=\'50\' width=\'10\' height=\'10\'/%3E%3Crect fill=\'black\' x=\'70\' y=\'70\' width=\'20\' height=\'20\'/%3E%3C/svg%3E'); background-size: contain; background-repeat: no-repeat;"></div>
        <div style="font-size: 8px; color: #666; margin-top: 2px;">QR Code</div>
    </div>
    @endif

    {{-- Footer --}}
    @if(($settings['receipt_show_thank_you'] ?? true))
    <div style="text-align: center; margin-top: 10px; padding-top: 8px; border-top: 1px dashed #000; font-weight: bold;">
        <div>{{ app()->getLocale() === 'ar' ? ($settings['receipt_thank_you_ar'] ?? 'شكراً لزيارتكم') : ($settings['receipt_thank_you_en'] ?? 'Thank you for your visit') }}</div>
    </div>
    @endif
</div>
