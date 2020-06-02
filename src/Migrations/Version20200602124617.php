<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200602124617 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE competences (id VARCHAR(36) NOT NULL, category_id VARCHAR(36) DEFAULT NULL, code VARCHAR(10) DEFAULT NULL, points NUMERIC(5, 1) NOT NULL, name VARCHAR(120) NOT NULL, slug VARCHAR(120) NOT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DB2077CE12469DE2 ON competences (category_id)');
        $this->addSql('CREATE UNIQUE INDEX competence_unique_slug ON competences (slug)');
        $this->addSql('CREATE UNIQUE INDEX competence_unique_code ON competences (code)');
        $this->addSql('CREATE TABLE competence_categories (id VARCHAR(36) NOT NULL, parent_id VARCHAR(36) DEFAULT NULL, slug VARCHAR(50) NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1473FE9F727ACA70 ON competence_categories (parent_id)');
        $this->addSql('CREATE UNIQUE INDEX competence_category_unique_slug ON competence_categories (slug)');
        $this->addSql('ALTER TABLE competences ADD CONSTRAINT FK_DB2077CE12469DE2 FOREIGN KEY (category_id) REFERENCES competence_categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE competence_categories ADD CONSTRAINT FK_1473FE9F727ACA70 FOREIGN KEY (parent_id) REFERENCES competence_categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE competences DROP CONSTRAINT FK_DB2077CE12469DE2');
        $this->addSql('ALTER TABLE competence_categories DROP CONSTRAINT FK_1473FE9F727ACA70');
        $this->addSql('DROP TABLE competences');
        $this->addSql('DROP TABLE competence_categories');
    }
}
