<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201227105222 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tbl_session (id INT AUTO_INCREMENT NOT NULL, quiz_id INT NOT NULL, started_at DATETIME NOT NULL, ended_at DATETIME DEFAULT NULL, INDEX IDX_8B17DDA0853CD175 (quiz_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tbl_session ADD CONSTRAINT FK_8B17DDA0853CD175 FOREIGN KEY (quiz_id) REFERENCES tbl_quiz (id)');
        $this->addSql('DROP TABLE session');
        $this->addSql('ALTER TABLE tbl_question CHANGE max_duration max_duration INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_history_question CHANGE question_success question_success TINYINT(1) DEFAULT NULL, CHANGE duration duration CHAR(255) DEFAULT NULL COMMENT \'(DC2Type:dateinterval)\', CHANGE ended_at ended_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_quiz CHANGE default_question_max_duration default_question_max_duration INT DEFAULT NULL, CHANGE actived_at actived_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_user CHANGE prefered_language_id prefered_language_id VARCHAR(2) DEFAULT NULL, CHANGE password_requested_at password_requested_at DATETIME DEFAULT NULL, CHANGE token token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_workout CHANGE ended_at ended_at DATETIME DEFAULT NULL, CHANGE score score DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, quiz_id INT NOT NULL, started_at DATETIME NOT NULL, ended_at DATETIME DEFAULT \'NULL\', INDEX IDX_D044D5D4853CD175 (quiz_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4853CD175 FOREIGN KEY (quiz_id) REFERENCES tbl_quiz (id)');
        $this->addSql('DROP TABLE tbl_session');
        $this->addSql('ALTER TABLE tbl_history_question CHANGE question_success question_success TINYINT(1) DEFAULT \'NULL\', CHANGE duration duration CHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci COMMENT \'(DC2Type:dateinterval)\', CHANGE ended_at ended_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE tbl_question CHANGE max_duration max_duration INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_quiz CHANGE default_question_max_duration default_question_max_duration INT DEFAULT NULL, CHANGE actived_at actived_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE tbl_user CHANGE prefered_language_id prefered_language_id VARCHAR(2) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE password_requested_at password_requested_at DATETIME DEFAULT \'NULL\', CHANGE token token VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE tbl_workout CHANGE ended_at ended_at DATETIME DEFAULT \'NULL\', CHANGE score score DOUBLE PRECISION DEFAULT \'NULL\'');
    }
}
