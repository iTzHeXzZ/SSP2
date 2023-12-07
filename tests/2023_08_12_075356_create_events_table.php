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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(column:'user_id');
            $table->string(column:'start');
            $table->string(column:'end');
            $table->integer(column:'status')->default(value: 0)->nullable();
            $table->integer(column:'is_all_day')->default(value: 0)->nullable();
            $table->string(column:'title');
            $table->text(column:'description');
            $table->string(column:'event_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
