@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'w-full px-4 py-3 bg-white/10 dark:bg-gray-800/50 border border-white/20 dark:border-gray-600 rounded-xl text-white dark:text-gray-200 placeholder-white/60 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white/30 dark:focus:ring-primary-500 focus:border-transparent transition-all duration-200 backdrop-blur-sm']) !!}>
