<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Certaines migrations interrogent information_schema pour éviter de recréer
 * une clé étrangère existante. La requête diffère entre MySQL et PostgreSQL
 * (DATABASE() / REFERENCED_TABLE_NAME n'existent pas en PostgreSQL).
 *
 * Ce trait centralise la vérification de façon compatible avec les deux drivers.
 */
trait ChecksForeignKeyExists
{
    /**
     * Vérifie si une clé étrangère existe déjà sur la colonne donnée.
     *
     * @return array<int, object> Les contraintes trouvées (vide si aucune).
     */
    protected function foreignKeyExists(string $tableName, string $columnName): array
    {
        if (DB::getDriverName() === 'pgsql') {
            return DB::select(
                "
                SELECT kcu.constraint_name AS constraint_name
                FROM information_schema.table_constraints tc
                JOIN information_schema.key_column_usage kcu
                    ON kcu.constraint_name = tc.constraint_name
                    AND kcu.table_schema = tc.table_schema
                WHERE tc.constraint_type = 'FOREIGN KEY'
                    AND tc.table_schema = current_schema()
                    AND tc.table_name = ?
                    AND kcu.column_name = ?
                ",
                [$tableName, $columnName]
            );
        }

        return DB::select(
            "
            SELECT CONSTRAINT_NAME AS constraint_name
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = ?
                AND COLUMN_NAME = ?
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ",
            [$tableName, $columnName]
        );
    }
}
