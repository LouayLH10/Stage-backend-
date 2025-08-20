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
        Schema::create('Feature', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('projectId');
            $table->unsignedBigInteger('optionId');
            $table->timestamps();
                        $table->foreign('projectId')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('optionId')->references('id')->on('options')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Feature');
    }
};
