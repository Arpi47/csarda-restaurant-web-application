<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MenuReviewController extends Controller
{
    public function index(int $menuId): JsonResponse
    {
        $menu = $this->findActiveMenu($menuId);
        $approvedReviews = MenuReview::where('menu_id', $menu->id)
            ->where('is_approved', true);
        $averageRating = round(
            (float) (clone $approvedReviews)->avg('rating'),
            1
        );
        $reviewsCount = (clone $approvedReviews)->count();
        $reviews = (clone $approvedReviews)
            ->whereNotNull('comment')
            ->whereRaw("TRIM(comment) <> ''")
            ->with('user:id,first_name,last_name')
            ->latest()
            ->get()
            ->map(function (MenuReview $review) {
                return [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'admin_reply' => $review->admin_reply,
                    'created_at' => $review->created_at,
                    'user' => [
                        'full_name' => $review->user?->full_name,
                    ],
                ];
            });

        return response()->json([
            'reviews' => $reviews,
            'average_rating' => $averageRating,
            'reviews_count' => $reviewsCount,
        ]);
    }

    public function myReview(
        Request $request,
        int $menuId
    ): JsonResponse {
        $menu = $this->findActiveMenu($menuId);

        $review = MenuReview::where('menu_id', $menu->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$review) {
            return response()->json([
                'review' => null,
            ]);
        }

        return response()->json([
            'review' => [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'is_approved' => $review->is_approved,
            ],
        ]);
    }

    public function store(
        Request $request,
        int $menuId
    ): JsonResponse {
        $menu = $this->findActiveMenu($menuId);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => [
                'nullable',
                'string',
                'min:5',
                'max:2000',
                'regex:/.*\S.*/',
            ],
        ]);

        $existingReview = MenuReview::where('menu_id', $menu->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existingReview) {
            throw ValidationException::withMessages([
                'review' => [
                    'You have already reviewed this menu item. Update your existing review instead.',
                ],
            ]);
        }

        $comment = isset($validated['comment'])
            ? trim($validated['comment'])
            : null;

        $comment = $comment === '' ? null : $comment;

        $review = MenuReview::create([
            'menu_id' => $menu->id,
            'user_id' => $request->user()->id,
            'rating' => $validated['rating'],
            'comment' => $comment,
            'is_approved' => $comment === null,
        ]);

        return response()->json([
            'message' => $comment === null
                ? 'Your rating has been submitted.'
                : 'Your rating has been submitted and your comment is awaiting approval.',
            'review' => [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'is_approved' => $review->is_approved,
            ],
        ], 201);
    }

    public function update(
        Request $request,
        int $menuId
    ): JsonResponse {
        $menu = $this->findActiveMenu($menuId);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => [
                'nullable',
                'string',
                'min:5',
                'max:2000',
                'regex:/.*\S.*/',
            ],
        ]);

        $review = MenuReview::where('menu_id', $menu->id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $comment = isset($validated['comment'])
            ? trim($validated['comment'])
            : null;

        $comment = $comment === '' ? null : $comment;

        $commentChanged = $comment !== $review->comment;

        $updates = [
            'rating' => $validated['rating'],
            'comment' => $comment,
        ];

        if ($commentChanged) {
            $updates['is_approved'] = $comment === null
                ? true
                : false;
            $updates['admin_reply'] = null;
        }

        $review->update($updates);

        return response()->json([
            'message' => $commentChanged && $comment !== null
                ? 'Your rating has been updated and your comment is awaiting approval.'
                : 'Your rating has been updated.',
            'review' => [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'is_approved' => $review->is_approved,
            ],
        ]);
    }

    public function deleteComment(
        Request $request,
        int $menuId
    ): JsonResponse {
        $menu = $this->findActiveMenu($menuId);

        $review = MenuReview::where('menu_id', $menu->id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if (!$review->comment) {
            return response()->json([
                'message' => 'There is no comment to delete.',
            ], 422);
        }

        $review->update([
            'comment' => null,
            'admin_reply' => null,
            'is_approved' => true,
        ]);

        return response()->json([
            'message' => 'Comment deleted successfully.',
            'review' => [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => null,
                'is_approved' => true,
            ],
        ]);
    }

    private function findActiveMenu(int $menuId): Menu
    {
        return Menu::where('is_active', true)
            ->whereHas('category', function ($query) {
                $query->where('is_active', true);
            })
            ->findOrFail($menuId);
    }

    public function destroy(MenuReview $menuReview)
    {
        $menuReview->delete();

        return redirect()
            ->route('admin.menu-reviews.index')
            ->with('success', __('messages.review_deleted_success'));
    }
}
