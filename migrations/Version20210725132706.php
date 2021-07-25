<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210725132706 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE tbl_user_bak');
        $this->addSql('ALTER TABLE tbl_user ADD login_type VARCHAR(2) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tbl_user_bak (id INT DEFAULT NULL, prefered_language_id VARCHAR(2) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, username VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, email VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, password VARCHAR(64) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, is_active TINYINT(1) DEFAULT NULL, roles LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, password_requested_at DATETIME DEFAULT NULL, token VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, organization_code VARCHAR(16) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, organization_label VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, account_type VARCHAR(5) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, phone VARCHAR(16) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, comment LONGTEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tbl_user DROP login_type');
    }
}
