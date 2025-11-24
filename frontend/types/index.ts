// Types أساسية للمشروع

export interface Category {
  id: number
  name: string
  name_en?: string
  slug: string
  parent_id?: number | null
  articles_count?: number
  created_at?: string
  updated_at?: string
}

export interface User {
  id: number
  name: string
  email: string
  avatar?: string
  bio?: string
  role?: string
}

export interface Article {
  id: number
  title: string
  title_en?: string
  slug: string
  subtitle?: string
  excerpt?: string
  excerpt_en?: string
  content: string
  content_en?: string
  source?: string
  image?: string
  image_url?: string
  thumbnail?: string
  thumbnail_url?: string
  medium_image_url?: string
  category_id?: number
  category?: Category
  user_id?: number
  user?: User
  author?: User  // Backend sends 'author' instead of 'user'
  is_published: boolean
  published_at?: string
  views: number
  featured_image?: string
  meta_description?: string
  keywords?: string | string[]  // Can be string or array
  created_at: string
  updated_at: string
}

export interface Writer {
  id: number
  name: string
  name_en?: string
  slug: string
  bio?: string
  bio_en?: string
  image?: string
  image_url?: string
  thumbnail?: string
  thumbnail_url?: string
  email?: string
  phone?: string
  position?: string
  position_en?: string
  specialty?: string
  specialization?: string
  specialization_en?: string
  social_links?: {
    twitter?: string
    facebook?: string
    linkedin?: string
    website?: string
  }
  is_active: boolean
  opinions_count: number
  created_at: string
  updated_at: string
}

export interface Opinion {
  id: number
  title: string
  title_en?: string
  slug: string
  excerpt?: string
  excerpt_en?: string
  content: string
  content_en?: string
  image?: string
  image_url?: string
  thumbnail_url?: string
  writer_id: number
  writer?: Writer
  is_published: boolean
  is_featured: boolean
  published_at?: string
  views: number
  likes: number
  shares: number
  meta_title?: string
  meta_description?: string
  keywords?: string
  created_at: string
  updated_at: string
  deleted_at?: string
}

export interface PaginationMeta {
  current_page: number
  from: number
  to: number
  per_page: number
  last_page: number
  total: number
}

export interface PaginatedResponse<T> {
  data: T[]
  meta: PaginationMeta
  links: {
    first: string
    last: string
    prev: string | null
    next: string | null
  }
}

export interface ApiResponse<T> {
  success: boolean
  data: T
  message?: string
}

export interface FilterParams {
  search?: string
  category?: number | string
  page?: number
  per_page?: number
  sort?: string
  order?: 'asc' | 'desc'
  is_published?: boolean | number
  is_featured?: boolean | number
}
