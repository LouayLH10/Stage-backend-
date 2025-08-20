<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // 👉 importer DB

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('city', function (Blueprint $table) {
            $table->id();
            $table->string('city');
        });

        // 👉 Insertion des données
        DB::table('city')->insert([
            ['city' => 'Tunis'],
            ['city' => 'Ariana'],
            ['city' => 'Ben Arous'],
            ['city' => 'Manouba'],
            ['city' => 'Nabeul'],
            ['city' => 'Zaghouan'],
            ['city' => 'Bizerte'],
            ['city' => 'Béja'],
            ['city' => 'Jendouba'],
            ['city' => 'Kef'],
            ['city' => 'Siliana'],
            ['city' => 'Sousse'],
            ['city' => 'Monastir'],
            ['city' => 'Mahdia'],
            ['city' => 'Sfax'],
            ['city' => 'Kairouan'],
            ['city' => 'Kasserine'],
            ['city' => 'Sidi Bouzid'],
            ['city' => 'Gabès'],
            ['city' => 'Medenine'],
            ['city' => 'Tataouine'],
            ['city' => 'Gafsa'],
            ['city' => 'Tozeur'],
            ['city' => 'Kebili'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('city');
    }
};
