<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Support\Str;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get all categories for admin panel
     */
    public function getCategoriesForAdmin()
    {
        return $this->categoryRepository->getAllWithCount();
    }

    /**
     * Create new category
     */
    public function createCategory(array $data): Category
    {
        // Process the data
        $processedData = $this->processCategoryData($data);
        
        return $this->categoryRepository->create($processedData);
    }

    /**
     * Update existing category
     */
    public function updateCategory(Category $category, array $data): bool
    {
        // Process the data
        $processedData = $this->processCategoryData($data, $category);
        
        return $this->categoryRepository->update($category, $processedData);
    }

    /**
     * Delete category
     */
    public function deleteCategory(Category $category): bool
    {
        // Check if category has articles
        if ($this->categoryRepository->hasArticles($category)) {
            $articlesCount = $this->categoryRepository->getArticlesCount($category);
            throw new \Exception("لا يمكن حذف هذا القسم لأنه يحتوي على {$articlesCount} خبر. احذف الأخبار أولاً أو انقلها لقسم آخر.");
        }

        return $this->categoryRepository->delete($category);
    }

    /**
     * Toggle category status
     */
    public function toggleStatus(Category $category): bool
    {
        return $this->categoryRepository->toggleStatus($category);
    }

    /**
     * Get category statistics
     */
    public function getStatistics(): array
    {
        return $this->categoryRepository->getStatistics();
    }

    /**
     * Search categories
     */
    public function searchCategories(string $query)
    {
        return $this->categoryRepository->search($query);
    }

    /**
     * Get active categories for frontend
     */
    public function getActiveCategories()
    {
        return $this->categoryRepository->getActive();
    }

    /**
     * Get category by slug for frontend
     */
    public function getBySlug(string $slug): ?Category
    {
        return $this->categoryRepository->findBySlug($slug);
    }

    /**
     * Get popular categories
     */
    public function getPopularCategories(int $limit = 5)
    {
        return $this->categoryRepository->getPopular($limit);
    }

    /**
     * Get categories for select dropdown
     */
    public function getCategoriesForSelect()
    {
        return $this->categoryRepository->getForSelect();
    }

    /**
     * Get category with its articles
     */
    public function getCategoryWithArticles(Category $category, int $limit = 10)
    {
        return $this->categoryRepository->getWithArticles($category, $limit);
    }

    /**
     * Process category data before saving
     */
    protected function processCategoryData(array $data, ?Category $existingCategory = null): array
    {
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name'], $existingCategory);
        } else {
            // Ensure slug is unique
            $data['slug'] = $this->ensureUniqueSlug($data['slug'], $existingCategory);
        }

        // Process keywords
        if (!empty($data['keywords'])) {
            $data['keywords'] = $this->processKeywords($data['keywords']);
        }

        // Validate and process color
        if (!empty($data['color'])) {
            $data['color'] = $this->processColor($data['color']);
        }

        // Generate meta fields if not provided
        if (empty($data['meta_title']) && !empty($data['name'])) {
            $data['meta_title'] = $this->generateMetaTitle($data['name']);
        }

        if (empty($data['meta_description']) && !empty($data['description'])) {
            $data['meta_description'] = $this->generateMetaDescription($data['description']);
        }

        return $data;
    }

    /**
     * Generate unique slug
     */
    protected function generateUniqueSlug(string $name, ?Category $existingCategory = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while ($this->slugExists($slug, $existingCategory)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Ensure slug is unique
     */
    protected function ensureUniqueSlug(string $slug, ?Category $existingCategory = null): string
    {
        $baseSlug = Str::slug($slug);
        $finalSlug = $baseSlug;
        $counter = 1;

        while ($this->slugExists($finalSlug, $existingCategory)) {
            $finalSlug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $finalSlug;
    }

    /**
     * Check if slug exists
     */
    protected function slugExists(string $slug, ?Category $existingCategory = null): bool
    {
        $category = $this->categoryRepository->findBySlug($slug);
        
        if (!$category) {
            return false;
        }
        
        if ($existingCategory && $category->id === $existingCategory->id) {
            return false;
        }
        
        return true;
    }

    /**
     * Process keywords string
     */
    protected function processKeywords(string $keywords): string
    {
        // Clean up keywords: remove extra spaces, ensure consistent comma separation
        $keywordArray = array_map('trim', explode(',', $keywords));
        $keywordArray = array_filter($keywordArray); // Remove empty values
        $keywordArray = array_unique($keywordArray); // Remove duplicates
        
        return implode(', ', $keywordArray);
    }

    /**
     * Process color value
     */
    protected function processColor(string $color): string
    {
        // Ensure color starts with #
        if (!str_starts_with($color, '#')) {
            $color = '#' . $color;
        }
        
        // Convert to uppercase
        return strtoupper($color);
    }

    /**
     * Generate meta title from name
     */
    protected function generateMetaTitle(string $name): string
    {
        return Str::limit($name . ' - أخبار وتقارير', 57); // 57 to leave room for "..."
    }

    /**
     * Generate meta description from description
     */
    protected function generateMetaDescription(string $description): string
    {
        $cleanDescription = strip_tags($description);
        $cleanDescription = preg_replace('/\s+/', ' ', $cleanDescription);
        
        return Str::limit(trim($cleanDescription), 157); // 157 to leave room for "..."
    }

    /**
     * Bulk operations
     */
    public function bulkStatusUpdate(array $categoryIds, bool $status): int
    {
        return $this->categoryRepository->bulkUpdateStatus($categoryIds, $status);
    }

    /**
     * Get categories with statistics
     */
    public function getCategoriesWithStats()
    {
        $categories = $this->categoryRepository->getAllWithCount();
        $stats = $this->getStatistics();
        
        return compact('categories', 'stats');
    }

    /**
     * Validate category data
     */
    public function validateCategoryData(array $data, ?Category $existingCategory = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'required|boolean',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'keywords' => 'nullable|string|max:255',
        ];

        // Add unique validation for name and slug
        if ($existingCategory) {
            $rules['name'] .= '|unique:categories,name,' . $existingCategory->id;
            if (!empty($data['slug'])) {
                $rules['slug'] .= '|unique:categories,slug,' . $existingCategory->id;
            }
        } else {
            $rules['name'] .= '|unique:categories,name';
            if (!empty($data['slug'])) {
                $rules['slug'] .= '|unique:categories,slug';
            }
        }

        return $rules;
    }

    /**
     * Get category insights
     */
    public function getCategoryInsights(Category $category): array
    {
        $articlesCount = $this->categoryRepository->getArticlesCount($category);
        $articles = $this->categoryRepository->getWithArticles($category, 5);
        
        return [
            'articles_count' => $articlesCount,
            'recent_articles' => $articles,
            'is_popular' => $articlesCount > 10, // Consider popular if more than 10 articles
            'last_article_date' => $articles->first()?->created_at,
            'status' => $category->is_active ? 'active' : 'inactive',
        ];
    }

    /**
     * Get SEO analysis for category
     */
    public function getSEOAnalysis(Category $category): array
    {
        return [
            'meta_title_length' => strlen($category->meta_title ?? ''),
            'meta_description_length' => strlen($category->meta_description ?? ''),
            'has_meta_title' => !empty($category->meta_title),
            'has_meta_description' => !empty($category->meta_description),
            'has_keywords' => !empty($category->keywords),
            'has_description' => !empty($category->description),
            'keyword_count' => !empty($category->keywords) ? count(explode(',', $category->keywords)) : 0,
            'seo_score' => $this->calculateSEOScore($category),
        ];
    }

    /**
     * Calculate SEO score for category
     */
    protected function calculateSEOScore(Category $category): int
    {
        $score = 0;
        
        // Meta title (25 points)
        if (!empty($category->meta_title)) {
            $titleLength = strlen($category->meta_title);
            if ($titleLength >= 30 && $titleLength <= 60) {
                $score += 25;
            } elseif ($titleLength > 0) {
                $score += 15;
            }
        }
        
        // Meta description (25 points)
        if (!empty($category->meta_description)) {
            $descLength = strlen($category->meta_description);
            if ($descLength >= 120 && $descLength <= 160) {
                $score += 25;
            } elseif ($descLength > 0) {
                $score += 15;
            }
        }
        
        // Keywords (20 points)
        if (!empty($category->keywords)) {
            $keywordCount = count(explode(',', $category->keywords));
            if ($keywordCount >= 3 && $keywordCount <= 10) {
                $score += 20;
            } elseif ($keywordCount > 0) {
                $score += 10;
            }
        }
        
        // Description (15 points)
        if (!empty($category->description)) {
            $score += 15;
        }
        
        // Slug quality (10 points)
        if (!empty($category->slug) && strlen($category->slug) <= 50) {
            $score += 10;
        }
        
        // Active status (5 points)
        if ($category->is_active) {
            $score += 5;
        }
        
        return $score;
    }
}
