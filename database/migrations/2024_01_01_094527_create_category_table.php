<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('category', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->timestamps();
        });
         DB::table('category')->insert([
            ['category' => 'S+0'],
            ['category' => 'S+1'],
            ['category' => 'S+2'],
            ['category' => 'S+3'],
            ['category' => 'S+4'],
            ['category' => 'S+5'],
            ['category' => 'S+6'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category');
    }
};
