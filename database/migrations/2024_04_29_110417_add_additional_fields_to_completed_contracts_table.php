<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('completed_contracts', function (Blueprint $table) {
            $table->string('gfpaket')->nullable();
            $table->boolean('fritzbox')->default(false);
            $table->boolean('firstflat')->default(false);
            // Weitere Spalten hier hinzufügen, falls erforderlich
        });
    }

    public function down()
    {
        Schema::table('completed_contracts', function (Blueprint $table) {
            $table->dropColumn('gfpaket');
            $table->dropColumn('fritzbox');
            $table->dropColumn('firstflat');
            // Weitere Spalten hier entfernen, falls erforderlich
        });
    }
};
