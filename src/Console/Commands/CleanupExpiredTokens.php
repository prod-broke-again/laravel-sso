<?php

declare(strict_types=1);

namespace Packages\LaravelSSO\Console\Commands;

use Packages\LaravelSSO\Models\SsoToken;
use Illuminate\Console\Command;

/**
 * Cleanup Expired SSO Tokens Command
 *
 * Removes expired SSO tokens from the database.
 */
class CleanupExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sso:cleanup
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--older-than= : Delete tokens older than specified hours (default: all expired)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired SSO tokens from the database';

    /**
     * Execute the console command.
     *
     * @return int Exit code
     */
    public function handle(): int
    {
        $query = SsoToken::where('expires_at', '<', now());

        // Apply additional filter if specified
        if ($this->option('older-than')) {
            $hours = (int) $this->option('older-than');
            $query->where('expires_at', '<', now()->subHours($hours));
        }

        $count = $query->count();

        if ($count === 0) {
            $this->info('No expired tokens found.');
            return self::SUCCESS;
        }

        $this->info("Found {$count} expired SSO tokens.");

        if ($this->option('dry-run')) {
            $this->warn('Dry run mode - no tokens will be deleted.');
            $this->table(
                ['ID', 'Token (partial)', 'Partner', 'Expired At'],
                $query->limit(10)->get(['id', 'token', 'partner_identifier', 'expires_at'])
                    ->map(fn ($token) => [
                        $token->id,
                        substr($token->token, 0, 8) . '...',
                        $token->partner_identifier,
                        $token->expires_at->format('Y-m-d H:i:s'),
                    ])
            );

            if ($count > 10) {
                $this->info("... and " . ($count - 10) . " more tokens.");
            }

            return self::SUCCESS;
        }

        if (!$this->confirm("Delete {$count} expired tokens?", true)) {
            $this->info('Operation cancelled.');
            return self::SUCCESS;
        }

        $deleted = $query->delete();

        $this->info("Successfully deleted {$deleted} expired SSO tokens.");

        return self::SUCCESS;
    }
}
