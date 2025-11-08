@extends('admin.layouts.auth')

@section('title', 'Forgot Password')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Forgot Password</h2>
            <p>Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>
                <input id="email" 
                       type="email" 
                       name="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary btn-block">
                    Email Password Reset Link
                </button>
            </div>
        </form>

        <div class="auth-footer">
            <a href="{{ route('admin.login') }}">Back to Login</a>
        </div>
    </div>
</div>
@endsection
