<!-- Product Modal -->
<div 
    x-show="showProductModal" 
    x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50"></div>

    <!-- Modal Panel -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div 
            x-show="showProductModal" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            class="relative bg-white rounded-lg shadow-xl"
            style="width: 600px; height: 600px;"
            @click.away="showProductModal = false"
        >
            <!-- Close Button (Bigger with #FF0059 hover) -->
            <button 
                @click="showProductModal = false"
                class="absolute top-4 right-4 text-gray-400 hover:text-[#FF0059] transition-colors duration-200 z-10"
                style="width: 40px; height: 40px;"
            >
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Modal Content -->
            <div class="h-full w-full flex items-center justify-center p-6">
                <p class="text-gray-700 text-center">add product details here</p>
            </div>
        </div>
    </div>
</div>
