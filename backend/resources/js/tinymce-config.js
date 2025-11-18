import tinymce from 'tinymce/tinymce';

// Import TinyMCE essentials
import 'tinymce/models/dom';
import 'tinymce/themes/silver';
import 'tinymce/icons/default';

// Import skins
import 'tinymce/skins/ui/oxide/skin.css';
import 'tinymce/skins/ui/oxide/content.css';
import 'tinymce/skins/content/default/content.css';

// Import only essential plugins (باقي الـ plugins تحمّل عند الطلب)
import 'tinymce/plugins/lists';
import 'tinymce/plugins/link';
import 'tinymce/plugins/image';
import 'tinymce/plugins/directionality';
import 'tinymce/plugins/code';
import 'tinymce/plugins/fullscreen';

/**
 * Helper: الحصول على المسار الديناميكي
 */
function getAdminMediaUploadUrl() {
    const baseUrl = window.location.origin;
    const pathPrefix = window.location.pathname.replace(/\/admin\/.*$/, '');
    return `${baseUrl}${pathPrefix}/admin/media/upload`;
}

/**
 * تهيئة محرر TinyMCE مع دعم رفع الصور
 */
export function initTinyMCE(selector = '#content') {
    tinymce.init({
        selector: selector,
        
        // License - استخدام النسخة المجانية
        license_key: 'gpl',
        
        // تعطيل البحث عن skins (تم تضمينها في Bundle)
        skin: false,
        content_css: false,
        
        // الارتفاع
        height: 500,
        
        // الاتجاه (RTL للعربية)
        directionality: 'rtl',
        
        // Plugins الأساسية فقط
        plugins: [
            'lists', 'link', 'image', 'code', 'fullscreen', 'directionality'
        ],
        
        // شريط أدوات مبسّط وسريع
        toolbar: 'undo redo | blocks | bold italic underline | forecolor backcolor | ' +
                 'alignleft aligncenter alignright alignjustify | ' +
                 'bullist numlist | link image multiimage | code fullscreen | ltr rtl',
        
        // إعدادات الفقرات
        forced_root_block: 'p',
        forced_root_block_attrs: {
            'class': ''
        },
        block_formats: 'فقرة=p; عنوان 1=h1; عنوان 2=h2; عنوان 3=h3; عنوان 4=h4; عنوان 5=h5; عنوان 6=h6',
        
        // Enter يُنشئ فقرة جديدة، Shift+Enter لكسر السطر
        newline_behavior: 'block',
        
        // تحسين التجربة
        paste_as_text: false,
        paste_data_images: true,
        paste_webkit_styles: 'all',
        paste_retain_style_properties: 'all',
        
        // Link settings
        link_context_toolbar: true,
        link_title: false,
        target_list: false,
        link_assume_external_targets: true,
        
        // إعدادات الصور
        image_advtab: false,
        image_title: true,
        image_description: true,
        image_dimensions: true,
        
        // خيارات المحتوى مع تنسيق الفقرات
        content_style: `
            body { 
                font-family: 'Segoe UI', Tahoma, Arial, sans-serif; 
                font-size: 16px; 
                line-height: 1.6;
                direction: rtl;
                text-align: right;
                padding: 20px;
                color: #333;
            }
            p {
                margin-bottom: 0.8em;
                margin-top: 0;
                line-height: 1.6;
            }
            h1, h2, h3, h4, h5, h6 {
                margin-top: 1em;
                margin-bottom: 0.5em;
                line-height: 1.3;
                font-weight: bold;
                color: #2c3e50;
            }
            h1 { font-size: 2em; }
            h2 { font-size: 1.75em; }
            h3 { font-size: 1.5em; }
            h4 { font-size: 1.3em; }
            h5 { font-size: 1.15em; }
            h6 { font-size: 1.05em; }
            ul, ol {
                margin: 0.5em 0;
                padding-right: 2em;
            }
            li {
                margin-bottom: 0.3em;
            }
            blockquote {
                border-right: 4px solid #e0e0e0;
                padding-right: 1em;
                margin: 0.8em 0;
                color: #666;
                font-style: italic;
            }
            img {
                max-width: 100%;
                height: auto;
                margin: 0.5em 0;
                border-radius: 4px;
            }
            table {
                border-collapse: collapse;
                width: 100%;
                margin: 0.5em 0;
            }
            table td, table th {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: right;
            }
        `,
        
        // إعدادات الصور - رفع مباشر فقط
        automatic_uploads: true,
        file_picker_types: 'image',
        
        // دالة رفع الصور إلى Media Library
        images_upload_handler: async (blobInfo, progress) => {
            return new Promise((resolve, reject) => {
                const formData = new FormData();
                formData.append('files[]', blobInfo.blob(), blobInfo.filename());
                formData.append('collection', 'articles');
                formData.append('alt', blobInfo.filename());
                
                // الحصول على CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                fetch(getAdminMediaUploadUrl(), {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.media && data.media.length > 0) {
                        // إرجاع URL الصورة من Media Library
                        resolve(data.media[0].url);
                    } else {
                        reject('خطأ في رفع الصورة: ' + (data.message || 'غير معروف'));
                    }
                })
                .catch(error => {
                    reject('خطأ في الاتصال: ' + error.message);
                });
            });
        },
        
        // السماح برفع صور متعددة
        file_picker_callback: (callback, value, meta) => {
            if (meta.filetype === 'image') {
                // إنشاء input file مخفي
                const input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.setAttribute('multiple', 'multiple'); // ✅ السماح باختيار صور متعددة
                
                input.onchange = async function() {
                    const files = Array.from(this.files);
                    
                    if (files.length === 0) return;
                    
                    // رفع كل الصور
                    const uploadPromises = files.map(file => {
                        return new Promise((resolve, reject) => {
                            const formData = new FormData();
                            formData.append('files[]', file);
                            formData.append('collection', 'articles');
                            formData.append('alt', file.name);
                            
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                            
                            fetch(getAdminMediaUploadUrl(), {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success && data.media && data.media.length > 0) {
                                    resolve(data.media[0].url);
                                } else {
                                    reject('فشل رفع الصورة: ' + file.name);
                                }
                            })
                            .catch(error => reject(error));
                        });
                    });
                    
                    try {
                        // انتظار رفع جميع الصور
                        const imageUrls = await Promise.all(uploadPromises);
                        
                        // إدراج الصورة الأولى في موضع المؤشر
                        if (imageUrls.length > 0) {
                            callback(imageUrls[0], { alt: files[0].name });
                            
                            // إدراج باقي الصور بعد الأولى
                            const editor = tinymce.activeEditor;
                            for (let i = 1; i < imageUrls.length; i++) {
                                editor.insertContent(`<p><img src="${imageUrls[i]}" alt="${files[i].name}" /></p>`);
                            }
                        }
                    } catch (error) {
                        console.error('خطأ في رفع الصور:', error);
                        alert('حدث خطأ أثناء رفع بعض الصور');
                    }
                };
                
                input.click();
            }
        },
        
        // خيارات إضافية
        menubar: false,  // إخفاء القائمة العلوية
        statusbar: true, // إظهار شريط الحالة
        promotion: false, // إخفاء إعلانات TinyMCE
        branding: false,  // إخفاء العلامة التجارية
        
        // Setup callback
        setup: (editor) => {
            // إضافة زر مخصص لرفع صور متعددة
            editor.ui.registry.addButton('multiimage', {
                icon: 'image',
                tooltip: 'رفع صور متعددة',
                onAction: () => {
                    const input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');
                    input.setAttribute('multiple', 'multiple');
                    
                    input.onchange = async function() {
                        const files = Array.from(this.files);
                        if (files.length === 0) return;
                        
                        editor.notificationManager.open({
                            text: `جاري رفع ${files.length} صورة...`,
                            type: 'info',
                            timeout: -1
                        });
                        
                        const uploadPromises = files.map(file => {
                            return new Promise((resolve, reject) => {
                                const formData = new FormData();
                                formData.append('files[]', file);
                                formData.append('collection', 'articles');
                                formData.append('alt', file.name);
                                
                                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                                
                                fetch(getAdminMediaUploadUrl(), {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-CSRF-TOKEN': csrfToken,
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success && data.media && data.media.length > 0) {
                                        resolve({ url: data.media[0].url, name: file.name });
                                    } else {
                                        reject('فشل رفع: ' + file.name);
                                    }
                                })
                                .catch(error => reject(error));
                            });
                        });
                        
                        try {
                            const results = await Promise.all(uploadPromises);
                            editor.notificationManager.close();
                            
                            // إدراج جميع الصور
                            results.forEach(result => {
                                editor.insertContent(`<p><img src="${result.url}" alt="${result.name}" style="max-width: 100%;" /></p>`);
                            });
                            
                            editor.notificationManager.open({
                                text: `تم رفع ${results.length} صورة بنجاح! ✅`,
                                type: 'success',
                                timeout: 3000
                            });
                        } catch (error) {
                            editor.notificationManager.close();
                            editor.notificationManager.open({
                                text: 'حدث خطأ أثناء رفع الصور',
                                type: 'error',
                                timeout: 5000
                            });
                        }
                    };
                    
                    input.click();
                }
            });
            
            editor.on('init', () => {
                console.log('✅ TinyMCE محمّل بنجاح');
            });
            
            // معالجة المحتوى عند التحميل - تحويل النص العادي إلى فقرات
            editor.on('BeforeSetContent', (e) => {
                // إذا كان المحتوى نص عادي بدون HTML tags
                if (e.content && !e.content.match(/<[^>]+>/)) {
                    // تقسيم النص على أساس السطور المزدوجة أو السطور الفردية
                    let paragraphs = e.content
                        .split(/\n\n+/)  // تقسيم على السطور المزدوجة
                        .map(p => p.trim())
                        .filter(p => p.length > 0)
                        .map(p => `<p>${p.replace(/\n/g, '<br>')}</p>`);
                    
                    if (paragraphs.length > 0) {
                        e.content = paragraphs.join('\n');
                    }
                }
            });
        }
    });
}

/**
 * تدمير محرر TinyMCE
 */
export function destroyTinyMCE(selector = '#content') {
    tinymce.remove(selector);
}

// تصدير TinyMCE للاستخدام العام
window.tinymce = tinymce;
window.initTinyMCE = initTinyMCE;
window.destroyTinyMCE = destroyTinyMCE;
