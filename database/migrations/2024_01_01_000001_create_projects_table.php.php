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
            $table->integer('numberOfAppartements');
            $table->float('surface');

            // On suppose que coverphoto, logo sont des chemins ou URLs d'images
            $table->string('coverphoto');

            // Gallerie images et vidéos peuvent être stockées en JSON (tableau de chemins/URLs)
            $table->json('galleryimages');
            $table->json('galleryvideos');

            $table->string('logo');
            $table->string('email');

            // Clé étrangère vers user (promoteur)
            $table->unsignedBigInteger('userId');
            $table->unsignedBigInteger('regionId');
            $table->unsignedBigInteger('typeId');

            $table->timestamps();

            // Clé étrangère userId qui référence la table users
            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('regionId')->references('id')->on('region')->onDelete('cascade');
            $table->foreign('typeId')->references('id')->on('type')->onDelete('cascade');

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
