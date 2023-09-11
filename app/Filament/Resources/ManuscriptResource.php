<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManuscriptResource\Pages;
use App\Filament\Resources\ManuscriptResource\RelationManagers\FoliosRelationManager;
use App\Filament\Resources\ManuscriptResource\RelationManagers\PartnersRelationManager;
use App\Models\Manuscript;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ManuscriptResource extends Resource
{
    protected static ?string $model = Manuscript::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('temporal'),
            Forms\Components\TextInput::make('name'),
            Forms\Components\TextInput::make('url')
                ->label('Nakala URL')
                ->unique('manuscripts', 'url', ignoreRecord: true),
            Forms\Components\Toggle::make('published'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('temporal')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('folios_count')
                    ->label('Folios')
                    ->counts('folios')
                    ->sortable(),

                Tables\Columns\TextColumn::make('images')
                    ->html()
                    ->getStateUsing(function (Manuscript $record): string {
                        $html = '';
                        foreach ($record->folios as $folio) {
                            $mediaItem = $folio->getFirstMedia();
                            if ($mediaItem) {
                                $imageUrl = "/iiif/{$mediaItem->id}/full/65,/0/default.jpg";
                                $imageUrlFull = "/iiif/{$mediaItem->id}/full/max,/0/default.jpg";
                                $html .= '<div>
                                    <a href="'.url($imageUrlFull).'" target="_blank">
                                        <img src="'.url($imageUrl).'" alt="'.$folio->name.'" class="max-w-none">
                                    </a>
                                </div>';
                            }

                        }

                        return $html;
                    }),
                Tables\Columns\TextColumn::make('partners')
                    ->html()
                    ->getStateUsing(function (Manuscript $record): string {
                        $html = '';
                        foreach ($record->getMedia('partners') as $partner) {
                            $imageUrl = "/iiif/{$partner->id}/full/,72/0/default.jpg";
                            $html .= '<a href="'.($partner->getCustomProperty('url') ?: '#').'" target="_blank">
                                <img src="'.url($imageUrl).'" alt="'.$partner->name.'" class="max-w-none">
                            </a>';
                        }

                        return $html;
                    }),
                    Tables\Columns\IconColumn::make('published')->boolean()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('author(s)')
                        ->html()
                        ->getStateUsing(function (Manuscript $record): string {
                            return 'ToDo';
                        }),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\Action::make('Nakala')
                //     ->disabled(fn (Manuscript $record) => ! $record->url)
                //     ->tooltip(fn (Manuscript $record) => $record->url ? 'Sync from Nakala' : 'No Nakala URL')
                //     ->action(function (Manuscript $record) {
                //         $syncFromNakala = $record->syncFromNakala();
                //         if (isset($syncFromNakala['version'])) {
                //             Notification::make()
                //                 ->title('Updated manuscript '.$record->getDisplayname().' to revision '.$syncFromNakala['version'])
                //                 ->success()
                //                 ->send();
                //         } else {
                //             Notification::make()
                //                 ->title('ERROR while try to syunc manuscript '.$record->getDisplayname())
                //                 ->danger()
                //                 ->send();
                //         }

                //     })
                //     // ->requiresConfirmation()
                //     ->icon('heroicon-o-arrow-path')
                //     ->color('success'),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            FoliosRelationManager::class,
            PartnersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListManuscripts::route('/'),
            'create' => Pages\CreateManuscript::route('/create'),
            'edit' => Pages\EditManuscript::route('/{record}/edit'),
        ];
    }
}
