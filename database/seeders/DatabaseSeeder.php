<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            /*
             * populates permissions and roles tables, see spaties laravel-permissions docs
             * https://spatie.be/docs/laravel-permission/v6/introduction
             */
            RolesPermissionsSeeder::class
        ]);

        if (app()->isLocal()) {
            // Populates following tables legal_entities, users and model has roles with test data
            $this->call(TestUserMigrate::class);
        }
    }
}
