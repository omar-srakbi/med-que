@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'السجلات الطبية' : 'Medical Records')
@section('page-title', app()->getLocale() === 'ar' ? 'السجلات الطبية' : 'Medical Records')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-file-earmark-medical"></i> {{ app()->getLocale() === 'ar' ? 'قائمة السجلات الطبية' : 'Medical Records List' }}</span>
        <a href="{{ route('medical-records.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus"></i> {{ app()->getLocale() === 'ar' ? 'إضافة سجل طبي' : 'Add Medical Record' }}
        </a>
    </div>
    <div class="card-body">
        <!-- Filter Form -->
        <form action="{{ route('medical-records.index') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <select name="patient" class="form-select">
                        <option value="">{{ app()->getLocale() === 'ar' ? 'كل المرضى' : 'All Patients' }}</option>
                        @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" {{ request('patient') == $patient->id ? 'selected' : '' }}>
                            {{ $patient->full_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="department" class="form-select">
                        <option value="">{{ app()->getLocale() === 'ar' ? 'كل الأقسام' : 'All Departments' }}</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                            {{ app()->getLocale() === 'ar' ? $dept->name_ar : $dept->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                    </button>
                    @if(request('patient') || request('department'))
                    <a href="{{ route('medical-records.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x"></i> {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Clear' }}
                    </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Records Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'المريض' : 'Patient' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الطبيب' : 'Doctor' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'التشخيص' : 'Diagnosis' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'موعد المتابعة' : 'Follow-up' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'إجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                    <tr>
                        <td>{{ $record->created_at->format('Y-m-d') }}</td>
                        <td>{{ $record->patient->full_name }}</td>
                        <td>{{ app()->getLocale() === 'ar' ? $record->department->name_ar : $record->department->name }}</td>
                        <td>{{ $record->doctor->full_name }}</td>
                        <td>{{ Str::limit($record->diagnosis, 40) ?? '-' }}</td>
                        <td>{{ $record->follow_up_date?->format('Y-m-d') ?? '-' }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('medical-records.show', $record) }}" class="btn btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('medical-records.edit', $record) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('medical-records.destroy', $record) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد؟' : 'Are you sure?' }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            {{ app()->getLocale() === 'ar' ? 'لا توجد سجلات طبية' : 'No medical records found' }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        {{ $records->links() }}
    </div>
</div>
@endsection
