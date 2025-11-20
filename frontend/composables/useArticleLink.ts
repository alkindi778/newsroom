import type { Article } from '~/types'

export const useArticleLink = () => {
  const settingsStore = useSettingsStore()
  const localePath = useLocalePath()

  const getArticleLink = (article: Article) => {
    const style = settingsStore.getSetting('article_permalink_style') || 'arabic'

    if (style === 'id') {
      return localePath(`/news/${article.id}`)
    }

    return localePath(`/news/${article.slug}`)
  }

  return { getArticleLink }
}
