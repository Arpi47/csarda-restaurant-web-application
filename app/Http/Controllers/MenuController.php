<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $locale = App::getLocale();
        $localeField = match ($locale) {
            'en' => 'en',
            'hu' => 'hu',
            'sr' => 'sr_lat',
            'sr_cyrl' => 'sr_cyr',
            default => 'en',
        };
        $query = Menu::with('category');
        if ($q = $request->input('q')) {
            $query->where("name_$localeField", 'like', "%$q%");
        }
        if ($categoryId = $request->input('category')) {
            $query->where('category_id', $categoryId);
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($request->sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($request->sort === 'name') {
            $query->orderBy("name_$localeField", 'asc');
        }
        $items = $query->get();
        $items->transform(function ($item) use ($localeField) {
            $item->name = $item->{'name_' . $localeField};
            $item->description = $item->{'description_' . $localeField};
            $item->category = $item->category
                ? $item->category->{'name_' . $localeField}
                : '-';

            return $item;
        });
        $categories = Category::all();
        return view('menu', compact('items', 'categories'));
    }
}