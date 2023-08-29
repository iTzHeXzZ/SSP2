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
        Schema::table('users', function (Blueprint $table) {
            $table->text(column: "gcalendar_access_token")->nullable();
            $table->text(column: "gcalendar_refresh_token")->nullable();
            $table->text(column: "gcalendar_user_account_info")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(columns: "gcalendar_access_token");
            $table->dropColumn(columns: "gcalendar_refresh_token");
            $table->dropColumn(columns: "gcalendar_user_account_info");
        });
    }
};
