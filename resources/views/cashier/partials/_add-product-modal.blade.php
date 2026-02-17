<!-- Add Product Modal -->
<x-modal name="add-product-modal" :show="$showProductModal ?? false" focusable maxWidth="2xl">
    <div class="p-8">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-900">
                Add New Product
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Fill in the product details below
            </p>
        </div>

        <form id="addProductForm" method="POST" enctype="multipart/form-data" x-data="{
            // Form fields
            productName: '',
            category: '',
            stock: '',
            price: '',
            description: '',
            imagePreview: null,
            imageFile: null,
            
            // UI states
            isSubmitting: false,
            submitSuccess: false,
            submitError: null,
            
            // Error states
            errors: {
                productName: false,
                category: false,
                stock: false,
                price: false,
                image: false
            },
            
            // Error messages from server
            serverErrors: {},
            
            // Touched fields for validation
            touched: {
                productName: false,
                category: false,
                stock: false,
                price: false
            },
            
            // Computed property for form validity
            get isFormValid() {
                return this.productName.trim() !== '' && 
                       this.category !== '' && 
                       this.stock !== '' && 
                       this.price !== '' && 
                       parseFloat(this.price) > 0;
            },
            
            // Stock status
            get stockStatus() {
                if (this.stock === '' || this.stock === null) return '';
                const stockNum = parseInt(this.stock);
                if (stockNum === 0) return 'out';
                if (stockNum < 6) return 'low';
                return 'in';
            },
            
            // Validate specific field
            validateField(field) {
                this.touched[field] = true;
                
                switch(field) {
                    case 'productName':
                        this.errors.productName = this.productName.trim() === '';
                        break;
                    case 'category':
                        this.errors.category = this.category === '';
                        break;
                    case 'stock':
                        this.errors.stock = this.stock === '' || parseInt(this.stock) < 0;
                        break;
                    case 'price':
                        this.errors.price = this.price === '' || parseFloat(this.price) <= 0;
                        break;
                }
            },
            
            // Validate all fields
            validateAll() {
                this.validateField('productName');
                this.validateField('category');
                this.validateField('stock');
                this.validateField('price');
                
                return !this.errors.productName && 
                       !this.errors.category && 
                       !this.errors.stock && 
                       !this.errors.price;
            },
            
            // Image handling
            previewImage(event) {
                const file = event.target.files[0];
                if (file) {
                    this.imageFile = file;
                    this.imagePreview = URL.createObjectURL(file);
                    this.errors.image = false;
                }
            },
            
            // Handle form submission
            async submitForm() {
                if (!this.validateAll()) {
                    this.showFormError('Please fill in all required fields correctly');
                    return;
                }
                
                this.isSubmitting = true;
                this.submitError = null;
                this.serverErrors = {};
                
                // Create FormData object
                const formData = new FormData();
                formData.append('name', this.productName);
                formData.append('category', this.category);
                formData.append('stock', this.stock);
                formData.append('price', this.price);
                formData.append('description', this.description || '');
                
                if (this.imageFile) {
                    formData.append('image', this.imageFile);
                }
                
                try {
                    const response = await fetch('/cashier/products', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content') || '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                        if (response.status === 422) {
                            // Validation errors
                            this.serverErrors = data.errors || {};
                            this.showFormError('Please check the form for errors');
                        } else {
                            throw new Error(data.message || 'Failed to add product');
                        }
                        return;
                    }
                    
                    // Success
                    this.submitSuccess = true;
                    this.showNotification('Product added successfully!', 'success');
                    
                    // Reset form and close modal after delay
                    setTimeout(() => {
                        this.resetForm();
                        $dispatch('close-modal', 'add-product-modal');
                        
                        // Trigger product list refresh if needed
                        if (window.productManager) {
                            window.productManager.refreshProducts();
                        }
                    }, 1500);
                    
                } catch (error) {
                    console.error('Error adding product:', error);
                    this.submitError = error.message || 'An error occurred';
                    this.showFormError(this.submitError);
                } finally {
                    this.isSubmitting = false;
                }
            },
            
            showNotification(message, type) {
                const alpineRoot = document.querySelector('[x-data]');
                if (alpineRoot && alpineRoot.__x) {
                    const cashierData = alpineRoot.__x.$data;
                    const newNotification = {
                        id: Date.now(),
                        type: type,
                        title: type === 'success' ? 'Success' : 'Info',
                        message: message,
                        time: 'Just now',
                        read: false
                    };
                    if (cashierData.notifications) {
                        cashierData.notifications.unshift(newNotification);
                        cashierData.unreadCount = (cashierData.unreadCount || 0) + 1;
                    }
                }
            },
            
            showFormError(message) {
                // You can implement a toast or alert here
                alert(message);
            },
            
            resetForm() {
                this.productName = '';
                this.category = '';
                this.stock = '';
                this.price = '';
                this.description = '';
                this.imagePreview = null;
                this.imageFile = null;
                this.errors = {
                    productName: false,
                    category: false,
                    stock: false,
                    price: false,
                    image: false
                };
                this.touched = {
                    productName: false,
                    category: false,
                    stock: false,
                    price: false
                };
                this.serverErrors = {};
                this.submitError = null;
                this.submitSuccess = false;
            },
            
            // Listen for image selection from gallery
            listenForImageSelection() {
                window.addEventListener('image-selected', (event) => {
                    const selectedImage = event.detail;
                    this.imagePreview = '/storage/' + selectedImage.path;
                    this.imageFile = null;
                    this.errors.image = false;
                });
            },
            
            // Initialize listeners
            init() {
                this.listenForImageSelection();
            }
        }">
            @csrf
            
            <!-- Image Section -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Product Image <span class="text-gray-400 text-xs font-normal">(Optional)</span>
                </label>
                <div class="flex items-start space-x-4">
                    <!-- Image Preview/Upload Box -->
                    <div class="relative flex-shrink-0">
                        <template x-if="!imagePreview">
                            <div class="w-20 h-20 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg flex flex-col items-center justify-center cursor-pointer hover:border-pink-border hover:bg-pink-50 transition-colors group">
                                <svg class="w-8 h-8 text-gray-400 group-hover:text-pink-border" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-xs text-gray-500 mt-1 group-hover:text-pink-border">Upload</span>
                                <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="previewImage" accept="image/*">
                            </div>
                        </template>
                        <template x-if="imagePreview">
                            <div class="relative group">
                                <img :src="imagePreview" class="w-24 h-24 rounded-lg object-cover border-2 border-pink-border shadow-md">
                                <button type="button" @click="imagePreview = null; imageFile = null" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors border-2 border-white shadow-md opacity-0 group-hover:opacity-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Image Info -->
                    <div class="flex-1">
                        <p class="text-sm text-gray-600 mb-2">
                            <i class="fas fa-info-circle text-pink-border mr-1"></i>
                            Accepted formats: JPEG, PNG, JPG, GIF (Max: 2MB)
                        </p>
                        <div class="flex space-x-2">
                            <button type="button" @click="$refs.fileInput.click()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition-colors flex items-center">
                                <i class="fas fa-upload mr-2"></i>
                                Choose File
                            </button>
                            <input type="file" x-ref="fileInput" @change="previewImage" accept="image/*" class="hidden">
                        </div>
                    </div>
                </div>
                <!-- Server error for image -->
                <template x-if="serverErrors.image">
                    <p class="text-xs text-red-500 mt-2" x-text="serverErrors.image[0]"></p>
                </template>
            </div>

            <!-- Name and Category -->
            <div class="grid grid-cols-2 gap-4 mt-6">
                <!-- Product Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Product Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           x-model="productName"
                           @blur="validateField('productName')"
                           @input="validateField('productName')"
                           :class="{'border-red-500 focus:border-red-500 focus:ring-red-500/30': errors.productName || serverErrors.name, 'border-gray-300 focus:border-pink-border focus:ring-pink-border/30': !errors.productName && !serverErrors.name}"
                           class="w-full h-10 px-3 border rounded-lg text-sm focus:ring-1 outline-none transition-colors"
                           placeholder="e.g. Cinnamon Roll">
                    <template x-if="errors.productName">
                        <p class="text-xs text-red-500 mt-1">Product name is required</p>
                    </template>
                    <template x-if="!errors.productName && serverErrors.name">
                        <p class="text-xs text-red-500 mt-1" x-text="serverErrors.name[0]"></p>
                    </template>
                </div>
                
                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select x-model="category"
                                @blur="validateField('category')"
                                @change="validateField('category')"
                                :class="{'border-red-500 focus:border-red-500 focus:ring-red-500/30': errors.category || serverErrors.category, 'border-gray-300 focus:border-pink-border focus:ring-pink-border/30': !errors.category && !serverErrors.category}"
                                class="w-full h-10 px-3 border rounded-lg text-sm focus:ring-1 outline-none transition-colors appearance-none bg-white">
                            <option value="">Select Category</option>
                            <option value="breads">Breads</option>
                            <option value="cakes">Cakes</option>
                            <option value="beverages">Beverages</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                    <template x-if="errors.category">
                        <p class="text-xs text-red-500 mt-1">Please select a category</p>
                    </template>
                    <template x-if="!errors.category && serverErrors.category">
                        <p class="text-xs text-red-500 mt-1" x-text="serverErrors.category[0]"></p>
                    </template>
                </div>
            </div>

            <!-- Stock and Price -->
            <div class="grid grid-cols-2 gap-4 mt-4">
                <!-- Stock -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Stock Quantity <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" 
                               x-model="stock"
                               @blur="validateField('stock')"
                               @input="validateField('stock')"
                               :class="{'border-red-500 focus:border-red-500 focus:ring-red-500/30': errors.stock || serverErrors.stock, 'border-gray-300 focus:border-pink-border focus:ring-pink-border/30': !errors.stock && !serverErrors.stock}"
                               class="w-full h-10 px-3 border rounded-lg text-sm focus:ring-1 outline-none transition-colors"
                               placeholder="Enter quantity"
                               min="0">
                        
                        <!-- Stock Status Badge -->
                        <div class="absolute right-3 top-1/2 -translate-y-1/2">
                            <template x-if="stockStatus === 'out' && stock !== ''">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                    Out of Stock
                                </span>
                            </template>
                            <template x-if="stockStatus === 'low' && stock !== ''">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Low Stock
                                </span>
                            </template>
                            <template x-if="stockStatus === 'in' && stock !== ''">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    In Stock
                                </span>
                            </template>
                        </div>
                    </div>
                    <template x-if="errors.stock">
                        <p class="text-xs text-red-500 mt-1">Valid stock quantity is required</p>
                    </template>
                    <template x-if="!errors.stock && serverErrors.stock">
                        <p class="text-xs text-red-500 mt-1" x-text="serverErrors.stock[0]"></p>
                    </template>
                </div>

                <!-- Price -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Price (₱) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-medium">₱</span>
                        <input type="number" 
                               x-model="price"
                               @blur="validateField('price')"
                               @input="validateField('price')"
                               :class="{'border-red-500 focus:border-red-500 focus:ring-red-500/30': errors.price || serverErrors.price, 'border-gray-300 focus:border-pink-border focus:ring-pink-border/30': !errors.price && !serverErrors.price}"
                               class="w-full h-10 pl-8 pr-3 border rounded-lg text-sm focus:ring-1 outline-none transition-colors"
                               step="0.01"
                               min="0"
                               placeholder="0.00">
                    </div>
                    <template x-if="errors.price">
                        <p class="text-xs text-red-500 mt-1">Please enter a valid price</p>
                    </template>
                    <template x-if="!errors.price && serverErrors.price">
                        <p class="text-xs text-red-500 mt-1" x-text="serverErrors.price[0]"></p>
                    </template>
                </div>
            </div>

            <!-- Description (Optional) -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Description <span class="text-gray-400 text-xs font-normal">(Optional)</span>
                </label>
                <textarea 
                    x-model="description"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-pink-border focus:border-pink-border outline-none transition-colors"
                    placeholder="Enter product description..."></textarea>
            </div>

            <!-- Success Message -->
            <template x-if="submitSuccess">
                <div class="mt-4 bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span class="text-sm font-medium">Product added successfully!</span>
                </div>
            </template>

            <!-- Error Message -->
            <template x-if="submitError">
                <div class="mt-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span class="text-sm font-medium" x-text="submitError"></span>
                </div>
            </template>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                <button type="button"
                        @click="$dispatch('close-modal', 'add-product-modal'); resetForm()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 transition-colors">
                    Cancel
                </button>
                <button type="button"
                        @click="submitForm()"
                        :disabled="!isFormValid || isSubmitting"
                        :class="isFormValid && !isSubmitting ? 'bg-[#FF0059] hover:opacity-90 cursor-pointer' : 'bg-gray-300 cursor-not-allowed'"
                        class="px-6 py-2 text-sm font-medium text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-border transition-all duration-200 min-w-[120px] shadow-md">
                    <span x-show="!isSubmitting" class="flex items-center justify-center">
                        <i class="fas fa-plus mr-2"></i>
                        Add Product
                    </span>
                    <span x-show="isSubmitting" class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Adding...
                    </span>
                </button>
            </div>
        </form>
    </div>
</x-modal>