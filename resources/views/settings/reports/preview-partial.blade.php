<div style="font-family: Arial, sans-serif; font-size: {{ $settings['report_font_size'] ?? 11 }}pt; line-height: 1.4;">

    {{-- Header --}}
    <div style="text-align: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #0d6efd;">
        @if(($settings['report_show_logo'] ?? true) && isset($settings['report_logo_path']))
        <img src="{{ asset('storage/' . $settings['report_logo_path']) }}" style="max-height: 50px; margin-bottom: 8px;">
        @endif
        @if(($settings['report_show_clinic_name'] ?? true))
        <h2 style="margin: 5px 0; font-size: 16pt; color: #0d6efd;">{{ \App\Models\Setting::getClinicName() }}</h2>
        @endif
        @if(($settings['report_show_address'] ?? true))
        <div style="font-size: 9pt; color: #666; margin: 3px 0;">{{ \App\Models\Setting::get('address', '') }}</div>
        @endif
        @if(($settings['report_show_phone'] ?? true))
        <div style="font-size: 9pt; color: #666; margin: 3px 0;">
            <i class="bi bi-telephone"></i> {{ \App\Models\Setting::get('phone', '0000-0000') }}
        </div>
        @endif
        @if(($settings['report_show_email'] ?? true))
        <div style="font-size: 9pt; color: #666; margin: 3px 0;">
            <i class="bi bi-envelope"></i> {{ \App\Models\Setting::get('email', 'info@clinic.com') }}
        </div>
        @endif
        @if(isset($settings['report_custom_header']) && !empty($settings['report_custom_header']))
        <div style="font-size: 9pt; margin-top: 5px; color: #666;">{!! nl2br(e($settings['report_custom_header'])) !!}</div>
        @endif
    </div>

    @php
        // Format number helper - uses global currency settings
        $formatNumber = function($value) use ($settings) {
            $decimalSeparator = $settings['report_decimal_separator'] ?? '.';
            $decimalPlaces = (int) ($settings['report_decimal_places'] ?? 2);
            $currencySymbol = \App\Models\Setting::getCurrencySymbol();
            return number_format($value, $decimalPlaces, $decimalSeparator, ',') . ' ' . $currencySymbol;
        };
    @endphp

    {{-- Report Title --}}
    <div style="text-align: center; margin-bottom: 15px; background: #f8f9fa; padding: 10px; border-radius: 5px;">
        <h3 style="margin: 0; font-size: 14pt; color: #212529;">{{ $sampleData['title'] }}</h3>
        <div style="font-size: 9pt; color: #666; margin-top: 5px;">
            {{ app()->getLocale() === 'ar' ? 'تاريخ' : 'Date' }}: {{ today()->format($settings['report_date_format'] ?? 'Y-m-d') }}
        </div>
    </div>

    {{-- Summary Section --}}
    @if(($settings['report_show_summary'] ?? true))
    <div style="margin-bottom: 15px;">
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            @if(isset($sampleData['totalPatients']))
            <div style="flex: 1; min-width: 120px; background: #e7f1ff; padding: 10px; border-radius: 5px; text-align: center;">
                <div style="font-size: 8pt; color: #666;">{{ app()->getLocale() === 'ar' ? 'إجمالي المرضى' : 'Total Patients' }}</div>
                <div style="font-size: 16pt; font-weight: bold; color: #0d6efd;">{{ $sampleData['totalPatients'] }}</div>
            </div>
            @endif
            @if(isset($sampleData['totalRevenue']))
            <div style="flex: 1; min-width: 120px; background: #d1e7dd; padding: 10px; border-radius: 5px; text-align: center;">
                <div style="font-size: 8pt; color: #666;">{{ app()->getLocale() === 'ar' ? 'إجمالي الإيرادات' : 'Total Revenue' }}</div>
                <div style="font-size: 16pt; font-weight: bold; color: #198754;">{{ $formatNumber($sampleData['totalRevenue']) }}</div>
            </div>
            @endif
            @if(isset($sampleData['total']))
            <div style="flex: 1; min-width: 120px; background: #fff3cd; padding: 10px; border-radius: 5px; text-align: center;">
                <div style="font-size: 8pt; color: #666;">{{ app()->getLocale() === 'ar' ? 'الإجمالي' : 'Total' }}</div>
                <div style="font-size: 16pt; font-weight: bold; color: #ffc107;">{{ $formatNumber($sampleData['total']) }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Data Table --}}
    @if(($settings['report_show_details'] ?? true))
    <div style="margin-bottom: 15px;">
        @php
            $tableClass = 'preview-table';
            if (($settings['report_striped_rows'] ?? true)) $tableClass .= ' striped';
            if (($settings['report_bordered'] ?? false)) $tableClass .= ' bordered';
            $rowSpacing = $settings['report_row_spacing'] ?? 'normal';
            $spacingStyle = $rowSpacing === 'compact' ? 'padding: 2px 4px;' : ($rowSpacing === 'comfortable' ? 'padding: 8px 10px;' : 'padding: 4px 6px;');
        @endphp

        <table class="{{ $tableClass }}" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="{{ ($settings['report_show_header_background'] ?? true) ? 'background-color: #f8f9fa;' : '' }}">
                    @if($sampleData['title'] === 'التقرير اليومي' || $sampleData['title'] === 'Daily Report')
                        <th style="{{ $spacingStyle }}">{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }}</th>
                        <th style="{{ $spacingStyle }}">{{ app()->getLocale() === 'ar' ? 'المرضى' : 'Patients' }}</th>
                        <th style="{{ $spacingStyle }}">{{ app()->getLocale() === 'ar' ? 'الإيرادات' : 'Revenue' }}</th>
                    @elseif($sampleData['title'] === 'تقرير الإيرادات' || $sampleData['title'] === 'Revenue Report')
                        <th style="{{ $spacingStyle }}">{{ app()->getLocale() === 'ar' ? 'طريقة الدفع' : 'Payment Method' }}</th>
                        <th style="{{ $spacingStyle }}">{{ app()->getLocale() === 'ar' ? 'العدد' : 'Count' }}</th>
                        <th style="{{ $spacingStyle }}">{{ app()->getLocale() === 'ar' ? 'المبلغ' : 'Amount' }}</th>
                    @elseif($sampleData['title'] === 'تقرير المرضى' || $sampleData['title'] === 'Patients Report')
                        <th style="{{ $spacingStyle }}">{{ app()->getLocale() === 'ar' ? 'الاسم' : 'Name' }}</th>
                        <th style="{{ $spacingStyle }}">{{ app()->getLocale() === 'ar' ? 'الرقم الوطني' : 'National ID' }}</th>
                        <th style="{{ $spacingStyle }}">{{ app()->getLocale() === 'ar' ? 'الهاتف' : 'Phone' }}</th>
                        <th style="{{ $spacingStyle }}">{{ app()->getLocale() === 'ar' ? 'الزيارات' : 'Visits' }}</th>
                    @elseif($sampleData['title'] === 'تقرير الخدمات' || $sampleData['title'] === 'Services Report')
                        <th style="{{ $spacingStyle }}">{{ app()->getLocale() === 'ar' ? 'الخدمة' : 'Service' }}</th>
                        <th style="{{ $spacingStyle }}">{{ app()->getLocale() === 'ar' ? 'السعر' : 'Price' }}</th>
                        <th style="{{ $spacingStyle }}">{{ app()->getLocale() === 'ar' ? 'العدد' : 'Count' }}</th>
                        <th style="{{ $spacingStyle }}">{{ app()->getLocale() === 'ar' ? 'الإجمالي' : 'Total' }}</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($sampleData['rows'] as $row)
                <tr>
                    @if(isset($row['department']))
                        <td style="{{ $spacingStyle }}">{{ $row['department'] }}</td>
                        <td style="{{ $spacingStyle }}">{{ $row['patients'] }}</td>
                        <td style="{{ $spacingStyle }}">{{ $formatNumber($row['revenue']) }}</td>
                    @elseif(isset($row['label']))
                        <td style="{{ $spacingStyle }}">{{ $row['label'] }}</td>
                        <td style="{{ $spacingStyle }}">{{ $row['count'] }}</td>
                        <td style="{{ $spacingStyle }}">{{ $formatNumber($row['value']) }}</td>
                    @elseif(isset($row['name']) && isset($row['national_id']))
                        <td style="{{ $spacingStyle }}">{{ $row['name'] }}</td>
                        <td style="{{ $spacingStyle }}">{{ $row['national_id'] }}</td>
                        <td style="{{ $spacingStyle }}">{{ $row['phone'] }}</td>
                        <td style="{{ $spacingStyle }}">{{ $row['visits'] }}</td>
                    @elseif(isset($row['name']) && isset($row['price']))
                        <td style="{{ $spacingStyle }}">{{ $row['name'] }}</td>
                        <td style="{{ $spacingStyle }}">{{ $formatNumber($row['price']) }}</td>
                        <td style="{{ $spacingStyle }}">{{ $row['count'] }}</td>
                        <td style="{{ $spacingStyle }}">{{ $formatNumber($row['total']) }}</td>
                    @endif
                </tr>
                @endforeach
            </tbody>
            @if(($settings['report_show_totals'] ?? true))
            <tfoot>
                <tr style="background-color: #e9ecef; font-weight: bold;">
                    @if($sampleData['title'] === 'التقرير اليومي' || $sampleData['title'] === 'Daily Report')
                        <td style="{{ $spacingStyle }}">{{ app()->getLocale() === 'ar' ? 'الإجمالي' : 'Total' }}</td>
                        <td style="{{ $spacingStyle }}">{{ $sampleData['totalPatients'] }}</td>
                        <td style="{{ $spacingStyle }}">{{ $formatNumber($sampleData['totalRevenue']) }}</td>
                    @elseif(isset($sampleData['totalCount']))
                        <td style="{{ $spacingStyle }}">{{ app()->getLocale() === 'ar' ? 'الإجمالي' : 'Total' }}</td>
                        <td style="{{ $spacingStyle }}">{{ $sampleData['totalCount'] }}</td>
                        <td style="{{ $spacingStyle }}">{{ $formatNumber($sampleData['total']) }}</td>
                    @else
                        <td colspan="3" style="{{ $spacingStyle }}">{{ app()->getLocale() === 'ar' ? 'الإجمالي' : 'Total' }}: {{ $formatNumber($sampleData['total']) }}</td>
                    @endif
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
    @endif

    {{-- Charts Placeholder --}}
    @if(($settings['report_show_charts'] ?? true))
    <div style="margin-bottom: 15px; padding: 15px; background: #f8f9fa; border-radius: 5px; text-align: center;">
        <div style="font-size: 9pt; color: #666; margin-bottom: 10px;">
            <i class="bi bi-bar-chart"></i> {{ app()->getLocale() === 'ar' ? 'رسم بياني' : 'Chart' }}
        </div>
        <div style="display: flex; align-items: flex-end; justify-content: center; gap: 20px; height: 100px; padding: 10px;">
            @foreach($sampleData['rows'] as $index => $row)
                @php
                    $height = isset($row['revenue']) ? ($row['revenue'] / 700 * 100) : (isset($row['total']) ? ($row['total'] / 1000 * 100) : 50);
                    $colors = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0'];
                    $color = $colors[$index % count($colors)];
                @endphp
                <div style="width: 40px; height: {{ min($height, 100) }}px; background: {{ $color }}; border-radius: 3px 3px 0 0; transition: all 0.3s;"></div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Footer --}}
    @if(($settings['report_show_generated_at'] ?? true) || ($settings['report_show_generated_by'] ?? true) || ($settings['report_show_page_numbers'] ?? true) || isset($settings['report_footer_note']))
    <div style="margin-top: 20px; padding-top: 10px; border-top: 1px solid #dee2e6; font-size: 8pt; color: #666;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            @if(($settings['report_show_generated_at'] ?? true))
            <div>
                <i class="bi bi-clock"></i> {{ app()->getLocale() === 'ar' ? 'تم الإنشاء' : 'Generated' }}: {{ now()->format('Y-m-d H:i') }}
            </div>
            @endif
            @if(($settings['report_show_generated_by'] ?? true))
            <div>
                <i class="bi bi-person"></i> {{ auth()->user()->full_name }}
            </div>
            @endif
            @if(($settings['report_show_page_numbers'] ?? true))
            <div>
                {{ app()->getLocale() === 'ar' ? 'صفحة' : 'Page' }} 1
            </div>
            @endif
        </div>
        @if(isset($settings['report_footer_note']) && !empty($settings['report_footer_note']))
        <div style="margin-top: 8px; text-align: center; padding: 8px; background: #fff3cd; border-radius: 3px;">
            {!! nl2br(e($settings['report_footer_note'])) !!}
        </div>
        @endif
    </div>
    @endif

</div>
