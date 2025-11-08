@extends('admin.layouts.app')

@section('title', 'لوحة التحكم')
@section('page-title', 'لوحة التحكم الرئيسية')

@section('content')
<div class="space-y-6">
    <!-- Quick Stats -->
    @include('admin.dashboard.stats')
    
    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
        @include('admin.dashboard.charts')
    </div>
    
    <!-- Recent Activity -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 lg:gap-6">
        @include('admin.dashboard.recent-articles')
        @include('admin.dashboard.recent-activity')
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Dashboard specific JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-refresh stats every 30 seconds
        setInterval(() => {
            // You can implement AJAX refresh here
            console.log('Refreshing dashboard stats...');
        }, 30000);
    });
</script>
@endpush
