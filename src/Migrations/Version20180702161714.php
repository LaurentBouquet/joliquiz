<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180702161714 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER SEQUENCE tbl_category_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_answer_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_question_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_quiz_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_user_id_seq INCREMENT BY 1');
        $this->addSql('CREATE TABLE quiz_category (quiz_id INT NOT NULL, category_id INT NOT NULL, PRIMARY KEY(quiz_id, category_id))');
        $this->addSql('CREATE INDEX IDX_D088E084853CD175 ON quiz_category (quiz_id)');
        $this->addSql('CREATE INDEX IDX_D088E08412469DE2 ON quiz_category (category_id)');
        $this->addSql('ALTER TABLE quiz_category ADD CONSTRAINT FK_D088E084853CD175 FOREIGN KEY (quiz_id) REFERENCES tbl_quiz (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz_category ADD CONSTRAINT FK_D088E08412469DE2 FOREIGN KEY (category_id) REFERENCES tbl_category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER SEQUENCE tbl_answer_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_category_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_user_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_question_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE tbl_quiz_id_seq INCREMENT BY 1');
        $this->addSql('DROP TABLE quiz_category');
    }
}
