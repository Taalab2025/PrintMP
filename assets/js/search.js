/**
 * Search Functionality JavaScript
 * 
 * File path: assets/js/search.js
 * 
 * Handles search functionality including suggestions dropdown
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get search elements
    const searchForms = document.querySelectorAll('.search-form');
    const globalSearchInput = document.getElementById('globalSearchInput');
    const suggestionDropdown = document.getElementById('searchSuggestions');
    const searchResults = document.getElementById('searchResults');
    const searchLoader = document.getElementById('searchLoader');
    
    // Only initialize if the search elements exist
    if (searchForms.length > 0) {
        // Set up search forms
        searchForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const searchInput = form.querySelector('input[type="text"]');
                if (!searchInput || searchInput.value.trim().length < 2) {
                    e.preventDefault();
                }
            });
        });
        
        // Set up live search if global search input exists
        if (globalSearchInput && suggestionDropdown) {
            initLiveSearch(globalSearchInput, suggestionDropdown, searchResults, searchLoader);
        }
    }
});

/**
 * Initialize live search functionality
 * 
 * @param {HTMLElement} input Search input element
 * @param {HTMLElement} dropdown Suggestion dropdown element
 * @param {HTMLElement} resultsContainer Search results container
 * @param {HTMLElement} loader Search loader element
 */
function initLiveSearch(input, dropdown, resultsContainer, loader) {
    let searchTimeout = null;
    let currentQuery = '';
    
    // Add event listeners
    input.addEventListener('input', handleSearchInput);
    input.addEventListener('focus', showSuggestions);
    document.addEventListener('click', function(e) {
        if (!dropdown.contains(e.target) && e.target !== input) {
            hideSuggestions();
        }
    });
    
    /**
     * Handle search input
     */
    function handleSearchInput() {
        const query = input.value.trim();
        
        // Show loader when typing
        if (loader) loader.classList.remove('hidden');
        
        // Clear previous timeout
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }
        
        // Hide dropdown if query is empty
        if (query.length < 2) {
            dropdown.classList.add('hidden');
            if (loader) loader.classList.add('hidden');
            return;
        }
        
        // Set timeout to avoid too many requests
        searchTimeout = setTimeout(() => {
            if (query === currentQuery) return;
            currentQuery = query;
            
            // Fetch suggestions
            fetchSuggestions(query);
        }, 300);
    }
    
    /**
     * Fetch search suggestions from API
     * 
     * @param {string} query Search query
     */
    function fetchSuggestions(query) {
        fetch(`/search/suggestions?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                renderSuggestions(data, query);
                if (loader) loader.classList.add('hidden');
            })
            .catch(error => {
                console.error('Error fetching suggestions:', error);
                dropdown.classList.add('hidden');
                if (loader) loader.classList.add('hidden');
            });
    }
    
    /**
     * Render search suggestions
     * 
     * @param {Object} data Suggestions data
     * @param {string} query Search query
     */
    function renderSuggestions(data, query) {
        if (!data.suggestions || data.suggestions.length === 0) {
            dropdown.innerHTML = `
                <div class="p-4 text-center text-gray-500">
                    <p>${translations.search.no_suggestions}</p>
                </div>
            `;
            dropdown.classList.remove('hidden');
            return;
        }
        
        // Group suggestions by type
        const serviceResults = data.suggestions.filter(item => item.type === 'service');
        const vendorResults = data.suggestions.filter(item => item.type === 'vendor');
        const categoryResults = data.suggestions.filter(item => item.type === 'category');
        
        // Build HTML
        let html = '';
        
        // Services section
        if (serviceResults.length > 0) {
            html += `
                <div class="py-2">
                    <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        ${translations.search.services}
                    </h3>
                    <ul>
            `;
            
            serviceResults.forEach(service => {
                html += `
                    <li>
                        <a href="${service.url}" class="flex items-center px-4 py-2 hover:bg-gray-100">
                            ${service.image ? `
                                <div class="w-10 h-10 rounded overflow-hidden bg-gray-200 flex-shrink-0">
                                    <img src="${service.image}" alt="${service.title}" class="w-full h-full object-cover">
                                </div>
                            ` : `
                                <div class="w-10 h-10 rounded bg-gray-200 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-print text-gray-400"></i>
                                </div>
                            `}
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">${service.title}</p>
                                <p class="text-xs text-gray-500">
                                    ${service.category ? service.category + ' • ' : ''}
                                    ${formatCurrency(service.price)}
                                </p>
                            </div>
                        </a>
                    </li>
                `;
            });
            
            html += `
                    </ul>
                </div>
            `;
        }
        
        // Vendors section
        if (vendorResults.length > 0) {
            html += `
                <div class="py-2 ${serviceResults.length > 0 ? 'border-t border-gray-200' : ''}">
                    <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        ${translations.search.vendors}
                    </h3>
                    <ul>
            `;
            
            vendorResults.forEach(vendor => {
                html += `
                    <li>
                        <a href="${vendor.url}" class="flex items-center px-4 py-2 hover:bg-gray-100">
                            ${vendor.image ? `
                                <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-200 flex-shrink-0">
                                    <img src="${vendor.image}" alt="${vendor.title}" class="w-full h-full object-cover">
                                </div>
                            ` : `
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-store text-gray-400"></i>
                                </div>
                            `}
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">${vendor.title}</p>
                                <p class="text-xs text-gray-500">
                                    ${vendor.service_count} ${translations.search.services}
                                    ${vendor.rating ? ` • ${renderStars(vendor.rating)}` : ''}
                                </p>
                            </div>
                        </a>
                    </li>
                `;
            });
            
            html += `
                    </ul>
                </div>
            `;
        }
        
        // Categories section
        if (categoryResults.length > 0) {
            html += `
                <div class="py-2 ${serviceResults.length > 0 || vendorResults.length > 0 ? 'border-t border-gray-200' : ''}">
                    <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        ${translations.search.categories}
                    </h3>
                    <ul>
            `;
            
            categoryResults.forEach(category => {
                html += `
                    <li>
                        <a href="${category.url}" class="flex items-center px-4 py-2 hover:bg-gray-100">
                            <div class="w-10 h-10 rounded bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <i class="${category.icon || 'fas fa-folder'} text-blue-500"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">${category.title}</p>
                            </div>
                        </a>
                    </li>
                `;
            });
            
            html += `
                    </ul>
                </div>
            `;
        }
        
        // View all results
        const totalResults = data.totalResults ? 
            (data.totalResults.services || 0) + (data.totalResults.vendors || 0) : 
            data.suggestions.length;
            
        if (totalResults > data.suggestions.length) {
            html += `
                <div class="py-3 px-4 border-t border-gray-200">
                    <a href="/search?q=${encodeURIComponent(query)}" class="block text-center text-sm text-blue-600 hover:text-blue-800">
                        ${translations.search.view_all_results.replace(':count', totalResults)}
                    </a>
                </div>
            `;
        }
        
        dropdown.innerHTML = html;
        dropdown.classList.remove('hidden');
    }
    
    /**
     * Show suggestions dropdown
     */
    function showSuggestions() {
        if (currentQuery.length >= 2) {
            dropdown.classList.remove('hidden');
        }
    }
    
    /**
     * Hide suggestions dropdown
     */
    function hideSuggestions() {
        dropdown.classList.add('hidden');
    }
    
    /**
     * Format currency value
     * 
     * @param {number} price Price value
     * @return {string} Formatted price
     */
    function formatCurrency(price) {
        if (!price) return '';
        
        return new Intl.NumberFormat(document.documentElement.lang, {
            style: 'currency',
            currency: 'EGP',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(price);
    }
    
    /**
     * Render star rating
     * 
     * @param {number} rating Rating value
     * @return {string} HTML for star rating
     */
    function renderStars(rating) {
        if (!rating) return '';
        
        const roundedRating = Math.round(rating * 2) / 2; // Round to nearest 0.5
        let html = '';
        
        for (let i = 1; i <= 5; i++) {
            if (roundedRating >= i) {
                html += '<i class="fas fa-star text-yellow-400"></i>';
            } else if (roundedRating >= i - 0.5) {
                html += '<i class="fas fa-star-half-alt text-yellow-400"></i>';
            } else {
                html += '<i class="far fa-star text-yellow-400"></i>';
            }
        }
        
        return html;
    }
}

// Global translations object - populated by the server
const translations = {
    search: {
        no_suggestions: 'No suggestions found',
        services: 'Services',
        vendors: 'Vendors',
        categories: 'Categories',
        view_all_results: 'View all :count results'
    }
};
