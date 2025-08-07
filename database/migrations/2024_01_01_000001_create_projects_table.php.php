<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('address');
            $table->text('presentation');
            $table->integer('nb_appartements');
            $table->float('surface');

            // On suppose que photo_couverture, logo sont des chemins ou URLs d'images
            $table->string('photo_couverture');

            // Gallerie images et vidéos peuvent être stockées en JSON (tableau de chemins/URLs)
            $table->json('gallerie_images');
            $table->json('gallerie_videos');

            $table->string('logo');
            $table->string('email');

            // Clé étrangère vers user (promoteur)
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('ville_id');

            $table->timestamps();

            // Clé étrangère user_id qui référence la table users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ville_id')->references('id')->on('ville')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
