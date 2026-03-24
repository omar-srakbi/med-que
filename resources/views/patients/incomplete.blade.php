@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'الملفات غير المكتملة' : 'Incomplete Profiles')
@section('page-title', app()->getLocale() === 'ar' ? 'الملفات غير المكتملة' : 'Incomplete Profiles')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-exclamation-triangle text-warning"></i> {{ app()->getLocale() === 'ar' ? 'الملفات الشخصية غير المكتملة' : 'Incomplete Patient Profiles' }}</span>
        <span class="badge bg-warning">{{ $patients->total() }} {{ app()->getLocale() === 'ar' ? 'مريض' : 'patients' }}</span>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            {{ app()->getLocale() === 'ar' ? 'هؤلاء المرضى تم إنشاؤهم بمعلومات قليلة أثناء ساعات الذروة. يرجى إكمال معلوماتهم.' : 'These patients were created with minimal information during rush hours. Please complete their information.' }}
        </div>

        @if($patients->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'الاسم' : 'Name' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الرقم الوطني' : 'National ID' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'تم الإنشاء بواسطة' : 'Created By' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'تاريخ الإنشاء' : 'Created Date' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patients as $patient)
                    <tr>
                        <td>
                            <strong>{{ $patient->full_name }}</strong>
                            <span class="badge bg-warning ms-2">{{ app()->getLocale() === 'ar' ? 'غير مكتمل' : 'Incomplete' }}</span>
                        </td>
                        <td><code>{{ $patient->national_id }}</code></td>
                        <td>{{ $patient->creator->full_name ?? 'System' }}</td>
                        <td>{{ $patient->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('patients.complete', $patient) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil"></i> {{ app()->getLocale() === 'ar' ? 'إكمال الملف' : 'Complete Profile' }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $patients->links() }}
        @else
        <div class="text-center py-5">
            <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
            <h5 class="mt-3">{{ app()->getLocale() === 'ar' ? 'جميع الملفات مكتملة!' : 'All profiles are complete!' }}</h5>
            <p class="text-muted">{{ app()->getLocale() === 'ar' ? 'لا توجد ملفات غير مكتملة' : 'No incomplete patient profiles' }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
