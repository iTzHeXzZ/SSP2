<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $sqlitePath = database_path('database.sqlite');
        config(['database.connections.sqlite.database' => $sqlitePath]);

        try {
            // Versuche, eine Verbindung zur Datenbank herzustellen
            DB::connection()->getPdo();
        
            // Wenn keine Exception geworfen wird, war die Verbindung erfolgreich
            echo "Datenbankverbindung erfolgreich hergestellt!";
        } catch (\Exception $e) {
            // Wenn eine Exception auftritt, gab es ein Problem mit der Verbindung
            echo "Datenbankverbindung fehlgeschlagen: " . $e->getMessage();
            die(); // Hier könntest du auch anders reagieren, je nach Anwendungsfall
        }
        
                $sqliteDataN2= DB::connection('sqlite')->table('project_user')->get();
                foreach ($sqliteDataN2 as $record) {
                    DB::connection('mysql')->table('project_user')->insert((array) $record);
                }

                
                
                

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mysql', function (Blueprint $table) {
            //
        });
    }
};
