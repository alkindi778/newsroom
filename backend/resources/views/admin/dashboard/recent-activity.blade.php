<!-- Recent Activity -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 col-span-2">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">النشاط الحديث</h3>
        <i class="fas fa-history text-gray-400"></i>
    </div>
    
    @if(isset($recentActivities) && $recentActivities->count() > 0)
        <div class="space-y-3">
            @foreach($recentActivities as $activity)
            <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                <!-- Icon -->
                <div class="flex-shrink-0 mt-1">
                    @if($activity->description === 'تم إنشاء خبر جديد')
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-plus text-blue-600 text-xs"></i>
                        </div>
                    @elseif($activity->description === 'تم تحديث الخبر')
                        <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center">
                            <i class="fas fa-edit text-amber-600 text-xs"></i>
                        </div>
                    @elseif($activity->description === 'تم حذف الخبر')
                        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                            <i class="fas fa-trash text-red-600 text-xs"></i>
                        </div>
                    @elseif(str_contains($activity->description, 'فيديو'))
                        <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                            <i class="fas fa-video text-purple-600 text-xs"></i>
                        </div>
                    @elseif(str_contains($activity->description, 'مقال رأي'))
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                            <i class="fas fa-pen-nib text-green-600 text-xs"></i>
                        </div>
                    @else
                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-circle text-gray-600 text-xs"></i>
                        </div>
                    @endif
                </div>
                
                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-900 font-medium">{{ $activity->description }}</p>
                    @if($activity->causer)
                        <p class="text-xs text-gray-500 mt-1">
                            بواسطة: {{ $activity->causer->name }}
                        </p>
                    @endif
                    <p class="text-xs text-gray-400 mt-1">
                        <i class="far fa-clock ml-1"></i>
                        {{ $activity->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-history text-3xl mb-2 opacity-50"></i>
            <p class="text-sm">لا توجد أنشطة حديثة</p>
        </div>
    @endif
</div>
