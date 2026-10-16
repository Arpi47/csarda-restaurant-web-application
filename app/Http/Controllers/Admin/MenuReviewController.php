<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MenuReviewController extends Controller
{
    public function index(): View
    {
        $menus = Menu::query()
            ->whereIn(
                'id',
                MenuReview::query()->select('menu_id')->distinct()
            )
            ->select('menu.*')
            ->selectSub(
                MenuReview::query()
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('menu_reviews.menu_id', 'menu.id'),
                'reviews_count'
            )
            ->orderByDesc('reviews_count')
            ->orderBy('name_hu')
            ->paginate(20);
        return view('admin.menu-reviews.index', compact('menus'));
    }

    public function show(Menu $menu): View
    {
        $reviews = MenuReview::query()
            ->where('menu_id', $menu->id)
            ->with('user')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20);
        return view(
            'admin.menu-reviews.show',
            compact('menu', 'reviews')
        );
    }

    public function destroy(MenuReview $review): RedirectResponse
    {
        $menuId = $review->menu_id;
        $review->delete();
        return redirect()
            ->route('admin.menu-reviews.show', $menuId)
            ->with('success', __('messages.review_deleted_success'));
    }
}
