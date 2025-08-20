<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateappartementsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('appartements', function (Blueprint $table) {
            $table->id();

            $table->integer('floor');
            $table->float('surface');
            $table->float('price');
            $table->string('plan');
            $table->string('view')->nullable();

   

            // Clé étrangère vers user (promoteur)
            $table->unsignedBigInteger('projectId');
            $table->unsignedBigInteger('categoryId');
      
$table->timestamps(); // Ajoute automatiquement created_at et updated_at

            // Clé étrangère userId qui référence la table users
            $table->foreign('projectId')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('categoryId')->references('id')->on('category')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('appartements');
    }
}
