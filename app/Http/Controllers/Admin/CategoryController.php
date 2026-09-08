<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('menu')
            ->orderBy('sort_order')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name_en' => 'required|string|min:1|max:255',
            'name_sr_lat' => 'required|string|min:1|max:255',
            'name_sr_cyr' => 'required|string|min:1|max:255',
            'name_hu' => 'required|string|min:1|max:255',
        ]);

        if ($this->duplicateExists($validatedData)) {
            return back()
                ->with('error', __('messages.duplicate_category'))
                ->withInput();
        }

        $validatedData['sort_order'] = (Category::max('sort_order') ?? 0) + 1;

        Category::create($validatedData);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', __('messages.saved'));
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validatedData = $request->validate([
            'name_en' => 'required|string|min:1|max:255',
            'name_sr_lat' => 'required|string|min:1|max:255',
            'name_sr_cyr' => 'required|string|min:1|max:255',
            'name_hu' => 'required|string|min:1|max:255',
        ]);

        if ($this->duplicateExists($validatedData, $category->id)) {
            return back()
                ->with('error', __('messages.duplicate_category'))
                ->withInput();
        }

        $category->update($validatedData);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', __('messages.updated'));
    }

    public function toggleActive(Category $category)
    {
        $category->update([
            'is_active' => !$category->is_active,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', __('messages.updated'));
    }

    public function reorder(Request $request)
    {
        $validatedData = $request->validate([
            'categories' => 'required|array',
            'categories.*' => 'integer|exists:categories,id',
        ]);

        foreach ($validatedData['categories'] as $index => $categoryId) {
            Category::where('id', $categoryId)->update([
                'sort_order' => $index + 1,
            ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function destroy(Category $category)
    {
        if ($category->menu()->exists()) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', __('messages.category_has_menu_items'));
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', __('messages.deleted'));
    }

    private function duplicateExists(
        array $data,
        ?int $excludeId = null
    ): bool {
        $query = Category::where(function ($query) use ($data) {
            $query->where('name_en', $data['name_en'])
                ->orWhere('name_sr_lat', $data['name_sr_lat'])
                ->orWhere('name_sr_cyr', $data['name_sr_cyr'])
                ->orWhere('name_hu', $data['name_hu']);
        });

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}