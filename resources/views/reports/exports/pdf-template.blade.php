<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Report' }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { color: #333; border-bottom: 2px solid #0d6efd; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .header { text-align: center; margin-bottom: 20px; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; }
        .summary { margin: 20px 0; padding: 10px; background: #e7f1ff; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title ?? 'Report' }}</h1>
        @if(isset($startDate) && isset($endDate))
        <p>{{ app()->getLocale() === 'ar' ? 'من' : 'From' }}: {{ $startDate }} - {{ app()->getLocale() === 'ar' ? 'إلى' : 'To' }}: {{ $endDate }}</p>
        @endif
        @if(isset($year))
        <p>{{ app()->getLocale() === 'ar' ? 'السنة' : 'Year' }}: {{ $year }}</p>
        @endif
    </div>

    @if(isset($totalRevenue))
    <div class="summary">
        <strong>{{ app()->getLocale() === 'ar' ? 'إجمالي الإيرادات' : 'Total Revenue' }}:</strong> {{ number_format($totalRevenue, 2) }} JD
    </div>
    @endif

    @if(isset($data) && count($data) > 0)
    <table>
        <thead>
            <tr>
                @foreach(array_keys($data[0]) as $key)
                <th>{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                @foreach($row as $value)
                <td>{{ $value }}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        <p>{{ app()->getLocale() === 'ar' ? 'تم الإنشاء' : 'Generated' }}: {{ $generated_at ?? now() }}</p>
        <p>{{ \App\Models\Setting::getClinicName() }}</p>
    </div>
</body>
</html>
