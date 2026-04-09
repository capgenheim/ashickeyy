<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaResource\Pages;
use App\Models\Media;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $pluralModelLabel = 'Media';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('filename')
                    ->label('Upload File')
                    ->disk('uploads')
                    ->directory('')
                    ->storeFileNamesIn('originalName')
                    ->getUploadedFileNameForStorageUsing(
                        fn (TemporaryUploadedFile $file): string => (string) Str::random(32) . '.' . $file->getClientOriginalExtension()
                    )
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Hidden::make('originalName'),
                Forms\Components\Hidden::make('url'),
                Forms\Components\Hidden::make('mimeType'),
                Forms\Components\Hidden::make('size'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('url')
                    ->label('Preview')
                    ->square(),
                Tables\Columns\TextColumn::make('originalName')
                    ->label('File Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mimeType')
                    ->label('Type')
                    ->badge(),
                Tables\Columns\TextColumn::make('size')
                    ->label('Size (Bytes)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMedia::route('/'),
        ];
    }
}
