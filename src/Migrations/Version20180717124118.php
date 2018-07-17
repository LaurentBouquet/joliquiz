<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180717124118 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER SEQUENCE tbl_user_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_history_question_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_answer_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_history_answer_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_category_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_question_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_quiz_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_workout_id_seq INCREMENT BY 1');
        $this->addSql('ALTER TABLE tbl_category ADD language_id VARCHAR(2) NOT NULL');
        $this->addSql('ALTER TABLE tbl_category ADD CONSTRAINT FK_517FFFEC82F1BAF4 FOREIGN KEY (language_id) REFERENCES tbl_language (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_517FFFEC82F1BAF4 ON tbl_category (language_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER SEQUENCE tbl_user_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_answer_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_category_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_question_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_workout_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_quiz_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_history_question_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_history_answer_id_seq INCREMENT BY 1');
        $this->addSql('ALTER TABLE tbl_category DROP CONSTRAINT FK_517FFFEC82F1BAF4');
        $this->addSql('DROP INDEX IDX_517FFFEC82F1BAF4');
        $this->addSql('ALTER TABLE tbl_category DROP language_id');
    }
}
