<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManuscriptResource\Pages;
use App\Filament\Resources\ManuscriptResource\RelationManagers\FoliosRelationManager;
use App\Filament\Resources\ManuscriptResource\RelationManagers\PartnersRelationManager;
use App\Models\Manuscript;
use Filament\Forms;
use Filament\Forms\Form;
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
            Forms\Components\TextInput::make('name')
                ->required()
                ->unique('manuscripts', 'name', ignoreRecord: true),
            Forms\Components\TextInput::make('authors'),
            Forms\Components\TextInput::make('nakala_url')
                ->label('Nakala URL'),
            Forms\Components\TextInput::make('dasch_url')
                ->label('DaSCH URL'),

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
                                $html .= '<a href="'.url($imageUrlFull).'" class="inline-block"  target="_blank">
                                        <img src="'.url($imageUrl).'" alt="'.$folio->name.'" class="max-w-none" />
                                    </a> ';
                            }

                        }

                        return $html;
                    }),
                Tables\Columns\IconColumn::make('published')->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('authors')
                    ->label('Author(s)')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('temporal', 'asc');
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
