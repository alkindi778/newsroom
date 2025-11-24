export const useLocalizedContent = () => {
    const { locale } = useI18n()

    /**
     * Get localized category name
     */
    const getCategoryName = (category: any): string => {
        if (!category) {
            return ''
        }

        const isEnglish = locale.value === 'en'
        const hasTranslation = category.name_en

        if (isEnglish && hasTranslation) {
            return category.name_en
        }

        return category.name
    }

    /**
     * Get localized article title
     */
    const getArticleTitle = (article: any): string => {
        if (!article) return ''

        const isEnglish = locale.value === 'en'
        const hasTranslation = article.title_en

        if (isEnglish && hasTranslation) {
            return article.title_en
        }

        return article.title
    }

    /**
     * Get localized article content
     */
    const getArticleContent = (article: any): string => {
        if (!article) return ''

        const isEnglish = locale.value === 'en'
        const hasTranslation = article.content_en

        if (isEnglish && hasTranslation) {
            return article.content_en
        }

        return article.content
    }

    /**
     * Get localized article excerpt
     */
    const getArticleExcerpt = (article: any): string => {
        if (!article) return ''

        const isEnglish = locale.value === 'en'
        const hasTranslation = article.excerpt_en

        if (isEnglish && hasTranslation) {
            return article.excerpt_en
        }

        return article.excerpt
    }

    return {
        getCategoryName,
        getArticleTitle,
        getArticleContent,
        getArticleExcerpt
    }
}
