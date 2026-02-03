<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS AI-POWERED - Cashier</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* Custom styles */
        body {
            margin: 0;
            padding: 0;
            background: white;
            overflow: auto;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        /* Main container */
        .main-container {
            width: 100%;
            min-width: 1440px;
            max-width: 1920px;
            min-height: 1024px;
            margin: 0 auto;
            background: white;
            padding-left: 40px;
            padding-right: 40px;
            box-sizing: border-box;
        }
        
        /* Header stays fixed to left */
        .header-container {
            padding-top: 24px;
            position: relative;
            left: 40px;
            width: fit-content;
        }
        
        /* Header content */
        .header-content {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        /* Tab container */
        .tab-container {
            position: relative;
            left: 40px;
            width: calc(100% - 80px);
            max-width: 1361px;
            height: 80px;
            border: 3px solid #FFC5D9;
            background: white;
            border-radius: 8px;
            margin-top: 40px;
            display: flex;
            align-items: center;
            padding: 0 40px;
        }
        
        /* Logo */
        .logo-responsive {
            flex-shrink: 0;
            transition: all 0.3s ease;
        }
        
        /* Logo color */
        .logo-triangle {
            stroke: #484545;
        }
        
        /* Header text */
        .title-responsive {
            font-weight: bold;
            color: #484545;
            white-space: nowrap;
            transition: all 0.3s ease;
        }
        
        /* Navigation Tabs Styles */
        .nav-tabs {
            display: flex;
            width: 100%;
            height: 100%;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }
        
        /* First column - Food items */
        .nav-column-1 {
            display: flex;
            align-items: center;
            gap: 60px;
            height: 100%;
        }
        
        /* Second column - Orders/Log */
        .nav-column-2 {
            display: flex;
            align-items: center;
            gap: 60px;
            height: 100%;
        }
        
        /* Vertical separator line - 3px with pink color and rounded edges */
        .vertical-separator {
            width: 3px;
            height: 57px;
            background-color: #FFC5D9;
            border-radius: 3px; /* Rounded edges */
            margin: 0 50px;
        }
        
        /* Tab item */
        .tab-item {
            height: 100%;
            display: flex;
            align-items: center;
            position: relative;
            cursor: pointer;
            user-select: none;
            padding: 0 5px;
        }
        
        /* Tab text - 30px font */
        .tab-text {
            font-size: 30px;
            font-weight: 500;
            color: #484545;
            opacity: 0.7;
            transition: opacity 0.2s ease;
            white-space: nowrap;
        }
        
        /* Active tab text */
        .tab-item.active .tab-text {
            opacity: 1;
            font-weight: 600;
        }
        
        /* Moving indicator container */
        .indicator-container {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            z-index: 1;
        }
        
        /* Moving indicator - QUICK animation */
        .moving-indicator {
            position: absolute;
            bottom: 0;
            height: 4px;
            background-color: #FFC5D9;
            border-radius: 2px 2px 0 0;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); /* Fast animation */
        }
        
        /* Hover effects */
        .tab-item:hover .tab-text {
            opacity: 0.85;
        }
        
        /* Media queries for responsive font sizing */
        @media (max-width: 1600px) {
            .tab-text {
                font-size: 28px;
            }
            .nav-column-1,
            .nav-column-2 {
                gap: 50px;
            }
            .vertical-separator {
                margin: 0 40px;
            }
        }
        
        @media (max-width: 1400px) {
            .tab-text {
                font-size: 26px;
            }
            .nav-column-1,
            .nav-column-2 {
                gap: 40px;
            }
            .vertical-separator {
                margin: 0 35px;
                height: 52px;
            }
            .tab-container {
                padding: 0 35px;
                height: 75px;
            }
        }
        
        @media (max-width: 1200px) {
            .tab-text {
                font-size: 24px;
            }
            .nav-column-1,
            .nav-column-2 {
                gap: 35px;
            }
            .vertical-separator {
                margin: 0 30px;
                height: 48px;
            }
            .tab-container {
                padding: 0 30px;
                height: 70px;
            }
        }
        
        @media (max-width: 1000px) {
            .main-container {
                padding-left: 35px;
                padding-right: 35px;
            }
            .header-container {
                left: 35px;
            }
            .tab-container {
                left: 35px;
                width: calc(100% - 70px);
                height: 65px;
                padding: 0 25px;
                max-width: 1100px;
            }
            .tab-text {
                font-size: 22px;
            }
            .nav-column-1,
            .nav-column-2 {
                gap: 30px;
            }
            .vertical-separator {
                margin: 0 25px;
                height: 44px;
            }
            .moving-indicator {
                height: 3px;
            }
        }
        
        @media (max-width: 900px) {
            .main-container {
                padding-left: 30px;
                padding-right: 30px;
                min-width: 100%;
            }
            .header-container {
                left: 30px;
            }
            .tab-container {
                left: 30px;
                width: calc(100% - 60px);
                height: 60px;
                padding: 0 20px;
            }
            .tab-text {
                font-size: 20px;
            }
            .nav-column-1,
            .nav-column-2 {
                gap: 25px;
            }
            .vertical-separator {
                margin: 0 20px;
                height: 40px;
            }
            .moving-indicator {
                height: 3px;
            }
        }
        
        @media (max-width: 768px) {
            .tab-container {
                flex-direction: column;
                height: auto;
                padding: 20px;
            }
            .nav-tabs {
                flex-direction: column;
                gap: 25px;
                width: 100%;
            }
            .nav-column-1,
            .nav-column-2 {
                width: 100%;
                justify-content: center;
                gap: 25px;
            }
            .vertical-separator {
                width: 80%;
                height: 3px;
                margin: 15px 0;
                border-radius: 3px;
            }
            .indicator-container {
                display: none;
            }
            .tab-item::after {
                content: '';
                position: absolute;
                bottom: -5px;
                left: 0;
                width: 100%;
                height: 3px;
                background-color: #FFC5D9;
                border-radius: 1.5px;
                transform: scaleX(0);
                transition: transform 0.2s ease;
            }
            .tab-item.active::after {
                transform: scaleX(1);
            }
        }
        
        @media (max-width: 600px) {
            .tab-text {
                font-size: 18px;
            }
            .nav-column-1,
            .nav-column-2 {
                gap: 20px;
            }
        }
        
        @media (max-width: 480px) {
            .tab-text {
                font-size: 16px;
            }
            .nav-column-1,
            .nav-column-2 {
                gap: 15px;
            }
        }
    </style>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'pink-border': '#FFC5D9',
                        'custom-gray': '#484545',
                    }
                }
            }
        }
        
        // Navigation functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Get all tab items
            const tabItems = document.querySelectorAll('.tab-item');
            const indicator = document.querySelector('.moving-indicator');
            const indicatorContainer = document.querySelector('.indicator-container');
            
            // Set default active tab (Breads)
            setActiveTab('breads', true);
            
            // Add click event to each tab
            tabItems.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    setActiveTab(tabId, false);
                });
            });
            
            function setActiveTab(tabId, initial = false) {
                // Remove active class from all tabs
                tabItems.forEach(tab => {
                    tab.classList.remove('active');
                });
                
                // Add active class to clicked tab
                const activeTab = document.querySelector(`[data-tab="${tabId}"]`);
                if (activeTab) {
                    activeTab.classList.add('active');
                    
                    // Move the indicator with QUICK animation
                    moveIndicator(activeTab, initial);
                    
                    // Dispatch custom event for other components to listen to
                    const event = new CustomEvent('tabChanged', { 
                        detail: { tabId: tabId, tabName: getTabName(tabId) }
                    });
                    window.dispatchEvent(event);
                }
            }
            
            function moveIndicator(tab, initial = false) {
                if (!indicator || !indicatorContainer) return;
                
                const tabRect = tab.getBoundingClientRect();
                const containerRect = indicatorContainer.getBoundingClientRect();
                
                // Calculate position relative to indicator container
                const leftPosition = tabRect.left - containerRect.left;
                const tabWidth = tabRect.width;
                
                if (initial) {
                    // Set initial position without animation
                    indicator.style.left = leftPosition + 'px';
                    indicator.style.width = tabWidth + 'px';
                } else {
                    // QUICK animation to new position
                    indicator.style.transition = 'all 0.25s cubic-bezier(0.4, 0, 0.2, 1)';
                    indicator.style.left = leftPosition + 'px';
                    indicator.style.width = tabWidth + 'px';
                }
            }
            
            function getTabName(tabId) {
                const tabNames = {
                    'breads': 'Breads',
                    'cakes': 'Cakes',
                    'beverages': 'Beverages',
                    'ongoing-orders': 'Ongoing Orders',
                    'todays-log': "Today's Log"
                };
                return tabNames[tabId] || tabId;
            }
            
            // Update indicator position on resize
            window.addEventListener('resize', function() {
                const activeTab = document.querySelector('.tab-item.active');
                if (activeTab) {
                    moveIndicator(activeTab, true);
                }
            });
        });
    </script>
</head>
<body class="bg-white">
    <!-- Main Container -->
    <div class="main-container">
        <!-- Header Section -->
        <div class="header-container">
            <div class="header-content">
                <!-- Triangle Logo -->
                <div class="logo-responsive">
                    <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet">
                        <polygon points="50,15 85,75 15,75" fill="none" stroke="#484545" stroke-width="6" class="logo-triangle" />
                    </svg>
                </div>

                <!-- Header Text -->
                <h1 class="title-responsive">
                    Cashier Bakery & Cafe POS
                </h1>
            </div>
        </div>

        <!-- Tab Section -->
        <div class="tab-container">
            <!-- Navigation Tabs -->
            <div class="nav-tabs">
                <!-- Moving indicator container -->
                <div class="indicator-container">
                    <div class="moving-indicator"></div>
                </div>
                
                <!-- First Column: Breads, Cakes, Beverages -->
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
                
                <!-- Vertical Separator - 3px pink with rounded edges -->
                <div class="vertical-separator"></div>
                
                <!-- Second Column: Ongoing Orders, Today's Log -->
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
        
        <!-- Active Tab Display -->
        <div class="fixed bottom-4 right-4 bg-gray-100 p-3 rounded text-sm opacity-75">
            <div class="font-semibold text-custom-gray">Active Tab:</div>
            <div id="active-tab-name" class="text-lg font-bold">Breads</div>
        </div>
    </div>
    
    <script>
        // Update active tab display
        window.addEventListener('tabChanged', function(e) {
            const activeTabDisplay = document.getElementById('active-tab-name');
            if (activeTabDisplay) {
                activeTabDisplay.textContent = e.detail.tabName;
            }
        });
        
        // Responsive scaling for header
        document.addEventListener('DOMContentLoaded', function() {
            const logo = document.querySelector('.logo-responsive');
            const title = document.querySelector('.title-responsive');
            
            function updateSizes() {
                const screenWidth = window.innerWidth;
                
                if (screenWidth >= 1440) {
                    const scaleFactor = (screenWidth - 1440) / 100;
                    const logoSize = 48 + Math.min(scaleFactor * 1, 8);
                    const textSize = 40 + Math.min(scaleFactor * 0.75, 6);
                    
                    if (logo) {
                        logo.style.width = logoSize + 'px';
                        logo.style.height = logoSize + 'px';
                    }
                    if (title) {
                        title.style.fontSize = textSize + 'px';
                    }
                } else if (screenWidth < 1440 && screenWidth > 600) {
                    const scaleFactor = (1440 - screenWidth) / 100;
                    const logoSize = Math.max(48 - scaleFactor * 2, 28);
                    const textSize = Math.max(40 - scaleFactor * 2, 18);
                    
                    if (logo) {
                        logo.style.width = logoSize + 'px';
                        logo.style.height = logoSize + 'px';
                    }
                    if (title) {
                        title.style.fontSize = textSize + 'px';
                    }
                }
            }
            
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(updateSizes, 50);
            });
            
            updateSizes();
        });
    </script>
</body>
</html>