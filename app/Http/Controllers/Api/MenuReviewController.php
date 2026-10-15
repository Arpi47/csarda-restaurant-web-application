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
        $ratings = MenuReview::where('menu_id', $menu->id)
            ->where('is_approved', true);
        $averageRating = round(
            (float) (clone $ratings)->avg('rating'),
            1
        );
        $ratingsCount = (clone $ratings)->count();
        $reviews = (clone $ratings)
            ->with('user')
            ->latest()
            ->get()
            ->map(function (MenuReview $review) {
                return [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'user' => $review->user ? [
                        'id' => $review->user->id,
                        'full_name' => $review->user->full_name,
                    ] : null,
                    'created_at' => $review->created_at,
                    'updated_at' => $review->updated_at,
                ];
            })
            ->values();
        return response()->json([
            'average_rating' => $averageRating,
            'ratings_count' => $ratingsCount,
            'reviews_count' => $ratingsCount,
            'reviews' => $reviews,
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
        return response()->json([
            'review' => $review ? [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
            ] : null,
        ]);
    }

    public function store(
        Request $request,
        int $menuId
    ): JsonResponse {
        $menu = $this->findActiveMenu($menuId);
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);
        $existingReview = MenuReview::where('menu_id', $menu->id)
            ->where('user_id', $request->user()->id)
            ->exists();
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
        $review = MenuReview::create([
            'menu_id' => $menu->id,
            'user_id' => $request->user()->id,
            'rating' => $validated['rating'],
            'comment' => $comment !== '' ? $comment : null,
            'is_approved' => true,
        ]);
        return response()->json([
            'message' => 'Your review has been published.',
            'review' => [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
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
            'comment' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ]);
        $review = MenuReview::where('menu_id', $menu->id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
        $review->rating = $validated['rating'];
        $review->is_approved = true;
        if (array_key_exists('comment', $validated)) {
            $comment = isset($validated['comment'])
                ? trim($validated['comment'])
                : null;
            $review->comment = $comment !== '' ? $comment : null;
        }
        $review->save();
        return response()->json([
            'message' => 'Your review has been updated.',
            'review' => [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
            ],
        ]);
    }

    public function destroy(
        Request $request,
        int $menuId
    ): JsonResponse {
        $menu = $this->findActiveMenu($menuId);
        $review = MenuReview::where('menu_id', $menu->id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
        $review->delete();
        return response()->json([
            'message' => 'Your review has been deleted.',
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
}
