<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210727121102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_category ADD created_by_id INT DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_category ADD CONSTRAINT FK_517FFFECB03A8386 FOREIGN KEY (created_by_id) REFERENCES tbl_user (id)');
        $this->addSql('CREATE INDEX IDX_517FFFECB03A8386 ON tbl_category (created_by_id)');
        $this->addSql('ALTER TABLE tbl_question ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_question ADD CONSTRAINT FK_E1C4AF63B03A8386 FOREIGN KEY (created_by_id) REFERENCES tbl_user (id)');
        $this->addSql('CREATE INDEX IDX_E1C4AF63B03A8386 ON tbl_question (created_by_id)');
        $this->addSql('ALTER TABLE tbl_quiz ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_quiz ADD CONSTRAINT FK_1132AF7AB03A8386 FOREIGN KEY (created_by_id) REFERENCES tbl_user (id)');
        $this->addSql('CREATE INDEX IDX_1132AF7AB03A8386 ON tbl_quiz (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_category DROP FOREIGN KEY FK_517FFFECB03A8386');
        $this->addSql('DROP INDEX IDX_517FFFECB03A8386 ON tbl_category');
        $this->addSql('ALTER TABLE tbl_category DROP created_by_id, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE tbl_question DROP FOREIGN KEY FK_E1C4AF63B03A8386');
        $this->addSql('DROP INDEX IDX_E1C4AF63B03A8386 ON tbl_question');
        $this->addSql('ALTER TABLE tbl_question DROP created_by_id');
        $this->addSql('ALTER TABLE tbl_quiz DROP FOREIGN KEY FK_1132AF7AB03A8386');
        $this->addSql('DROP INDEX IDX_1132AF7AB03A8386 ON tbl_quiz');
        $this->addSql('ALTER TABLE tbl_quiz DROP created_by_id');
    }
}
