export const useShare = () => {
  const config = useRuntimeConfig()

  // التوقيع الموحد للمشاركة
  const getShareSignature = () => {
    return `

_إعلام انتقالـﮯ العاصمة #عدن_

صفحتنا على الفيس بوك
www.facebook.com/adenstc1

صفحتنا على تويتر
www.twitter.com/qpwVSfBG8b8pMI1`
  }

  // بناء نص المشاركة مع التوقيع
  const buildShareText = (title: string, url: string, includeSignature: boolean = true) => {
    if (includeSignature) {
      return `${title}

${url}
${getShareSignature()}`
    }
    return `${title} ${url}`
  }

  // مشاركة على فيسبوك (فيسبوك لا يدعم النص المخصص، فقط الرابط)
  const shareOnFacebook = (url: string, title: string) => {
    const shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`
    window.open(shareUrl, '_blank', 'width=600,height=400')
  }

  // مشاركة على تويتر
  const shareOnTwitter = (url: string, title: string) => {
    const shareText = `${title}

_إعلام انتقالـﮯ العاصمة #عدن_`
    const shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(shareText)}`
    window.open(shareUrl, '_blank', 'width=600,height=400')
  }

  // مشاركة على واتساب
  const shareOnWhatsApp = (url: string, title: string) => {
    const shareText = buildShareText(title, url)
    const shareUrl = `https://wa.me/?text=${encodeURIComponent(shareText)}`
    window.open(shareUrl, '_blank')
  }

  // مشاركة على تيليجرام
  const shareOnTelegram = (url: string, title: string) => {
    const shareText = buildShareText(title, url)
    const shareUrl = `https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(shareText)}`
    window.open(shareUrl, '_blank')
  }

  // نسخ الرابط مع التوقيع
  const copyToClipboard = async (url: string, title?: string): Promise<boolean> => {
    try {
      let textToCopy = url
      if (title) {
        textToCopy = buildShareText(title, url)
      }
      await navigator.clipboard.writeText(textToCopy)
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
    copyToClipboard,
    buildShareText,
    getShareSignature
  }
}

