@props([
    'id' => 'chart-' . Str::random(8),
    'type' => 'line', // line, bar, doughnut, pie, radar, polarArea
    'data' => [],
    'labels' => [],
    'datasets' => [],
    'options' => [],
    'height' => '400px',
    'width' => '100%',
    'colors' => [
        '#8b5cf6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444',
        '#6366f1', '#ec4899', '#84cc16', '#f97316', '#64748b'
    ],
    'responsive' => true,
    'maintainAspectRatio' => false,
    'title' => null,
    'subtitle' => null,
    'showLegend' => true,
    'legendPosition' => 'top'
])

@php
    // Ensure we have proper data structure
    if (empty($datasets) && !empty($data)) {
        // If only data is provided, create a single dataset
        $datasets = [[
            'label' => $title ?? 'Data',
            'data' => $data,
            'backgroundColor' => $colors,
            'borderColor' => $colors,
            'borderWidth' => 2
        ]];
    }
    
    // Add colors to datasets if not provided
    foreach ($datasets as $index => $dataset) {
        if (!isset($dataset['backgroundColor'])) {
            $datasets[$index]['backgroundColor'] = $colors;
        }
        if (!isset($dataset['borderColor']) && in_array($type, ['line', 'bar'])) {
            $datasets[$index]['borderColor'] = $colors;
        }
    }
    
    // Default options based on chart type
    $defaultOptions = [
        'responsive' => $responsive,
        'maintainAspectRatio' => $maintainAspectRatio,
        'plugins' => [
            'legend' => [
                'display' => $showLegend,
                'position' => $legendPosition
            ],
            'tooltip' => [
                'enabled' => true
            ]
        ]
    ];
    
    // Merge with provided options
    $chartOptions = array_merge_recursive($defaultOptions, $options);
    
    // Add title if provided
    if ($title) {
        $chartOptions['plugins']['title'] = [
            'display' => true,
            'text' => $title,
            'font' => [
                'size' => 16,
                'weight' => 'bold'
            ]
        ];
    }
    
    // Add subtitle if provided
    if ($subtitle) {
        $chartOptions['plugins']['subtitle'] = [
            'display' => true,
            'text' => $subtitle,
            'font' => [
                'size' => 12
            ]
        ];
    }
@endphp

<div class="chart-container" style="position: relative; height: {{ $height }}; width: {{ $width }};">
    @if($title || $subtitle)
        <div class="chart-header mb-3">
            @if($title)
                <h5 class="chart-title mb-1">{{ $title }}</h5>
            @endif
            @if($subtitle)
                <p class="chart-subtitle text-muted mb-0">{{ $subtitle }}</p>
            @endif
        </div>
    @endif
    
    <canvas id="{{ $id }}" 
            style="max-height: {{ $height }}; max-width: {{ $width }};"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('{{ $id }}').getContext('2d');
    
    const chartData = {
        labels: @json($labels),
        datasets: @json($datasets)
    };
    
    const chartOptions = @json($chartOptions);
    
    new Chart(ctx, {
        type: '{{ $type }}',
        data: chartData,
        options: chartOptions
    });
});
</script>

@push('styles')
<style>
    .chart-container {
        background: #fff;
        border-radius: 8px;
        padding: 1rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .chart-title {
        color: #1e293b;
        font-weight: 600;
    }
    
    .chart-subtitle {
        font-size: 0.875rem;
    }
    
    .chart-container canvas {
        border-radius: 4px;
    }
    
    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        .chart-container {
            background: #1e293b;
            color: #f8fafc;
        }
        
        .chart-title {
            color: #f8fafc;
        }
        
        .chart-subtitle {
            color: #94a3b8;
        }
    }
</style>
@endpush
