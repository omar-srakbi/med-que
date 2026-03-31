@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'إضافة قسم' : 'Add Department')
@section('page-title', app()->getLocale() === 'ar' ? 'إضافة قسم' : 'Add Department')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="bi bi-building"></i> {{ app()->getLocale() === 'ar' ? 'بيانات القسم' : 'Department Information' }}
    </div>
    <div class="card-body">
        <form action="{{ route('departments.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم القسم (إنجليزي)' : 'Department Name (English)' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="name_ar" class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم القسم (عربي)' : 'Department Name (Arabic)' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name_ar') is-invalid @enderror"
                           id="name_ar" name="name_ar" value="{{ old('name_ar') }}" required>
                    @error('name_ar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="sequence_prefix" class="form-label">{{ app()->getLocale() === 'ar' ? 'بادئة التذكرة' : 'Ticket Prefix' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('sequence_prefix') is-invalid @enderror"
                           id="sequence_prefix" name="sequence_prefix" value="{{ old('sequence_prefix', 'TK') }}" maxlength="2" required>
                    <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'حرفان - مشترك بين الأقسام (مثال: TK, OP)' : '2 chars - shared among depts (e.g., TK, OP)' }}</small>
                    @error('sequence_prefix')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="queue_prefix" class="form-label">{{ app()->getLocale() === 'ar' ? 'بادئة الطابور' : 'Queue Prefix' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('queue_prefix') is-invalid @enderror"
                           id="queue_prefix" name="queue_prefix" value="{{ old('queue_prefix') }}" maxlength="2" required>
                    <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'حرفان - فريد لكل قسم (أحرف أو أرقام)' : '2 chars - unique per dept (letters or numbers)' }}</small>
                    @error('queue_prefix')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">{{ app()->getLocale() === 'ar' ? 'الوصف' : 'Description' }}</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> {{ app()->getLocale() === 'ar' ? 'حفظ' : 'Save' }}
                </button>
                <a href="{{ route('departments.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </a>
            </div>
        </form>
    </div>
</div>

<div id="queue-prefix-feedback" class="mt-2"></div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const queuePrefixInput = document.getElementById('queue_prefix');
    const feedbackDiv = document.getElementById('queue-prefix-feedback');
    const submitBtn = document.querySelector('button[type="submit"]');
    let isAvailable = false;
    let checkTimeout;

    // Auto-suggest next available on focus if input is empty
    queuePrefixInput.addEventListener('focus', function() {
        if (!this.value) {
            fetchNextAvailable();
        }
    });

    // Real-time validation on input
    queuePrefixInput.addEventListener('input', function() {
        clearTimeout(checkTimeout);
        const value = this.value.toUpperCase();
        this.value = value;

        if (value.length === 2) {
            checkTimeout = setTimeout(() => checkPrefix(value), 300);
        } else {
            feedbackDiv.innerHTML = '';
            isAvailable = false;
        }
    });

    // Check prefix availability
    function checkPrefix(prefix) {
        if (!/^[A-Z0-9]{2}$/.test(prefix)) {
            feedbackDiv.innerHTML = '';
            return;
        }

        fetch(`{{ route('departments.check-queue-prefix') }}?prefix=${encodeURIComponent(prefix)}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.available) {
                feedbackDiv.innerHTML = `
                    <div class="alert alert-success py-2 px-3 mb-0">
                        <i class="bi bi-check-circle-fill"></i> 
                        ${data.message}
                    </div>`;
                isAvailable = true;
                submitBtn.disabled = false;
            } else if (data.taken) {
                feedbackDiv.innerHTML = `
                    <div class="alert alert-warning py-2 px-3 mb-0">
                        <i class="bi bi-exclamation-triangle-fill"></i> 
                        ${data.message}. 
                        <strong>{{ app()->getLocale() === 'ar' ? 'البادئة المقترحة:' : 'Suggested:' }}</strong> 
                        <button type="button" class="btn btn-sm btn-outline-primary ms-1" onclick="useSuggested('${data.suggested}')">
                            ${data.suggested}
                        </button>
                    </div>`;
                isAvailable = false;
            } else {
                feedbackDiv.innerHTML = `
                    <div class="alert alert-danger py-2 px-3 mb-0">
                        <i class="bi bi-x-circle-fill"></i> 
                        ${data.message}
                    </div>`;
                isAvailable = false;
            }
        })
        .catch(error => {
            console.error('Error checking prefix:', error);
            feedbackDiv.innerHTML = '';
        });
    }

    // Fetch and auto-fill next available
    function fetchNextAvailable() {
        fetch(`{{ route('departments.check-queue-prefix') }}?prefix=`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.suggested) {
                queuePrefixInput.value = data.suggested;
                checkPrefix(data.suggested);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Use suggested prefix
    window.useSuggested = function(prefix) {
        queuePrefixInput.value = prefix;
        checkPrefix(prefix);
    };

    // Form submission validation
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!isAvailable && queuePrefixInput.value.length === 2) {
            e.preventDefault();
            checkPrefix(queuePrefixInput.value);
        }
    });
});
</script>
@endpush
