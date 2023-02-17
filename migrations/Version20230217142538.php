<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230217142538 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_user_group DROP FOREIGN KEY FK_E641F50CA76ED395');
        $this->addSql('ALTER TABLE tbl_user_group ADD CONSTRAINT FK_E641F50CA76ED395 FOREIGN KEY (user_id) REFERENCES `tbl_user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_user_group DROP FOREIGN KEY FK_E641F50CA76ED395');
        $this->addSql('ALTER TABLE tbl_user_group ADD CONSTRAINT FK_E641F50CA76ED395 FOREIGN KEY (user_id) REFERENCES tbl_user (id)');
    }
}
