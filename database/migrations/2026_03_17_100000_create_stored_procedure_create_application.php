<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared("
            CREATE PROCEDURE create_application_with_validation(
                IN p_user_id INT,
                IN p_internship_id INT,
                IN p_motivation_letter TEXT,
                OUT p_application_id INT,
                OUT p_error_message VARCHAR(255)
            )
            BEGIN
                DECLARE v_user_exists INT DEFAULT 0;
                DECLARE v_internship_exists INT DEFAULT 0;
                DECLARE v_internship_start DATE;
                DECLARE v_internship_end DATE;
                DECLARE v_existing_application INT DEFAULT 0;
                DECLARE v_user_role VARCHAR(50);
                DECLARE v_conflicting_application INT DEFAULT 0;

                -- Initialize output
                SET p_application_id = NULL;
                SET p_error_message = NULL;

                -- Start transaction
                START TRANSACTION;

                -- a) Pārbauda vai lietotājs ir datubāzē
                SELECT COUNT(*) INTO v_user_exists
                FROM users
                WHERE id = p_user_id;

                IF v_user_exists = 0 THEN
                    SET p_error_message = 'Lietotājs netika atrasts datubāzē.';
                    ROLLBACK;
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Lietotājs netika atrasts datubāzē.';
                END IF;

                -- b) Pārbauda vai prakse ir derīga
                SELECT COUNT(*), start_at, end_at
                INTO v_internship_exists, v_internship_start, v_internship_end
                FROM internships
                WHERE id = p_internship_id;

                IF v_internship_exists = 0 THEN
                    SET p_error_message = 'Prakse netika atrasta.';
                    ROLLBACK;
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Prakse netika atrasta.';
                END IF;

                -- Pārbauda vai prakse ir aktīva
                IF NOW() < v_internship_start THEN
                    SET p_error_message = 'Prakse vēl nav sākusies.';
                    ROLLBACK;
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Prakse vēl nav sākusies.';
                END IF;

                IF NOW() > v_internship_end THEN
                    SET p_error_message = 'Prakse ir beigusies.';
                    ROLLBACK;
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Prakse ir beigusies.';
                END IF;

                -- c) Pārbauda vai lietotājam atļauts pieteikties šajā praksei
                -- Pārbauda vai lietotājs jau nav pieteicies šai praksei
                SELECT COUNT(*) INTO v_existing_application
                FROM applications
                WHERE user_id = p_user_id
                AND internship_id = p_internship_id;

                IF v_existing_application > 0 THEN
                    SET p_error_message = 'Jūs jau esat pieteicies šai praksei.';
                    ROLLBACK;
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Jūs jau esat pieteicies šai praksei.';
                END IF;

                -- Pārbauda vai lietotājs ir studenta lomā
                SELECT r.name INTO v_user_role
                FROM users u
                LEFT JOIN roles r ON u.role_id = r.id
                WHERE u.id = p_user_id;

                IF v_user_role IS NOT NULL AND v_user_role != 'students' THEN
                    SET p_error_message = 'Tikai studenti var pieteikties praksēm.';
                    ROLLBACK;
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Tikai studenti var pieteikties praksēm.';
                END IF;

                -- Pārbauda vai lietotājs nav jau apstiprināts citai praksei šajā periodā
                SELECT COUNT(*) INTO v_conflicting_application
                FROM applications a
                JOIN internships i ON a.internship_id = i.id
                WHERE a.user_id = p_user_id
                AND a.is_approved = TRUE
                AND i.start_at <= v_internship_end
                AND i.end_at >= v_internship_start;

                IF v_conflicting_application > 0 THEN
                    SET p_error_message = 'Jūs jau esat apstiprināts citai praksei šajā laika periodā.';
                    ROLLBACK;
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Jūs jau esat apstiprināts citai praksei šajā laika periodā.';
                END IF;

                -- Izveido prakses pieteikumu
                INSERT INTO applications (user_id, internship_id, motivation_letter, is_approved, approved_at, created_at, updated_at)
                VALUES (p_user_id, p_internship_id, p_motivation_letter, FALSE, NULL, NOW(), NOW());

                SET p_application_id = LAST_INSERT_ID();

                -- Commit transaction
                COMMIT;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS create_application_with_validation");
    }
};
