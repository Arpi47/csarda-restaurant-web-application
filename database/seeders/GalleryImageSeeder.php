<?php

namespace Database\Seeders;

use App\Models\GalleryImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class GalleryImageSeeder extends Seeder
{
    public function run(): void
    {
        $sourceDirectory = database_path('seeders/gallery');
        $destinationDirectory = public_path('images/gallery');

        if (!File::exists($destinationDirectory)) {
            File::makeDirectory(
                $destinationDirectory,
                0755,
                true
            );
        }
        GalleryImage::query()->delete();

        $existingFiles = File::files(
            $destinationDirectory
        );
        foreach ($existingFiles as $file) {
            File::delete(
                $file->getPathname()
            );
        }
        $files = File::files(
            $sourceDirectory
        );
        usort(
            $files,
            function ($a, $b) {
                return strcmp(
                    $a->getFilename(),
                    $b->getFilename()
                );
            }
        );
        foreach ($files as $index => $file) {

            $filename =
                $file->getFilename();

            File::copy(
                $file->getPathname(),
                $destinationDirectory . '/' . $filename
            );
            GalleryImage::create([
                'image' => $filename,
                'order' => $index,
            ]);
        }
    }
}