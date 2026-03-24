@extends('layouts.app')

@section('title', __('Login'))

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-4">
            <div class="card shadow-lg">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-hospital text-primary" style="font-size: 3rem;"></i>
                        <h3 class="mt-3">{{ app()->getLocale() === 'ar' ? 'نظام المركز الطبي' : 'Medical Center System' }}</h3>
                        <p class="text-muted">{{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Login' }}</p>
                    </div>
                    
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email' }}</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">{{ app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password' }}</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                {{ app()->getLocale() === 'ar' ? 'تذكرني' : 'Remember me' }}
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-box-arrow-in-right"></i>
                            {{ app()->getLocale() === 'ar' ? 'دخول' : 'Login' }}
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Language Switcher -->
            <div class="text-center mt-3">
                <form action="{{ route('language.switch') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-globe"></i>
                        {{ app()->getLocale() === 'ar' ? 'Switch to English' : 'التبديل للعربية' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
</style>
@endsection
