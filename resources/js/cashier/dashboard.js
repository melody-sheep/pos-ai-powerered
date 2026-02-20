// CASHIER DASHBOARD JAVASCRIPT - Clean version

// Navigation functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard JS: DOMContentLoaded fired');
    
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
        console.log('Setting active tab:', tabId);
        
        // Remove active class from all tabs
        tabItems.forEach(tab => {
            tab.classList.remove('active');
        });
        
        // Add active class to clicked tab
        const activeTab = document.querySelector(`[data-tab="${tabId}"]`);
        if (activeTab) {
            activeTab.classList.add('active');
            
            // Move the indicator
            moveIndicator(activeTab, initial);
            
            // Update active tab display
            const activeTabNameEl = document.getElementById('active-tab-name');
            if (activeTabNameEl) {
                const tabName = activeTab.querySelector('.tab-text')?.textContent || getTabName(tabId);
                activeTabNameEl.textContent = tabName;
            }
            
            // Dispatch custom event for other components to listen to
            const event = new CustomEvent('tabChanged', { 
                detail: { tabId: tabId, tabName: getTabName(tabId) }
            });
            window.dispatchEvent(event);
            
            // Refresh products if ProductManager exists
            if (window.productManager) {
                console.log('Dashboard: ProductManager found, refreshing products for', tabId);
                window.productManager.currentCategory = tabId;
                window.productManager.refreshProducts();
            } else {
                console.log('Dashboard: ProductManager not found yet');
            }
        }
    }
    
    function moveIndicator(tab, initial = false) {
        if (!indicator || !indicatorContainer) return;
        
        const tabRect = tab.getBoundingClientRect();
        const containerRect = indicatorContainer.getBoundingClientRect();
        
        // Calculate position relative to indicator container
        const leftPosition = tabRect.left - containerRect.left;
        const tabWidth = tabRect.width;
        
        // Set position without animation
        indicator.style.left = leftPosition + 'px';
        indicator.style.width = tabWidth + 'px';
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

// Console log for debugging
console.log('Cashier Dashboard loaded successfully');