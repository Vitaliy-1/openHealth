<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('koatuu_level1', function (Blueprint $table) {
            $table->char('id', 2)->primary();
            $table->string('name');
        });

        DB::insert("INSERT INTO koatuu_level1 VALUES ('01','автономна республіка крим'),('05','вінницька область'),('07','волинська область'),('12','дніпропетровська область'),('14','донецька область'),('18','житомирська область'),('21','закарпатська область'),('23','запорізька область'),('26','івано-франківська область'),('32','київська область'),('35','кіровоградська область'),('44','луганська область'),('46','львівська область'),('48','миколаївська область'),('51','одеська область'),('53','полтавська область'),('56','рівненська область'),('59','сумська область'),('61','тернопільська область'),('63','харківська область'),('65','херсонська область'),('68','хмельницька область'),('71','черкаська область'),('73','чернівецька область'),('74','чернігівська область'),('80','м.київ'),('85','м.севастополь');");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('koatuu_level1');
    }



};
