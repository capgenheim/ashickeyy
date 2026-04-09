<?php

namespace App\Filament\Resources\MediaResource\Pages;

use App\Filament\Resources\MediaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Storage;

class ManageMedia extends ManageRecords
{
    protected static string $resource = MediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $path = $data['filename'];
                    $disk = Storage::disk('uploads');
                    
                    $data['url'] = '/uploads/' . basename($path);
                    $data['mimeType'] = $disk->mimeType($path);
                    $data['size'] = $disk->size($path);
                    
                    return $data;
                }),
        ];
    }
}
