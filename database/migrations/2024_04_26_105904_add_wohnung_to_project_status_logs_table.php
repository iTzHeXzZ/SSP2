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
        Schema::table('project_status_logs', function (Blueprint $table) {
            $table->string('wohnung_nr')->nullable()->after('project_id');
        });
    }

    public function down()
    {
        Schema::table('project_status_logs', function (Blueprint $table) {
            $table->dropColumn('wohnung');
        });
    }
};
