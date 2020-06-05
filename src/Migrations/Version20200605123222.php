<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200605123222 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE mandate_competence_category_stats (id VARCHAR(36) NOT NULL, mandate_id VARCHAR(36) NOT NULL, competence_category_id VARCHAR(36) NOT NULL, competence_uses_count INT DEFAULT 0 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BB0DF5A06C1129CD ON mandate_competence_category_stats (mandate_id)');
        $this->addSql('CREATE INDEX IDX_BB0DF5A031B17AE5 ON mandate_competence_category_stats (competence_category_id)');
        $this->addSql('CREATE UNIQUE INDEX mandate_competence_category_stats_unique_category ON mandate_competence_category_stats (mandate_id, competence_category_id)');
        $this->addSql('ALTER TABLE mandate_competence_category_stats ADD CONSTRAINT FK_BB0DF5A06C1129CD FOREIGN KEY (mandate_id) REFERENCES mandates (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mandate_competence_category_stats ADD CONSTRAINT FK_BB0DF5A031B17AE5 FOREIGN KEY (competence_category_id) REFERENCES competence_categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mandates ADD competence_uses_count INT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE mandate_competence_category_stats');
        $this->addSql('ALTER TABLE mandates DROP competence_uses_count');
    }
}
