export const useDateFormat = () => {
  // تنسيق التاريخ بالعربية
  const formatDate = (date: string | Date | undefined, format: 'full' | 'short' | 'relative' = 'full'): string => {
    if (!date) return ''
    
    const d = new Date(date)
    
    if (isNaN(d.getTime())) return ''

    if (format === 'relative') {
      return getRelativeTime(d)
    }

    const options: Intl.DateTimeFormatOptions = format === 'full'
      ? { 
          year: 'numeric', 
          month: 'long', 
          day: 'numeric', 
          hour: '2-digit', 
          minute: '2-digit',
          calendar: 'gregory' // استخدام التقويم الميلادي
        }
      : { 
          year: 'numeric', 
          month: 'short', 
          day: 'numeric',
          calendar: 'gregory' // استخدام التقويم الميلادي
        }

    return new Intl.DateTimeFormat('ar-EG', options).format(d)
  }

  // حساب الوقت النسبي (منذ 5 دقائق، منذ ساعة، إلخ)
  const getRelativeTime = (date: Date): string => {
    const now = new Date()
    const diffInSeconds = Math.floor((now.getTime() - date.getTime()) / 1000)

    if (diffInSeconds < 60) return 'منذ لحظات'
    if (diffInSeconds < 3600) return `منذ ${Math.floor(diffInSeconds / 60)} دقيقة`
    if (diffInSeconds < 86400) return `منذ ${Math.floor(diffInSeconds / 3600)} ساعة`
    if (diffInSeconds < 604800) return `منذ ${Math.floor(diffInSeconds / 86400)} يوم`
    if (diffInSeconds < 2592000) return `منذ ${Math.floor(diffInSeconds / 604800)} أسبوع`
    if (diffInSeconds < 31536000) return `منذ ${Math.floor(diffInSeconds / 2592000)} شهر`
    
    return `منذ ${Math.floor(diffInSeconds / 31536000)} سنة`
  }

  return {
    formatDate,
    getRelativeTime
  }
}
