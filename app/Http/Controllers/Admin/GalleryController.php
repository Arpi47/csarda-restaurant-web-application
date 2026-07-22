<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function index()
    {
        $images = GalleryImage::orderBy('order')->get();

        return view(
            'admin.gallery.index',
            compact('images')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'images' => [
                'required',
                'array',
            ],

            'images.*' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp,gif',
                'max:4096',
            ],
        ]);

        $directory = public_path(
            'images/gallery'
        );

        if (!File::exists($directory)) {

            File::makeDirectory(
                $directory,
                0755,
                true
            );

        }

        $order =
            GalleryImage::max('order') + 1;

        foreach ($request->file('images') as $file) {

            $filename =
                time()
                .'_'
                .Str::random(10)
                .'.'
                .$file->getClientOriginalExtension();

            $file->move(
                $directory,
                $filename
            );

            GalleryImage::create([
                'image' => $filename,
                'order' => $order,
            ]);
            $order++;
        }
        return redirect()
            ->route('admin.gallery.index')
            ->with(
                'success',
                __('messages.upload_success')
            );
    }
    public function destroy(
        GalleryImage $gallery
    )
    {
        $path =
            public_path(
                'images/gallery/'.$gallery->image
            );
        if (
            File::exists($path)
        ) {
            File::delete($path);
        }
        $gallery->delete();
        return redirect()
            ->route('admin.gallery.index')
            ->with(
                'success',
                __('messages.delete_success')
            );
    }
    public function reorder(
        Request $request
    )
    {
        $request->validate([
            'order' => [
                'required',
                'array',
            ],
            'order.*' => [
                'integer',
                'exists:gallery_images,id',
            ],
        ]);
        foreach (
            $request->order
            as $index => $id
        ) {
            GalleryImage::where(
                'id',
                $id
            )
            ->update([
                'order' => $index,
            ]);
        }
        return response()->json([

            'success' => true,

        ]);

    }
}