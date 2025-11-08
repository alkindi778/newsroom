import { defineStore } from 'pinia'

export interface HomepageSection {
  id: number
  name: string
  slug: string
  type: string
  title: string | null
  subtitle: string | null
  category_id: number | null
  category: {
    id: number
    name: string
    slug: string
  } | null
  order: number
  items_count: number
  is_active: boolean
  settings: Record<string, any> | null
}

interface HomepageSectionsState {
  sections: HomepageSection[]
  loading: boolean
  error: string | null
}

export const useHomepageSectionsStore = defineStore('homepageSections', {
  state: (): HomepageSectionsState => ({
    sections: [],
    loading: false,
    error: null
  }),

  getters: {
    /**
     * الحصول على جميع الأقسام النشطة مرتبة
     */
    activeSections: (state): HomepageSection[] => {
      return state.sections.sort((a, b) => a.order - b.order)
    },

    /**
     * الحصول على قسم محدد بواسطة slug
     */
    getSectionBySlug: (state) => {
      return (slug: string): HomepageSection | undefined => {
        return state.sections.find(section => section.slug === slug)
      }
    },

    /**
     * الحصول على قسم محدد بواسطة type
     */
    getSectionByType: (state) => {
      return (type: string): HomepageSection | undefined => {
        return state.sections.find(section => section.type === type)
      }
    },

    /**
     * الحصول على جميع أقسام نوع معين
     */
    getSectionsByType: (state) => {
      return (type: string): HomepageSection[] => {
        return state.sections.filter(section => section.type === type)
      }
    }
  },

  actions: {
    /**
     * جلب جميع الأقسام النشطة من API
     */
    async fetchSections(): Promise<void> {
      this.loading = true
      this.error = null

      try {
        const { apiFetch } = useApi()
        const response = await apiFetch<{ status: string; data: HomepageSection[] }>(
          '/homepage-sections'
        )

        if (response?.status === 'success' && response?.data) {
          this.sections = response.data
        } else {
          throw new Error('Invalid response format')
        }
      } catch (err: any) {
        this.error = err.message || 'حدث خطأ في جلب أقسام الصفحة الرئيسية'
        console.error('Error fetching homepage sections:', err)
        
        // في حالة الخطأ، نستخدم التكوين الافتراضي
        this.setDefaultSections()
      } finally {
        this.loading = false
      }
    },

    /**
     * جلب قسم محدد بواسطة slug
     */
    async fetchSectionBySlug(slug: string): Promise<HomepageSection | null> {
      try {
        const { apiFetch } = useApi()
        const response = await apiFetch<{ status: string; data: HomepageSection }>(
          `/homepage-sections/${slug}`
        )

        if (response?.status === 'success' && response?.data) {
          // تحديث القسم في المصفوفة إذا كان موجوداً
          const index = this.sections.findIndex(s => s.slug === slug)
          if (index !== -1) {
            this.sections[index] = response.data
          } else {
            this.sections.push(response.data)
          }
          return response.data
        }
        return null
      } catch (err: any) {
        console.error('Error fetching section:', err)
        return null
      }
    },

    /**
     * تعيين الأقسام الافتراضية في حالة فشل API
     */
    setDefaultSections(): void {
      this.sections = [
        {
          id: 1,
          name: 'السلايدر الرئيسي',
          slug: 'main-slider',
          type: 'slider',
          title: null,
          subtitle: null,
          category_id: null,
          category: null,
          order: 0,
          items_count: 6,
          is_active: true,
          settings: null
        },
        {
          id: 2,
          name: 'آخر الأخبار',
          slug: 'latest-news',
          type: 'latest_news',
          title: 'آخر الأخبار',
          subtitle: null,
          category_id: null,
          category: null,
          order: 1,
          items_count: 8,
          is_active: true,
          settings: null
        },
        {
          id: 3,
          name: 'الأكثر قراءة',
          slug: 'trending',
          type: 'trending',
          title: 'الأكثر قراءة',
          subtitle: null,
          category_id: null,
          category: null,
          order: 2,
          items_count: 6,
          is_active: true,
          settings: null
        },
        {
          id: 4,
          name: 'الفيديوهات',
          slug: 'videos',
          type: 'videos',
          title: 'فيديو العربية',
          subtitle: null,
          category_id: null,
          category: null,
          order: 3,
          items_count: 6,
          is_active: true,
          settings: null
        },
        {
          id: 5,
          name: 'مقالات الرأي',
          slug: 'opinions',
          type: 'opinions',
          title: 'مقالات الرأي',
          subtitle: null,
          category_id: null,
          category: null,
          order: 4,
          items_count: 6,
          is_active: true,
          settings: null
        }
      ]
    },

    /**
     * مسح جميع الأقسام
     */
    clearSections(): void {
      this.sections = []
      this.error = null
    }
  }
})
