/**
 * Composable for handling HTML entity decoding
 */
export const useHtmlEntities = () => {
  /**
   * فك ترميز HTML entities من النص
   */
  const decodeHtmlEntities = (text: string | null | undefined): string | null => {
    if (!text) return text || null

    let decoded = text

    // Try DOM method for client-side
    if (process.client) {
      try {
        const div = document.createElement('div')
        div.innerHTML = text
        decoded = div.textContent || div.innerText || text
      } catch (e) {
        // Fallback to manual replacement if DOM method fails
        decoded = text
      }
    }

    // تنظيف إضافي للنصوص العربية
    return decoded
      .replace(/&nbsp;/g, ' ')
      .replace(/&ldquo;/g, '"')
      .replace(/&rdquo;/g, '"')
      .replace(/&lsquo;/g, "'")
      .replace(/&rsquo;/g, "'")
      .replace(/&hellip;/g, '…')
      .replace(/&amp;/g, '&')
      .replace(/&lt;/g, '<')
      .replace(/&gt;/g, '>')
      .replace(/&quot;/g, '"')
      .replace(/&#39;/g, "'")
      .replace(/&#8220;/g, '"')
      .replace(/&#8221;/g, '"')
      .replace(/&#8216;/g, "'")
      .replace(/&#8217;/g, "'")
      .replace(/&#8230;/g, '…')
      .trim()
  }

  /**
   * فك ترميز HTML entities من المحتوى مع الحفاظ على HTML tags
   */
  const decodeHtmlContent = (content: string | null | undefined): string | null => {
    if (!content) return content || null

    return content
      .replace(/&nbsp;/g, ' ')
      .replace(/&ldquo;/g, '"')
      .replace(/&rdquo;/g, '"')
      .replace(/&lsquo;/g, "'")
      .replace(/&rsquo;/g, "'")
      .replace(/&hellip;/g, '…')
      .replace(/&#8220;/g, '"')
      .replace(/&#8221;/g, '"')
      .replace(/&#8216;/g, "'")
      .replace(/&#8217;/g, "'")
      .replace(/&#8230;/g, '…')
      // لا نستبدل &amp; و &lt; و &gt; في المحتوى لأنها قد تكون جزء من HTML صحيح
  }

  /**
   * معالجة نص المقال كاملاً
   */
  const processArticleText = (article: any) => {
    if (!article) return article

    return {
      ...article,
      title: decodeHtmlEntities(article.title),
      subtitle: decodeHtmlEntities(article.subtitle),
      excerpt: decodeHtmlEntities(article.excerpt),
      source: decodeHtmlEntities(article.source),
      content: decodeHtmlContent(article.content),
      meta_description: decodeHtmlEntities(article.meta_description)
    }
  }

  return {
    decodeHtmlEntities,
    decodeHtmlContent,
    processArticleText
  }
}
