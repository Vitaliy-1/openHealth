<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\MigrationsCommand;

class Update extends MigrationsCommand
{
    protected string $version = '0_1';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update 
        {--clear : Clear the cache before installation} 
        {--rollback : Rollback the last batch of migrations} 
        {--step=0 : The number of migration batches to rollback} 
        {--ver= : Specify the target version to update to} 
        {--scopes : Update the scopes (depends on config)} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the system\'s base tables and data to the latest version';

    /**
     * Set the migrations path (and supply stuff aka seeders) for the application
     *
     * This method configures the path where database migration files (and seeders) are located
     * or should be executed from during the installation process.
     *
     * @return void
     */
    protected function setMigrationsPath(): void
    {
        $this->migrations_path = config('ehealth.migrations.update.path', database_path('migrations/update')) . '/' . $this->version;
    }

    /**
     * Execute actions before running database migrations
     *
     * This method is called during the installation process before any database
     * migrations are executed. It can be used to perform preparatory tasks such as
     * checking database connectivity, creating initial configuration, or setting up
     * required database structures
     *
     * @return void
     */
    protected function beforeMigrations(): void
    {
        $this->isRollback = (bool) ($this->option('rollback') ?? false);

        $this->version = $this->getVersion();

        $this->newLine();
        $this->components->info($this->isRollback ? 'Start DB rollback...' : 'Start DB update...');
        $this->newLine();
    }

    /**
     * Performs post-migration setup tasks
     *
     * This method is called after database migrations have been succesfully! executed.
     * It can be used to seed data, configure application settings, or perform
     * any other necessary initialization tasks that depend on the database schema.
     *
     * @return void
     */
    protected function afterMigrations(): void
    {
        $this->newLine();

        $this->components->info('DB update completed successfully.');
    }

    /**
     * Get the current version of the application.
     *
     * This metod expected the version to be provided via the --ver option and it value must be in format x.y.z
     *
     * @return string The current version of the application in format x_y_z
     */
    protected function getVersion(): string
    {
        $version = $this->option('ver') ?? config('ehealth.migrations.update.version.curr');

        return str_replace('.', '_', $version)  ;
    }
}
