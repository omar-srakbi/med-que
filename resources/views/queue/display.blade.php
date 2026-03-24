<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="30">
    <title>{{ app()->getLocale() === 'ar' ? 'عرض الطابور' : 'Queue Display' }}</title>
    
    @if(app()->getLocale() === 'ar')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    @else
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    @endif
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .header {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-radius: 1rem;
        }
        
        .header h1 {
            color: #fff;
            font-size: 2.5rem;
            margin: 0;
        }
        
        .department-card {
            background: rgba(255,255,255,0.95);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .department-name {
            font-size: 1.5rem;
            font-weight: bold;
            color: #0d6efd;
            margin-bottom: 1rem;
        }
        
        .queue-info {
            display: flex;
            gap: 2rem;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .queue-item {
            text-align: center;
            padding: 1rem 2rem;
            background: #f8f9fa;
            border-radius: 0.5rem;
            min-width: 150px;
        }
        
        .queue-item.serving {
            background: linear-gradient(135deg, #198754 0%, #146c43 100%);
            color: #fff;
        }
        
        .queue-item.next {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: #000;
        }
        
        .queue-number {
            font-size: 3rem;
            font-weight: bold;
            display: block;
        }
        
        .queue-label {
            font-size: 0.875rem;
            opacity: 0.8;
        }
        
        .stats {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .stat-badge {
            background: #e9ecef;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
        }
        
        .time-display {
            position: fixed;
            top: 1rem;
            right: 1rem;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            color: #fff;
            font-size: 1.25rem;
            font-weight: bold;
        }
        
        @media (max-width: 768px) {
            .queue-info {
                flex-direction: column;
                gap: 1rem;
            }
            
            .queue-item {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="time-display" id="clock"></div>
    
    <div class="container py-4">
        <div class="header text-center">
            <h1><i class="bi bi-hospital"></i> {{ app()->getLocale() === 'ar' ? 'المركز الطبي' : 'Medical Center' }}</h1>
            <p class="text-white mb-0">{{ app()->getLocale() === 'ar' ? 'عرض الطابور' : 'Queue Display' }}</p>
        </div>
        
        <div class="row">
            @foreach($queueData as $data)
            <div class="col-md-6 col-lg-4">
                <div class="department-card">
                    <div class="department-name">
                        {{ app()->getLocale() === 'ar' ? $data['department']->name_ar : $data['department']->name }}
                    </div>
                    
                    <div class="queue-info">
                        @if($data['current_serving'])
                        <div class="queue-item serving">
                            <span class="queue-label">{{ app()->getLocale() === 'ar' ? 'جاري الخدمة' : 'Now Serving' }}</span>
                            <span class="queue-number">{{ $data['current_serving']->queue_number }}</span>
                        </div>
                        @endif
                        
                        @if($data['next_queue'])
                        <div class="queue-item next">
                            <span class="queue-label">{{ app()->getLocale() === 'ar' ? 'التالي' : 'Next' }}</span>
                            <span class="queue-number">{{ $data['next_queue']->queue_number }}</span>
                        </div>
                        @endif
                        
                        @if(!$data['current_serving'] && !$data['next_queue'])
                        <div class="text-muted text-center w-100">
                            {{ app()->getLocale() === 'ar' ? 'لا يوجد انتظار' : 'No Queue' }}
                        </div>
                        @endif
                    </div>
                    
                    <div class="stats">
                        <span class="stat-badge">
                            <i class="bi bi-people"></i> 
                            {{ app()->getLocale() === 'ar' ? 'اليوم' : 'Today' }}: {{ $data['total_today'] }}
                        </span>
                        <span class="stat-badge">
                            <i class="bi bi-check-circle"></i> 
                            {{ app()->getLocale() === 'ar' ? 'مكتمل' : 'Completed' }}: {{ $data['completed_count'] }}
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
    <script>
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('{{ app()->getLocale() === 'ar' ? 'ar' : 'en' }}', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('clock').textContent = timeString;
        }
        
        updateClock();
        setInterval(updateClock, 1000);
    </script>
</body>
</html>
