<div
    x-data="{open: false}"
    class="relative"
    @keydown.escape.window="open = false"
>
    {{-- Trigger --}}
    <button
        type="button"
        class="w-full flex flex-col items-center justify-center gap-1 py-3 hover:bg-base-200"
        @click="open = !open"
        :aria-expanded="open.toString()"
        aria-haspopup="menu"
    >
        {{ $trigger }}
    </button>

    {{-- Backdrop --}}
    <div
        x-cloak
        x-show="open"
        class="fixed inset-0 z-40"
        @click="open = false"
    ></div>

    {{-- Panel --}}
    <div
        x-cloak
        x-show="open"
        x-transition.origin.bottom
        class="fixed inset-x-0 bottom-[100px] z-50"
    >
        <div class="bg-base-100 overflow-hidden">
            {{ $slot }}
        </div>
    </div>

</div>
