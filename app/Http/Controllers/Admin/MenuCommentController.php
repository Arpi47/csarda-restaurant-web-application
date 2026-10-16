<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuReviewComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MenuCommentController extends Controller
{
    public function index(): View
    {
        $menus = Menu::query()
            ->whereIn(
                'id',
                MenuReviewComment::query()
                    ->whereNull('parent_id')
                    ->select('menu_id')
                    ->distinct()
            )
            ->select('menu.*')
            ->selectSub(
                MenuReviewComment::query()
                    ->whereColumn('menu_review_comments.menu_id', 'menu.id')
                    ->whereNull('menu_review_comments.parent_id')
                    ->selectRaw('COUNT(*)'),
                'comments_count'
            )
            ->orderByDesc('comments_count')
            ->orderBy('name_hu')
            ->paginate(20);
        return view('admin.menu-comments.index', compact('menus'));
    }

    public function show(Menu $menu): View
    {
        $comments = MenuReviewComment::query()
            ->where('menu_id', $menu->id)
            ->whereNull('parent_id')
            ->with('user')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20);
        $allComments = MenuReviewComment::query()
            ->where('menu_id', $menu->id)
            ->with('user')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
        $commentsByParent = $allComments->groupBy(
            fn ($comment) => $comment->parent_id ?? 0
        );
        $commentsById = $allComments->keyBy('id');
        $attachReplies = function ($comment) use (
            &$attachReplies,
            $commentsByParent
        ) {
            $replies = $commentsByParent->get(
                $comment->id,
                collect()
            );
            foreach ($replies as $reply) {
                $attachReplies($reply);
            }
            $comment->setRelation('replies', $replies);
        };
        $rootIds = $comments->getCollection()->pluck('id');
        $rootComments = $rootIds
            ->map(fn ($id) => $commentsById->get($id))
            ->filter()
            ->values();
        foreach ($rootComments as $comment) {
            $attachReplies($comment);
        }
        $comments->setCollection($rootComments);
        return view(
            'admin.menu-comments.show',
            compact('menu', 'comments')
        );
    }

    public function reply(
        Request $request,
        MenuReviewComment $comment
    ): RedirectResponse {
        try {
            $validated = $request->validateWithBag(
                'reply_' . $comment->id,
                [
                    'body' => [
                        'required',
                        'string',
                        'min:5',
                        'max:2000',
                        'regex:/.*\S.*/',
                    ],
                ]
            );
        } catch (ValidationException $exception) {
            $request->session()->flash(
                'reply_body_' . $comment->id,
                $request->input('body', '')
            );
            throw $exception;
        }
        MenuReviewComment::create([
            'menu_id' => $comment->menu_id,
            'user_id' => null,
            'parent_id' => $comment->id,
            'body' => trim($validated['body']),
            'is_admin' => true,
        ]);
        return redirect()
            ->route('admin.menu-comments.show', $comment->menu_id)
            ->with('success', __('messages.review_reply_saved'));
    }

    public function destroy(
        MenuReviewComment $comment
    ): RedirectResponse {
        $menuId = $comment->menu_id;
        $comment->delete();
        return redirect()
            ->route('admin.menu-comments.show', $menuId)
            ->with('success', __('messages.review_deleted_success'));
    }
}
