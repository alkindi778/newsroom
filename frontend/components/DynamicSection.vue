<template>
  <component 
    :is="sectionComponent" 
    v-if="sectionComponent"
    v-bind="componentProps"
  />
</template>

<script setup lang="ts">
import type { Component } from 'vue'
import type { HomepageSection } from '~/stores/homepageSections'
import SectionsSliderSection from '~/components/sections/SliderSection.vue'
import SectionsLatestNewsSection from '~/components/sections/LatestNewsSection.vue'
import SectionsCategoryNewsSection from '~/components/sections/CategoryNewsSection.vue'
import SectionsOpinionsSection from '~/components/sections/OpinionsSection.vue'
import TrendingArticles from '~/components/TrendingArticles.vue'
import TrendingVideos from '~/components/TrendingVideos.vue'
import SectionsNewspaperIssuesSection from '~/components/sections/NewspaperIssuesSection.vue'
// Templates
import TemplatesGridTemplate from '~/components/templates/GridTemplate.vue'
import TemplatesFeaturedTemplate from '~/components/templates/FeaturedTemplate.vue'
import TemplatesListTemplate from '~/components/templates/ListTemplate.vue'
import TemplatesMagazineTemplate from '~/components/templates/MagazineTemplate.vue'

interface Props {
  section: HomepageSection
}

const props = defineProps<Props>()

// Template map for content sections
const templateMap: Record<string, Component> = {
  'grid': TemplatesGridTemplate,
  'featured': TemplatesFeaturedTemplate,
  'list': TemplatesListTemplate,
  'magazine': TemplatesMagazineTemplate,
}

// Map section types to imported components
const componentMap: Record<string, Component> = {
  'slider': SectionsSliderSection,
  'latest_news': SectionsLatestNewsSection,
  'category_news': SectionsCategoryNewsSection,
  'trending': TrendingArticles,
  'opinions': SectionsOpinionsSection,
  'videos': TrendingVideos,
  'newspaper_issues': SectionsNewspaperIssuesSection,
}

const sectionComponent = computed(() => {
  // Check if section has a custom template
  const template = props.section.settings?.template
  const templatesSupportingTypes = ['latest_news', 'category_news', 'trending']
  
  // If template is set and not 'default', use custom template
  if (template && template !== 'default' && templatesSupportingTypes.includes(props.section.type)) {
    const templateComponent = templateMap[template]
    if (templateComponent) {
      return templateComponent
    }
  }
  
  // Fallback to default component for this type
  const component = componentMap[props.section.type]
  
  if (!component) {
    console.warn(`Component not found for section type: ${props.section.type}`)
    return null
  }
  
  return component
})

// Component-specific props
const componentProps = computed(() => {
  const { locale } = useI18n()
  const isEnglish = locale.value === 'en'
  
  const baseProps: any = {
    title: isEnglish && props.section.title_en ? props.section.title_en : props.section.title,
    subtitle: isEnglish && props.section.subtitle_en ? props.section.subtitle_en : props.section.subtitle,
    limit: props.section.items_count,
    settings: props.section.settings
  }

  // Add category for category_news type
  if (props.section.type === 'category_news' && props.section.category) {
    baseProps.category = props.section.category
    baseProps.categorySlug = props.section.category.slug
  }

  return baseProps
})
</script>
