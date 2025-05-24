/**
 * Quote Compare JavaScript
 * File path: assets/js/quote-compare.js
 * 
 * Handles interactions for the quote comparison page
 */

document.addEventListener('DOMContentLoaded', function() {
    // Mobile Quote Cards Scrolling Enhancement
    const mobileQuoteContainer = document.querySelector('.md\\:hidden .flex.overflow-x-auto');
    
    if (mobileQuoteContainer) {
        // Add snap scrolling for better mobile UX
        mobileQuoteContainer.classList.add('snap-x', 'snap-mandatory');
        
        // Add snap center to each quote card
        const quoteCards = mobileQuoteContainer.querySelectorAll('.min-w-[280px]');
        quoteCards.forEach(card => {
            card.classList.add('snap-center');
        });
        
        // Add scroll indicators if more than one quote
        if (quoteCards.length > 1) {
            // Create indicators container
            const indicatorsContainer = document.createElement('div');
            indicatorsContainer.className = 'flex justify-center space-x-2 mt-4';
            
            // Add dot indicator for each quote
            for (let i = 0; i < quoteCards.length; i++) {
                const indicator = document.createElement('div');
                indicator.className = 'h-2 w-2 rounded-full bg-gray-300 transition-colors';
                indicator.setAttribute('data-index', i);
                
                // Make first indicator active
                if (i === 0) {
                    indicator.classList.add('bg-primary-600');
                    indicator.classList.remove('bg-gray-300');
                }
                
                indicatorsContainer.appendChild(indicator);
            }
            
            // Add indicators after the scroll container
            mobileQuoteContainer.parentNode.insertBefore(indicatorsContainer, mobileQuoteContainer.nextSibling);
            
            // Update indicators on scroll
            let lastCheckedScrollPosition = -1;
            
            mobileQuoteContainer.addEventListener('scroll', function() {
                // Throttle the scroll event calculations
                window.requestAnimationFrame(() => {
                    const scrollPosition = mobileQuoteContainer.scrollLeft;
                    
                    // Only process if scroll position changed significantly
                    if (Math.abs(scrollPosition - lastCheckedScrollPosition) > 20 || scrollPosition === 0) {
                        lastCheckedScrollPosition = scrollPosition;
                        
                        // Calculate which card is most in view
                        const containerWidth = mobileQuoteContainer.offsetWidth;
                        const cardWidth = quoteCards[0].offsetWidth;
                        const activeIndex = Math.round(scrollPosition / cardWidth);
                        
                        // Update indicators
                        const indicators = indicatorsContainer.querySelectorAll('div');
                        indicators.forEach((dot, i) => {
                            if (i === activeIndex) {
                                dot.classList.add('bg-primary-600');
                                dot.classList.remove('bg-gray-300');
                            } else {
                                dot.classList.remove('bg-primary-600');
                                dot.classList.add('bg-gray-300');
                            }
                        });
                    }
                });
            });
            
            // Add click handler for indicators
            indicatorsContainer.addEventListener('click', function(e) {
                if (e.target.hasAttribute('data-index')) {
                    const index = parseInt(e.target.getAttribute('data-index'));
                    const cardWidth = quoteCards[0].offsetWidth;
                    
                    // Scroll to the corresponding card
                    mobileQuoteContainer.scrollTo({
                        left: index * cardWidth,
                        behavior: 'smooth'
                    });
                }
            });
        }
    }
    
    // Table row highlighting on hover (desktop)
    const comparisonTable = document.querySelector('.md\\:block table');
    if (comparisonTable) {
        const rows = comparisonTable.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                row.classList.add('bg-primary-50');
            });
            
            row.addEventListener('mouseleave', function() {
                row.classList.remove('bg-primary-50');
            });
        });
    }
});
