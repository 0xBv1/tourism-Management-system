@props([
    'id' => 'advanced-chart-' . Str::random(8),
    'type' => 'line',
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
    'legendPosition' => 'top',
    'showTooltip' => true,
    'showGrid' => true,
    'animation' => true,
    'gradient' => false,
    'borderRadius' => 4,
    'borderWidth' => 2,
    'fill' => false,
    'tension' => 0.4,
    'pointRadius' => 4,
    'pointHoverRadius' => 6,
    'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
    'borderColor' => '#8b5cf6',
    'pointBackgroundColor' => '#8b5cf6',
    'pointBorderColor' => '#ffffff',
    'pointBorderWidth' => 2,
    'showDataLabels' => false,
    'dataLabelColor' => '#374151',
    'dataLabelFontSize' => 12,
    'customTooltip' => null,
    'onClick' => null,
    'onHover' => null,
    'loading' => false,
    'error' => null,
    'emptyMessage' => 'No data available',
    'refreshable' => false,
    'exportable' => false
])

@php
    // Generate unique ID if not provided
    $chartId = $id;
    
    // Process data based on chart type
    $processedData = [];
    $processedLabels = $labels;
    
    if (empty($datasets) && !empty($data)) {
        // Single dataset from simple data array
        $processedData = [
            [
                'label' => $title ?? 'Dataset',
                'data' => $data,
                'backgroundColor' => $gradient ? 
                    array_map(fn($color) => $color . '20', $colors) : 
                    $colors,
                'borderColor' => $colors,
                'borderWidth' => $borderWidth,
                'borderRadius' => $borderRadius,
                'fill' => $fill,
                'tension' => $tension,
                'pointRadius' => $pointRadius,
                'pointHoverRadius' => $pointHoverRadius,
                'pointBackgroundColor' => $pointBackgroundColor,
                'pointBorderColor' => $pointBorderColor,
                'pointBorderWidth' => $pointBorderWidth
            ]
        ];
    } else {
        // Multiple datasets
        $processedData = $datasets;
        foreach ($processedData as $index => $dataset) {
            if (!isset($dataset['backgroundColor'])) {
                $processedData[$index]['backgroundColor'] = $gradient ? 
                    array_map(fn($color) => $color . '20', $colors) : 
                    $colors;
            }
            if (!isset($dataset['borderColor'])) {
                $processedData[$index]['borderColor'] = $colors;
            }
            if (!isset($dataset['borderWidth'])) {
                $processedData[$index]['borderWidth'] = $borderWidth;
            }
        }
    }
    
    // Build chart options
    $chartOptions = [
        'responsive' => $responsive,
        'maintainAspectRatio' => $maintainAspectRatio,
        'animation' => $animation ? [
            'duration' => 1000,
            'easing' => 'easeInOutQuart'
        ] : false,
        'plugins' => [
            'legend' => [
                'display' => $showLegend,
                'position' => $legendPosition,
                'labels' => [
                    'usePointStyle' => true,
                    'padding' => 20,
                    'font' => [
                        'size' => 12,
                        'weight' => '500'
                    ]
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
                'displayColors' => true,
                'intersect' => false,
                'mode' => 'index'
            ]
        ],
        'scales' => $showGrid ? [
            'x' => [
                'display' => true,
                'grid' => [
                    'display' => true,
                    'color' => 'rgba(0, 0, 0, 0.1)',
                    'drawBorder' => false
                ],
                'ticks' => [
                    'color' => '#6b7280',
                    'font' => [
                        'size' => 11
                    ]
                ]
            ],
            'y' => [
                'display' => true,
                'grid' => [
                    'display' => true,
                    'color' => 'rgba(0, 0, 0, 0.1)',
                    'drawBorder' => false
                ],
                'ticks' => [
                    'color' => '#6b7280',
                    'font' => [
                        'size' => 11
                    ]
                ]
            ]
        ] : [],
        'onClick' => $onClick,
        'onHover' => $onHover
    ];
    
    // Add title
    if ($title) {
        $chartOptions['plugins']['title'] = [
            'display' => true,
            'text' => $title,
            'font' => [
                'size' => 16,
                'weight' => 'bold',
                'family' => 'Inter, sans-serif'
            ],
            'color' => '#1e293b',
            'padding' => 20
        ];
    }
    
    // Add subtitle
    if ($subtitle) {
        $chartOptions['plugins']['subtitle'] = [
            'display' => true,
            'text' => $subtitle,
            'font' => [
                'size' => 12,
                'family' => 'Inter, sans-serif'
            ],
            'color' => '#6b7280',
            'padding' => [
                'top' => 0,
                'bottom' => 20
            ]
        ];
    }
    
    // Merge with custom options
    $chartOptions = array_merge_recursive($chartOptions, $options);
@endphp

<div class="advanced-chart-container" 
     style="position: relative; height: {{ $height }}; width: {{ $width }};"
     data-chart-id="{{ $chartId }}">
     
    @if($loading)
        <div class="chart-loading d-flex justify-content-center align-items-center" 
             style="height: {{ $height }};">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    @elseif($error)
        <div class="chart-error d-flex justify-content-center align-items-center" 
             style="height: {{ $height }};">
            <div class="text-center text-danger">
                <i class="fa fa-exclamation-triangle fa-2x mb-2"></i>
                <p class="mb-0">{{ $error }}</p>
            </div>
        </div>
    @elseif(empty($processedData) || (count($processedData) === 1 && empty($processedData[0]['data'])))
        <div class="chart-empty d-flex justify-content-center align-items-center" 
             style="height: {{ $height }};">
            <div class="text-center text-muted">
                <i class="fa fa-chart-bar fa-2x mb-2"></i>
                <p class="mb-0">{{ $emptyMessage }}</p>
            </div>
        </div>
    @else
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
        
        @if($refreshable || $exportable)
            <div class="chart-controls mb-3 d-flex justify-content-end gap-2">
                @if($refreshable)
                    <button type="button" class="btn btn-sm btn-outline-primary chart-refresh-btn">
                        <i class="fa fa-refresh"></i> Refresh
                    </button>
                @endif
                @if($exportable)
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                type="button" data-bs-toggle="dropdown">
                            <i class="fa fa-download"></i> Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item chart-export-btn" href="#" data-format="png">
                                <i class="fa fa-image"></i> PNG
                            </a></li>
                            <li><a class="dropdown-item chart-export-btn" href="#" data-format="jpg">
                                <i class="fa fa-image"></i> JPG
                            </a></li>
                            <li><a class="dropdown-item chart-export-btn" href="#" data-format="pdf">
                                <i class="fa fa-file-pdf"></i> PDF
                            </a></li>
                        </ul>
                    </div>
                @endif
            </div>
        @endif
        
        <canvas id="{{ $chartId }}" 
                style="max-height: {{ $height }}; max-width: {{ $width }};"></canvas>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@if($exportable)
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
@endif
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartContainer = document.querySelector('[data-chart-id="{{ $chartId }}"]');
    const canvas = document.getElementById('{{ $chartId }}');
    
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    
    const chartData = {
        labels: @json($processedLabels),
        datasets: @json($processedData)
    };
    
    const chartOptions = @json($chartOptions);
    
    // Create chart
    const chart = new Chart(ctx, {
        type: '{{ $type }}',
        data: chartData,
        options: chartOptions
    });
    
    // Export functionality
    @if($exportable)
    document.querySelectorAll('.chart-export-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const format = this.getAttribute('data-format');
            const url = chart.toBase64Image();
            
            if (format === 'png' || format === 'jpg') {
                const link = document.createElement('a');
                link.download = 'chart-{{ $chartId }}.' + format;
                link.href = url;
                link.click();
            } else if (format === 'pdf') {
                // For PDF export, you might want to use jsPDF
                console.log('PDF export not implemented yet');
            }
        });
    });
    @endif
    
    // Refresh functionality
    @if($refreshable)
    const refreshBtn = chartContainer.querySelector('.chart-refresh-btn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            // Trigger a custom event that can be handled by parent components
            chartContainer.dispatchEvent(new CustomEvent('chart-refresh', {
                detail: { chartId: '{{ $chartId }}', chart: chart }
            }));
        });
    }
    @endif
    
    // Store chart instance for external access
    window.chartInstances = window.chartInstances || {};
    window.chartInstances['{{ $chartId }}'] = chart;
});
</script>

@push('styles')
<style>
    .advanced-chart-container {
        background: #fff;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    
    .advanced-chart-container:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
    
    .chart-title {
        color: #1e293b;
        font-weight: 600;
        font-family: 'Inter', sans-serif;
    }
    
    .chart-subtitle {
        font-size: 0.875rem;
        font-family: 'Inter', sans-serif;
    }
    
    .chart-controls .btn {
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .chart-controls .btn:hover {
        transform: translateY(-1px);
    }
    
    .chart-loading,
    .chart-error,
    .chart-empty {
        background: #f8fafc;
        border-radius: 8px;
        border: 2px dashed #e2e8f0;
    }
    
    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        .advanced-chart-container {
            background: #1e293b;
            border-color: rgba(255,255,255,0.1);
        }
        
        .chart-title {
            color: #f8fafc;
        }
        
        .chart-subtitle {
            color: #94a3b8;
        }
        
        .chart-loading,
        .chart-error,
        .chart-empty {
            background: #0f172a;
            border-color: rgba(255,255,255,0.1);
        }
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .advanced-chart-container {
            padding: 1rem;
        }
        
        .chart-controls {
            flex-direction: column;
            align-items: stretch;
        }
        
        .chart-controls .btn {
            margin-bottom: 0.5rem;
        }
    }
</style>
@endpush
