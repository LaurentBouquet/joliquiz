<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201226214601 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tbl_question CHANGE max_duration max_duration INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_history_question CHANGE question_success question_success TINYINT(1) DEFAULT NULL, CHANGE duration duration CHAR(255) DEFAULT NULL COMMENT \'(DC2Type:dateinterval)\', CHANGE ended_at ended_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_quiz ADD actived_at DATETIME DEFAULT NULL, CHANGE default_question_max_duration default_question_max_duration INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_user CHANGE prefered_language_id prefered_language_id VARCHAR(2) DEFAULT NULL, CHANGE password_requested_at password_requested_at DATETIME DEFAULT NULL, CHANGE token token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_workout CHANGE ended_at ended_at DATETIME DEFAULT NULL, CHANGE score score DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tbl_history_question CHANGE question_success question_success TINYINT(1) DEFAULT \'NULL\', CHANGE duration duration CHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci COMMENT \'(DC2Type:dateinterval)\', CHANGE ended_at ended_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE tbl_question CHANGE max_duration max_duration INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_quiz DROP actived_at, CHANGE default_question_max_duration default_question_max_duration INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_user CHANGE prefered_language_id prefered_language_id VARCHAR(2) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE password_requested_at password_requested_at DATETIME DEFAULT \'NULL\', CHANGE token token VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE tbl_workout CHANGE ended_at ended_at DATETIME DEFAULT \'NULL\', CHANGE score score DOUBLE PRECISION DEFAULT \'NULL\'');
    }
}
