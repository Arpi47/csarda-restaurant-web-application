<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;

class GalleryController extends Controller
{
    public function index()
    {
        $images = GalleryImage::orderBy('order')->get();

        return response()->json($images);
    }
}
