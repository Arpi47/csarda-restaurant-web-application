<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuReviewComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuReviewCommentController extends Controller
{
    public function index(int $menuId): JsonResponse
    {
        $menu = $this->findActiveMenu($menuId);
        $comments = MenuReviewComment::query()
            ->where('menu_id', $menu->id)
            ->whereNull('parent_id')
            ->with([
                'user:id,first_name,last_name',
                'replies.user:id,first_name,last_name',
            ])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn (MenuReviewComment $comment) => [
                'id' => $comment->id,
                'user_id' => $comment->user_id,
                'body' => $comment->body,
                'is_admin' => $comment->is_admin,
                'created_at' => $comment->created_at,
                'user' => [
                    'full_name' => $comment->user?->full_name
                        ?? ($comment->is_admin ? 'Csárda' : 'Deleted user'),
                ],
                'replies' => $comment->replies->map(
                    fn (MenuReviewComment $reply) => [
                        'id' => $reply->id,
                        'user_id' => $reply->user_id,
                        'body' => $reply->body,
                        'is_admin' => $reply->is_admin,
                        'created_at' => $reply->created_at,
                        'user' => [
                            'full_name' => $reply->user?->full_name
                                ?? ($reply->is_admin ? 'Csárda' : 'Deleted user'),
                        ],
                    ]
                )->values(),
            ]);
        return response()->json([
            'comments' => $comments,
        ]);
    }

    public function store(Request $request, int $menuId): JsonResponse
    {
        $menu = $this->findActiveMenu($menuId);
        $validated = $request->validate([
            'body' => [
                'required',
                'string',
                'min:5',
                'max:2000',
                'regex:/.*\S.*/',
            ],
            'parent_id' => ['nullable', 'integer'],
        ]);
        $parent = null;
        if (! empty($validated['parent_id'])) {
            $parent = MenuReviewComment::query()
                ->where('menu_id', $menu->id)
                ->whereKey($validated['parent_id'])
                ->whereNull('parent_id')
                ->firstOrFail();
        }
        $comment = MenuReviewComment::create([
            'menu_id' => $menu->id,
            'user_id' => $request->user()->id,
            'parent_id' => $parent?->id,
            'body' => trim($validated['body']),
            'is_admin' => false,
        ]);
        $comment->load('user:id,first_name,last_name');
        return response()->json([
            'message' => 'Comment submitted successfully.',
            'comment' => [
                'id' => $comment->id,
                'body' => $comment->body,
                'is_admin' => $comment->is_admin,
                'created_at' => $comment->created_at,
                'user' => [
                    'full_name' => $comment->user?->full_name,
                ],
            ],
        ], 201);
    }

    public function destroy(
        Request $request,
        int $menuId,
        MenuReviewComment $comment
    ): JsonResponse {
        $menu = $this->findActiveMenu($menuId);
        abort_unless($comment->menu_id === $menu->id, 404);
        abort_unless(
            $comment->user_id === $request->user()->id
                && ! $comment->is_admin,
            403
        );
        $comment->delete();
        return response()->json([
            'message' => 'Comment deleted successfully.',
        ]);
    }

    private function findActiveMenu(int $menuId): Menu
    {
        return Menu::query()
            ->where('is_active', true)
            ->whereHas('category', fn ($query) => $query->where('is_active', true))
            ->findOrFail($menuId);
    }

    public function update(
        Request $request,
        int $menuId,
        MenuReviewComment $comment
    ): JsonResponse {
        $menu = $this->findActiveMenu($menuId);
        abort_unless($comment->menu_id === $menu->id, 404);
        abort_unless(
            $comment->user_id === $request->user()->id
                && ! $comment->is_admin,
            403
        );
        $hasAdminReply = MenuReviewComment::query()
            ->where('parent_id', $comment->id)
            ->where('is_admin', true)
            ->exists();
        if ($hasAdminReply) {
            return response()->json([
                'message' => 'This comment cannot be edited because the administrator has replied.',
            ], 422);
        }
        $validated = $request->validate([
            'body' => [
                'required',
                'string',
                'min:5',
                'max:2000',
                'regex:/.*\S.*/',
            ],
        ]);
        $comment->update([
            'body' => trim($validated['body']),
        ]);
        return response()->json([
            'message' => 'Comment updated successfully.',
            'comment' => [
                'id' => $comment->id,
                'body' => $comment->body,
            ],
        ]);
    }
}
