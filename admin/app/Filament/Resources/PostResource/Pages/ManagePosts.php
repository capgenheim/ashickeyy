<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Jobs\BlastEmailJob;
use App\Models\AuditLog;
use App\Models\Subscriber;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManagePosts extends ManageRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['author'] = auth()->user()->email ?? 'admin';
                    $data['readTime'] = \App\Models\Post::calculateReadTime($data['content'] ?? '');
                    $data['views'] = 0;
                    return $data;
                })
                ->after(function ($record) {
                    AuditLog::record('create', 'admin', [
                        'resource' => 'post',
                        'resource_id' => (string) $record->_id,
                        'details' => ['title' => $record->title, 'status' => $record->status],
                    ]);
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Post created')
                        ->body('Use the "Notify Subscribers" action on the post row to blast emails.'),
                ),
        ];
    }
}
