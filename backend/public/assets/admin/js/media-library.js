/**
 * Media Library JavaScript (WordPress Style)
 * Handles media upload, selection, and management
 */

class MediaLibrary {
    constructor() {
        this.config = {};
        this.selectedMedia = new Set();
        this.currentMode = 'select';
        this.multiple = false;
        this.targetField = '';
        this.uploadQueue = [];
        this.translations = {};
    }

    init(config) {
        this.config = {
            uploadUrl: '/admin/media/upload',
            csrfToken: '',
            maxFileSize: 10 * 1024 * 1024, // 10MB
            allowedTypes: ['image/*', 'video/*', 'audio/*', '.pdf', '.doc', '.docx'],
            ...config
        };

        this.translations = config.translations || {};
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Upload area events
        this.setupUploadArea();
        
        // Mode switching
        document.addEventListener('change', (e) => {
            if (e.target.name === 'picker-mode') {
                this.switchMode(e.target.value);
            }
        });

        // Search and filters
        this.setupFilters();
        
        // Insert button
        const insertBtn = document.getElementById('picker-insert-button');
        if (insertBtn) {
            insertBtn.addEventListener('click', () => this.insertSelectedMedia());
        }
    }

    setupUploadArea() {
        const uploadArea = document.getElementById('picker-upload-area');
        const fileInput = document.getElementById('picker-file-input');
        
        if (!uploadArea || !fileInput) return;

        // Drag and drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            const files = Array.from(e.dataTransfer.files);
            this.handleFileUpload(files);
        });

        // File input change
        fileInput.addEventListener('change', (e) => {
            const files = Array.from(e.target.files);
            this.handleFileUpload(files);
        });
    }

    setupFilters() {
        const searchInput = document.getElementById('picker-search');
        const typeFilter = document.getElementById('picker-type-filter');
        const collectionFilter = document.getElementById('picker-collection-filter');
        const dateFilter = document.getElementById('picker-date-filter');

        // Search with debounce
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.filterMedia();
                }, 300);
            });
        }

        // Filter changes
        [typeFilter, collectionFilter, dateFilter].forEach(filter => {
            if (filter) {
                filter.addEventListener('change', () => this.filterMedia());
            }
        });
    }

    switchMode(mode) {
        this.currentMode = mode;
        
        const uploadTab = document.getElementById('upload-tab');
        const libraryTab = document.getElementById('library-tab');
        
        if (mode === 'upload') {
            uploadTab.style.display = 'block';
            libraryTab.style.display = 'none';
        } else {
            uploadTab.style.display = 'none';
            libraryTab.style.display = 'block';
        }
    }

    async handleFileUpload(files) {
        if (!files.length) return;

        const progressContainer = document.getElementById('picker-upload-progress');
        const progressBar = progressContainer.querySelector('.progress-bar');
        const statusDiv = progressContainer.querySelector('.upload-status');
        
        progressContainer.style.display = 'block';
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            
            // Validate file
            if (!this.validateFile(file)) continue;
            
            try {
                statusDiv.textContent = `جاري رفع ${file.name}...`;
                const result = await this.uploadFile(file, (progress) => {
                    progressBar.style.width = `${progress}%`;
                });
                
                if (result.success) {
                    this.showUploadSuccess(result.media);
                    this.refreshMediaGrid();
                } else {
                    this.showError(result.message || 'خطأ في رفع الملف');
                }
            } catch (error) {
                this.showError(`خطأ في رفع ${file.name}: ${error.message}`);
            }
        }
        
        progressContainer.style.display = 'none';
        progressBar.style.width = '0%';
    }

    validateFile(file) {
        // Check file size
        if (file.size > this.config.maxFileSize) {
            this.showError(`الملف ${file.name} كبير جداً. الحد الأقصى ${this.formatFileSize(this.config.maxFileSize)}`);
            return false;
        }

        // Check file type
        const isAllowed = this.config.allowedTypes.some(type => {
            if (type.includes('*')) {
                return file.type.startsWith(type.replace('*', ''));
            }
            return file.name.toLowerCase().endsWith(type);
        });

        if (!isAllowed) {
            this.showError(`نوع الملف ${file.name} غير مدعوم`);
            return false;
        }

        return true;
    }

    async uploadFile(file, onProgress) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('collection', 'library');
        formData.append('_token', this.config.csrfToken);

        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            
            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const progress = (e.loaded / e.total) * 100;
                    onProgress(progress);
                }
            });

            xhr.addEventListener('load', () => {
                try {
                    const response = JSON.parse(xhr.responseText);
                    resolve(response);
                } catch (error) {
                    reject(new Error('خطأ في تحليل الاستجابة'));
                }
            });

            xhr.addEventListener('error', () => {
                reject(new Error('خطأ في الشبكة'));
            });

            xhr.open('POST', this.config.uploadUrl);
            xhr.send(formData);
        });
    }

    showUploadSuccess(media) {
        const recentlyUploaded = document.getElementById('recently-uploaded');
        const uploadedList = recentlyUploaded.querySelector('.uploaded-list');
        
        const item = document.createElement('div');
        item.className = 'uploaded-item d-flex align-items-center p-2 border rounded mb-2 bg-white';
        item.innerHTML = `
            <img src="${media.thumbnail_url || media.url}" alt="${media.name}" 
                 style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;" class="me-2">
            <div class="flex-grow-1">
                <div class="fw-bold small">${media.name}</div>
                <div class="text-muted small">${media.human_readable_size}</div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectUploadedMedia(${media.id})">
                اختيار
            </button>
        `;
        
        uploadedList.appendChild(item);
        recentlyUploaded.style.display = 'block';
        
        this.showSuccess(this.translations.uploadSuccess || 'تم رفع الملف بنجاح');
    }

    selectMediaItem(mediaId) {
        const item = document.querySelector(`[data-media-id="${mediaId}"]`);
        if (!item) return;

        const mediaItem = item.querySelector('.picker-media-item');
        
        if (this.multiple) {
            // Multiple selection
            if (this.selectedMedia.has(mediaId)) {
                this.selectedMedia.delete(mediaId);
                mediaItem.classList.remove('selected');
            } else {
                this.selectedMedia.add(mediaId);
                mediaItem.classList.add('selected');
            }
        } else {
            // Single selection
            document.querySelectorAll('.picker-media-item.selected').forEach(el => {
                el.classList.remove('selected');
            });
            
            this.selectedMedia.clear();
            this.selectedMedia.add(mediaId);
            mediaItem.classList.add('selected');
        }
        
        this.updateSelectedInfo();
    }

    updateSelectedInfo() {
        const count = this.selectedMedia.size;
        const countSpan = document.getElementById('picker-selected-count');
        const insertBtn = document.getElementById('picker-insert-button');
        const selectedInfo = document.getElementById('selected-info');
        
        if (countSpan) countSpan.textContent = count;
        if (insertBtn) insertBtn.disabled = count === 0;
        
        if (count > 0) {
            selectedInfo.style.display = 'block';
            this.updateSelectedPreview();
        } else {
            selectedInfo.style.display = 'none';
        }
    }

    updateSelectedPreview() {
        const preview = document.querySelector('.selected-preview');
        if (!preview) return;
        
        preview.innerHTML = '';
        
        this.selectedMedia.forEach(mediaId => {
            const mediaData = this.getMediaData(mediaId);
            if (!mediaData) return;
            
            const item = document.createElement('div');
            item.className = 'selected-item';
            item.innerHTML = `
                <img src="${mediaData.thumbnail_url || mediaData.url}" alt="${mediaData.name}">
                <div class="item-info">
                    <div class="item-name">${mediaData.name}</div>
                    <div class="item-size">${mediaData.human_readable_size}</div>
                </div>
                <div class="remove-btn" onclick="MediaLibraryInstance.removeFromSelection(${mediaId})">
                    <i class="fas fa-times"></i>
                </div>
            `;
            preview.appendChild(item);
        });
    }

    removeFromSelection(mediaId) {
        this.selectedMedia.delete(mediaId);
        const mediaItem = document.querySelector(`[data-media-id="${mediaId}"] .picker-media-item`);
        if (mediaItem) {
            mediaItem.classList.remove('selected');
        }
        this.updateSelectedInfo();
    }

    async filterMedia() {
        const search = document.getElementById('picker-search')?.value || '';
        const type = document.getElementById('picker-type-filter')?.value || 'all';
        const collection = document.getElementById('picker-collection-filter')?.value || 'all';
        const date = document.getElementById('picker-date-filter')?.value || 'all';
        
        const loading = document.getElementById('picker-loading');
        const container = document.getElementById('media-items-container');
        
        loading.classList.remove('d-none');
        
        try {
            const params = new URLSearchParams({
                search,
                type,
                collection,
                date,
                ajax: 1
            });
            
            const response = await fetch(`/admin/media?${params}`);
            const data = await response.json();
            
            if (data.media && data.media.data) {
                this.renderMediaItems(data.media.data);
            }
        } catch (error) {
            this.showError('خطأ في تحميل البيانات');
        } finally {
            loading.classList.add('d-none');
        }
    }

    renderMediaItems(mediaItems) {
        const container = document.getElementById('media-items-container');
        if (!container) return;
        
        container.innerHTML = '';
        
        if (mediaItems.length === 0) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد نتائج</h5>
                        <p class="text-muted">جرب تعديل معايير البحث</p>
                    </div>
                </div>
            `;
            return;
        }
        
        mediaItems.forEach(item => {
            const col = document.createElement('div');
            col.className = 'col-xl-2 col-lg-3 col-md-4 col-sm-6';
            col.setAttribute('data-media-id', item.id);
            
            let thumbnail = '';
            if (item.mime_type.startsWith('image/')) {
                thumbnail = `<img src="${item.thumbnail_url || item.url}" alt="${item.name}" loading="lazy">`;
            } else if (item.mime_type.startsWith('video/')) {
                thumbnail = `<div class="media-icon bg-dark"><i class="fas fa-play-circle text-white fa-2x"></i></div>`;
            } else if (item.mime_type.startsWith('audio/')) {
                thumbnail = `<div class="media-icon bg-info"><i class="fas fa-music text-white fa-2x"></i></div>`;
            } else {
                thumbnail = `<div class="media-icon bg-secondary"><i class="fas fa-file-alt text-white fa-2x"></i></div>`;
            }
            
            col.innerHTML = `
                <div class="picker-media-item ${this.selectedMedia.has(item.id) ? 'selected' : ''}" onclick="MediaLibraryInstance.selectMediaItem(${item.id})">
                    <div class="picker-media-thumbnail">
                        ${thumbnail}
                        <div class="selection-checkmark">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="picker-media-info p-2">
                        <div class="media-name small fw-bold text-truncate" title="${item.name}">
                            ${item.name}
                        </div>
                        <div class="media-size small text-muted">
                            ${item.human_readable_size}
                        </div>
                    </div>
                </div>
            `;
            
            container.appendChild(col);
        });
    }

    insertSelectedMedia() {
        if (this.selectedMedia.size === 0) return;
        
        const selectedData = Array.from(this.selectedMedia).map(id => this.getMediaData(id)).filter(Boolean);
        
        // Trigger custom event with selected media data
        window.dispatchEvent(new CustomEvent('mediaSelected', {
            detail: {
                media: selectedData,
                multiple: this.multiple,
                field: this.targetField
            }
        }));
        
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('mediaPickerModal'));
        if (modal) {
            modal.hide();
        }
    }

    getMediaData(mediaId) {
        // This should return media data from cache or fetch it
        // For now, extract from DOM
        const item = document.querySelector(`[data-media-id="${mediaId}"]`);
        if (!item) return null;
        
        const name = item.querySelector('.media-name')?.textContent || '';
        const size = item.querySelector('.media-size')?.textContent || '';
        const img = item.querySelector('img');
        
        return {
            id: mediaId,
            name: name,
            human_readable_size: size,
            url: img?.src || '',
            thumbnail_url: img?.src || ''
        };
    }

    refreshMediaGrid() {
        // Refresh the media grid after upload
        this.filterMedia();
    }

    // Helper methods
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    showError(message) {
        this.showNotification(message, 'error');
    }

    showNotification(message, type = 'info') {
        // Create a simple toast notification
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(toast);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 5000);
    }
}

// Global instance
const MediaLibraryInstance = new MediaLibrary();

// Global functions for onclick handlers
window.selectMediaItem = (mediaId) => MediaLibraryInstance.selectMediaItem(mediaId);
window.selectUploadedMedia = (mediaId) => MediaLibraryInstance.selectMediaItem(mediaId);

// Media Picker functions
window.openMediaPicker = function(options = {}) {
    const defaults = {
        multiple: false,
        field: 'image',
        collection: 'all',
        onSelect: null
    };
    
    const config = { ...defaults, ...options };
    
    // Set picker configuration
    MediaLibraryInstance.multiple = config.multiple;
    MediaLibraryInstance.targetField = config.field;
    MediaLibraryInstance.selectedMedia.clear();
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('mediaPickerModal'));
    modal.show();
    
    // Handle selection
    if (config.onSelect) {
        const handler = (e) => {
            config.onSelect(e.detail.media);
            window.removeEventListener('mediaSelected', handler);
        };
        window.addEventListener('mediaSelected', handler);
    }
};

// Bulk actions for main library
window.bulkDelete = function() {
    const selected = Array.from(selectedMedia || selectedMediaList || []);
    if (selected.length === 0) {
        alert('يرجى اختيار ملفات للحذف');
        return;
    }
    
    if (!confirm(`هل أنت متأكد من حذف ${selected.length} ملف؟`)) {
        return;
    }
    
    // Implement bulk delete
    fetch('/admin/media/bulk-action', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            action: 'delete',
            media_ids: selected
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('خطأ في حذف الملفات');
        }
    });
};

window.copyToClipboard = function(text) {
    navigator.clipboard.writeText(text).then(() => {
        MediaLibraryInstance.showSuccess('تم نسخ الرابط');
    });
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Auto-initialize if config is available
    if (typeof window.MediaLibraryConfig !== 'undefined') {
        MediaLibraryInstance.init(window.MediaLibraryConfig);
    }
});
