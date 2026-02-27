 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS AI-POWERED - Cashier</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- AlpineJS for dropdowns -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Font Awesome 6 for modern icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Inter Font - Modern font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Local CSS -->
    <link rel="stylesheet" href="{{ asset('css/cashier/dashboard.css') }}">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        'pink-border': '#FFC5D9',
                        'custom-gray': '#484545',
                        'notification': {
                            'info': '#3B82F6',
                            'warning': '#F59E0B',
                            'success': '#10B981'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Hide scrollbar but keep functionality */
        .hide-scrollbar::-webkit-scrollbar {
            width: 0px;
            background: transparent;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Column content - FIXED for product grid */
        .column-content {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-start;
            width: 100%;
            height: 100%;
            padding: 35px;
            overflow-y: auto;
        }

        /* Products grid - FIXED: 2 cards per row */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            width: 100%;
            align-items: start;
            justify-items: start;
        }

        /* Product card - FIXED dimensions */
        .product-card {
            width: 100%;
            height: 310px;
            justify-self: start;
        }

        /* Add product frame - FIXED dimensions */
        .add-product-frame {
            width: 100%;
            height: 310px;
            justify-self: start;
        }

        /* Empty state container - maintains column height */
        .product-selection-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 500px; /* INCREASED for 2 rows */
            padding: 1.5rem;
            text-align: center;
            width: 100%;
            height: 100%;
            margin: 0;
            grid-column: 1 / -1;
        }

        .selection-icon {
            margin-bottom: 1rem; /* REDUCED margin */
            padding: 0.75rem; /* REDUCED padding */
            background: linear-gradient(135deg, rgba(255, 197, 217, 0.2), transparent);
            border-radius: 1rem;
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .selection-icon svg {
            width: 4rem; /* REDUCED from 5rem */
            height: 4rem; /* REDUCED from 5rem */
            color: #484545;
        }

        .selection-title {
            font-size: 1.25rem; /* REDUCED from 1.5rem */
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.5rem; /* REDUCED margin */
            width: 100%;
            text-align: center;
        }

        .selection-subtext {
            color: #6B7280;
            margin-bottom: 1.5rem; /* REDUCED from 2rem */
            max-width: 24rem;
            font-size: 0.875rem; /* REDUCED from 1rem */
            width: 100%;
            text-align: center;
        }

        .add-product-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.6rem 1.25rem; /* REDUCED padding */
            background: linear-gradient(to right, #FFC5D9, #FFB0C8);
            color: white;
            font-weight: 500;
            border-radius: 0.75rem;
            border: none;
            cursor: pointer;
            font-size: 0.9rem; /* ADDED font size */
        }

        .add-product-btn:hover {
            background: #FF0059;
        }

        .add-product-btn svg {
            width: 1.125rem; /* REDUCED from 1.25rem */
            height: 1.125rem; /* REDUCED from 1.25rem */
        }

        /* Two-column container - INCREASED HEIGHT for 2 rows of products */
        .two-column-container {
            position: relative;
            left: 40px;
            width: calc(100% - 80px);
            max-width: 1361px;
            margin-top: 40px;
            display: flex;
            gap: 30px;
            align-items: stretch;
            height: 550px; /* INCREASED for 2 rows */
        }

        /* Product selection column - INCREASED HEIGHT for 2 rows of products */
        .column-products {
            flex: 1;
            min-width: 0;
            height: 550px; /* INCREASED for 2 rows */
            background: white;
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: 2px solid rgba(255, 197, 217, 0.5);
            background: linear-gradient(145deg, #ffffff, #fcfcfc);
        }

        .column-products:hover {
            border-color: #FFC5D9;
        }

        /* Orders column - ORIGINAL HEIGHT */
        .column-orders {
            flex: 1;
            min-width: 0;
            height: 450px; /* ORIGINAL height */
            background: white;
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: 2px dashed rgba(72, 69, 69, 0.3);
            background: linear-gradient(145deg, #ffffff, #fafafa);
        }

        .column-orders:hover {
            border-color: #484545;
            border-style: dashed;
        }
    </style>
    
    <script>
        // Global cashier data function for Alpine.js
        function cashierData() {
            return {
                notificationOpen: false,
                profileOpen: false,
                showProductModal: false,
                selectedProduct: null,
                quantity: 1,
                notifications: [
                    { id: 1, type: 'info', title: 'System Update', message: 'System is running smoothly', time: '2 hours ago', read: true },
                    { id: 2, type: 'warning', title: 'Low Inventory', message: 'Bread stock is running low', time: '5 hours ago', read: false },
                    { id: 3, type: 'success', title: 'New Order', message: 'Order #1234 has been placed', time: '1 day ago', read: true }
                ],
                unreadCount: 1,
                init() {
                    console.log('Cashier data initialized');
                },
                addToCart(product, quantity) {
                    if (window.productManager) {
                        window.productManager.addToCart(product, quantity);
                    }
                    console.log('Added to cart:', product, 'Quantity:', quantity);
                },
                markAllAsRead() {
                    this.notifications.forEach(n => {
                        n.read = true;
                    });
                    this.unreadCount = 0;
                }
            };
        }
    </script>
</head>
<body class="bg-gray-50 font-sans antialiased" x-data="cashierData()" x-init="init()">
    <!-- Main Container -->
    <div class="main-container">
        <!-- Header Section -->
        <div class="header-container">
            <!-- Left side: Logo and Title -->
            <div class="header-left-content group">
                <!-- Triangle Logo -->
                <div class="logo-responsive">
                    <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet">
                        <polygon points="50,15 85,75 15,75" fill="none" stroke="#484545" stroke-width="6" class="logo-triangle" />
                    </svg>
                </div>

                <!-- Header Text -->
                <h1 class="title-responsive bg-gradient-to-r from-custom-gray to-gray-600 bg-clip-text text-transparent">
                    Cashier Bakery & Cafe POS
                </h1>
            </div>

            <!-- Right side: Notification Bell and Profile Icon -->
            <div class="header-right-content" x-data="cashierData()" x-init="init()">
                <!-- MODERN NOTIFICATION BELL ICON -->
                <div class="relative" x-on:click.outside="notificationOpen = false">
                    <button
                        x-on:click="notificationOpen = !notificationOpen"
                        class="relative p-3 rounded-xl bg-white shadow-sm hover:shadow-md"
                        :class="{ 'ring-2 ring-pink-border ring-opacity-50': notificationOpen }"
                        aria-label="Notifications"
                    >
                        <!-- Bell Icon -->
                        <i class="fas fa-bell text-2xl text-custom-gray"></i>
                        
                        <!-- Modern Notification Badge -->
                        <span 
                            x-show="unreadCount > 0"
                            x-text="unreadCount"
                            class="absolute -top-1 -right-1 bg-gradient-to-r from-red-500 to-rose-500 text-white text-xs font-bold rounded-full min-w-[20px] h-5 px-1 flex items-center justify-center shadow-lg"
                        ></span>
                        
                        <!-- Active indicator dot -->
                        <span x-show="notificationOpen" 
                              class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-pink-border rounded-full"></span>
                    </button>
                    
                    <!-- MODERN NOTIFICATION DROPDOWN -->
                    <div 
                        x-show="notificationOpen"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                        class="absolute right-0 mt-3 w-96 bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl z-50 border border-gray-100 overflow-hidden"
                        style="display: none;"
                        @click.away="notificationOpen = false"
                    >
                        <div class="py-2">
                            <!-- Notification Header -->
                            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Notifications</h3>
                                    <p class="text-sm text-gray-500 mt-0.5">
                                        <span x-text="unreadCount" class="font-semibold text-pink-border"></span> 
                                        unread notification<span x-show="unreadCount !== 1">s</span>
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button 
                                        x-show="unreadCount > 0"
                                        @click="markAllAsRead"
                                        class="text-xs text-gray-500 hover:text-gray-700 px-3 py-1.5 rounded-lg hover:bg-gray-50"
                                    >
                                        Mark all as read
                                    </button>
                                    <button @click="notificationOpen = false" class="text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-times text-sm"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Notification Items -->
                            <div class="max-h-[400px] overflow-y-auto hide-scrollbar">
                                <template x-for="notification in notifications" :key="notification.id">
                                    <div 
                                        class="px-5 py-4 hover:bg-gray-50/80 border-b border-gray-50 cursor-pointer"
                                        :class="{ 
                                            'bg-gradient-to-r from-blue-50/30 to-transparent': !notification.read,
                                            'opacity-75': notification.read
                                        }"
                                        x-on:click="
                                            if (!notification.read) {
                                                notification.read = true; 
                                                unreadCount = Math.max(0, unreadCount - 1);
                                            }
                                        "
                                    >
                                        <div class="flex items-start space-x-3">
                                            <!-- Notification Icon -->
                                            <div class="flex-shrink-0">
                                                <div 
                                                    class="w-10 h-10 rounded-xl flex items-center justify-center shadow-sm"
                                                    :class="{
                                                        'bg-gradient-to-br from-blue-400 to-blue-500': notification.type === 'info',
                                                        'bg-gradient-to-br from-yellow-400 to-amber-500': notification.type === 'warning',
                                                        'bg-gradient-to-br from-emerald-400 to-teal-500': notification.type === 'success'
                                                    }"
                                                >
                                                    <i class="fas text-white text-sm"
                                                       :class="{
                                                           'fa-circle-info': notification.type === 'info',
                                                           'fa-triangle-exclamation': notification.type === 'warning',
                                                           'fa-circle-check': notification.type === 'success'
                                                       }"></i>
                                                </div>
                                            </div>
                                            
                                            <!-- Notification Content -->
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm font-semibold text-gray-900" x-text="notification.title"></p>
                                                    <span class="text-xs text-gray-400" x-text="notification.time"></span>
                                                </div>
                                                <p class="text-xs text-gray-600 mt-0.5 line-clamp-2" x-text="notification.message"></p>
                                            </div>
                                            
                                            <!-- Unread Dot -->
                                            <div x-show="!notification.read" class="flex-shrink-0">
                                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                
                                <!-- Empty State -->
                                <div x-show="notifications.length === 0" class="px-5 py-12 text-center">
                                    <div class="w-20 h-20 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-bell text-3xl text-gray-300"></i>
                                    </div>
                                    <h3 class="text-base font-semibold text-gray-900">All caught up!</h3>
                                    <p class="text-sm text-gray-500 mt-1">No new notifications at the moment</p>
                                </div>
                            </div>
                            
                            <!-- Notification Footer -->
                            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                                <a href="#" class="text-sm text-pink-border hover:text-[#FFB0C8] font-medium flex items-center justify-center space-x-2 group">
                                    <span>View all notifications</span>
                                    <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODERN PROFILE ICON WITH DROPDOWN -->
                <div class="relative" x-on:click.outside="profileOpen = false">
                    <button
                        x-on:click="profileOpen = !profileOpen"
                        class="relative"
                        :class="{ 'ring-2 ring-pink-border ring-opacity-50 rounded-2xl': profileOpen }"
                        aria-label="User Profile"
                    >
                        <div class="profile-icon-circle">
                            <i class="fas fa-user text-xl text-custom-gray"></i>
                        </div>
                    </button>
                    
                    <!-- MODERN PROFILE DROPDOWN -->
                    <div 
                        x-show="profileOpen"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                        class="absolute right-0 mt-3 w-64 bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl z-50 border border-gray-100 overflow-hidden"
                        style="display: none;"
                    >
                        <div class="py-3">
                            <!-- User Info -->
                            <div class="px-4 py-3 border-b border-gray-100">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-pink-border to-[#FFB0C8] rounded-2xl flex items-center justify-center shadow-md">
                                        <i class="fas fa-user text-white text-xl"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-900 truncate">Cashier User</p>
                                        <p class="text-xs text-gray-500 truncate mt-0.5">cashier@example.com</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Menu Items -->
                            <div class="py-2">
                                <!-- Profile Link -->
                                <a 
                                    href="/profile" 
                                    class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-gray-50 hover:to-transparent group"
                                >
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-pink-border/20">
                                        <i class="fas fa-user text-gray-600 group-hover:text-custom-gray"></i>
                                    </div>
                                    <span class="font-medium">Profile</span>
                                </a>
                                
                                <!-- Settings Link -->
                                <a 
                                    href="#" 
                                    class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-gray-50 hover:to-transparent group"
                                >
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-pink-border/20">
                                        <i class="fas fa-cog text-gray-600 group-hover:text-custom-gray"></i>
                                    </div>
                                    <span class="font-medium">Settings</span>
                                </a>
                                
                                <!-- Divider -->
                                <div class="my-2 border-t border-gray-100"></div>
                                
                                <!-- Logout Link -->
                                <a 
                                    href="/logout" 
                                    class="flex items-center px-4 py-3 text-sm text-red-600 hover:bg-gradient-to-r hover:from-red-50 hover:to-transparent group"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                >
                                    <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center mr-3 group-hover:bg-red-100">
                                        <i class="fas fa-sign-out-alt text-red-600"></i>
                                    </div>
                                    <span class="font-medium">Logout</span>
                                </a>
                            </div>
                            
                            <!-- Hidden logout form -->
                            <form id="logout-form" action="/logout" method="POST" style="display: none;">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Section -->
        <div class="tab-container">
            <div class="nav-tabs">
                <div class="indicator-container">
                    <div class="moving-indicator"></div>
                </div>
                
                <div class="nav-column-1">
                    <div class="tab-item active" data-tab="breads">
                        <span class="tab-text">Breads</span>
                    </div>
                    <div class="tab-item" data-tab="cakes">
                        <span class="tab-text">Cakes</span>
                    </div>
                    <div class="tab-item" data-tab="beverages">
                        <span class="tab-text">Beverages</span>
                    </div>
                </div>
                
                <div class="vertical-separator"></div>
                
                <div class="nav-column-2">
                    <div class="tab-item" data-tab="ongoing-orders">
                        <span class="tab-text">Ongoing Orders</span>
                    </div>
                    <div class="tab-item" data-tab="todays-log">
                        <span class="tab-text">Today's Log</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- TWO-COLUMN LAYOUT -->
        <div class="two-column-container">
            <!-- PRODUCT SELECTION COLUMN -->
            <div class="column column-products">
                <div class="column-content">
                    <!-- Products Grid Container -->
                    <div id="productsGrid" class="products-grid">
                        <!-- Products will be loaded here dynamically -->
                    </div>
                </div>
            </div>
            
            <!-- ORDERS COLUMN -->
            <div class="column column-orders">
                <div class="column-content">
                    <div class="placeholder-content">
                        <div class="placeholder-icon">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                            </svg>
                        </div>
                        <h3 class="placeholder-title">Order Place Here</h3>
                        <p class="placeholder-text">
                            Review items, apply discounts, and complete transactions.
                            Current order total will appear here.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Active Tab Display -->
        <div class="fixed bottom-4 right-4 bg-white/80 backdrop-blur-sm p-4 rounded-xl shadow-lg text-sm border border-gray-100">
            <div class="font-semibold text-custom-gray">Active Tab:</div>
            <div id="active-tab-name" class="text-lg font-bold bg-gradient-to-r from-custom-gray to-gray-600 bg-clip-text text-transparent">Breads</div>
        </div>
    </div>

    <!-- Include Product Modal Partial -->
    @include('cashier.partials._add-product-modal')

    <!-- Local JavaScript -->
    <script src="{{ asset('js/cashier/dashboard.js') }}"></script>

    <!-- Product Manager JS - Load this AFTER dashboard.js -->
    <script src="{{ asset('js/cashier/product-manager.js') }}"></script>

    <!-- Debug Overlay -->
    <div id="debug-overlay" style="position: fixed; bottom: 20px; left: 20px; background: rgba(0,0,0,0.9); color: #0f0; padding: 15px; border-radius: 8px; font-family: monospace; font-size: 12px; max-width: 350px; max-height: 250px; overflow-y: auto; z-index: 9999; display: block; border: 2px solid #0f0;">
        <div style="color: #ff6b6b; font-weight: bold; margin-bottom: 10px;">⚙️ DEBUG CONSOLE</div>
        <div id="debug-messages"></div>
    </div>

    <script>
        // Debug overlay functionality
        const debugOverlay = document.getElementById('debug-overlay');
        const debugMessages = document.getElementById('debug-messages');
        const originalLog = console.log;
        const originalError = console.error;
        
        let messageCount = 0;
        
        function addDebugMessage(msg, type = 'log') {
            if (messageCount > 20) {
                debugMessages.innerHTML = '<div style="color: #999;">--- cleared ---</div>';
                messageCount = 0;
            }
            const color = type === 'error' ? '#ff6b6b' : type === 'warn' ? '#ffd93d' : '#0f0';
            const msgEl = document.createElement('div');
            msgEl.style.color = color;
            msgEl.textContent = msg;
            debugMessages.appendChild(msgEl);
            debugOverlay.style.display = 'block';
            messageCount++;
        }
        
        console.log = function(...args) {
            originalLog(...args);
            addDebugMessage(args.map(a => typeof a === 'object' ? JSON.stringify(a) : String(a)).join(' '), 'log');
        };
        
        console.error = function(...args) {
            originalError(...args);
            addDebugMessage(args.map(a => typeof a === 'object' ? JSON.stringify(a) : String(a)).join(' '), 'error');
        };
        
        // Toggle debug overlay with Ctrl+Shift+D
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.shiftKey && e.key === 'D') {
                e.preventDefault();
                debugOverlay.style.display = debugOverlay.style.display === 'none' ? 'block' : 'none';
            }
        });
    </script>

    <!-- Include Gallery Modal -->
    @include('cashier.partials._image-gallery-modal')
</body>
</html>
