<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        // Vista view_active_drugs
        DB::statement("
            CREATE OR REPLACE VIEW view_active_drugs AS
            SELECT
                dc.descripcion AS category,
                d.descripcion AS descripcion,
                dp.descripcion AS presentation,
                d.created_at AS created_at,
                d.id AS id
            FROM drogas d
            JOIN droga_categoria dc ON d.id_categoria = dc.id
            JOIN droga_presentacion dp ON d.id_presentacion = dp.id
            WHERE d.deleted_at IS NULL
              AND dc.deleted_at IS NULL
              AND dp.deleted_at IS NULL
            ORDER BY d.descripcion ASC
        ");

        // Vista view_active_histories
        DB::statement("
            CREATE OR REPLACE VIEW view_active_histories AS
            SELECT
                CAST(h.created_at AS DATE) AS fecha,
                h.dni AS dni,
                UCASE(h.nombres) AS nombres,
                h.fecha_nacimiento AS fecha_nacimiento,
                YEAR(CURDATE()) - YEAR(h.fecha_nacimiento) -
                (RIGHT(CURDATE(), 5) < RIGHT(h.fecha_nacimiento, 5)) AS edad,
                s.descripcion AS sexo,
                h.id AS id
            FROM historias h
            JOIN sexo s ON h.id_sexo = s.id
            WHERE h.deleted_at IS NULL
              AND h.estado = '1'
            ORDER BY h.id DESC
        ");

        // Vista view_active_users
        DB::statement("
            CREATE OR REPLACE VIEW view_active_users AS
            SELECT
                u.name AS name,
                u.email AS email,
                ul.last_login AS last_login,
                u.created_at AS created_at,
                u.id AS id
            FROM users u
            LEFT JOIN user_last_login ul
                ON u.id = ul.id_user
                AND ul.id = (
                    SELECT MAX(ull.id)
                    FROM user_last_login ull
                    WHERE ull.id_user = u.id
                    AND u.deleted_at IS NULL
                )
        ");

        // Vista view_user_roles_last_login
        DB::statement("
            CREATE OR REPLACE VIEW view_user_roles_last_login AS
            SELECT
                u.name AS name,
                u.email AS email,
                r.name AS rol,
                ull.last_login AS last_login,
                u.created_at AS created_at,
                u.id AS id
            FROM users u
            LEFT JOIN model_has_roles mhr
                ON u.id = mhr.model_id
                AND mhr.model_type = 'App\\Models\\User'
            LEFT JOIN roles r ON mhr.role_id = r.id
            LEFT JOIN (
                SELECT id_user, MAX(last_login) AS last_login
                FROM user_last_login
                GROUP BY id_user
            ) ull ON u.id = ull.id_user
            WHERE u.deleted_at IS NULL
        ");

        // Vista vista_estado_civil
        DB::statement("
            CREATE OR REPLACE VIEW vista_estado_civil AS
            SELECT
                e.descripcion AS estados,
                COUNT(h.dni) AS cantidad
            FROM historias h
            JOIN estado_civil e ON h.id_estado = e.id
            GROUP BY e.descripcion
            HAVING COUNT(h.dni) >= 1
            ORDER BY COUNT(h.dni) DESC
        ");

        // Vista vista_grupo_sanguineo
        DB::statement("
            CREATE OR REPLACE VIEW vista_grupo_sanguineo AS
            SELECT
                g.descripcion AS grupos,
                COUNT(h.dni) AS cantidad
            FROM historias h
            JOIN grupo_sanguineo g ON h.id_gs = g.id
            GROUP BY g.descripcion
            HAVING COUNT(h.dni) >= 1
            ORDER BY COUNT(h.dni) DESC
        ");

        // Vista vista_tabaquismo
        DB::statement("
            CREATE OR REPLACE VIEW vista_tabaquismo AS
            SELECT
                t.consumo AS label,
                COUNT(h.dni) AS cantidad
            FROM historias h
            JOIN tabaquismo t ON h.id_ct = t.id
            GROUP BY t.consumo
            ORDER BY COUNT(h.dni) ASC
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS view_active_drugs');
        DB::statement('DROP VIEW IF EXISTS view_active_histories');
        DB::statement('DROP VIEW IF EXISTS view_active_users');
        DB::statement('DROP VIEW IF EXISTS view_user_roles_last_login');
        DB::statement('DROP VIEW IF EXISTS vista_estado_civil');
        DB::statement('DROP VIEW IF EXISTS vista_grupo_sanguineo');
        DB::statement('DROP VIEW IF EXISTS vista_tabaquismo');
    }
};
