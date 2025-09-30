<button {{ $attributes->merge(['type' => 'submit', 'class' => 'w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl']) }}>
    {{ $slot }}
</button>
