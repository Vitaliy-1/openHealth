<?php

namespace Database\Seeders;

use App\Models\Declaration;

class DeclarationSeeder
{
    public function run()
    {

        // generate 500 declarations
        Declaration::factory()->count(10)->create();
    }
}
