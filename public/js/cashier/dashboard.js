// CASHIER DASHBOARD JAVASCRIPT

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

// Column interaction (optional for future features)
document.addEventListener('DOMContentLoaded', function() {
    const columns = document.querySelectorAll('.column');
    
    columns.forEach(column => {
        // Add hover effect
        column.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.1)';
        });
        
        column.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.05)';
        });
    });
    
    // Console log for debugging
    console.log('Cashier Dashboard loaded successfully');
    console.log('Tab container max-width: 1361px');
    console.log('Each column width: ~670.5px (calculated: (1361px - 20px gap) / 2)');
});