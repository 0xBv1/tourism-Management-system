@props([
    'id' => 'dashboard-chart-' . Str::random(6),
    'type' => 'line',
    'data' => [],
    'labels' => [],
    'datasets' => [],
    'title' => null,
    'subtitle' => null,
    'height' => '350px',
    'colors' => [
        '#8b5cf6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444'
    ],
    'showLegend' => true,
    'showTooltip' => true,
    'gradient' => false,
    'animation' => true,
    'cardClass' => 'card',
    'cardHeaderClass' => 'card-header',
    'cardBodyClass' => 'card-body',
    'showCard' => true,
    'loading' => false,
    'error' => null,
    'emptyMessage' => 'No data available',
    'refreshable' => false,
    'exportable' => false,
    'statistics' => null, // Array of stats to show above chart
    'trend' => null, // Trend indicator (up, down, stable)
    'trendValue' => null, // Trend percentage
    'trendLabel' => null // Trend description
])

@php
    // Process datasets
    if (empty($datasets) && !empty($data)) {
        $datasets = [[
            'label' => $title ?? 'Data',
            'data' => $data,
            'backgroundColor' => $gradient ? 
                array_map(fn($color) => $color . '20', $colors) : 
                $colors,
            'borderColor' => $colors,
            'borderWidth' => 2,
            'fill' => $gradient,
            'tension' => 0.4
        ]];
    }
    
    // Ensure colors array has enough colors for all data points
    $dataCount = !empty($datasets) ? count($datasets[0]['data']) : count($data);
    while (count($colors) < $dataCount) {
        $colors = array_merge($colors, $colors);
    }
    $colors = array_slice($colors, 0, $dataCount);
    
    // Chart options
    $chartOptions = [
        'responsive' => true,
        'maintainAspectRatio' => false,
        'animation' => $animation ? [
            'duration' => 1000,
            'easing' => 'easeInOutQuart'
        ] : false,
        'plugins' => [
            'legend' => [
                'display' => $showLegend,
                'position' => 'top',
                'labels' => [
                    'usePointStyle' => true,
                    'padding' => 15,
                    'font' => ['size' => 11, 'weight' => '500']
                ]
            ],
            'tooltip' => [
                'enabled' => $showTooltip,
                'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                'titleColor' => '#ffffff',
                'bodyColor' => '#ffffff',
                'borderColor' => 'rgba(255, 255, 255, 0.1)',
                'borderWidth' => 1,
                'cornerRadius' => 8,
                'intersect' => false,
                'mode' => 'index'
            ]
        ],
        'scales' => [
            'x' => [
                'display' => true,
                'grid' => [
                    'display' => true,
                    'color' => 'rgba(0, 0, 0, 0.05)',
                    'drawBorder' => false
                ],
                'ticks' => [
                    'color' => '#6b7280',
                    'font' => ['size' => 10]
                ]
            ],
            'y' => [
                'display' => true,
                'grid' => [
                    'display' => true,
                    'color' => 'rgba(0, 0, 0, 0.05)',
                    'drawBorder' => false
                ],
                'ticks' => [
                    'color' => '#6b7280',
                    'font' => ['size' => 10]
                ]
            ]
        ]
    ];
@endphp

@if($showCard)
    <div class="{{ $cardClass }} chart-card">
        @if($title || $refreshable || $exportable)
            <div class="{{ $cardHeaderClass }}">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        @if($title)
                            <h5 class="card-title mb-0">{{ $title }}</h5>
                        @endif
                        @if($subtitle)
                            <small class="text-muted">{{ $subtitle }}</small>
                        @endif
                    </div>
                    <div class="chart-controls d-flex gap-2">
                        @if($refreshable)
                            <button type="button" class="btn btn-sm btn-outline-primary chart-refresh-btn">
                                <i class="fa fa-refresh"></i>
                            </button>
                        @endif
                        @if($exportable)
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                        type="button" data-bs-toggle="dropdown">
                                    <i class="fa fa-download"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item chart-export-btn" href="#" data-format="png">
                                        <i class="fa fa-image"></i> PNG
                                    </a></li>
                                    <li><a class="dropdown-item chart-export-btn" href="#" data-format="jpg">
                                        <i class="fa fa-image"></i> JPG
                                    </a></li>
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
        
        <div class="{{ $cardBodyClass }}">
            @if($statistics)
                <div class="statistics-grid">
                    @foreach($statistics as $stat)
                        <div class="stat-item text-center">
                            <h4 class="mb-1 text-{{ $stat['color'] ?? 'primary' }}">
                                {{ $stat['value'] }}
                            </h4>
                            <small class="text-muted">{{ $stat['label'] }}</small>
                            @if(isset($stat['trend']))
                                <div class="trend-indicator mt-1">
                                    <i class="fa fa-arrow-{{ $stat['trend'] }} text-{{ $stat['trend'] === 'up' ? 'success' : ($stat['trend'] === 'down' ? 'danger' : 'secondary') }}"></i>
                                    <small class="text-muted">{{ $stat['trendValue'] ?? '' }}</small>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
            
            @if($trend)
                <div class="trend-indicator mb-3 text-center">
                    <span class="badge bg-{{ $trend === 'up' ? 'success' : ($trend === 'down' ? 'danger' : 'secondary') }}">
                        <i class="fa fa-arrow-{{ $trend }}"></i>
                        {{ $trendValue }}
                    </span>
                    @if($trendLabel)
                        <small class="text-muted ms-2">{{ $trendLabel }}</small>
                    @endif
                </div>
            @endif
            
            <div class="chart-container" style="height: {{ $height }};">
                @if($loading)
                    <div class="d-flex justify-content-center align-items-center h-100">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                @elseif($error)
                    <div class="d-flex justify-content-center align-items-center h-100">
                        <div class="text-center text-danger">
                            <i class="fa fa-exclamation-triangle fa-2x mb-2"></i>
                            <p class="mb-0">{{ $error }}</p>
                        </div>
                    </div>
                @elseif(empty($datasets) || (count($datasets) === 1 && count($datasets[0]['data']) === 0))
                    <div class="d-flex justify-content-center align-items-center h-100">
                        <div class="text-center text-muted">
                            <i class="fa fa-chart-bar fa-2x mb-2"></i>
                            <p class="mb-0">{{ $emptyMessage }}</p>
                        </div>
                    </div>
                @else
                    <canvas id="{{ $id }}"></canvas>
                @endif
            </div>
        </div>
    </div>
@else
    <div class="chart-container" style="height: {{ $height }};">
        @if($loading)
            <div class="d-flex justify-content-center align-items-center h-100">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        @elseif($error)
            <div class="d-flex justify-content-center align-items-center h-100">
                <div class="text-center text-danger">
                    <i class="fa fa-exclamation-triangle fa-2x mb-2"></i>
                    <p class="mb-0">{{ $error }}</p>
                </div>
            </div>
        @elseif(empty($datasets) || (count($datasets) === 1 && count($datasets[0]['data']) === 0))
            <div class="d-flex justify-content-center align-items-center h-100">
                <div class="text-center text-muted">
                    <i class="fa fa-chart-bar fa-2x mb-2"></i>
                    <p class="mb-0">{{ $emptyMessage }}</p>
                </div>
            </div>
        @else
            <canvas id="{{ $id }}"></canvas>
        @endif
    </div>
@endif

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('{{ $id }}');
    if (!canvas) {
        console.error('Chart canvas not found for ID: {{ $id }}');
        return;
    }
    
    const ctx = canvas.getContext('2d');
    
    const chartData = {
        labels: @json($labels),
        datasets: @json($datasets)
    };
    
    const chartOptions = @json($chartOptions);
    
    try {
        const chart = new Chart(ctx, {
            type: '{{ $type }}',
            data: chartData,
            options: chartOptions
        });
        
        console.log('Chart {{ $id }} created successfully:', chart);
        
        // Export functionality
        @if($exportable)
        document.querySelectorAll('.chart-export-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const format = this.getAttribute('data-format');
                const url = chart.toBase64Image();
                
                const link = document.createElement('a');
                link.download = 'chart-{{ $id }}.' + format;
                link.href = url;
                link.click();
            });
        });
        @endif
        
        // Refresh functionality
        @if($refreshable)
        const refreshBtn = document.querySelector('.chart-refresh-btn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                const container = this.closest('.card, .chart-container');
                container.dispatchEvent(new CustomEvent('chart-refresh', {
                    detail: { chartId: '{{ $id }}', chart: chart }
                }));
            });
        }
        @endif
        
        // Store chart instance
        window.chartInstances = window.chartInstances || {};
        window.chartInstances['{{ $id }}'] = chart;
        
    } catch (error) {
        console.error('Error creating chart {{ $id }}:', error);
    }
});
</script>

@push('styles')
<style>
    .chart-container {
        position: relative;
    }
    
    .stat-item {
        padding: 0.5rem;
        border-radius: 8px;
        background: rgba(139, 92, 246, 0.05);
        transition: all 0.3s ease;
    }
    
    .stat-item:hover {
        background: rgba(139, 92, 246, 0.1);
        transform: translateY(-2px);
    }
    
    .trend-indicator {
        font-size: 0.875rem;
    }
    
    .chart-container canvas {
        border-radius: 4px;
    }
    
    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        .stat-item {
            background: rgba(139, 92, 246, 0.1);
        }
        
        .stat-item:hover {
            background: rgba(139, 92, 246, 0.2);
        }
    }
</style>
@endpush
