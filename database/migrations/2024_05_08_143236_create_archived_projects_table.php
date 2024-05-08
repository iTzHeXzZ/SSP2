<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('archived_projects', function (Blueprint $table) {
            $table->id();
            $table->string('ort');
            $table->string('postleitzahl');
            $table->string('strasse');
            $table->string('hausnummer');
            $table->integer('wohneinheiten');
            $table->string('bestand');
            $table->string('notiz')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('archived_projects');
    }

    
};
