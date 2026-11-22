/**
 * Composable for handling HTML entity decoding
 */
export const useHtmlEntities = () => {
  /**
   * فك ترميز HTML entities من النص
   */
  const decodeHtmlEntities = (text: string | null | undefined): string | null => {
    if (!text) return text || null

    let decoded = String(text)

    // Try DOM method for client-side
    if (process.client) {
      try {
        const div = document.createElement('div')
        div.innerHTML = decoded
        decoded = div.textContent || div.innerText || decoded
      } catch (e) {
        // Fallback to manual replacement if DOM method fails
      }
    }

    // تنظيف إضافي للنصوص العربية والإنجليزية وإزالة &nbsp; تماماً
    return decoded
      .replace(/&amp;nbsp;/gi, ' ')
      .replace(/&nbsp;/gi, ' ')
      .replace(/\u00A0/g, ' ')
      .replace(/&ldquo;/gi, '"')
      .replace(/&rdquo;/gi, '"')
      .replace(/&lsquo;/gi, "'")
      .replace(/&rsquo;/gi, "'")
      .replace(/&hellip;/gi, '…')
      .replace(/&quot;/gi, '"')
      .replace(/&#0*39;/g, "'")
      .replace(/&#8220;/g, '"')
      .replace(/&#8221;/g, '"')
      .replace(/&#8216;/g, "'")
      .replace(/&#8217;/g, "'")
      .replace(/&#8230;/g, '…')
      .replace(/&amp;/gi, '&')
      .replace(/&lt;/gi, '<')
      .replace(/&gt;/gi, '>')
      .replace(/[ \t]+/g, ' ')
      .trim()
  }

  /**
   * فك ترميز HTML entities من المحتوى مع الحفاظ على HTML tags
   */
  const decodeHtmlContent = (content: string | null | undefined): string | null => {
    if (!content) return content || null

    return String(content)
      .replace(/&amp;nbsp;/gi, ' ')
      .replace(/&nbsp;/gi, ' ')
      .replace(/\u00A0/g, ' ')
      .replace(/&ldquo;/gi, '"')
      .replace(/&rdquo;/gi, '"')
      .replace(/&lsquo;/gi, "'")
      .replace(/&rsquo;/gi, "'")
      .replace(/&hellip;/gi, '…')
      .replace(/&quot;/gi, '"')
      .replace(/&#0*39;/g, "'")
      .replace(/&#8220;/g, '"')
      .replace(/&#8221;/g, '"')
      .replace(/&#8216;/g, "'")
      .replace(/&#8217;/g, "'")
      .replace(/&#8230;/g, '…')
      .replace(/^(?:\s*<p[^>]*>(?:\s|<br\s*\/?>)*<\/p>\s*)+/i, '')
  }

  /**
   * معالجة نص المقال كاملاً (عربي + إنجليزي)
   */
  const processArticleText = (article: any): any => {
    if (!article) return article

    return {
      ...article,
      title: decodeHtmlEntities(article.title),
      title_en: decodeHtmlEntities(article.title_en),
      subtitle: decodeHtmlEntities(article.subtitle),
      subtitle_en: decodeHtmlEntities(article.subtitle_en),
      excerpt: decodeHtmlEntities(article.excerpt),
      excerpt_en: decodeHtmlEntities(article.excerpt_en),
      source: decodeHtmlEntities(article.source),
      source_en: decodeHtmlEntities(article.source_en),
      content: decodeHtmlContent(article.content),
      content_en: decodeHtmlContent(article.content_en),
      meta_description: decodeHtmlEntities(article.meta_description),
      related_articles: Array.isArray(article.related_articles)
        ? article.related_articles.map((rel: any) => ({
            ...rel,
            title: decodeHtmlEntities(rel.title),
            title_en: decodeHtmlEntities(rel.title_en),
            subtitle: decodeHtmlEntities(rel.subtitle),
            subtitle_en: decodeHtmlEntities(rel.subtitle_en),
            excerpt: decodeHtmlEntities(rel.excerpt),
            excerpt_en: decodeHtmlEntities(rel.excerpt_en),
          }))
        : article.related_articles
    }
  }

  return {
    decodeHtmlEntities,
    decodeHtmlContent,
    processArticleText
  }
}
