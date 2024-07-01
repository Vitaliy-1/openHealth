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

        DB::insert("INSERT INTO koatuu_level1 VALUES ('01','Автономна республіка крим'),('05','Вінницька'),('07','Волинська'),('12','Дніпропетровська'),('14','Донецька'),('18','Житомирська'),('21','Закарпатська'),('23','Запорізька'),('26','Івано-франківська'),('32','Київська'),('35','Кіровоградська'),('44','Луганська'),('46','Львівська'),('48','Миколаївська'),('51','Лдеська'),('53','Полтавська'),('56','Рівненська'),('59','Сумська'),('61','Тернопільська'),('63','Харківська'),('65','Херсонська'),('68','Хмельницька'),('71','Черкаська'),('73','Чернівецька'),('74','Чернігівська'),('80','м.Київ'),('85','м.Севастополь');");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('koatuu_level1');
    }



};
