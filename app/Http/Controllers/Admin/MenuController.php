<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

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
            'image' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
            'images' => 'nullable|array|max:10',
            'images.*' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
        ]);
        if ($this->duplicateExists($validatedData)) {
            return back()
                ->with('error', __('messages.duplicate_menu_item'))
                ->withInput();
        }
        $primaryImage = $validatedData['image'];
        $galleryFiles = $validatedData['images'] ?? [];
        unset($validatedData['image'], $validatedData['images']);
        $uploadedFiles = [];
        try {
            $primaryFilename = $this->storeImageFile($primaryImage);
            $uploadedFiles[] = $primaryFilename;
            $galleryFilenames = [];
            foreach ($galleryFiles as $file) {
                $filename = $this->storeMenuGalleryImageFile($file);
                $uploadedFiles[] = $filename;
                $galleryFilenames[] = $filename;
            }
            DB::transaction(function () use (
                $validatedData,
                $primaryFilename,
                $galleryFilenames
            ) {
                $validatedData['image'] = $primaryFilename;
                $validatedData['sort_order'] = (
                    Menu::where(
                        'category_id',
                        $validatedData['category_id']
                    )->max('sort_order') ?? 0
                ) + 1;
                $menu = Menu::create($validatedData);
                foreach ($galleryFilenames as $index => $filename) {
                    $menu->images()->create([
                        'image' => $filename,
                        'sort_order' => $index + 1,
                    ]);
                }
            });
        } catch (Throwable $exception) {
            $this->deleteImageFiles($uploadedFiles);
            throw $exception;
        }
        return redirect()
            ->route('admin.menu.index')
            ->with('success', __('messages.saved'));
    }
    public function edit(Menu $menu)
    {
        $categories = Category::orderBy('sort_order')->get();
        $menu->load([
            'images' => fn ($query) => $query
                ->orderBy('sort_order')
                ->orderBy('id'),
        ]);
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
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
            'images' => 'nullable|array|max:10',
            'images.*' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
            'delete_images' => 'nullable|array',
            'delete_images.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('menu_images', 'id')
                    ->where('menu_id', $menu->id),
            ],
            'image_order' => 'nullable|array',
            'image_order.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('menu_images', 'id')
                    ->where('menu_id', $menu->id),
            ],
        ]);
        if ($this->duplicateExists($validatedData, $menu->id)) {
            return back()
                ->with('error', __('messages.duplicate_menu_item'))
                ->withInput();
        }
        $primaryImage = $validatedData['image'] ?? null;
        $galleryFiles = $validatedData['images'] ?? [];
        $deleteImageIds = $validatedData['delete_images'] ?? [];
        $imageOrder = $validatedData['image_order'] ?? [];
        unset(
            $validatedData['image'],
            $validatedData['images'],
            $validatedData['delete_images'],
            $validatedData['image_order']
        );
        $imagesToDelete = $menu->images()
            ->whereIn('id', $deleteImageIds)
            ->get();
        $uploadedFiles = [];
        $oldPrimaryImage = $menu->image;
        $newPrimaryFilename = null;
        try {
            if ($primaryImage instanceof UploadedFile) {
                $newPrimaryFilename = $this->storeImageFile(
                    $primaryImage
                );
                $uploadedFiles[] = $newPrimaryFilename;
            }
            $galleryFilenames = [];
            foreach ($galleryFiles as $file) {
                $filename = $this->storeMenuGalleryImageFile($file);
                $uploadedFiles[] = $filename;
                $galleryFilenames[] = $filename;
            }
            DB::transaction(function () use (
                $menu,
                $validatedData,
                $newPrimaryFilename,
                $galleryFilenames,
                $imagesToDelete,
                $imageOrder
            ) {
                if ($newPrimaryFilename !== null) {
                    $validatedData['image'] = $newPrimaryFilename;
                }
                $menu->update($validatedData);
                foreach ($imagesToDelete as $image) {
                    $image->delete();
                }
                $remainingImages = $menu->images()
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get()
                    ->keyBy('id');
                $nextOrder = 1;
                $processedIds = [];
                foreach ($imageOrder as $imageId) {
                    $imageId = (int) $imageId;
                    if (!$remainingImages->has($imageId)) {
                        continue;
                    }
                    $image = $remainingImages->get($imageId);
                    $image->update([
                        'sort_order' => $nextOrder++,
                    ]);
                    $processedIds[] = $imageId;
                }
                foreach ($remainingImages as $image) {
                    if (in_array($image->id, $processedIds, true)) {
                        continue;
                    }
                    $image->update([
                        'sort_order' => $nextOrder++,
                    ]);
                }
                foreach ($galleryFilenames as $filename) {
                    $menu->images()->create([
                        'image' => $filename,
                        'sort_order' => $nextOrder++,
                    ]);
                }
            });
        } catch (Throwable $exception) {
            $this->deleteImageFiles($uploadedFiles);
            throw $exception;
        }
        if (
            $newPrimaryFilename !== null &&
            $oldPrimaryImage !== $newPrimaryFilename
        ) {
            $this->deleteImageFiles([$oldPrimaryImage]);
        }
        $this->deleteImageFiles(
            $imagesToDelete->pluck('image')->all()
        );
        return redirect()
            ->route('admin.menu.edit', $menu)
            ->with('success', __('messages.menu_updated'));
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
        $filenames = $menu->images()
            ->pluck('image')
            ->all();
        if ($menu->image) {
            $filenames[] = $menu->image;
        }
        DB::transaction(function () use ($menu) {
            $menu->delete();
        });
        $this->deleteImageFiles($filenames);
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
    private function storeImageFile(UploadedFile $file): string
    {
        $directory = public_path('images');
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
        $filename = Str::uuid()->toString()
            .'.'
            .$file->guessExtension();
        $file->move($directory, $filename);
        return $filename;
    }
    private function storeMenuGalleryImageFile(
        UploadedFile $file
    ): string {
        $directory = public_path('images/menu');
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
        $filename = Str::uuid()->toString()
            . '.'
            . $file->guessExtension();
        $file->move($directory, $filename);
        return 'menu/' . $filename;
    }
    private function deleteImageFiles(array $filenames): void
    {
        foreach (array_unique($filenames) as $filename) {
            if (!$filename) {
                continue;
            }
            $path = public_path('images/'.$filename);
            if (File::exists($path) && File::isFile($path)) {
                File::delete($path);
            }
        }
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