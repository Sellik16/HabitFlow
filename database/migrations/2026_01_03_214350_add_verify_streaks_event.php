<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared("
            ALTER TABLE completions
            ADD INDEX IF NOT EXISTS idx_completions_habit_completed (habit_id, completed_at);
        ");

        DB::unprepared("
            DROP PROCEDURE IF EXISTS verify_streaks_reset;
        ");

        DB::unprepared("
            CREATE PROCEDURE verify_streaks_reset()
            BEGIN
                UPDATE habits h
                LEFT JOIN (
                    SELECT habit_id, MAX(completed_at) AS last_completed_at
                    FROM completions
                    GROUP BY habit_id
                ) lc ON lc.habit_id = h.id
                SET h.current_streak = 0
                WHERE h.current_streak > 0
                  AND (
                    lc.last_completed_at IS NULL
                    OR lc.last_completed_at < (CURDATE() - INTERVAL 1 DAY)
                  );
            END
            ");

        DB::unprepared("
            DROP EVENT IF EXISTS ev_verify_streaks_daily;
        ");

        DB::unprepared("
            CREATE EVENT ev_verify_streaks_daily
            ON SCHEDULE EVERY 1 DAY
            STARTS TIMESTAMP(CURDATE() + INTERVAL 1 DAY)
            DO
              CALL verify_streaks_reset();
        ");    }

    public function down(): void
    {
        DB::unprepared("DROP EVENT IF EXISTS ev_verify_streaks_daily;");
        DB::unprepared("DROP PROCEDURE IF EXISTS verify_streaks_reset;");
        DB::unprepared("DROP INDEX IF EXISTS idx_completions_habit_completed ON completions;");
    }
};

