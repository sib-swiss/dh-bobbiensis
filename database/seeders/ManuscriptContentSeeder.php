<?php

namespace Database\Seeders;

use App\Models\Manuscript;
use App\Models\ManuscriptContent;
use App\Models\ManuscriptContentMeta;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ManuscriptContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $folders = File::directories(storage_path('app/import'));
        ManuscriptContent::truncate();
        Storage::deleteDirectory('public');
        foreach ($folders as $folder) {
            $manuscript = Manuscript::firstWhere('name', basename($folder));
            if (! $manuscript) {
                continue;
            }

            $foliosFolders = File::directories($folder);
            foreach ($foliosFolders as $folioFolder) {
                $manuscriptContentData = [
                    'manuscript_id' => $manuscript->id,
                    'name' => basename($folioFolder),
                    'extension' => 'xml',
                ];
                $manuscriptFolio = ManuscriptContentMeta::create($manuscriptContentData);

                $imagePath = $folioFolder.'/VL1_f.'.basename($folioFolder).'.jpg';

                if (File::exists($imagePath)) {
                    $addMedia = $manuscriptFolio->addMedia($imagePath)
                        ->preservingOriginal()
                        ->withCustomProperties([
                            // 'fontsize' => '12',
                            'copyright' => 'TEST copyright',
                        ])
                        ->toMediaCollection();
                }
            }
        }
    }
}
