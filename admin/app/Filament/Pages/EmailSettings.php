<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class EmailSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationLabel = 'Email Settings';
    protected static ?string $title = 'Email Settings';
    protected static string $view = 'filament.pages.email-settings';

    public function getMailConfig(): array
    {
        return [
            'Mailer' => config('mail.default', 'N/A'),
            'Host' => config('mail.mailers.smtp.host', 'N/A'),
            'Port' => config('mail.mailers.smtp.port', 'N/A'),
            'Encryption' => config('mail.mailers.smtp.encryption', 'N/A'),
            'Username' => config('mail.mailers.smtp.username')
                ? $this->maskValue(config('mail.mailers.smtp.username'))
                : 'Not set',
            'Password' => config('mail.mailers.smtp.password') ? '••••••••' : 'Not set',
            'From Address' => config('mail.from.address', 'N/A'),
            'From Name' => config('mail.from.name', 'N/A'),
        ];
    }

    private function maskValue(string $value): string
    {
        if (strlen($value) <= 4) {
            return str_repeat('•', strlen($value));
        }

        return substr($value, 0, 3) . str_repeat('•', strlen($value) - 3);
    }
}
