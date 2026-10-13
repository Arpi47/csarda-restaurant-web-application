<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuReviewComment;
use Illuminate\Http\Request;

class MenuReviewController extends Controller
{
    public function index()
    {
        $menus = Menu::query()
            ->whereHas('reviewComments', function ($query) {
                $query->whereNull('parent_id');
            })
            ->withCount([
                'reviewComments as reviews_count' => function ($query) {
                    $query->whereNull('parent_id');
                },
            ])
            ->orderByDesc('reviews_count')
            ->orderBy('name_hu')
            ->paginate(20);

        return view('admin.menu-reviews.index', compact('menus'));
    }

    public function show(Menu $menu)
    {
        $comments = MenuReviewComment::query()
            ->where('menu_id', $menu->id)
            ->whereNull('parent_id')
            ->with([
                'user',
                'replies.user',
            ])
            ->withCount('replies')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20);

        return view(
            'admin.menu-reviews.show',
            compact('menu', 'comments')
        );
    }

    public function reply(Request $request, MenuReviewComment $comment)
    {
        abort_if($comment->parent_id !== null, 404);
        $validated = $request->validate([
            'body' => [
                'required',
                'string',
                'min:5',
                'max:2000',
                'regex:/.*\S.*/',
            ],
        ]);

        MenuReviewComment::create([
            'menu_id' => $comment->menu_id,
            'user_id' => null,
            'parent_id' => $comment->id,
            'body' => trim($validated['body']),
            'is_admin' => true,
        ]);

        return redirect()
            ->route('admin.menu-reviews.show', $comment->menu_id)
            ->with('success', __('messages.review_reply_saved'));
    }

    public function destroy(MenuReviewComment $comment)
    {
        $menuId = $comment->menu_id;
        $comment->delete();

        return redirect()
            ->route('admin.menu-reviews.show', $menuId)
            ->with('success', __('messages.review_deleted_success'));
    }
}
