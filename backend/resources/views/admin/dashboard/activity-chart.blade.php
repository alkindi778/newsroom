<!-- Activity Chart -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-900">نشاط آخر 7 أيام</h3>
        <div class="flex items-center gap-4 text-sm">
            <span class="inline-flex items-center">
                <span class="w-3 h-3 rounded-full bg-blue-500 ml-2"></span>
                أخبار
            </span>
            <span class="inline-flex items-center">
                <span class="w-3 h-3 rounded-full bg-red-500 ml-2"></span>
                فيديوهات
            </span>
            <span class="inline-flex items-center">
                <span class="w-3 h-3 rounded-full bg-amber-500 ml-2"></span>
                مقالات رأي
            </span>
        </div>
    </div>
    
    <div class="relative h-64">
        <canvas id="activityChart"></canvas>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/chart.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('activityChart');
    if (!ctx) return;
    
    const labels = {!! json_encode(collect($last7Days)->pluck('label')) !!};
    const articlesData = {!! json_encode(collect($last7Days)->pluck('articles')) !!};
    const videosData = {!! json_encode(collect($last7Days)->pluck('videos')) !!};
    const opinionsData = {!! json_encode(collect($last7Days)->pluck('opinions')) !!};
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'أخبار',
                    data: articlesData,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'فيديوهات',
                    data: videosData,
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'مقالات رأي',
                    data: opinionsData,
                    borderColor: 'rgb(245, 158, 11)',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    rtl: true,
                    textDirection: 'rtl'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });
});
</script>
@endpush
