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
        // Beispiel: Erstelle eine neue MySQL-Tabelle
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('ort');
            $table->string('postleitzahl');
            $table->string('strasse');
            $table->string('hausnummer');
            $table->integer('wohneinheiten');
            $table->integer('bestand');
            $table->string('status');
            $table->string('notiz');
            $table->timestamps();
        });
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('detail');
            $table->timestamps();
        });

        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $teams = config('permission.teams');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }
        if ($teams && empty($columnNames['team_foreign_key'] ?? null)) {
            throw new \Exception('Error: team_foreign_key on config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->bigIncrements('id'); // permission id
            $table->string('name');       // For MySQL 8.0 use string('name', 125);
            $table->string('guard_name'); // For MySQL 8.0 use string('guard_name', 125);
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create($tableNames['roles'], function (Blueprint $table) use ($teams, $columnNames) {
            $table->bigIncrements('id'); // role id
            if ($teams || config('permission.testing')) { // permission.testing is a fix for sqlite testing
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable();
                $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
            }
            $table->string('name');       // For MySQL 8.0 use string('name', 125);
            $table->string('guard_name'); // For MySQL 8.0 use string('guard_name', 125);
            $table->timestamps();
            if ($teams || config('permission.testing')) {
                $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']);
            } else {
                $table->unique(['name', 'guard_name']);
            }
        });

        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $columnNames, $teams) {
            $table->unsignedBigInteger(PermissionRegistrar::$pivotPermission);
            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign(PermissionRegistrar::$pivotPermission)
                ->references('id') // permission id
                ->on($tableNames['permissions'])
                ->onDelete('cascade');
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');

                $table->primary([$columnNames['team_foreign_key'], PermissionRegistrar::$pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
            } else {
                $table->primary([PermissionRegistrar::$pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
            }

        });

        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $columnNames, $teams) {
            $table->unsignedBigInteger(PermissionRegistrar::$pivotRole);

            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign(PermissionRegistrar::$pivotRole)
                ->references('id') // role id
                ->on($tableNames['roles'])
                ->onDelete('cascade');
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');

                $table->primary([$columnNames['team_foreign_key'], PermissionRegistrar::$pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
            } else {
                $table->primary([PermissionRegistrar::$pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
            }
        });

        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames) {
            $table->unsignedBigInteger(PermissionRegistrar::$pivotPermission);
            $table->unsignedBigInteger(PermissionRegistrar::$pivotRole);

            $table->foreign(PermissionRegistrar::$pivotPermission)
                ->references('id') // permission id
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->foreign(PermissionRegistrar::$pivotRole)
                ->references('id') // role id
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary([PermissionRegistrar::$pivotPermission, PermissionRegistrar::$pivotRole], 'role_has_permissions_permission_id_role_id_primary');
        });

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('project_user', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->primary(['project_id', 'user_id']);
        });

        Schema::create('streets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('project_user', function (Blueprint $table) {
            $table->string('hausnummer')->nullable();
        });

                // Beispiel: Kopiere Daten von SQLite-Tabelle zu MySQL-Tabelle
                $sqliteDataN = DB::connection('sqlite')->table('projects')->get();
                foreach ($sqliteDataN as $record) {
                    DB::connection('mysql')->table('projects')->insert((array) $record);
                }
                $sqliteDataN2= DB::connection('sqlite')->table('password_reset_tokens')->get();
                foreach ($sqliteDataN2 as $record) {
                    DB::connection('mysql')->table('password_reset_tokens')->insert((array) $record);
                }
                $sqliteDataN3= DB::connection('sqlite')->table('password_resets')->get();
                foreach ($sqliteDataN3 as $record) {
                    DB::connection('mysql')->table('password_resets')->insert((array) $record);
                }
                $sqliteDataN4= DB::connection('sqlite')->table('failed_jobs')->get();
                foreach ($sqliteDataN4 as $record) {
                    DB::connection('mysql')->table('failed_jobs')->insert((array) $record);
                }
                $sqliteDataN5= DB::connection('sqlite')->table('products')->get();
                foreach ($sqliteDataN5 as $record) {
                    DB::connection('mysql')->table('products')->insert((array) $record);
                }
                $sqliteDataN7= DB::connection('sqlite')->table('users')->get();
                foreach ($sqliteDataN7 as $record) {
                    DB::connection('mysql')->table('users')->insert((array) $record);
                }
                $sqliteDataN8= DB::connection('sqlite')->table('permissions')->get();
                foreach ($sqliteDataN8 as $record) {
                    DB::connection('mysql')->table('permissions')->insert((array) $record);
                }
                $sqliteDataN9= DB::connection('sqlite')->table('roles')->get();
                foreach ($sqliteDataN9 as $record) {
                    DB::connection('mysql')->table('roles')->insert((array) $record);
                }
                $sqliteDataN10= DB::connection('sqlite')->table('model_has_permissions')->get();
                foreach ($sqliteDataN10 as $record) {
                    DB::connection('mysql')->table('model_has_permissions')->insert((array) $record);
                }
                $sqliteDataN11= DB::connection('sqlite')->table('model_has_roles')->get();
                foreach ($sqliteDataN11 as $record) {
                    DB::connection('mysql')->table('model_has_roles')->insert((array) $record);
                }
                $sqliteDataN12= DB::connection('sqlite')->table('role_has_permissions')->get();
                foreach ($sqliteDataN12 as $record) {
                    DB::connection('mysql')->table('role_has_permissions')->insert((array) $record);
                }
                $sqliteDataN13= DB::connection('sqlite')->table('users')->get();
                foreach ($sqliteDataN13 as $record) {
                    DB::connection('mysql')->table('users')->insert((array) $record);
                }
                $sqliteDataN14= DB::connection('sqlite')->table('personal_access_tokens')->get();
                foreach ($sqliteDataN14 as $record) {
                    DB::connection('mysql')->table('personal_access_tokens')->insert((array) $record);
                }
                
                $sqliteDataN16= DB::connection('sqlite')->table('streets')->get();
                foreach ($sqliteDataN16 as $record) {
                    DB::connection('mysql')->table('streets')->insert((array) $record);
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
