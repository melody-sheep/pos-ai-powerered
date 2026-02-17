<!-- Image Gallery Modal -->
<x-modal name="gallery-modal" :show="$showGalleryModal ?? false" focusable>
    <div class="p-6" x-data="imageGallery()" x-init="loadImages()">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900">Image Gallery</h2>
                <p class="text-sm text-gray-500 mt-1">Select an image to use for your product</p>
            </div>
            <button @click="$dispatch('close-modal', 'gallery-modal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Upload New Section -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-medium text-gray-700">Upload New Image</h3>
                    <p class="text-xs text-gray-500">Upload a new image to your gallery</p>
                </div>
                <div class="relative">
                    <button @click="$refs.galleryFileInput.click()" 
                            class="px-4 py-2 bg-pink-border text-white rounded-lg hover:bg-[#FF0059] transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Choose File
                    </button>
                    <input type="file" x-ref="galleryFileInput" @change="uploadImage" accept="image/*" class="hidden">
                </div>
            </div>
            
            <!-- Upload Progress -->
            <template x-if="uploading">
                <div class="mt-3">
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="text-gray-600">Uploading...</span>
                        <span class="text-gray-500" x-text="uploadProgress + '%'"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-pink-border h-2 rounded-full transition-all duration-300" :style="'width: ' + uploadProgress + '%'"></div>
                    </div>
                </div>
            </template>
            
            <!-- Upload Error -->
            <template x-if="uploadError">
                <div class="mt-3 text-sm text-red-600" x-text="uploadError"></div>
            </template>
        </div>

        <!-- Gallery Grid -->
        <div class="mb-4">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-medium text-gray-700">Existing Images</h3>
                <span class="text-sm text-gray-500" x-text="images.length + ' images'"></span>
            </div>
            
            <!-- Loading State -->
            <div x-show="loading" class="text-center py-12">
                <svg class="animate-spin h-8 w-8 text-pink-border mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-500">Loading images...</p>
            </div>
            
            <!-- Empty State -->
            <div x-show="!loading && images.length === 0" class="text-center py-12 bg-gray-50 rounded-lg">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-gray-500">No images in gallery yet</p>
                <p class="text-sm text-gray-400 mt-1">Upload some images to get started</p>
            </div>
            
            <!-- Image Grid -->
            <div x-show="!loading && images.length > 0" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3 max-h-96 overflow-y-auto p-1">
                <template x-for="image in images" :key="image.id">
                    <div class="relative group cursor-pointer" @click="selectImage(image)">
                        <!-- Image -->
                        <div class="aspect-square rounded-lg overflow-hidden border-2 transition-all duration-200"
                             :class="selectedImage && selectedImage.id === image.id ? 'border-pink-border ring-2 ring-pink-border ring-opacity-50' : 'border-gray-200 hover:border-pink-border'">
                            <img :src="'/storage/' + image.path" 
                                 :alt="image.name"
                                 class="w-full h-full object-cover"
                                 loading="lazy">
                        </div>
                        
                        <!-- Selection Overlay -->
                        <div x-show="selectedImage && selectedImage.id === image.id" 
                             class="absolute top-1 right-1 bg-pink-border text-white rounded-full p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        
                        <!-- Image Name Tooltip -->
                        <div class="absolute inset-x-0 bottom-0 bg-black bg-opacity-50 text-white text-xs p-1 truncate opacity-0 group-hover:opacity-100 transition-opacity rounded-b-lg"
                             x-text="image.name">
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
            <button type="button"
                    @click="$dispatch('close-modal', 'gallery-modal')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button type="button"
                    @click="confirmSelection"
                    :disabled="!selectedImage"
                    :class="selectedImage ? 'bg-pink-border hover:bg-[#FF0059] cursor-pointer' : 'bg-gray-300 cursor-not-allowed'"
                    class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors">
                Use Selected Image
            </button>
        </div>
    </div>
</x-modal>

<script>
function imageGallery() {
    return {
        images: [],
        selectedImage: null,
        loading: false,
        uploading: false,
        uploadProgress: 0,
        uploadError: null,
        
        async loadImages() {
            this.loading = true;
            try {
                const response = await fetch('/api/gallery/images');
                const data = await response.json();
                this.images = data.images || [];
            } catch (error) {
                console.error('Error loading images:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async uploadImage(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            this.uploading = true;
            this.uploadProgress = 0;
            this.uploadError = null;
            
            const formData = new FormData();
            formData.append('image', file);
            
            try {
                const progressInterval = setInterval(() => {
                    this.uploadProgress = Math.min(this.uploadProgress + 10, 90);
                }, 100);
                
                const response = await fetch('/api/gallery/upload', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
                
                clearInterval(progressInterval);
                
                if (!response.ok) {
                    throw new Error('Upload failed');
                }
                
                const data = await response.json();
                this.uploadProgress = 100;
                
                setTimeout(() => {
                    this.images.unshift(data.image);
                    this.uploading = false;
                    this.uploadProgress = 0;
                }, 500);
                
            } catch (error) {
                this.uploadError = error.message;
                this.uploading = false;
            }
        },
        
        selectImage(image) {
            this.selectedImage = image;
        },
        
        confirmSelection() {
            if (this.selectedImage) {
                window.dispatchEvent(new CustomEvent('image-selected', { 
                    detail: this.selectedImage 
                }));
                $dispatch('close-modal', 'gallery-modal');
            }
        }
    };
}
</script>