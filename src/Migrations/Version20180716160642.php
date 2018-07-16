<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180716160642 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER SEQUENCE tbl_user_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_answer_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_category_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_question_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_workout_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_quiz_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_history_question_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_history_answer_id_seq INCREMENT BY 1');
        $this->addSql('CREATE TABLE tbl_language (id VARCHAR(2) NOT NULL, english_name VARCHAR(50) NOT NULL, native_name VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_83E89798734D08E1 ON tbl_language (english_name)');
        $this->addSql('ALTER TABLE tbl_user ADD prefered_language_id VARCHAR(2) DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_user ADD CONSTRAINT FK_38B383A197E28A86 FOREIGN KEY (prefered_language_id) REFERENCES tbl_language (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_38B383A197E28A86 ON tbl_user (prefered_language_id)');
        $this->addSql('ALTER TABLE tbl_question ADD language_id VARCHAR(2) NOT NULL');
        $this->addSql('ALTER TABLE tbl_question ADD CONSTRAINT FK_E1C4AF6382F1BAF4 FOREIGN KEY (language_id) REFERENCES tbl_language (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E1C4AF6382F1BAF4 ON tbl_question (language_id)');
        $this->addSql('ALTER TABLE tbl_quiz ADD language_id VARCHAR(2) NOT NULL');
        $this->addSql('ALTER TABLE tbl_quiz ADD CONSTRAINT FK_1132AF7A82F1BAF4 FOREIGN KEY (language_id) REFERENCES tbl_language (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_1132AF7A82F1BAF4 ON tbl_quiz (language_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE tbl_user DROP CONSTRAINT FK_38B383A197E28A86');
        $this->addSql('ALTER TABLE tbl_question DROP CONSTRAINT FK_E1C4AF6382F1BAF4');
        $this->addSql('ALTER TABLE tbl_quiz DROP CONSTRAINT FK_1132AF7A82F1BAF4');
        $this->addSql('ALTER SEQUENCE tbl_answer_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_category_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_question_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_workout_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_quiz_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_user_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_history_question_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_history_answer_id_seq INCREMENT BY 1');
        $this->addSql('DROP TABLE tbl_language');
        $this->addSql('DROP INDEX IDX_E1C4AF6382F1BAF4');
        $this->addSql('ALTER TABLE tbl_question DROP language_id');
        $this->addSql('DROP INDEX IDX_38B383A197E28A86');
        $this->addSql('ALTER TABLE tbl_user DROP prefered_language_id');
        $this->addSql('DROP INDEX IDX_1132AF7A82F1BAF4');
        $this->addSql('ALTER TABLE tbl_quiz DROP language_id');
    }
}
