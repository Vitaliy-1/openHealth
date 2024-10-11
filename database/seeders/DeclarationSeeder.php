<?php

namespace Database\Seeders;

use App\Models\Declaration;

class DeclarationSeeder
{
    public function run($count = 10)
    {
        // generate count declarations
        Declaration::factory()->count($count)->create();
    }
}
