<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180705085605 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER SEQUENCE tbl_answer_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_category_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_question_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_quiz_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_user_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_history_question_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_workout_id_seq INCREMENT BY 1');
        $this->addSql('CREATE SEQUENCE tbl_history_answer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE tbl_history_answer (id INT NOT NULL, date_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN tbl_history_answer.date_time IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER SEQUENCE tbl_user_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_answer_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_category_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_question_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_quiz_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_workout_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_history_question_id_seq INCREMENT BY 1');
        $this->addSql('DROP SEQUENCE tbl_history_answer_id_seq CASCADE');
        $this->addSql('DROP TABLE tbl_history_answer');
    }
}
