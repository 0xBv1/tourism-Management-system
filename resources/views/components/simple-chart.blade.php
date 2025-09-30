@props([
    'id' => 'simple-chart-' . Str::random(6),
    'type' => 'line',
    'data' => [],
    'labels' => [],
    'title' => null,
    'height' => '300px',
    'color' => '#8b5cf6',
    'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
    'borderWidth' => 2,
    'fill' => false,
    'showLegend' => false,
    'showGrid' => true,
    'responsive' => true
])

@php
    // Simple data processing
    $chartData = [
        'labels' => $labels,
        'datasets' => [[
            'label' => $title ?? 'Data',
            'data' => $data,
            'backgroundColor' => $backgroundColor,
            'borderColor' => $color,
            'borderWidth' => $borderWidth,
            'fill' => $fill,
            'tension' => 0.4
        ]]
    ];
    
    $chartOptions = [
        'responsive' => $responsive,
        'maintainAspectRatio' => false,
        'plugins' => [
            'legend' => [
                'display' => $showLegend
            ]
        ],
        'scales' => $showGrid ? [
            'x' => [
                'grid' => ['display' => true, 'color' => 'rgba(0,0,0,0.1)']
            ],
            'y' => [
                'grid' => ['display' => true, 'color' => 'rgba(0,0,0,0.1)']
            ]
        ] : []
    ];
@endphp

<div class="simple-chart" style="height: {{ $height }};">
    <canvas id="{{ $id }}"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('{{ $id }}').getContext('2d');
    
    new Chart(ctx, {
        type: '{{ $type }}',
        data: @json($chartData),
        options: @json($chartOptions)
    });
});
</script>

@push('styles')
<style>
    .simple-chart {
        background: #fff;
        border-radius: 8px;
        padding: 1rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .simple-chart canvas {
        border-radius: 4px;
    }
</style>
@endpush
