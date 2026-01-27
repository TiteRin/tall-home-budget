<footer class="border-t bg-base-200 border-gray-200 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-6 py-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                © {{ date('Y') }} {{ config('app.name') }}
            </div>

            <nav class="flex gap-4 text-sm">
                <a href="{{ route('mentions-legales') }}"
                   class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white underline-offset-4 hover:underline">
                    Mentions légales
                </a>
                <a href="{{ route('cgu') }}"
                   class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white underline-offset-4 hover:underline">
                    CGU
                </a>
            </nav>
        </div>
    </div>
</footer>
