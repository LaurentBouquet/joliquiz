<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180707214807 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE tbl_answer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tbl_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tbl_question_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tbl_workout_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tbl_quiz_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tbl_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tbl_history_question_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tbl_history_answer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE tbl_answer (id INT NOT NULL, question_id INT NOT NULL, text TEXT NOT NULL, correct BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_577B239A1E27F6BF ON tbl_answer (question_id)');
        $this->addSql('CREATE TABLE tbl_category (id INT NOT NULL, shortname VARCHAR(50) NOT NULL, longname VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tbl_question (id INT NOT NULL, text TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tbl_question_category (question_id INT NOT NULL, category_id INT NOT NULL, PRIMARY KEY(question_id, category_id))');
        $this->addSql('CREATE INDEX IDX_DB45675F1E27F6BF ON tbl_question_category (question_id)');
        $this->addSql('CREATE INDEX IDX_DB45675F12469DE2 ON tbl_question_category (category_id)');
        $this->addSql('CREATE TABLE tbl_workout (id INT NOT NULL, student_id INT NOT NULL, quiz_id INT NOT NULL, started_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, ended_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, number_of_questions INT NOT NULL, completed BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3FCCF306CB944F1A ON tbl_workout (student_id)');
        $this->addSql('CREATE INDEX IDX_3FCCF306853CD175 ON tbl_workout (quiz_id)');
        $this->addSql('CREATE TABLE tbl_quiz (id INT NOT NULL, title VARCHAR(255) NOT NULL, summary TEXT DEFAULT NULL, number_of_questions INT NOT NULL, active BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, show_result_question BOOLEAN NOT NULL, show_result_quiz BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tbl_quiz_category (quiz_id INT NOT NULL, category_id INT NOT NULL, PRIMARY KEY(quiz_id, category_id))');
        $this->addSql('CREATE INDEX IDX_C91A858F853CD175 ON tbl_quiz_category (quiz_id)');
        $this->addSql('CREATE INDEX IDX_C91A858F12469DE2 ON tbl_quiz_category (category_id)');
        $this->addSql('CREATE TABLE tbl_user (id INT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(64) NOT NULL, is_active BOOLEAN NOT NULL, roles TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_38B383A1F85E0677 ON tbl_user (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_38B383A1E7927C74 ON tbl_user (email)');
        $this->addSql('COMMENT ON COLUMN tbl_user.roles IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE tbl_history_question (id INT NOT NULL, workout_id INT NOT NULL, question_id INT NOT NULL, question_text TEXT NOT NULL, completed BOOLEAN NOT NULL, question_success BOOLEAN DEFAULT NULL, duration CHAR(255) DEFAULT NULL, started_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, ended_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FCD2A776A6CCCFC9 ON tbl_history_question (workout_id)');
        $this->addSql('COMMENT ON COLUMN tbl_history_question.duration IS \'(DC2Type:dateinterval)\'');
        $this->addSql('CREATE TABLE tbl_history_answer (id INT NOT NULL, question_history_id INT NOT NULL, answer_id INT NOT NULL, answer_text TEXT NOT NULL, answer_correct BOOLEAN NOT NULL, correct_given BOOLEAN NOT NULL, answer_succes BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9E994D3B79D11BDA ON tbl_history_answer (question_history_id)');
        $this->addSql('ALTER TABLE tbl_answer ADD CONSTRAINT FK_577B239A1E27F6BF FOREIGN KEY (question_id) REFERENCES tbl_question (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tbl_question_category ADD CONSTRAINT FK_DB45675F1E27F6BF FOREIGN KEY (question_id) REFERENCES tbl_question (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tbl_question_category ADD CONSTRAINT FK_DB45675F12469DE2 FOREIGN KEY (category_id) REFERENCES tbl_category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tbl_workout ADD CONSTRAINT FK_3FCCF306CB944F1A FOREIGN KEY (student_id) REFERENCES tbl_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tbl_workout ADD CONSTRAINT FK_3FCCF306853CD175 FOREIGN KEY (quiz_id) REFERENCES tbl_quiz (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tbl_quiz_category ADD CONSTRAINT FK_C91A858F853CD175 FOREIGN KEY (quiz_id) REFERENCES tbl_quiz (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tbl_quiz_category ADD CONSTRAINT FK_C91A858F12469DE2 FOREIGN KEY (category_id) REFERENCES tbl_category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tbl_history_question ADD CONSTRAINT FK_FCD2A776A6CCCFC9 FOREIGN KEY (workout_id) REFERENCES tbl_workout (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tbl_history_answer ADD CONSTRAINT FK_9E994D3B79D11BDA FOREIGN KEY (question_history_id) REFERENCES tbl_history_question (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE tbl_question_category DROP CONSTRAINT FK_DB45675F12469DE2');
        $this->addSql('ALTER TABLE tbl_quiz_category DROP CONSTRAINT FK_C91A858F12469DE2');
        $this->addSql('ALTER TABLE tbl_answer DROP CONSTRAINT FK_577B239A1E27F6BF');
        $this->addSql('ALTER TABLE tbl_question_category DROP CONSTRAINT FK_DB45675F1E27F6BF');
        $this->addSql('ALTER TABLE tbl_history_question DROP CONSTRAINT FK_FCD2A776A6CCCFC9');
        $this->addSql('ALTER TABLE tbl_workout DROP CONSTRAINT FK_3FCCF306853CD175');
        $this->addSql('ALTER TABLE tbl_quiz_category DROP CONSTRAINT FK_C91A858F853CD175');
        $this->addSql('ALTER TABLE tbl_workout DROP CONSTRAINT FK_3FCCF306CB944F1A');
        $this->addSql('ALTER TABLE tbl_history_answer DROP CONSTRAINT FK_9E994D3B79D11BDA');
        $this->addSql('DROP SEQUENCE tbl_answer_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tbl_category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tbl_question_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tbl_workout_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tbl_quiz_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tbl_user_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tbl_history_question_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tbl_history_answer_id_seq CASCADE');
        $this->addSql('DROP TABLE tbl_answer');
        $this->addSql('DROP TABLE tbl_category');
        $this->addSql('DROP TABLE tbl_question');
        $this->addSql('DROP TABLE tbl_question_category');
        $this->addSql('DROP TABLE tbl_workout');
        $this->addSql('DROP TABLE tbl_quiz');
        $this->addSql('DROP TABLE tbl_quiz_category');
        $this->addSql('DROP TABLE tbl_user');
        $this->addSql('DROP TABLE tbl_history_question');
        $this->addSql('DROP TABLE tbl_history_answer');
    }
}
