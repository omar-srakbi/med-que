@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'المرضى' : 'Patients')
@section('page-title', app()->getLocale() === 'ar' ? 'المرضى' : 'Patients')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people"></i> {{ app()->getLocale() === 'ar' ? 'قائمة المرضى' : 'Patient List' }}</span>
        @if(auth()->user()->hasPermission('manage_patients') || auth()->user()->hasPermission('create_patients'))
        <a href="{{ route('patients.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus"></i> {{ app()->getLocale() === 'ar' ? 'إضافة مريض' : 'Add Patient' }}
        </a>
        @endif
    </div>
    <div class="card-body">
        <!-- Search Form -->
        <form action="{{ route('patients.index') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control" 
                           placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث بالاسم، الرقم الوطني، الهاتف...' : 'Search by name, national ID, phone...' }}"
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> {{ app()->getLocale() === 'ar' ? 'بحث' : 'Search' }}
                    </button>
                    @if(request('search'))
                    <a href="{{ route('patients.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x"></i> {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Clear' }}
                    </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Patients Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'الرقم الوطني' : 'National ID' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الاسم' : 'Name' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'تاريخ الميلاد' : 'Birth Date' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الهاتف' : 'Phone' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'أضيف بواسطة' : 'Added By' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'تاريخ الإضافة' : 'Added Date' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                    <tr>
                        <td>{{ $patient->national_id }}</td>
                        <td>{{ $patient->full_name }}</td>
                        <td>{{ $patient->birth_date ? $patient->birth_date->format('Y-m-d') : '-' }}</td>
                        <td>{{ $patient->phone }}</td>
                        <td>{{ $patient->creator->full_name }}</td>
                        <td>{{ $patient->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('patients.show', $patient) }}" class="btn btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(auth()->user()->hasPermission('manage_patients'))
                                <a href="{{ route('patients.edit', $patient) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                                @if(auth()->user()->hasPermission('delete_patients'))
                                <form action="{{ route('patients.destroy', $patient) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد من حذف هذا المريض؟' : 'Are you sure you want to delete this patient?' }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            {{ app()->getLocale() === 'ar' ? 'لا يوجد مرضى' : 'No patients found' }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        {{ $patients->links() }}
    </div>
</div>
@endsection
