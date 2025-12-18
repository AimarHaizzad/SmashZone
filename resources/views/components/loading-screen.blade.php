<!-- Loading Screen -->
<div id="loading-screen" class="fixed inset-0 z-[9999] bg-gradient-to-br from-blue-600 via-green-500 to-blue-700 flex items-center justify-center transition-opacity duration-500 ease-in-out opacity-0 pointer-events-none">
    <div class="text-center">
        <!-- Animated Logo/Icon -->
        <div class="mb-8 relative">
            <!-- Main Badminton Shuttlecock Animation -->
            <div class="relative w-24 h-24 mx-auto">
                <!-- Rotating Circle -->
                <div class="absolute inset-0 border-4 border-white border-t-transparent rounded-full animate-spin"></div>
                
                <!-- Badminton Shuttlecock Icon -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-16 h-16 text-white animate-bounce" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                </div>
                
                <!-- Pulsing Rings -->
                <div class="absolute inset-0 border-2 border-white rounded-full animate-ping opacity-75"></div>
                <div class="absolute inset-0 border-2 border-white rounded-full animate-ping opacity-50" style="animation-delay: 0.5s;"></div>
            </div>
        </div>
        
        <!-- App Name -->
        <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-2 tracking-wide animate-pulse">
            SmashZone
        </h2>
        
        <!-- Loading Text -->
        <p class="text-white/90 text-sm sm:text-base font-medium animate-pulse" style="animation-delay: 0.2s;">
            Loading...
        </p>
        
        <!-- Progress Bar -->
        <div class="mt-6 w-64 mx-auto">
            <div class="h-1 bg-white/20 rounded-full overflow-hidden">
                <div id="loading-progress" class="h-full bg-white rounded-full transition-all duration-300 ease-out" style="width: 0%"></div>
            </div>
        </div>
        
        <!-- Dots Animation -->
        <div class="flex justify-center gap-2 mt-6">
            <div class="w-2 h-2 bg-white rounded-full animate-bounce" style="animation-delay: 0s;"></div>
            <div class="w-2 h-2 bg-white rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
            <div class="w-2 h-2 bg-white rounded-full animate-bounce" style="animation-delay: 0.4s;"></div>
        </div>
    </div>
</div>

<style>
@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-20px);
    }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

/* Smooth fade animations */
#loading-screen.show {
    opacity: 1;
    pointer-events: all;
}

#loading-screen.hide {
    opacity: 0;
    pointer-events: none;
}
</style>

<script>
(function() {
    'use strict';
    
    const loadingScreen = document.getElementById('loading-screen');
    const progressBar = document.getElementById('loading-progress');
    
    if (!loadingScreen) return;
    
    // Show loading screen
    function showLoading() {
        loadingScreen.classList.remove('hide');
        loadingScreen.classList.add('show');
        
        // Simulate progress
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90; // Don't complete until page is ready
            progressBar.style.width = progress + '%';
        }, 200);
        
        // Store interval to clear later
        loadingScreen.dataset.interval = interval;
    }
    
    // Hide loading screen
    function hideLoading() {
        // Complete progress bar
        progressBar.style.width = '100%';
        
        // Wait a bit then fade out
        setTimeout(() => {
            const interval = loadingScreen.dataset.interval;
            if (interval) clearInterval(interval);
            
            loadingScreen.classList.remove('show');
            loadingScreen.classList.add('hide');
            
            // Remove from DOM after animation
            setTimeout(() => {
                loadingScreen.style.display = 'none';
            }, 500);
        }, 300);
    }
    
    // Show on page load start
    if (document.readyState === 'loading') {
        showLoading();
    }
    
    // Hide when page is fully loaded
    if (document.readyState === 'complete') {
        hideLoading();
    } else {
        window.addEventListener('load', hideLoading);
    }
    
    // Show on navigation (for SPA-like behavior)
    // But don't interfere with form submissions that cause redirects
    let navigationTimeout;
    document.addEventListener('click', function(e) {
        // Don't show loading if clicking inside a form
        if (e.target.closest('form')) {
            return;
        }
        
        const link = e.target.closest('a[href]');
        if (link && link.href && !link.href.startsWith('javascript:') && !link.href.startsWith('#')) {
            const href = link.getAttribute('href');
            // Only show for internal links
            if (href && (!href.startsWith('http') || href.includes(window.location.hostname))) {
                clearTimeout(navigationTimeout);
                navigationTimeout = setTimeout(() => {
                    showLoading();
                }, 100);
            }
        }
    });
    
    // Handle browser back/forward
    window.addEventListener('popstate', function() {
        showLoading();
        setTimeout(hideLoading, 500);
    });
    
    // Show on form submissions (only for AJAX forms, not regular form submissions)
    document.addEventListener('submit', function(e) {
        const form = e.target;
        // Only show loading for AJAX forms or forms explicitly marked with data-ajax
        // Regular form submissions will cause a page reload, so we don't need to show loading
        if (form.tagName === 'FORM' && (form.dataset.ajax || form.hasAttribute('data-ajax'))) {
            showLoading();
        }
        // Don't show for regular form submissions - let the browser handle the navigation
    });
    
    // Handle AJAX requests
    let activeRequests = 0;
    
    // Intercept fetch requests
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        activeRequests++;
        if (activeRequests === 1) {
            showLoading();
        }
        
        return originalFetch.apply(this, args)
            .then(response => {
                activeRequests--;
                if (activeRequests === 0) {
                    setTimeout(hideLoading, 300);
                }
                return response;
            })
            .catch(error => {
                activeRequests--;
                if (activeRequests === 0) {
                    setTimeout(hideLoading, 300);
                }
                throw error;
            });
    };
    
    // Handle XMLHttpRequest
    const originalXHROpen = XMLHttpRequest.prototype.open;
    const originalXHRSend = XMLHttpRequest.prototype.send;
    
    XMLHttpRequest.prototype.open = function(...args) {
        this._loadingScreenActive = true;
        return originalXHROpen.apply(this, args);
    };
    
    XMLHttpRequest.prototype.send = function(...args) {
        if (this._loadingScreenActive && this.addEventListener) {
            activeRequests++;
            if (activeRequests === 1) {
                showLoading();
            }
            
            const handleComplete = () => {
                activeRequests--;
                if (activeRequests === 0) {
                    setTimeout(hideLoading, 300);
                }
            };
            
            this.addEventListener('load', handleComplete);
            this.addEventListener('error', handleComplete);
            this.addEventListener('abort', handleComplete);
        }
        
        return originalXHRSend.apply(this, args);
    };
    
    // Expose functions globally for manual control
    window.showLoadingScreen = showLoading;
    window.hideLoadingScreen = hideLoading;
})();
</script>

