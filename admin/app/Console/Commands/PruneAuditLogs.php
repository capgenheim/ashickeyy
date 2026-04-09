<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use Illuminate\Console\Command;

class PruneAuditLogs extends Command
{
    protected $signature = 'audit:prune {--days=30 : Number of days to retain logs}';
    protected $description = 'Delete audit log entries older than the specified retention period';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $deleted = AuditLog::where('logged_at', '<', $cutoff)->delete();

        $this->info("Pruned {$deleted} audit log entries older than {$days} days.");

        AuditLog::record('audit_prune', 'system', [
            'resource' => 'audit_logs',
            'details' => ['deleted_count' => $deleted, 'retention_days' => $days],
        ]);

        return self::SUCCESS;
    }
}
