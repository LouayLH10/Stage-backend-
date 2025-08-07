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

            $table->integer('etage');
            $table->float('superfice');
            $table->float('prix');
            $table->string('plan')->nullable();
            $table->string('vue')->nullable();

   

            // Clé étrangère vers user (promoteur)
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('categorie_id');
      
$table->timestamps(); // Ajoute automatiquement created_at et updated_at

            // Clé étrangère user_id qui référence la table users
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('categorie_id')->references('id')->on('categorie')->onDelete('cascade');

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
