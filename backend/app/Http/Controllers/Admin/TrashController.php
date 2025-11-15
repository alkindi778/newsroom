<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Video;
use App\Models\Opinion;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TrashController extends Controller
{
    /**
     * Display the trash page with deleted articles, videos, and opinions
     */
    public function index(Request $request): View
    {
        // Get type filter (articles, videos, or opinions)
        $type = $request->get('type', 'articles');
        
        if ($type === 'opinions') {
            // Opinions query
            $query = Opinion::onlyTrashed()
                ->with(['user:id,name', 'writer:id,name'])
                ->orderBy('deleted_at', 'desc');

            // Search functionality
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('title', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('excerpt', 'LIKE', "%{$searchTerm}%")
                      ->orWhereHas('writer', function($writerQuery) use ($searchTerm) {
                          $writerQuery->where('name', 'LIKE', "%{$searchTerm}%");
                      });
                });
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('deleted_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('deleted_at', '<=', $request->date_to);
            }

            $opinions = $query->paginate(10);
            $categories = null;
            
            return view('admin.trash.index', compact('opinions', 'categories', 'type'));
        } elseif ($type === 'videos') {
            // Videos query
            $query = Video::onlyTrashed()
                ->with(['user:id,name'])
                ->orderBy('deleted_at', 'desc');

            // Search functionality
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('title', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('description', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('deleted_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('deleted_at', '<=', $request->date_to);
            }

            $videos = $query->paginate(10);
            $categories = null;
            
            return view('admin.trash.index', compact('videos', 'categories', 'type'));
        } else {
            // Articles query
            $query = Article::onlyTrashed()
                ->with(['user:id,name', 'category:id,name'])
                ->orderBy('deleted_at', 'desc');

            // Search functionality
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('title', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('subtitle', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('source', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Filter by category
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('deleted_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('deleted_at', '<=', $request->date_to);
            }

            $articles = $query->paginate(10);
            
            // Get categories for filter
            $categories = \App\Models\Category::select('id', 'name')->get();

            return view('admin.trash.index', compact('articles', 'categories', 'type'));
        }
    }

    /**
     * Restore a specific article from trash
     */
    public function restore(string $id): RedirectResponse
    {
        try {
            $article = Article::onlyTrashed()->findOrFail($id);
            
            // Check permissions
            if (!auth()->user()->can('restore_articles') && !auth()->user()->can('manage_trash') && !auth()->user()->can('delete_articles')) {
                return redirect()->route('admin.trash.index')
                    ->with('error', 'ليس لديك صلاحية لاستعادة الأخبار');
            }

            $article->restore();

            return redirect()->route('admin.trash.index')
                ->with('success', 'تم استعادة الخبر بنجاح');

        } catch (\Exception $e) {
            \Log::error('Error restoring article: ' . $e->getMessage());
            return redirect()->route('admin.trash.index')
                ->with('error', 'حدث خطأ أثناء استعادة الخبر');
        }
    }

    /**
     * Permanently delete an article
     */
    public function forceDelete(string $id): RedirectResponse
    {
        try {
            $article = Article::onlyTrashed()->findOrFail($id);
            
            // Check permissions
            if (!auth()->user()->can('force_delete_articles') && !auth()->user()->can('manage_trash')) {
                return redirect()->route('admin.trash.index')
                    ->with('error', 'ليس لديك صلاحية لحذف الأخبار نهائياً');
            }

            // Delete associated image if exists
            if ($article->image && \Storage::disk('public')->exists($article->image)) {
                \Storage::disk('public')->delete($article->image);
            }

            $article->forceDelete();

            return redirect()->route('admin.trash.index')
                ->with('success', 'تم حذف الخبر نهائياً');

        } catch (\Exception $e) {
            \Log::error('Error force deleting article: ' . $e->getMessage());
            return redirect()->route('admin.trash.index')
                ->with('error', 'حدث خطأ أثناء حذف الخبر نهائياً');
        }
    }

    /**
     * Bulk restore selected articles
     */
    public function bulkRestore(Request $request): RedirectResponse
    {
        try {
            $articleIds = $request->input('article_ids', []);
            
            if (empty($articleIds)) {
                return redirect()->route('admin.trash.index')
                    ->with('error', 'يرجى اختيار أخبار للاستعادة');
            }

            // Check permissions
            if (!auth()->user()->can('restore_articles') && !auth()->user()->can('manage_trash') && !auth()->user()->can('delete_articles')) {
                return redirect()->route('admin.trash.index')
                    ->with('error', 'ليس لديك صلاحية لاستعادة الأخبار');
            }

            $restoredCount = Article::onlyTrashed()
                ->whereIn('id', $articleIds)
                ->restore();

            return redirect()->route('admin.trash.index')
                ->with('success', "تم استعادة {$restoredCount} خبر بنجاح");

        } catch (\Exception $e) {
            \Log::error('Error bulk restoring articles: ' . $e->getMessage());
            return redirect()->route('admin.trash.index')
                ->with('error', 'حدث خطأ أثناء استعادة الأخبار');
        }
    }

    /**
     * Bulk permanently delete selected articles
     */
    public function bulkForceDelete(Request $request): RedirectResponse
    {
        try {
            $articleIds = $request->input('article_ids', []);
            
            if (empty($articleIds)) {
                return redirect()->route('admin.trash.index')
                    ->with('error', 'يرجى اختيار أخبار للحذف');
            }

            // Check permissions
            if (!auth()->user()->can('force_delete_articles') && !auth()->user()->can('manage_trash')) {
                return redirect()->route('admin.trash.index')
                    ->with('error', 'ليس لديك صلاحية لحذف الأخبار نهائياً');
            }

            $articles = Article::onlyTrashed()->whereIn('id', $articleIds)->get();
            
            // Delete associated images
            foreach ($articles as $article) {
                if ($article->image && \Storage::disk('public')->exists($article->image)) {
                    \Storage::disk('public')->delete($article->image);
                }
            }

            $deletedCount = Article::onlyTrashed()
                ->whereIn('id', $articleIds)
                ->forceDelete();

            return redirect()->route('admin.trash.index')
                ->with('success', "تم حذف {$deletedCount} خبر نهائياً");

        } catch (\Exception $e) {
            \Log::error('Error bulk force deleting articles: ' . $e->getMessage());
            return redirect()->route('admin.trash.index')
                ->with('error', 'حدث خطأ أثناء حذف الأخبار نهائياً');
        }
    }

    /**
     * Empty entire trash
     */
    public function emptyTrash(Request $request): RedirectResponse
    {
        try {
            $type = $request->get('type', 'articles');
            
            // Check permissions
            if (!auth()->user()->can('force_delete_articles') && !auth()->user()->can('manage_trash')) {
                return redirect()->route('admin.trash.index', ['type' => $type])
                    ->with('error', 'ليس لديك صلاحية لإفراغ سلة المهملات');
            }

            if ($type === 'opinions') {
                $opinions = Opinion::onlyTrashed()->get();
                
                // Delete all associated images
                foreach ($opinions as $opinion) {
                    if ($opinion->image && \Storage::disk('public')->exists($opinion->image)) {
                        \Storage::disk('public')->delete($opinion->image);
                    }
                }

                $deletedCount = Opinion::onlyTrashed()->forceDelete();

                return redirect()->route('admin.trash.index', ['type' => 'opinions'])
                    ->with('success', "تم إفراغ سلة المهملات - حُذف {$deletedCount} مقال رأي نهائياً");
            } elseif ($type === 'videos') {
                $videos = Video::onlyTrashed()->get();
                
                // Delete all associated thumbnails
                foreach ($videos as $video) {
                    if ($video->thumbnail && \Storage::disk('public')->exists($video->thumbnail)) {
                        \Storage::disk('public')->delete($video->thumbnail);
                    }
                }

                $deletedCount = Video::onlyTrashed()->forceDelete();

                return redirect()->route('admin.trash.index', ['type' => 'videos'])
                    ->with('success', "تم إفراغ سلة المهملات - حُذف {$deletedCount} فيديو نهائياً");
            } else {
                $articles = Article::onlyTrashed()->get();
                
                // Delete all associated images
                foreach ($articles as $article) {
                    if ($article->image && \Storage::disk('public')->exists($article->image)) {
                        \Storage::disk('public')->delete($article->image);
                    }
                }

                $deletedCount = Article::onlyTrashed()->forceDelete();

                return redirect()->route('admin.trash.index')
                    ->with('success', "تم إفراغ سلة المهملات - حُذف {$deletedCount} خبر نهائياً");
            }

        } catch (\Exception $e) {
            \Log::error('Error emptying trash: ' . $e->getMessage());
            return redirect()->route('admin.trash.index')
                ->with('error', 'حدث خطأ أثناء إفراغ سلة المهملات');
        }
    }

    /**
     * Restore a video from trash
     */
    public function restoreVideo(string $id): RedirectResponse
    {
        try {
            $video = Video::onlyTrashed()->findOrFail($id);
            $video->restore();

            return redirect()->route('admin.trash.index', ['type' => 'videos'])
                ->with('success', 'تم استعادة الفيديو بنجاح');

        } catch (\Exception $e) {
            \Log::error('Error restoring video: ' . $e->getMessage());
            return redirect()->route('admin.trash.index', ['type' => 'videos'])
                ->with('error', 'حدث خطأ أثناء استعادة الفيديو');
        }
    }

    /**
     * Permanently delete a video
     */
    public function forceDeleteVideo(string $id): RedirectResponse
    {
        try {
            $video = Video::onlyTrashed()->findOrFail($id);
            
            // Delete associated thumbnail if exists
            if ($video->thumbnail && \Storage::disk('public')->exists($video->thumbnail)) {
                \Storage::disk('public')->delete($video->thumbnail);
            }

            $video->forceDelete();

            return redirect()->route('admin.trash.index', ['type' => 'videos'])
                ->with('success', 'تم حذف الفيديو نهائياً');

        } catch (\Exception $e) {
            \Log::error('Error force deleting video: ' . $e->getMessage());
            return redirect()->route('admin.trash.index', ['type' => 'videos'])
                ->with('error', 'حدث خطأ أثناء حذف الفيديو نهائياً');
        }
    }

    /**
     * Restore an opinion from trash
     */
    public function restoreOpinion(string $id): RedirectResponse
    {
        try {
            $opinion = Opinion::onlyTrashed()->findOrFail($id);
            $opinion->restore();

            return redirect()->route('admin.trash.index', ['type' => 'opinions'])
                ->with('success', 'تم استعادة المقال بنجاح');

        } catch (\Exception $e) {
            \Log::error('Error restoring opinion: ' . $e->getMessage());
            return redirect()->route('admin.trash.index', ['type' => 'opinions'])
                ->with('error', 'حدث خطأ أثناء استعادة المقال');
        }
    }

    /**
     * Permanently delete an opinion
     */
    public function forceDeleteOpinion(string $id): RedirectResponse
    {
        try {
            $opinion = Opinion::onlyTrashed()->findOrFail($id);
            
            // Delete associated image if exists
            if ($opinion->image && \Storage::disk('public')->exists($opinion->image)) {
                \Storage::disk('public')->delete($opinion->image);
            }

            $opinion->forceDelete();

            return redirect()->route('admin.trash.index', ['type' => 'opinions'])
                ->with('success', 'تم حذف المقال نهائياً');

        } catch (\Exception $e) {
            \Log::error('Error force deleting opinion: ' . $e->getMessage());
            return redirect()->route('admin.trash.index', ['type' => 'opinions'])
                ->with('error', 'حدث خطأ أثناء حذف المقال نهائياً');
        }
    }
}
