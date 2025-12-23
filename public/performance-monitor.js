// Performance monitoring script for LENLAB homepage
(function() {
    'use strict';
    
    // Performance metrics collection
    const performanceMetrics = {
        pageLoadStart: performance.now(),
        domContentLoaded: null,
        windowLoaded: null,
        firstPaint: null,
        firstContentfulPaint: null,
        largestContentfulPaint: null,
        cumulativeLayoutShift: 0,
        firstInputDelay: null
    };
    
    // Collect Core Web Vitals
    function collectWebVitals() {
        // First Paint and First Contentful Paint
        const paintEntries = performance.getEntriesByType('paint');
        paintEntries.forEach(entry => {
            if (entry.name === 'first-paint') {
                performanceMetrics.firstPaint = entry.startTime;
            } else if (entry.name === 'first-contentful-paint') {
                performanceMetrics.firstContentfulPaint = entry.startTime;
            }
        });
        
        // Largest Contentful Paint
        if ('PerformanceObserver' in window) {
            const lcpObserver = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                const lastEntry = entries[entries.length - 1];
                performanceMetrics.largestContentfulPaint = lastEntry.startTime;
            });
            lcpObserver.observe({ entryTypes: ['largest-contentful-paint'] });
            
            // Cumulative Layout Shift
            const clsObserver = new PerformanceObserver((list) => {
                for (const entry of list.getEntries()) {
                    if (!entry.hadRecentInput) {
                        performanceMetrics.cumulativeLayoutShift += entry.value;
                    }
                }
            });
            clsObserver.observe({ entryTypes: ['layout-shift'] });
            
            // First Input Delay
            const fidObserver = new PerformanceObserver((list) => {
                for (const entry of list.getEntries()) {
                    performanceMetrics.firstInputDelay = entry.processingStart - entry.startTime;
                }
            });
            fidObserver.observe({ entryTypes: ['first-input'] });
        }
    }
    
    // Image loading performance
    function monitorImageLoading() {
        const images = document.querySelectorAll('img');
        let loadedImages = 0;
        let totalImages = images.length;
        
        images.forEach((img, index) => {
            const startTime = performance.now();
            
            img.addEventListener('load', function() {
                loadedImages++;
                const loadTime = performance.now() - startTime;
                
                console.log(`Image ${index + 1}/${totalImages} loaded in ${loadTime.toFixed(2)}ms`);
                
                if (loadedImages === totalImages) {
                    console.log('All images loaded successfully');
                }
            });
            
            img.addEventListener('error', function() {
                console.warn(`Failed to load image: ${img.src}`);
            });
        });
    }
    
    // Network performance monitoring
    function monitorNetworkRequests() {
        if ('PerformanceObserver' in window) {
            const resourceObserver = new PerformanceObserver((list) => {
                for (const entry of list.getEntries()) {
                    if (entry.initiatorType === 'img' && entry.duration > 1000) {
                        console.warn(`Slow image load: ${entry.name} took ${entry.duration.toFixed(2)}ms`);
                    }
                }
            });
            resourceObserver.observe({ entryTypes: ['resource'] });
        }
    }
    
    // Memory usage monitoring
    function monitorMemoryUsage() {
        if ('memory' in performance) {
            const memory = performance.memory;
            console.log('Memory usage:', {
                used: `${(memory.usedJSHeapSize / 1048576).toFixed(2)} MB`,
                total: `${(memory.totalJSHeapSize / 1048576).toFixed(2)} MB`,
                limit: `${(memory.jsHeapSizeLimit / 1048576).toFixed(2)} MB`
            });
        }
    }
    
    // Report performance metrics
    function reportMetrics() {
        console.group('🚀 LENLAB Performance Report');
        console.log('Page Load Time:', `${(performanceMetrics.windowLoaded - performanceMetrics.pageLoadStart).toFixed(2)}ms`);
        console.log('DOM Content Loaded:', `${performanceMetrics.domContentLoaded.toFixed(2)}ms`);
        
        if (performanceMetrics.firstPaint) {
            console.log('First Paint:', `${performanceMetrics.firstPaint.toFixed(2)}ms`);
        }
        
        if (performanceMetrics.firstContentfulPaint) {
            console.log('First Contentful Paint:', `${performanceMetrics.firstContentfulPaint.toFixed(2)}ms`);
        }
        
        if (performanceMetrics.largestContentfulPaint) {
            console.log('Largest Contentful Paint:', `${performanceMetrics.largestContentfulPaint.toFixed(2)}ms`);
            
            // LCP scoring
            if (performanceMetrics.largestContentfulPaint <= 2500) {
                console.log('✅ LCP Score: Good');
            } else if (performanceMetrics.largestContentfulPaint <= 4000) {
                console.log('⚠️ LCP Score: Needs Improvement');
            } else {
                console.log('❌ LCP Score: Poor');
            }
        }
        
        if (performanceMetrics.cumulativeLayoutShift !== null) {
            console.log('Cumulative Layout Shift:', performanceMetrics.cumulativeLayoutShift.toFixed(4));
            
            // CLS scoring
            if (performanceMetrics.cumulativeLayoutShift <= 0.1) {
                console.log('✅ CLS Score: Good');
            } else if (performanceMetrics.cumulativeLayoutShift <= 0.25) {
                console.log('⚠️ CLS Score: Needs Improvement');
            } else {
                console.log('❌ CLS Score: Poor');
            }
        }
        
        if (performanceMetrics.firstInputDelay !== null) {
            console.log('First Input Delay:', `${performanceMetrics.firstInputDelay.toFixed(2)}ms`);
            
            // FID scoring
            if (performanceMetrics.firstInputDelay <= 100) {
                console.log('✅ FID Score: Good');
            } else if (performanceMetrics.firstInputDelay <= 300) {
                console.log('⚠️ FID Score: Needs Improvement');
            } else {
                console.log('❌ FID Score: Poor');
            }
        }
        
        console.groupEnd();
        
        // Memory usage
        monitorMemoryUsage();
    }
    
    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        performanceMetrics.domContentLoaded = performance.now() - performanceMetrics.pageLoadStart;
        collectWebVitals();
        monitorImageLoading();
        monitorNetworkRequests();
    });
    
    window.addEventListener('load', function() {
        performanceMetrics.windowLoaded = performance.now();
        
        // Report metrics after a short delay to ensure all measurements are captured
        setTimeout(reportMetrics, 1000);
    });
    
    // Expose metrics for debugging
    window.LENLAB_PERFORMANCE = performanceMetrics;
    
})();