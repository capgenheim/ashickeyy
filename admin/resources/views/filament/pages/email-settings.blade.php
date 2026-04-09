<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Current Mail Configuration</x-slot>
        <x-slot name="description">These values are read from your environment configuration. To update them, modify the environment variables in your deployment.</x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($this->getMailConfig() as $key => $value)
                <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $key }}</dt>
                    <dd class="mt-1 text-sm font-mono text-gray-900 dark:text-gray-100">{{ $value }}</dd>
                </div>
            @endforeach
        </div>
    </x-filament::section>

    <x-filament::section>
        <x-slot name="heading">Environment Variables Reference</x-slot>
        <x-slot name="description">Update these values in your Docker Compose or .env file to change email settings.</x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3">Variable</th>
                        <th class="px-4 py-3">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach([
                        'MAIL_MAILER' => 'Mail transport driver (smtp, sendmail, ses, etc.)',
                        'MAIL_HOST' => 'SMTP server hostname',
                        'MAIL_PORT' => 'SMTP server port (587 for TLS, 465 for SSL)',
                        'MAIL_USERNAME' => 'SMTP authentication username',
                        'MAIL_PASSWORD' => 'SMTP authentication password',
                        'MAIL_ENCRYPTION' => 'Encryption protocol (tls or ssl)',
                        'MAIL_FROM_ADDRESS' => 'Default sender email address',
                        'MAIL_FROM_NAME' => 'Default sender display name',
                    ] as $var => $desc)
                        <tr>
                            <td class="px-4 py-3 font-mono text-xs text-primary-600 dark:text-primary-400">{{ $var }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $desc }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-panels::page>
