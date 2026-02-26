<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Migrations\Migrator;

abstract class MigrationsCommand extends Command
{
    // Flag indicating whether the command is performing a rollback operation.
    protected bool $isRollback = false;

    // Paths to migration files
    protected string $migrations_path;

    // Path to migration seeders
    protected string $migrations_seeders_path;

    // Array of migration files to be processed
    protected array $migrationFiles = [];

    // Options passed to the command
    protected array $options;

    // Arguments passed to the command
    protected array $arguments;

    // Set the migrations path and seeders path
    abstract protected function setMigrationsPath(): void;

    // Execute actions before running database migrations
    abstract protected function beforeMigrations(): void;

    // Execute actions after running database migrations
    abstract protected function afterMigrations(): void;

    /**
     * Execute the console command.
     */
    public function handle(Migrator $migrator): void
    {
        if ($this->option('scopes')) {
            $this->updateScopes();

            return;
        }

        $this->cacheClear();

        $this->beforeMigrations();

        if (!$this->setMigrationsFiles($migrator)) {
            return;
        }

        // Rollback mode: if --rollback provided, perform rollback and exit
        if ($this->isRollback) {
            $this->doRollback($migrator);

            return;
        }

        $this->doMigrations($migrator);

        $this->afterMigrations();
    }

    protected function cacheClear(): void
    {
        $isCacheCleared = (bool) ($this->option('clear') ?? false);

        if (! $isCacheCleared) {
            return;
        }

        $this->info('Clearing cache...');

        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('config:cache');
        $this->call('route:clear');
        $this->call('view:clear');

        $this->info('Cache cleared.');
    }

    // Get the migration files to be processed
    protected function getMigrationFiles(): array
    {
        return $this->migrationFiles;
    }

    // Set the migration files to be processed (only those that have not been run yet)
    protected function setMigrationsFiles(Migrator $migrator): bool
    {
        $this->setMigrationsPath();

        // Get migration files from the specified path
        $this->migrationFiles = $migrator->getMigrationFiles($this->migrations_path);

        if (empty($this->migrationFiles)) {
            $this->warn("No migration files found in the path: {$this->migrations_path}");

            return false;
        }

        return true;
    }

    protected function doRollback(Migrator $migrator): void
    {
        $this->setMigrationsPath();

        $step = $this->option('step');

        if (!empty($step)) {
            $this->info("Rolling back {$step} batch(es) of migrations...");

            // Laravel's rollback supports --step; call the artisan command to leverage options
            $this->call('migrate:rollback', [
                '--path' => $this->migrations_path,
                '--step' => (int) $step,
            ]);
        } else {
            $this->info('Rolling back last batch of migrations...');

            // Use Migrator directly for the given path (last batch)
            $migrator->rollback($this->migrations_path);
        }
    }

    // Perform the migrations (depends on the $migrationFiles set previously)
    protected function doMigrations(Migrator $migrator): void
    {
        if($migrator->hasRunAnyMigrations()) {
            // Filter out already run migrations
            $migrationsAlreadyCompleted = $migrator?->getRepository()?->getRan() ?? [];

            $migrationsToRun = \array_diff(\array_keys($this->migrationFiles), $migrationsAlreadyCompleted);

            if (empty($migrationsToRun)) {
                $this->warn('All migrations have already been run.');
                $this->newLine();

                return;
            };
        } else {
            $migrationsToRun = \array_keys($this->migrationFiles);
        }

        $pendingFiles = [];

        /**
         * $migrationFiles - is a key-value array where key is the migration class name and value is the full path to the migration file
         *
         *  Here we prepare an array of pending migration files to be run.
         *  Pass to migrator only those files that have not been run yet.
         */
        foreach ($migrationsToRun as $key) {
            $pendingFiles[] = $this->migrationFiles[$key];
        }

        natsort($pendingFiles);

        $this->info("Running all the new migrations:");
        $this->newLine();

        // Apply all the migrations at one time
        $migrator->run($pendingFiles);

        // Run the pending migrations
        foreach ($pendingFiles as $file) {
            $this->line("<comment>   Proceeded: </comment>{$file}");
        }
    }

    /**
     * Update permissions dependencies for all concerns tables if scopes has been updated in the config file
     *
     * @return void
     */
    protected function updateScopes(): void
    {
        $this->info('Updating scopes...');

    }
}
