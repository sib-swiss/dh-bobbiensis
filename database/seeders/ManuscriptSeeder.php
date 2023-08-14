<?php

namespace Database\Seeders;

use App\Models\Manuscript;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ManuscriptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $folders = File::directories(storage_path('app/import'));
        // dd($folders);
        Manuscript::truncate();
        foreach ($folders as $folder) {
            $manuscripData = [
                'name' => basename($folder),
                'content' => File::exists($folder.'/metadata.json')
                    ? json_decode(File::get($folder.'/metadata.json'), true)
                    : null,
                'published' => true,
            ];
            Manuscript::create($manuscripData);
        }
    }
}
