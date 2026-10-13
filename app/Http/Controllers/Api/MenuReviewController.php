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

        $ratings = MenuReview::where('menu_id', $menu->id);

        $averageRating = round(
            (float) (clone $ratings)->avg('rating'),
            1
        );

        $ratingsCount = (clone $ratings)->count();

        return response()->json([
            'average_rating' => $averageRating,
            'ratings_count' => $ratingsCount,
            'reviews_count' => $ratingsCount,
            'reviews' => [],
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
        ]);

        $existingReview = MenuReview::where('menu_id', $menu->id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if ($existingReview) {
            throw ValidationException::withMessages([
                'review' => [
                    'You have already rated this menu item. Update your existing rating instead.',
                ],
            ]);
        }

        $review = MenuReview::create([
            'menu_id' => $menu->id,
            'user_id' => $request->user()->id,
            'rating' => $validated['rating'],
            'comment' => null,
            'is_approved' => true,
        ]);

        return response()->json([
            'message' => 'Your rating has been submitted.',
            'review' => [
                'id' => $review->id,
                'rating' => $review->rating,
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
        ]);

        $review = MenuReview::where('menu_id', $menu->id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $review->update([
            'rating' => $validated['rating'],
        ]);

        return response()->json([
            'message' => 'Your rating has been updated.',
            'review' => [
                'id' => $review->id,
                'rating' => $review->rating,
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
            'message' => 'Your rating has been deleted.',
        ]);
    }
}
