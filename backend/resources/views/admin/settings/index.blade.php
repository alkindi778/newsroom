@extends('admin.layouts.app')

@section('title', 'إعدادات الموقع')
@section('page-title', 'إعدادات الموقع')

@section('content')
<div class="space-y-6">
    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-green-50 border-r-4 border-green-500 p-4 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="mr-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-r-4 border-red-500 p-4 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="mr-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900">إعدادات الموقع</h1>
            <p class="mt-1 text-sm text-gray-600">إدارة جميع إعدادات وبيانات الموقع</p>
        </div>
        
        <div class="flex gap-3">
            <button type="button" 
                    onclick="document.getElementById('settingsForm').reset()"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                إعادة تعيين
            </button>
            <button type="submit" 
                    form="settingsForm"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                </svg>
                حفظ التغييرات
            </button>
        </div>
    </div>

    <!-- Tabs & Content Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Tabs Navigation -->
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px overflow-x-auto" id="tabs-nav">
                <button type="button" 
                        data-tab="general"
                        class="tab-button flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 focus:outline-none active"
                        onclick="switchTab('general')">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>إعدادات عامة</span>
                    </div>
                </button>
                
                <button type="button" 
                        data-tab="seo"
                        class="tab-button flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 focus:outline-none"
                        onclick="switchTab('seo')">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span>محسنات SEO</span>
                    </div>
                </button>
                
                <button type="button" 
                        data-tab="organization"
                        class="tab-button flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 focus:outline-none"
                        onclick="switchTab('organization')">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span>معلومات المنظمة</span>
                    </div>
                </button>
                
                <button type="button" 
                        data-tab="contact"
                        class="tab-button flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 focus:outline-none"
                        onclick="switchTab('contact')">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span>معلومات الاتصال</span>
                    </div>
                </button>
                
                <button type="button" 
                        data-tab="social"
                        class="tab-button flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 focus:outline-none"
                        onclick="switchTab('social')">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                        </svg>
                        <span>مواقع التواصل</span>
                    </div>
                </button>
            </nav>
        </div>

        <!-- Form -->
        <form id="settingsForm" method="POST" action="{{ route('admin.settings.bulk-update') }}" enctype="multipart/form-data" class="p-6">
            @csrf
            
            <!-- General Settings Tab -->
            <div id="tab-general" class="tab-content space-y-6">
                @include('admin.settings.partials.general')
            </div>

            <!-- SEO Settings Tab -->
            <div id="tab-seo" class="tab-content space-y-6 hidden">
                @include('admin.settings.partials.seo')
            </div>

            <!-- Organization Settings Tab -->
            <div id="tab-organization" class="tab-content space-y-6 hidden">
                @include('admin.settings.partials.organization')
            </div>

            <!-- Contact Settings Tab -->
            <div id="tab-contact" class="tab-content space-y-6 hidden">
                @include('admin.settings.partials.contact')
            </div>

            <!-- Social Settings Tab -->
            <div id="tab-social" class="tab-content space-y-6 hidden">
                @include('admin.settings.partials.social')
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Tab Switching
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-blue-500', 'text-blue-600');
        button.classList.add('text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById('tab-' + tabName).classList.remove('hidden');
    
    // Add active class to selected button
    const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
    activeButton.classList.add('active', 'border-blue-500', 'text-blue-600');
    activeButton.classList.remove('text-gray-500');
    
    // Store active tab in localStorage
    localStorage.setItem('activeSettingsTab', tabName);
}

// Restore last active tab on page load
document.addEventListener('DOMContentLoaded', function() {
    const lastActiveTab = localStorage.getItem('activeSettingsTab') || 'general';
    switchTab(lastActiveTab);
});

// Auto-save indication
const form = document.getElementById('settingsForm');
let changesMade = false;

form.addEventListener('change', function() {
    changesMade = true;
});

// Warn before leaving if changes were made
window.addEventListener('beforeunload', function(e) {
    if (changesMade) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Reset changes flag on submit
form.addEventListener('submit', function() {
    changesMade = false;
});

// Logo Preview Functionality
function previewLogo(input) {
    const file = input.files[0];
    if (file) {
        // Check file size (10MB limit)
        if (file.size > 10 * 1024 * 1024) {
            alert('حجم الملف كبير جداً. الحد الأقصى 10MB');
            input.value = '';
            return;
        }
        
        // Check file type
        if (!file.type.startsWith('image/')) {
            alert('يرجى اختيار ملف صورة صحيح');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logo_preview_image').src = e.target.result;
            document.getElementById('logo_preview_container').style.display = '';
            document.getElementById('logo_empty_state').style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
}

function removeLogo() {
    const input = document.getElementById('site_logo');
    input.value = '';
    document.getElementById('logo_preview_container').style.display = 'none';
    document.getElementById('logo_empty_state').style.display = '';
    document.getElementById('logo_preview_image').src = '';
}

// Favicon Preview Functionality
function previewFavicon(input) {
    const file = input.files[0];
    if (file) {
        // Check file size (10MB limit)
        if (file.size > 10 * 1024 * 1024) {
            alert('حجم الملف كبير جداً. الحد الأقصى 10MB');
            input.value = '';
            return;
        }
        
        // Check file type
        if (!file.type.startsWith('image/')) {
            alert('يرجى اختيار ملف صورة صحيح');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('favicon_preview_image').src = e.target.result;
            document.getElementById('favicon_preview_container').style.display = '';
            document.getElementById('favicon_empty_state').style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
}

function removeFavicon() {
    const input = document.getElementById('site_favicon');
    input.value = '';
    document.getElementById('favicon_preview_container').style.display = 'none';
    document.getElementById('favicon_empty_state').style.display = '';
    document.getElementById('favicon_preview_image').src = '';
}

// Logo Width Control
function updateLogoWidth(value) {
    // Update display value
    document.getElementById('site_logo_width_display').value = value;
    document.getElementById('current_width_display').textContent = value;
    
    // Update preview
    const preview = document.getElementById('logo_size_preview');
    if (preview) {
        preview.style.width = value + 'px';
    }
}

// Footer Logo Preview Functionality
function previewFooterLogo(input) {
    const file = input.files[0];
    if (file) {
        // Check file size (10MB limit)
        if (file.size > 10 * 1024 * 1024) {
            alert('حجم الملف كبير جداً. الحد الأقصى 10MB');
            input.value = '';
            return;
        }
        
        // Check file type
        if (!file.type.startsWith('image/')) {
            alert('يرجى اختيار ملف صورة صحيح');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('footer_logo_preview_image').src = e.target.result;
            document.getElementById('footer_logo_preview_container').style.display = '';
            document.getElementById('footer_logo_empty_state').style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
}

function removeFooterLogo() {
    const input = document.getElementById('footer_logo');
    input.value = '';
    document.getElementById('footer_logo_preview_container').style.display = 'none';
    document.getElementById('footer_logo_empty_state').style.display = '';
    document.getElementById('footer_logo_preview_image').src = '';
}

// Footer Logo Width Control
function updateFooterLogoWidth(value) {
    // Update display value
    document.getElementById('footer_logo_width_display').value = value;
    document.getElementById('footer_current_width_display').textContent = value;
    
    // Update preview
    const preview = document.getElementById('footer_logo_size_preview');
    if (preview) {
        preview.style.width = value + 'px';
    }
}
</script>

<style>
.tab-button.active {
    @apply border-blue-500 text-blue-600;
}
.tab-button {
    @apply text-gray-500;
}
</style>
@endpush
@endsection
