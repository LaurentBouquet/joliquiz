<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230214211133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tbl_answer (id INT AUTO_INCREMENT NOT NULL, question_id INT NOT NULL, text LONGTEXT NOT NULL, correct TINYINT(1) NOT NULL, INDEX IDX_577B239A1E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_category (id INT AUTO_INCREMENT NOT NULL, language_id VARCHAR(2) NOT NULL, created_by_id INT DEFAULT NULL, shortname VARCHAR(50) NOT NULL, longname VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_517FFFEC82F1BAF4 (language_id), INDEX IDX_517FFFECB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_group (id INT AUTO_INCREMENT NOT NULL, school_id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, shortname VARCHAR(50) DEFAULT NULL, ed_id INT DEFAULT NULL, INDEX IDX_C3A40EDAC32A47EE (school_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_history_answer (id INT AUTO_INCREMENT NOT NULL, question_history_id INT NOT NULL, answer_id INT NOT NULL, answer_text LONGTEXT NOT NULL, answer_correct TINYINT(1) NOT NULL, correct_given TINYINT(1) NOT NULL, answer_succes TINYINT(1) NOT NULL, INDEX IDX_9E994D3B79D11BDA (question_history_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_history_question (id INT AUTO_INCREMENT NOT NULL, workout_id INT NOT NULL, question_id INT NOT NULL, question_text LONGTEXT NOT NULL, completed TINYINT(1) NOT NULL, question_success TINYINT(1) DEFAULT NULL, duration VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:dateinterval)\', started_at DATETIME NOT NULL, ended_at DATETIME DEFAULT NULL, INDEX IDX_FCD2A776A6CCCFC9 (workout_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_language (id VARCHAR(2) NOT NULL, english_name VARCHAR(50) NOT NULL, native_name VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_83E89798734D08E1 (english_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_question (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, language_id VARCHAR(2) NOT NULL, text LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, max_duration INT DEFAULT NULL, INDEX IDX_E1C4AF63B03A8386 (created_by_id), INDEX IDX_E1C4AF6382F1BAF4 (language_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_question_category (question_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_DB45675F1E27F6BF (question_id), INDEX IDX_DB45675F12469DE2 (category_id), PRIMARY KEY(question_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_quiz (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, language_id VARCHAR(2) NOT NULL, title VARCHAR(255) NOT NULL, summary LONGTEXT DEFAULT NULL, number_of_questions INT NOT NULL, active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, show_result_question TINYINT(1) NOT NULL, show_result_quiz TINYINT(1) NOT NULL, allow_anonymous_workout TINYINT(1) NOT NULL, result_quiz_comment LONGTEXT DEFAULT NULL, start_quiz_comment LONGTEXT DEFAULT NULL, default_question_max_duration INT DEFAULT NULL, actived_at DATETIME DEFAULT NULL, INDEX IDX_1132AF7AB03A8386 (created_by_id), INDEX IDX_1132AF7A82F1BAF4 (language_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_quiz_category (quiz_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_C91A858F853CD175 (quiz_id), INDEX IDX_C91A858F12469DE2 (category_id), PRIMARY KEY(quiz_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_school (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(16) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_session (id INT AUTO_INCREMENT NOT NULL, quiz_id INT NOT NULL, started_at DATETIME NOT NULL, ended_at DATETIME DEFAULT NULL, sended_to_ed TINYINT(1) DEFAULT NULL, INDEX IDX_8B17DDA0853CD175 (quiz_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `tbl_user` (id INT AUTO_INCREMENT NOT NULL, prefered_language_id VARCHAR(2) DEFAULT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, username VARCHAR(255) DEFAULT NULL, is_active TINYINT(1) NOT NULL, password_requested_at DATETIME DEFAULT NULL, token LONGTEXT DEFAULT NULL, organization_code VARCHAR(16) DEFAULT NULL, organization_label VARCHAR(255) DEFAULT NULL, account_type VARCHAR(5) DEFAULT NULL, phone VARCHAR(16) DEFAULT NULL, comment LONGTEXT DEFAULT NULL, login_type VARCHAR(2) DEFAULT NULL, to_receive_my_result_by_email TINYINT(1) DEFAULT NULL, firstname VARCHAR(50) DEFAULT NULL, lastname VARCHAR(50) DEFAULT NULL, current_school_year VARCHAR(20) DEFAULT NULL, ed_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_38B383A1E7927C74 (email), INDEX IDX_38B383A197E28A86 (prefered_language_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_user_group (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_E641F50CA76ED395 (user_id), INDEX IDX_E641F50CFE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_workout (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, quiz_id INT NOT NULL, session_id INT DEFAULT NULL, started_at DATETIME NOT NULL, ended_at DATETIME DEFAULT NULL, number_of_questions INT NOT NULL, completed TINYINT(1) NOT NULL, score DOUBLE PRECISION DEFAULT NULL, comment LONGTEXT DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, INDEX IDX_3FCCF306CB944F1A (student_id), INDEX IDX_3FCCF306853CD175 (quiz_id), INDEX IDX_3FCCF306613FECDF (session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tbl_answer ADD CONSTRAINT FK_577B239A1E27F6BF FOREIGN KEY (question_id) REFERENCES tbl_question (id)');
        $this->addSql('ALTER TABLE tbl_category ADD CONSTRAINT FK_517FFFEC82F1BAF4 FOREIGN KEY (language_id) REFERENCES tbl_language (id)');
        $this->addSql('ALTER TABLE tbl_category ADD CONSTRAINT FK_517FFFECB03A8386 FOREIGN KEY (created_by_id) REFERENCES `tbl_user` (id)');
        $this->addSql('ALTER TABLE tbl_group ADD CONSTRAINT FK_C3A40EDAC32A47EE FOREIGN KEY (school_id) REFERENCES tbl_school (id)');
        $this->addSql('ALTER TABLE tbl_history_answer ADD CONSTRAINT FK_9E994D3B79D11BDA FOREIGN KEY (question_history_id) REFERENCES tbl_history_question (id)');
        $this->addSql('ALTER TABLE tbl_history_question ADD CONSTRAINT FK_FCD2A776A6CCCFC9 FOREIGN KEY (workout_id) REFERENCES tbl_workout (id)');
        $this->addSql('ALTER TABLE tbl_question ADD CONSTRAINT FK_E1C4AF63B03A8386 FOREIGN KEY (created_by_id) REFERENCES `tbl_user` (id)');
        $this->addSql('ALTER TABLE tbl_question ADD CONSTRAINT FK_E1C4AF6382F1BAF4 FOREIGN KEY (language_id) REFERENCES tbl_language (id)');
        $this->addSql('ALTER TABLE tbl_question_category ADD CONSTRAINT FK_DB45675F1E27F6BF FOREIGN KEY (question_id) REFERENCES tbl_question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tbl_question_category ADD CONSTRAINT FK_DB45675F12469DE2 FOREIGN KEY (category_id) REFERENCES tbl_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tbl_quiz ADD CONSTRAINT FK_1132AF7AB03A8386 FOREIGN KEY (created_by_id) REFERENCES `tbl_user` (id)');
        $this->addSql('ALTER TABLE tbl_quiz ADD CONSTRAINT FK_1132AF7A82F1BAF4 FOREIGN KEY (language_id) REFERENCES tbl_language (id)');
        $this->addSql('ALTER TABLE tbl_quiz_category ADD CONSTRAINT FK_C91A858F853CD175 FOREIGN KEY (quiz_id) REFERENCES tbl_quiz (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tbl_quiz_category ADD CONSTRAINT FK_C91A858F12469DE2 FOREIGN KEY (category_id) REFERENCES tbl_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tbl_session ADD CONSTRAINT FK_8B17DDA0853CD175 FOREIGN KEY (quiz_id) REFERENCES tbl_quiz (id)');
        $this->addSql('ALTER TABLE `tbl_user` ADD CONSTRAINT FK_38B383A197E28A86 FOREIGN KEY (prefered_language_id) REFERENCES tbl_language (id)');
        $this->addSql('ALTER TABLE tbl_user_group ADD CONSTRAINT FK_E641F50CA76ED395 FOREIGN KEY (user_id) REFERENCES `tbl_user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tbl_user_group ADD CONSTRAINT FK_E641F50CFE54D947 FOREIGN KEY (group_id) REFERENCES tbl_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tbl_workout ADD CONSTRAINT FK_3FCCF306CB944F1A FOREIGN KEY (student_id) REFERENCES `tbl_user` (id)');
        $this->addSql('ALTER TABLE tbl_workout ADD CONSTRAINT FK_3FCCF306853CD175 FOREIGN KEY (quiz_id) REFERENCES tbl_quiz (id)');
        $this->addSql('ALTER TABLE tbl_workout ADD CONSTRAINT FK_3FCCF306613FECDF FOREIGN KEY (session_id) REFERENCES tbl_session (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_answer DROP FOREIGN KEY FK_577B239A1E27F6BF');
        $this->addSql('ALTER TABLE tbl_category DROP FOREIGN KEY FK_517FFFEC82F1BAF4');
        $this->addSql('ALTER TABLE tbl_category DROP FOREIGN KEY FK_517FFFECB03A8386');
        $this->addSql('ALTER TABLE tbl_group DROP FOREIGN KEY FK_C3A40EDAC32A47EE');
        $this->addSql('ALTER TABLE tbl_history_answer DROP FOREIGN KEY FK_9E994D3B79D11BDA');
        $this->addSql('ALTER TABLE tbl_history_question DROP FOREIGN KEY FK_FCD2A776A6CCCFC9');
        $this->addSql('ALTER TABLE tbl_question DROP FOREIGN KEY FK_E1C4AF63B03A8386');
        $this->addSql('ALTER TABLE tbl_question DROP FOREIGN KEY FK_E1C4AF6382F1BAF4');
        $this->addSql('ALTER TABLE tbl_question_category DROP FOREIGN KEY FK_DB45675F1E27F6BF');
        $this->addSql('ALTER TABLE tbl_question_category DROP FOREIGN KEY FK_DB45675F12469DE2');
        $this->addSql('ALTER TABLE tbl_quiz DROP FOREIGN KEY FK_1132AF7AB03A8386');
        $this->addSql('ALTER TABLE tbl_quiz DROP FOREIGN KEY FK_1132AF7A82F1BAF4');
        $this->addSql('ALTER TABLE tbl_quiz_category DROP FOREIGN KEY FK_C91A858F853CD175');
        $this->addSql('ALTER TABLE tbl_quiz_category DROP FOREIGN KEY FK_C91A858F12469DE2');
        $this->addSql('ALTER TABLE tbl_session DROP FOREIGN KEY FK_8B17DDA0853CD175');
        $this->addSql('ALTER TABLE `tbl_user` DROP FOREIGN KEY FK_38B383A197E28A86');
        $this->addSql('ALTER TABLE tbl_user_group DROP FOREIGN KEY FK_E641F50CA76ED395');
        $this->addSql('ALTER TABLE tbl_user_group DROP FOREIGN KEY FK_E641F50CFE54D947');
        $this->addSql('ALTER TABLE tbl_workout DROP FOREIGN KEY FK_3FCCF306CB944F1A');
        $this->addSql('ALTER TABLE tbl_workout DROP FOREIGN KEY FK_3FCCF306853CD175');
        $this->addSql('ALTER TABLE tbl_workout DROP FOREIGN KEY FK_3FCCF306613FECDF');
        $this->addSql('DROP TABLE tbl_answer');
        $this->addSql('DROP TABLE tbl_category');
        $this->addSql('DROP TABLE tbl_group');
        $this->addSql('DROP TABLE tbl_history_answer');
        $this->addSql('DROP TABLE tbl_history_question');
        $this->addSql('DROP TABLE tbl_language');
        $this->addSql('DROP TABLE tbl_question');
        $this->addSql('DROP TABLE tbl_question_category');
        $this->addSql('DROP TABLE tbl_quiz');
        $this->addSql('DROP TABLE tbl_quiz_category');
        $this->addSql('DROP TABLE tbl_school');
        $this->addSql('DROP TABLE tbl_session');
        $this->addSql('DROP TABLE `tbl_user`');
        $this->addSql('DROP TABLE tbl_user_group');
        $this->addSql('DROP TABLE tbl_workout');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
