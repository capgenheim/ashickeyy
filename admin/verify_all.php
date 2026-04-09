<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Component Verification ===\n";
echo "Subscriber model: " . App\Models\Subscriber::count() . " records\n";
echo "AuditLog model: " . App\Models\AuditLog::count() . " records\n";
echo "Post model: " . App\Models\Post::count() . " records\n";
echo "BlastEmailJob: " . (class_exists(App\Jobs\BlastEmailJob::class) ? 'OK' : 'FAIL') . "\n";
echo "EmailSettings page: " . (class_exists(App\Filament\Pages\EmailSettings::class) ? 'OK' : 'FAIL') . "\n";
echo "SubscriberResource: " . (class_exists(App\Filament\Resources\SubscriberResource::class) ? 'OK' : 'FAIL') . "\n";
echo "AuditLogResource: " . (class_exists(App\Filament\Resources\AuditLogResource::class) ? 'OK' : 'FAIL') . "\n";
echo "SubscriberStatsWidget: " . (class_exists(App\Filament\Widgets\SubscriberStatsWidget::class) ? 'OK' : 'FAIL') . "\n";
echo "PruneAuditLogs command: " . (class_exists(App\Console\Commands\PruneAuditLogs::class) ? 'OK' : 'FAIL') . "\n";
echo "=== All checks complete ===\n";
