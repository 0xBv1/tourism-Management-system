{{-- 
    Chart Components Usage Examples
    ==============================
    
    This file demonstrates how to use the different chart components
    available in the application.
--}}

{{-- 1. SIMPLE CHART COMPONENT --}}
{{-- Basic usage with minimal configuration --}}
<x-simple-chart 
    :data="[10, 20, 30, 40, 50]"
    :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May']"
    title="Monthly Sales"
    type="line"
    height="300px" />

{{-- 2. BASIC CHART COMPONENT --}}
{{-- More configuration options --}}
<x-chart 
    id="sales-chart"
    type="bar"
    :data="[100, 200, 150, 300, 250]"
    :labels="['Q1', 'Q2', 'Q3', 'Q4', 'Q5']"
    title="Quarterly Revenue"
    subtitle="Revenue breakdown by quarter"
    height="400px"
    :colors="['#8b5cf6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444']" />

{{-- 3. ADVANCED CHART COMPONENT --}}
{{-- Full-featured chart with all options --}}
<x-advanced-chart 
    id="advanced-sales-chart"
    type="line"
    :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']"
    :datasets="[
        [
            'label' => 'Sales',
            'data' => [100, 200, 150, 300, 250, 400],
            'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
            'borderColor' => '#8b5cf6',
            'borderWidth' => 3,
            'fill' => true,
            'tension' => 0.4
        ],
        [
            'label' => 'Profit',
            'data' => [50, 100, 75, 150, 125, 200],
            'backgroundColor' => 'rgba(6, 182, 212, 0.1)',
            'borderColor' => '#06b6d4',
            'borderWidth' => 3,
            'fill' => true,
            'tension' => 0.4
        ]
    ]"
    title="Sales vs Profit Analysis"
    subtitle="Monthly comparison of sales and profit margins"
    height="450px"
    :gradient="true"
    :animation="true"
    :showLegend="true"
    :showTooltip="true"
    :exportable="true"
    :refreshable="true" />

{{-- 4. DASHBOARD CHART COMPONENT --}}
{{-- Perfect for dashboard widgets --}}
<x-dashboard-chart 
    id="dashboard-revenue-chart"
    type="doughnut"
    :labels="['Paid', 'Pending', 'Not Paid']"
    :data="[65, 25, 10]"
    title="Payment Status Distribution"
    subtitle="Current payment status breakdown"
    height="350px"
    :colors="['#10b981', '#f59e0b', '#ef4444']"
    :statistics="[
        [
            'label' => 'Total Revenue',
            'value' => '$125,000',
            'color' => 'success',
            'trend' => 'up',
            'trendValue' => '+12%'
        ],
        [
            'label' => 'Pending',
            'value' => '$25,000',
            'color' => 'warning',
            'trend' => 'down',
            'trendValue' => '-5%'
        ]
    ]"
    :exportable="true" />

{{-- 5. MULTIPLE DATASETS EXAMPLE --}}
<x-chart 
    id="multi-dataset-chart"
    type="bar"
    :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May']"
    :datasets="[
        [
            'label' => 'Revenue',
            'data' => [1000, 1200, 1100, 1400, 1300],
            'backgroundColor' => 'rgba(139, 92, 246, 0.8)',
            'borderColor' => '#8b5cf6',
            'borderWidth' => 1
        ],
        [
            'label' => 'Expenses',
            'data' => [600, 700, 650, 800, 750],
            'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
            'borderColor' => '#ef4444',
            'borderWidth' => 1
        ],
        [
            'label' => 'Profit',
            'data' => [400, 500, 450, 600, 550],
            'backgroundColor' => 'rgba(16, 185, 129, 0.8)',
            'borderColor' => '#10b981',
            'borderWidth' => 1
        ]
    ]"
    title="Financial Overview"
    subtitle="Revenue, Expenses, and Profit Analysis"
    height="400px" />

{{-- 6. PIE CHART EXAMPLE --}}
<x-simple-chart 
    id="pie-chart-example"
    type="pie"
    :data="[30, 25, 20, 15, 10]"
    :labels="['Desktop', 'Mobile', 'Tablet', 'Other', 'Unknown']"
    title="Device Usage Distribution"
    height="300px"
    :colors="['#8b5cf6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444']" />

{{-- 7. RADAR CHART EXAMPLE --}}
<x-chart 
    id="radar-chart-example"
    type="radar"
    :labels="['Sales', 'Marketing', 'Development', 'Support', 'Management']"
    :datasets="[
        [
            'label' => 'Current Performance',
            'data' => [80, 70, 90, 60, 85],
            'backgroundColor' => 'rgba(139, 92, 246, 0.2)',
            'borderColor' => '#8b5cf6',
            'borderWidth' => 2
        ],
        [
            'label' => 'Target Performance',
            'data' => [90, 80, 95, 70, 90],
            'backgroundColor' => 'rgba(6, 182, 212, 0.2)',
            'borderColor' => '#06b6d4',
            'borderWidth' => 2
        ]
    ]"
    title="Performance Radar"
    subtitle="Current vs Target Performance Metrics"
    height="400px" />

{{-- 8. POLAR AREA CHART EXAMPLE --}}
<x-chart 
    id="polar-area-chart"
    type="polarArea"
    :data="[11, 16, 7, 3, 14]"
    :labels="['Red', 'Green', 'Yellow', 'Grey', 'Blue']"
    title="Color Distribution"
    height="350px"
    :colors="['#ef4444', '#10b981', '#f59e0b', '#6b7280', '#3b82f6']" />

{{-- 9. CHART WITH CUSTOM OPTIONS --}}
<x-chart 
    id="custom-options-chart"
    type="line"
    :data="[10, 20, 30, 40, 50, 60, 70]"
    :labels="['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']"
    title="Weekly Activity"
    :options="[
        'scales' => [
            'y' => [
                'beginAtZero' => true,
                'max' => 100
            ]
        ],
        'plugins' => [
            'tooltip' => [
                'mode' => 'index',
                'intersect' => false
            ]
        ]
    ]"
    height="300px" />

{{-- 10. CHART WITH LOADING STATE --}}
<x-dashboard-chart 
    id="loading-chart"
    type="bar"
    :data="[]"
    :labels="[]"
    title="Loading Chart Example"
    :loading="true"
    height="300px" />

{{-- 11. CHART WITH ERROR STATE --}}
<x-dashboard-chart 
    id="error-chart"
    type="line"
    :data="[]"
    :labels="[]"
    title="Error Chart Example"
    error="Failed to load data"
    height="300px" />

{{-- 12. CHART WITH EMPTY STATE --}}
<x-dashboard-chart 
    id="empty-chart"
    type="pie"
    :data="[]"
    :labels="[]"
    title="Empty Chart Example"
    emptyMessage="No data available for the selected period"
    height="300px" />

{{-- 
    USAGE NOTES:
    ===========
    
    1. Simple Chart: Use for basic charts with minimal configuration
    2. Chart: Use for standard charts with more options
    3. Advanced Chart: Use for complex charts with all features
    4. Dashboard Chart: Use for dashboard widgets with statistics
    
    CHART TYPES SUPPORTED:
    =====================
    - line: Line chart
    - bar: Bar chart
    - doughnut: Doughnut chart
    - pie: Pie chart
    - radar: Radar chart
    - polarArea: Polar area chart
    
    COMMON PROPS:
    =============
    - id: Unique identifier for the chart
    - type: Chart type (line, bar, doughnut, pie, radar, polarArea)
    - data: Array of data values
    - labels: Array of label strings
    - datasets: Array of dataset objects (for multiple datasets)
    - title: Chart title
    - subtitle: Chart subtitle
    - height: Chart height (CSS value)
    - colors: Array of colors for the chart
    - showLegend: Show/hide legend
    - showTooltip: Show/hide tooltips
    - animation: Enable/disable animations
    - responsive: Make chart responsive
    - exportable: Enable export functionality
    - refreshable: Enable refresh functionality
    
    CUSTOMIZATION:
    ==============
    - Use the 'options' prop to pass custom Chart.js options
    - Override styles using CSS classes
    - Add custom JavaScript using the chart instance stored in window.chartInstances
--}}
