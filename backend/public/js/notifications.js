/**
 * نظام إدارة الإشعارات في لوحة التحكم
 */

class NotificationManager {
    constructor() {
        this.button = document.getElementById('notifications-button');
        this.dropdown = document.getElementById('notifications-dropdown');
        this.badge = document.getElementById('notifications-badge');
        this.list = document.getElementById('notifications-list');
        this.markAllReadBtn = document.getElementById('mark-all-read');
        this.clearAllBtn = document.getElementById('clear-all-notifications');
        
        this.isOpen = false;
        this.notifications = [];
        this.updateInterval = null;
        
        // الحصول على base URL من meta tag
        const baseUrlMeta = document.querySelector('meta[name="base-url"]');
        this.baseUrl = baseUrlMeta ? baseUrlMeta.content : window.location.origin;
        
        // التحقق من وجود العناصر
        if (!this.button || !this.dropdown || !this.badge || !this.list) {
            console.error('بعض عناصر الإشعارات غير موجودة في الصفحة');
            return;
        }
        
        this.init();
    }
    
    init() {
        // إغلاق القائمة عند الضغط خارجها
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#notifications-wrapper')) {
                this.closeDropdown();
            }
        });
        
        // فتح/إغلاق القائمة
        if (this.button) {
            this.button.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleDropdown();
            });
        }
        
        // تحديد الكل كمقروء
        if (this.markAllReadBtn) {
            this.markAllReadBtn.addEventListener('click', () => {
                this.markAllAsRead();
            });
        }
        
        // مسح جميع الإشعارات
        if (this.clearAllBtn) {
            this.clearAllBtn.addEventListener('click', () => {
                if (confirm('هل تريد حذف جميع الإشعارات؟')) {
                    this.clearAll();
                }
            });
        }
        
        // تحميل الإشعارات الأولي
        this.updateUnreadCount();
        
        // تحديث تلقائي كل 30 ثانية
        this.updateInterval = setInterval(() => {
            this.updateUnreadCount();
        }, 30000);
    }
    
    toggleDropdown() {
        if (this.isOpen) {
            this.closeDropdown();
        } else {
            this.openDropdown();
        }
    }
    
    openDropdown() {
        this.isOpen = true;
        this.dropdown.classList.remove('hidden');
        this.loadNotifications();
    }
    
    closeDropdown() {
        this.isOpen = false;
        this.dropdown.classList.add('hidden');
    }
    
    async loadNotifications() {
        try {
            const response = await fetch(`${this.baseUrl}/admin/notifications`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) {
                console.error('خطأ HTTP:', response.status, response.statusText);
                throw new Error(`فشل تحميل الإشعارات: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (!data.success) {
                throw new Error('استجابة غير صحيحة من الخادم');
            }
            
            this.notifications = data.notifications || [];
            this.renderNotifications();
            this.updateBadge(data.unread_count || 0);
        } catch (error) {
            console.error('خطأ في تحميل الإشعارات:', error);
            // لا نعرض خطأ للمستخدم في التحميل الأولي
        }
    }
    
    async updateUnreadCount() {
        try {
            const response = await fetch(`${this.baseUrl}/admin/notifications/unread-count`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) throw new Error('فشل تحديث العدد');
            
            const data = await response.json();
            this.updateBadge(data.count || 0);
        } catch (error) {
            console.error('خطأ في تحديث عدد الإشعارات:', error);
        }
    }
    
    renderNotifications() {
        if (this.notifications.length === 0) {
            this.list.innerHTML = `
                <div class="px-4 py-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <p>لا توجد إشعارات</p>
                </div>
            `;
            return;
        }
        
        this.list.innerHTML = this.notifications.map(n => this.renderNotification(n)).join('');
        
        // إضافة event listeners
        this.list.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', () => {
                const id = parseInt(item.dataset.id);
                const link = item.dataset.link;
                this.markAsRead(id);
                if (link) {
                    window.location.href = link;
                }
            });
            
            item.querySelector('.delete-notification')?.addEventListener('click', (e) => {
                e.stopPropagation();
                const id = parseInt(item.dataset.id);
                this.deleteNotification(id);
            });
        });
    }
    
    renderNotification(notification) {
        const bgColor = notification.is_read ? 'bg-white' : 'bg-blue-50';
        const iconColor = this.getIconColor(notification.color);
        
        return `
            <div class="notification-item ${bgColor} px-4 py-3 hover:bg-gray-50 cursor-pointer transition-colors duration-150 relative group" 
                 data-id="${notification.id}" 
                 data-link="${notification.link || ''}">
                <div class="flex items-start space-x-3 space-x-reverse">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full ${iconColor} flex items-center justify-center">
                            ${this.getIcon(notification.icon)}
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">
                            ${notification.title}
                        </p>
                        <p class="text-sm text-gray-600 mt-1">
                            ${notification.message}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            ${notification.time_ago}
                        </p>
                    </div>
                    <button class="delete-notification opacity-0 group-hover:opacity-100 transition-opacity p-1 hover:bg-red-100 rounded" title="حذف">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;
    }
    
    getIcon(iconName) {
        const icons = {
            'newspaper': '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>',
            'clock': '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'check-circle': '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'x-circle': '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'edit': '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>',
            'bell': '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>',
        };
        return icons[iconName] || icons['bell'];
    }
    
    getIconColor(color) {
        const colors = {
            'blue': 'bg-blue-500',
            'yellow': 'bg-yellow-500',
            'green': 'bg-green-500',
            'red': 'bg-red-500',
            'purple': 'bg-purple-500',
            'indigo': 'bg-indigo-500',
            'teal': 'bg-teal-500',
            'gray': 'bg-gray-500',
        };
        return colors[color] || colors['gray'];
    }
    
    updateBadge(count) {
        if (count > 0) {
            this.badge.textContent = count > 99 ? '99+' : count;
            this.badge.classList.remove('hidden');
            this.badge.classList.add('flex');
        } else {
            this.badge.classList.add('hidden');
            this.badge.classList.remove('flex');
        }
    }
    
    async markAsRead(id) {
        try {
            const response = await fetch(`${this.baseUrl}/admin/notifications/${id}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) throw new Error('فشل التحديد كمقروء');
            
            // تحديث الحالة محلياً
            const notification = this.notifications.find(n => n.id === id);
            if (notification) {
                notification.is_read = true;
            }
            
            this.updateUnreadCount();
        } catch (error) {
            console.error('خطأ في تحديد الإشعار:', error);
        }
    }
    
    async markAllAsRead() {
        try {
            const response = await fetch(`${this.baseUrl}/admin/notifications/mark-all-as-read`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) throw new Error('فشل التحديد');
            
            this.loadNotifications();
        } catch (error) {
            console.error('خطأ في تحديد جميع الإشعارات:', error);
            this.showError('فشل تحديد جميع الإشعارات');
        }
    }
    
    async deleteNotification(id) {
        try {
            const response = await fetch(`${this.baseUrl}/admin/notifications/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) throw new Error('فشل الحذف');
            
            this.loadNotifications();
        } catch (error) {
            console.error('خطأ في حذف الإشعار:', error);
            this.showError('فشل حذف الإشعار');
        }
    }
    
    async clearAll() {
        try {
            const response = await fetch(`${this.baseUrl}/admin/notifications`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) throw new Error('فشل المسح');
            
            this.loadNotifications();
            this.closeDropdown();
        } catch (error) {
            console.error('خطأ في مسح الإشعارات:', error);
            this.showError('فشل مسح الإشعارات');
        }
    }
    
    showError(message) {
        // يمكنك استخدام notification system آخر هنا
        alert(message);
    }
    
    destroy() {
        if (this.updateInterval) {
            clearInterval(this.updateInterval);
        }
    }
}

// تهيئة عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', () => {
    window.notificationManager = new NotificationManager();
});
