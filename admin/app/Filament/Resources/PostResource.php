<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Jobs\BlastEmailJob;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Post;
use App\Models\Subscriber;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                    ->maxLength(200),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Textarea::make('excerpt')
                    ->required()
                    ->maxLength(500),
                Forms\Components\RichEditor::make('content')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('coverImage')
                    ->label('Cover Image URL')
                    ->maxLength(255),
                Forms\Components\Select::make('category')
                    ->options(Category::all()->pluck('name', '_id'))
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('tags')
                    ->multiple()
                    ->options(Tag::all()->pluck('name', '_id'))
                    ->searchable(),
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ])
                    ->required(),
                Forms\Components\DateTimePicker::make('publishedAt'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('author')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('views')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('readTime')
                    ->label('Read (min)')
                    ->suffix(' min')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('publishedAt')
                    ->label('Published')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('publishedAt', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function ($record) {
                        AuditLog::record('update', 'admin', [
                            'resource' => 'post',
                            'resource_id' => (string) $record->_id,
                            'details' => ['title' => $record->title],
                        ]);
                    }),
                Tables\Actions\Action::make('notifySubscribers')
                    ->label('Notify Subscribers')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Blast Email to Subscribers')
                    ->modalDescription(function () {
                        $count = Subscriber::count();
                        return "This will send an email notification about this post to all {$count} subscriber(s). Are you sure?";
                    })
                    ->modalSubmitActionLabel('Send Emails')
                    ->action(function ($record) {
                        $subscriberCount = Subscriber::count();

                        if ($subscriberCount === 0) {
                            Notification::make()
                                ->warning()
                                ->title('No subscribers')
                                ->body('There are no subscribers to notify.')
                                ->send();
                            return;
                        }

                        BlastEmailJob::dispatch(
                            $record->title,
                            $record->slug,
                            $record->excerpt ?? '',
                            $record->coverImage ?? ''
                        );

                        AuditLog::record('email_blast', 'admin', [
                            'resource' => 'post',
                            'resource_id' => (string) $record->_id,
                            'details' => [
                                'title' => $record->title,
                                'subscribers_count' => $subscriberCount,
                            ],
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Emails queued')
                            ->body("Notification emails are being sent to {$subscriberCount} subscriber(s).")
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function ($record) {
                        AuditLog::record('delete', 'admin', [
                            'resource' => 'post',
                            'resource_id' => (string) $record->_id,
                            'details' => ['title' => $record->title],
                        ]);
                    }),
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
            'index' => Pages\ManagePosts::route('/'),
        ];
    }
}
