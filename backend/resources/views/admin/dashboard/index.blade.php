@extends('admin.layouts.app')

@section('title', 'لوحة التحكم')
@section('page-title', 'لوحة التحكم الرئيسية')

@section('content')
<div class="space-y-6">
  
    <!-- Quick Actions -->
    @include('admin.dashboard.quick-actions')

    <!-- Quick Stats -->
    @include('admin.dashboard.stats')
 
    <!-- Activity Chart -->
    @include('admin.dashboard.activity-chart')
    
    <!-- Charts & Breaking News Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
        <div>
            @include('admin.dashboard.charts')
        </div>
        <div>
            @include('admin.dashboard.breaking-news')
        </div>
    </div>
    
    <!-- Recent Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4 lg:gap-6">
        <!-- Recent Articles -->
        <div>
            @include('admin.dashboard.recent-articles')
        </div>
        
        <!-- Top Articles -->
        <div>
            @include('admin.dashboard.top-articles')
        </div>
        
        <!-- Recent Activity -->
        <div>
            @include('admin.dashboard.recent-activity')
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Dashboard specific JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-refresh stats every 60 seconds
        setInterval(() => {
            // You can implement AJAX refresh here
            console.log('Dashboard refreshed at', new Date().toLocaleString('ar-EG'));
        }, 60000);
    });
</script>
@endpush
