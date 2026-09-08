<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $items = Menu::with('category')
            ->join('categories', 'menu.category_id', '=', 'categories.id')
            ->select('menu.*')
            ->orderBy('categories.sort_order')
            ->orderBy('menu.sort_order')
            ->orderBy('menu.id')
            ->get();

        return view('admin.menu.index', compact('items'));
    }

    public function create()
    {
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.menu.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name_en' => 'required|string|min:1|max:255',
            'name_sr_lat' => 'required|string|min:1|max:255',
            'name_sr_cyr' => 'required|string|min:1|max:255',
            'name_hu' => 'required|string|min:1|max:255',
            'description_en' => 'required|string|min:1',
            'description_sr_lat' => 'required|string|min:1',
            'description_sr_cyr' => 'required|string|min:1',
            'description_hu' => 'required|string|min:1',
            'price' => 'required|numeric',
            'image' => 'required|image|max:2048',
        ]);

        if ($this->duplicateExists($validatedData)) {
            return back()
                ->with('error', __('messages.duplicate_menu_item'))
                ->withInput();
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time().'_'.$file->getClientOriginalName();

            $file->move(
                public_path('images'),
                $filename
            );

            $validatedData['image'] = $filename;
        }

        $validatedData['sort_order'] = Menu::where(
            'category_id',
            $validatedData['category_id']
        )->max('sort_order') + 1;

        Menu::create($validatedData);

        return redirect()
            ->route('admin.menu.index')
            ->with('success', __('messages.saved'));
    }

    public function edit(Menu $menu)
    {
        $categories = Category::orderBy('sort_order')->get();

        return view(
            'admin.menu.edit',
            compact('menu', 'categories')
        );
    }

    public function update(Request $request, Menu $menu)
    {
        $validatedData = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name_en' => 'required|string|min:1|max:255',
            'name_sr_lat' => 'required|string|min:1|max:255',
            'name_sr_cyr' => 'required|string|min:1|max:255',
            'name_hu' => 'required|string|min:1|max:255',
            'description_en' => 'required|string|min:1',
            'description_sr_lat' => 'required|string|min:1',
            'description_sr_cyr' => 'required|string|min:1',
            'description_hu' => 'required|string|min:1',
            'price' => 'required|numeric',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($this->duplicateExists($validatedData, $menu->id)) {
            return back()
                ->with('error', __('messages.duplicate_menu_item'))
                ->withInput();
        }

        if ($request->hasFile('image')) {
            if (
                $menu->image &&
                file_exists(public_path('images/'.$menu->image))
            ) {
                unlink(
                    public_path('images/'.$menu->image)
                );
            }

            $file = $request->file('image');
            $filename = time().'_'.$file->getClientOriginalName();

            $file->move(
                public_path('images'),
                $filename
            );

            $validatedData['image'] = $filename;
        }

        $menu->update($validatedData);

        return redirect()
            ->route('admin.menu.index')
            ->with('success', __('messages.updated'));
    }

    public function toggleActive(Menu $menu)
    {
        $menu->update([
            'is_active' => !$menu->is_active,
        ]);

        return redirect()
            ->route('admin.menu.index')
            ->with('success', __('messages.updated'));
    }

    public function destroy(Menu $menu)
    {
        if (
            $menu->image &&
            file_exists(public_path('images/'.$menu->image))
        ) {
            unlink(
                public_path('images/'.$menu->image)
            );
        }

        $menu->delete();

        return redirect()
            ->route('admin.menu.index')
            ->with('success', __('messages.deleted'));
    }

    public function reorder(Request $request)
    {
        $validatedData = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menu,id',
            'items.*.sort_order' => 'required|integer|min:1',
        ]);

        foreach ($validatedData['items'] as $item) {
            Menu::where('id', $item['id'])->update([
                'sort_order' => $item['sort_order'],
            ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    private function duplicateExists(
        array $data,
        ?int $excludeId = null
    ): bool {
        $query = Menu::where('category_id', $data['category_id'])
            ->where(function ($query) use ($data) {
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