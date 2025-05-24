/**
 * Quote Request JavaScript
 * File path: assets/js/quote-request.js
 *
 * Handles interactions for the quote request form
 */

document.addEventListener('DOMContentLoaded', function() {
    // File upload handling
    const fileInput = document.getElementById('design_files');
    const fileList = document.getElementById('file-list');
    const dropZone = document.querySelector('.border-dashed');

    if (fileInput && dropZone) {
        // Handle file input change
        fileInput.addEventListener('change', updateFileList);

        // Handle drag and drop events
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            dropZone.classList.add('border-primary-400');
            dropZone.classList.add('bg-primary-50');
        }

        function unhighlight() {
            dropZone.classList.remove('border-primary-400');
            dropZone.classList.remove('bg-primary-50');
        }

        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            updateFileList();
        }
    }

    function updateFileList() {
        if (!fileList) return;

        fileList.innerHTML = '';

        if (fileInput.files.length > 0) {
            const fileListContainer = document.createElement('div');
            fileListContainer.className = 'mt-3 space-y-2';

            Array.from(fileInput.files).forEach(file => {
                const fileItem = document.createElement('div');
                fileItem.className = 'flex items-center p-2 bg-gray-50 rounded';

                // File icon based on type
                let iconSvg;
                if (file.type.startsWith('image/')) {
                    iconSvg = '<svg class="h-5 w-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>';
                } else if (file.type === 'application/pdf') {
                    iconSvg = '<svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>';
                } else if (file.type === 'application/zip' || file.type === 'application/x-zip-compressed' || file.type === 'application/x-rar-compressed') {
                    iconSvg = '<svg class="h-5 w-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>';
                } else {
                    iconSvg = '<svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>';
                }

                // Determine if RTL is active
                const isRtl = document.documentElement.dir === 'rtl' || document.documentElement.lang === 'ar';
                const marginClass = isRtl ? 'ml-2' : 'mr-2';

                fileItem.innerHTML = `
                    <div class="${marginClass}">${iconSvg}</div>
                    <div class="flex-1 overflow-hidden">
                        <p class="text-sm font-medium truncate">${file.name}</p>
                        <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
                    </div>
                `;

                fileListContainer.appendChild(fileItem);
            });

            fileList.appendChild(fileListContainer);
        }
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';

        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Copy tracking link to clipboard
    const copyLinkButton = document.getElementById('copy-tracking-link');
    if (copyLinkButton) {
        copyLinkButton.addEventListener('click', function() {
            const trackingLink = document.getElementById('tracking-link');
            trackingLink.select();
            trackingLink.setSelectionRange(0, 99999); // For mobile devices

            document.execCommand('copy');

            // Show success message
            const copySuccess = document.getElementById('copy-success');
            if (copySuccess) {
                copySuccess.classList.remove('hidden');

                // Hide after 3 seconds
                setTimeout(function() {
                    copySuccess.classList.add('hidden');
                }, 3000);
            }
        });
    }
});
