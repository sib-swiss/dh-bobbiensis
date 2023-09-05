<?php

namespace App\Filament\Resources\ManuscriptResource\RelationManagers;

use App\Models\ManuscriptContentMeta;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class FoliosRelationManager extends RelationManager
{
    protected static string $relationship = 'folios';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {

        return $form->schema([
            Forms\Components\TextInput::make('name'),
            SpatieMediaLibraryFileUpload::make('image'),
            Forms\Components\Textarea::make('copyright'),
            Forms\Components\TextInput::make('fontsize'),
            SpatieMediaLibraryFileUpload::make('attachment_pdf')
                ->collection('pdf')
                ->label('PDF')
                ->acceptedFileTypes([
                    'application/pdf',
                ]),
            SpatieMediaLibraryFileUpload::make('attachment_tei')
                ->collection('tei')
                ->label('TEI/XML')
                ->acceptedFileTypes([
                    'application/xml',
                    'text/xml',
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),

                Tables\Columns\TextColumn::make('images')
                    ->html()
                    ->getStateUsing(function (ManuscriptContentMeta $record): string {
                        $html = '';
                        $mediaItem = $record->getFirstMedia();
                        if ($mediaItem) {
                            $imageUrl = "/iiif/{$mediaItem->id}/full/65,/0/default.jpg";
                            $image = Image::make($mediaItem->getPath());
                            $imageUrlFull = "/iiif/{$mediaItem->id}/full/{$image->width()},/0/default.jpg";
                            $html .= '<a href="'.url($imageUrlFull).'" target="_blank">
                                <img src="'.url($imageUrl).'" alt="'.$record->name.'" width="100" height="100">
                            </a>';
                        }

                        return $html;
                    }),

                Tables\Columns\TextColumn::make('pdf')
                    ->html()
                    ->label('PDF')
                    ->getStateUsing(function (ManuscriptContentMeta $record): string {
                        $html = '';
                        $mediaItem = $record->getFirstMedia('pdf');
                        if ($mediaItem) {
                            $html .= '<a href="'.$mediaItem->getUrl().'" target="_blank">
                                    PDF
                                </a>';
                        }

                        return $html;
                    }),

                Tables\Columns\TextColumn::make('tei')
                    ->html()
                    ->label('TEI/XML')
                    ->getStateUsing(function (ManuscriptContentMeta $record): string {
                        $html = '';
                        $mediaItem = $record->getFirstMedia('tei');
                        if ($mediaItem) {
                            $html .= '<a href="'.$mediaItem->getUrl().'" target="_blank">
                            TEI/XML
                                </a>';
                        }

                        return $html;
                    }),
                Tables\Columns\TextColumn::make('copyright Text')
                    ->html()
                    ->wrap()
                    ->getStateUsing(function (ManuscriptContentMeta $record): string {
                        $mediaItem = $record->getFirstMedia();

                        return $mediaItem && $mediaItem->getCustomProperty('copyright')
                        ? $mediaItem->getCustomProperty('copyright')
                        : '';

                    }),

                Tables\Columns\TextColumn::make('copyright fontSize')
                    ->html()
                    ->getStateUsing(function (ManuscriptContentMeta $record): string {
                        $mediaItem = $record->getFirstMedia();

                        return $mediaItem && $mediaItem->getCustomProperty('fontsize')
                            ? $mediaItem->getCustomProperty('fontsize')
                            : '';

                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (FoliosRelationManager $livewire, array $data) {
                        $manuscript = $livewire->getOwnerRecord();

                        $manuscriptFolio = ManuscriptContentMeta::create([
                            'name' => $data['name'],
                            'extension' => 'xml',
                            'manuscript_id' => $manuscript->id,
                        ]);
                        if (isset($data['image'])) {
                            $imagePath = storage_path("/app/public/{$data['image']}");
                            if (file_exists($imagePath)) {
                                $addMedia = $manuscriptFolio->addMedia($imagePath)
                                    ->preservingOriginal()
                                    ->withCustomProperties([
                                        'fontsize' => $data['fontsize'],
                                        'copyright' => $data['copyright'],
                                    ])
                                    ->toMediaCollection();
                            }
                        }

                        return $manuscriptFolio;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (array $data): array {
                        $mediaItem = ManuscriptContentMeta::find($data['id'])->getFirstMedia();
                        if ($mediaItem) {
                            $data['copyright'] = $mediaItem->getCustomProperty('copyright') ?: '';
                            $data['fontsize'] = $mediaItem->getCustomProperty('fontsize') ?: '';
                        }

                        return $data;
                    })->using(function (ManuscriptContentMeta $record, array $data): ManuscriptContentMeta {

                        $record->update([
                            'name' => $data['name'],
                        ]);

                        $mediaItem = $record->getFirstMedia();
                        if ($mediaItem) {
                            $mediaItem->setCustomProperty('fontsize', $data['fontsize']);
                            $mediaItem->setCustomProperty('copyright', $data['copyright']);
                            $mediaItem->save();

                            // delete cached image to regenerate with new copyright/size
                            $filePath = 'images/'.$mediaItem->id.'_'.$mediaItem->file_name;
                            $storage = Storage::disk('public');
                            if ($storage->exists($filePath)) {
                                $storage->delete($filePath);
                            }
                        }

                        return $record;

                    }),
            ])
            ->filters([
            ]);
    }
}
