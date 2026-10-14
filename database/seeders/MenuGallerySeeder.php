<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

class MenuGallerySeeder extends Seeder
{
    public function run(): void
    {
        $galleries = [
            [
                'aliases' => ['gulyas', 'goulash', 'gulas'],
                'images' => ['goulash1.jpg', 'goulash2.jpeg'],
            ],
            [
                'aliases' => [
                    'rantott hus',
                    'breaded meat',
                    'pohovano meso',
                ],
                'images' => [
                    'rantotthus1.jpg',
                    'rantotthus2.jpeg',
                    'rantotthus3.jpg',
                ],
            ],
            [
                'aliases' => ['spaghetti', 'spageti'],
                'images' => [
                    'spaghetti1.jpeg',
                    'spaghetti2.jpeg',
                    'spaghetti3.jpg',
                ],
            ],
            [
                'aliases' => [
                    'caesar salad',
                    'caesar salata',
                    'cezar salata',
                ],
                'images' => [
                    'caesar1.jpg',
                    'caesar2.jpeg',
                    'caesar3.jpg',
                    'caesar4.jpg',
                    'caesar5.jpg',
                    'caesar6.jpg',
                    'caesar7.jpg',
                ],
            ],
            [
                'aliases' => ['somloi', 'somloi galuska'],
                'images' => [
                    'somloi1.jpeg',
                    'somloi2.jpg',
                    'somloi3.jpeg',
                ],
            ],
        ];
        $sourceDirectory = database_path('seeders/menu');
        $destinationDirectory = public_path('images/menu');
        foreach ($galleries as $gallery) {
            foreach ($gallery['images'] as $filename) {
                $source = $sourceDirectory . DIRECTORY_SEPARATOR
                    . $filename;
                if (! is_file($source)) {
                    throw new RuntimeException(
                        "A seedkép nem található: {$source}"
                    );
                }
            }
        }
        $menuByAlias = [];
        foreach ($galleries as $gallery) {
            $aliases = array_map(
                fn (string $alias) => Str::lower(
                    Str::ascii($alias)
                ),
                $gallery['aliases']
            );
            $matches = Menu::query()->get()->filter(
                function (Menu $menu) use ($aliases) {
                    foreach ([
                        $menu->name_hu,
                        $menu->name_en,
                        $menu->name_sr_lat,
                        $menu->name_sr_cyr,
                    ] as $name) {
                        if (! $name) {
                            continue;
                        }
                        $normalizedName = Str::lower(
                            Str::ascii($name)
                        );
                        foreach ($aliases as $alias) {
                            if (Str::contains(
                                $normalizedName,
                                $alias
                            )) {
                                return true;
                            }
                        }
                    }
                    return false;
                }
            );
            if ($matches->count() !== 1) {
                throw new RuntimeException(
                    'Az étel nem található egyértelműen. '
                    . 'Keresési nevek: '
                    . implode(', ', $gallery['aliases'])
                    . '. Találatok: '
                    . $matches->count()
                );
            }
            $menuByAlias[] = [
                'menu' => $matches->first(),
                'images' => $gallery['images'],
            ];
        }
        File::ensureDirectoryExists($destinationDirectory);
        File::cleanDirectory($destinationDirectory);
        foreach ($galleries as $gallery) {
            foreach ($gallery['images'] as $filename) {
                File::copy(
                    $sourceDirectory . DIRECTORY_SEPARATOR . $filename,
                    $destinationDirectory . DIRECTORY_SEPARATOR . $filename
                );
            }
        }
        DB::transaction(function () use ($menuByAlias) {
            MenuImage::query()->delete();
            foreach ($menuByAlias as $entry) {
                $menu = $entry['menu'];
                foreach ($entry['images'] as $index => $filename) {
                    MenuImage::create([
                        'menu_id' => $menu->id,
                        'image' => 'menu/' . $filename,
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        });
    }
}