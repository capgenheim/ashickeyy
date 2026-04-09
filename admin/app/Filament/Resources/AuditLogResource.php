<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationLabel = 'Audit Logs';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('action')->disabled(),
                Forms\Components\TextInput::make('source')->disabled(),
                Forms\Components\TextInput::make('user')->disabled(),
                Forms\Components\TextInput::make('ip_address')->disabled(),
                Forms\Components\TextInput::make('resource')->disabled(),
                Forms\Components\TextInput::make('resource_id')->disabled(),
                Forms\Components\Textarea::make('user_agent')->disabled()->columnSpanFull(),
                Forms\Components\KeyValue::make('details')->disabled()->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('action')
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        str_contains($state, 'create') => 'success',
                        str_contains($state, 'update') => 'warning',
                        str_contains($state, 'delete') => 'danger',
                        str_contains($state, 'login') => 'info',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('source')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'primary',
                        'api' => 'info',
                        'frontend' => 'success',
                        'system' => 'gray',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('user')
                    ->searchable(),
                Tables\Columns\TextColumn::make('resource')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('logged_at')
                    ->label('When')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('logged_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('source')
                    ->options([
                        'admin' => 'Admin',
                        'api' => 'API',
                        'frontend' => 'Frontend',
                        'system' => 'System',
                    ]),
                Tables\Filters\SelectFilter::make('action')
                    ->options([
                        'login' => 'Login',
                        'create' => 'Create',
                        'update' => 'Update',
                        'delete' => 'Delete',
                        'email_blast' => 'Email Blast',
                        'subscribe' => 'Subscribe',
                        'page_view' => 'Page View',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ManageAuditLogs::route('/'),
        ];
    }
}
