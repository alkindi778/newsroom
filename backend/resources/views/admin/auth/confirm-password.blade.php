@extends('admin.layouts.auth')

@section('title', 'Confirm Password')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Confirm Password</h2>
            <p>This is a secure area of the application. Please confirm your password before continuing.</p>
        </div>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" 
                       type="password" 
                       name="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       required 
                       autofocus>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary btn-block">
                    Confirm
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
