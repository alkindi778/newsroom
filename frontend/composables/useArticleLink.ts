import type { Article } from '~/types'

export const useArticleLink = () => {
  const settingsStore = useSettingsStore()

  const getArticleLink = (article: Article) => {
    const style = settingsStore.getSetting('article_permalink_style') || 'arabic'

    if (style === 'id') {
      return `/news/${article.id}`
    }

    return `/news/${article.slug}`
  }

  return { getArticleLink }
}
