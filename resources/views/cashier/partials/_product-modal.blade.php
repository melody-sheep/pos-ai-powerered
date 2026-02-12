<!-- Product Modal - Minimal Version -->
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
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

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
            class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full"
            @click.away="showProductModal = false"
        >
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-pink-border to-[#FFB0C8] rounded-xl flex items-center justify-center">
                        <span x-text="selectedProduct?.image || '🛒'" class="text-2xl"></span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900" x-text="selectedProduct?.name || 'Product Details'"></h3>
                        <p class="text-sm text-gray-500" x-text="selectedProduct?.category ? selectedProduct.category.charAt(0).toUpperCase() + selectedProduct.category.slice(1) : ''"></p>
                    </div>
                </div>
                <button 
                    @click="showProductModal = false"
                    class="text-gray-400 hover:text-gray-600 transition-colors"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <div class="space-y-4">
                    <!-- Product Image Placeholder -->
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-8 flex items-center justify-center">
                        <span x-text="selectedProduct?.image || '🛒'" class="text-7xl"></span>
                    </div>

                    <!-- Product Description -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Description</h4>
                        <p class="mt-1 text-gray-700" x-text="selectedProduct?.description || 'No description available'"></p>
                    </div>

                    <!-- Price -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Price</span>
                        <span class="text-3xl font-bold text-custom-gray">
                            ₱<span x-text="selectedProduct?.price?.toFixed(2) || '0.00'"></span>
                        </span>
                    </div>

                    <!-- Quantity Selector -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <span class="font-medium text-gray-700">Quantity</span>
                        <div class="flex items-center space-x-3">
                            <button 
                                @click="quantity = Math.max(1, quantity - 1)"
                                class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors"
                            >
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                            <span x-text="quantity" class="w-8 text-center font-semibold"></span>
                            <button 
                                @click="quantity = Math.min(99, quantity + 1)"
                                class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors"
                            >
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end space-x-3 p-6 border-t border-gray-100">
                <button 
                    @click="showProductModal = false"
                    class="px-6 py-2.5 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors font-medium"
                >
                    Cancel
                </button>
                <button 
                    @click="addToCart(selectedProduct, quantity); showProductModal = false"
                    class="px-6 py-2.5 bg-gradient-to-r from-pink-border to-[#FFB0C8] text-white rounded-xl hover:opacity-90 transition-opacity font-medium flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Add to Cart</span>
                </button>
            </div>
        </div>
    </div>
</div>
