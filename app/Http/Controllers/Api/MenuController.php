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
                ->select('menu.*')
                ->orderBy('categories.sort_order')
                ->orderBy('menu.sort_order')
                ->orderBy('menu.id')
                ->get()
        );
    }
}
