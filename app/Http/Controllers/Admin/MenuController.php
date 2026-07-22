<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $items = Menu::with('category')->latest()->get();

        return view('admin.menu.index', compact('items'));
    }

    public function create()
    {
        $categories = Category::all();

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

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $filename);
            $validatedData['image'] = $filename;
        }

        Menu::create($validatedData);

        return redirect()
            ->route('admin.menu.index')
            ->with('success', __('messages.saved'));
    }

    public function edit(Menu $menu)
    {
        $categories = Category::all();
        return view('admin.menu.edit', compact('menu', 'categories'));
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

        if ($request->hasFile('image')) {

            if ($menu->image && file_exists(public_path('images/' . $menu->image))) {
                unlink(public_path('images/' . $menu->image));
            }

            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $filename);
            $validatedData['image'] = $filename;
        }
        $menu->update($validatedData);
        return redirect()
            ->route('admin.menu.index')
            ->with('success', __('messages.updated'));
    }

    public function destroy(Menu $menu)
    {
        if ($menu->image && file_exists(public_path('images/' . $menu->image))) {
            unlink(public_path('images/' . $menu->image));
        }
        $menu->delete();

        return redirect()
            ->route('admin.menu.index')
            ->with('success', __('messages.deleted'));
    }
}