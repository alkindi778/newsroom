<!-- Success Message -->
<x-admin.success-message />

<!-- Error Message -->
<x-admin.error-message />

<!-- Info Message -->
<x-admin.info-message />

<!-- Warning Message -->
<x-admin.warning-message />

<!-- Validation Errors -->
@if($errors->any())
<div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
    <div class="flex items-center mb-2">
        <svg class="w-5 h-5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
        </svg>
        <h3 class="text-sm font-medium mr-3">يرجى تصحيح الأخطاء التالية:</h3>
    </div>
    <ul class="list-disc list-inside text-sm">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
