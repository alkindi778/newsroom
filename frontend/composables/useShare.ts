export const useShare = () => {
  const config = useRuntimeConfig()
  
  // مشاركة على فيسبوك
  const shareOnFacebook = (url: string, title: string) => {
    const shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`
    window.open(shareUrl, '_blank', 'width=600,height=400')
  }

  // مشاركة على تويتر
  const shareOnTwitter = (url: string, title: string) => {
    const shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`
    window.open(shareUrl, '_blank', 'width=600,height=400')
  }

  // مشاركة على واتساب
  const shareOnWhatsApp = (url: string, title: string) => {
    const shareUrl = `https://wa.me/?text=${encodeURIComponent(title + ' ' + url)}`
    window.open(shareUrl, '_blank')
  }

  // مشاركة على تيليجرام
  const shareOnTelegram = (url: string, title: string) => {
    const shareUrl = `https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`
    window.open(shareUrl, '_blank')
  }

  // نسخ الرابط
  const copyToClipboard = async (url: string): Promise<boolean> => {
    try {
      await navigator.clipboard.writeText(url)
      return true
    } catch (err) {
      console.error('Failed to copy:', err)
      return false
    }
  }

  return {
    shareOnFacebook,
    shareOnTwitter,
    shareOnWhatsApp,
    shareOnTelegram,
    copyToClipboard
  }
}
