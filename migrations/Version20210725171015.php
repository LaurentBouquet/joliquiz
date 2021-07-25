<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210725171015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_group (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_8F02BF9DA76ED395 (user_id), INDEX IDX_8F02BF9DFE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9DA76ED395 FOREIGN KEY (user_id) REFERENCES tbl_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9DFE54D947 FOREIGN KEY (group_id) REFERENCES `tbl_group` (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE migration_versions');
        $this->addSql('ALTER TABLE tbl_group ADD CONSTRAINT FK_C3A40EDAC32A47EE FOREIGN KEY (school_id) REFERENCES `tbl_school` (id)');
        $this->addSql('DROP INDEX UNIQ_38B383A1E7927C74 ON tbl_user');
        $this->addSql('ALTER TABLE tbl_user ADD organization_code VARCHAR(16) DEFAULT NULL, ADD organization_label VARCHAR(255) DEFAULT NULL, ADD account_type VARCHAR(5) DEFAULT NULL, ADD phone VARCHAR(16) DEFAULT NULL, ADD comment LONGTEXT DEFAULT NULL, ADD login_type VARCHAR(2) DEFAULT NULL, CHANGE token token LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE migration_versions (version VARCHAR(14) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, executed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(version)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE user_group');
        $this->addSql('ALTER TABLE `tbl_group` DROP FOREIGN KEY FK_C3A40EDAC32A47EE');
        $this->addSql('ALTER TABLE tbl_user DROP organization_code, DROP organization_label, DROP account_type, DROP phone, DROP comment, DROP login_type, CHANGE token token VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_38B383A1E7927C74 ON tbl_user (email)');
    }
}
