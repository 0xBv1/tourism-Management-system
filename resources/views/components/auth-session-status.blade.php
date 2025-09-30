@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'bg-green-500/20 border border-green-500/30 rounded-lg p-4 text-sm text-green-200 dark:text-green-300 flex items-center']) }}>
        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        {{ $status }}
    </div>
@endif
