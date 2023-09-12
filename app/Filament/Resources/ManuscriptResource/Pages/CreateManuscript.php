<?php

namespace App\Filament\Resources\ManuscriptResource\Pages;

use App\Filament\Resources\ManuscriptResource;
use App\Models\Manuscript;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateManuscript extends CreateRecord
{
    protected static string $resource = ManuscriptResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $manuscript = Manuscript::create([
            'name' => $data['name'],
            'temporal' => $data['temporal'],
            'nakala_url' => $data['nakala_url'],
        ]);
        $manuscript->published = $data['published'];
        $manuscript->save();

        return $manuscript;
    }
}
