<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuReview;
use Illuminate\Http\Request;

class MenuReviewController extends Controller
{   
    public function index()
    {
        $reviews = MenuReview::with(['menu', 'user'])
            ->whereNotNull('comment')
            ->whereRaw("TRIM(comment) <> ''")
            ->orderBy('is_approved')
            ->latest()
            ->paginate(20);

        return view('admin.menu-reviews.index', compact('reviews'));
    }

    public function approve(MenuReview $menuReview)
    {
        $menuReview->update([
            'is_approved' => true,
        ]);

        return redirect()
            ->route('admin.menu-reviews.index')
            ->with('success', 'Review approved successfully.');
    }

    public function reply(Request $request, MenuReview $menuReview)
    {
       $validated = $request->validate([
            'admin_reply' => [
                'required',
                'string',
                'min:5',
                'max:2000',
                'regex:/.*\S.*/',
            ],
        ]);

        $menuReview->update([
            'admin_reply' => trim($validated['admin_reply']),
        ]);

        return redirect()
            ->route('admin.menu-reviews.index')
            ->with('success', __('messages.review_reply_saved'));
    }

    public function destroy(MenuReview $menuReview)
    {
        $menuReview->delete();

        return redirect()
            ->route('admin.menu-reviews.index')
            ->with('success', __('messages.review_deleted_success'));
    }

    public function reject(MenuReview $menuReview)
    {
        if ($menuReview->is_approved) {
            return redirect()
                ->route('admin.menu-reviews.index')
                ->with('error', 'Only pending reviews can be rejected.');
        }

        $menuReview->delete();

        return redirect()
            ->route('admin.menu-reviews.index')
            ->with('success', 'Review rejected successfully.');
    }
}
