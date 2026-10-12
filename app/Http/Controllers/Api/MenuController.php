<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;

class MenuController extends Controller
{
    public function index()
    {
        return response()->json(
            Menu::with('category')
                ->join('categories', 'menu.category_id', '=', 'categories.id')
                ->where('menu.is_active', true)
                ->where('categories.is_active', true)
                ->select('menu.*')
                ->orderBy('categories.sort_order')
                ->orderBy('menu.sort_order')
                ->orderBy('menu.id')
                ->get()
        );
    }

    public function show(int $id)
    {
        $menu = Menu::with(['category', 'images'])
            ->where('is_active', true)
            ->whereHas('category', function ($query) {
                $query->where('is_active', true);
            })
            ->findOrFail($id);

        return response()->json($menu);
    }
}