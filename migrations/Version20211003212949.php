<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211003212949 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_workout ADD session_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_workout ADD CONSTRAINT FK_3FCCF306613FECDF FOREIGN KEY (session_id) REFERENCES tbl_session (id)');
        $this->addSql('CREATE INDEX IDX_3FCCF306613FECDF ON tbl_workout (session_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_workout DROP FOREIGN KEY FK_3FCCF306613FECDF');
        $this->addSql('DROP INDEX IDX_3FCCF306613FECDF ON tbl_workout');
        $this->addSql('ALTER TABLE tbl_workout DROP session_id');
    }
}
