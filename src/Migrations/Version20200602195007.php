<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200602195007 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE competence_uses (id VARCHAR(36) NOT NULL, mandate_id VARCHAR(36) NOT NULL, competence_id VARCHAR(36) NOT NULL, use_date DATE NOT NULL, source_link VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BD0CECC46C1129CD ON competence_uses (mandate_id)');
        $this->addSql('CREATE INDEX IDX_BD0CECC415761DAB ON competence_uses (competence_id)');
        $this->addSql('ALTER TABLE competence_uses ADD CONSTRAINT FK_BD0CECC46C1129CD FOREIGN KEY (mandate_id) REFERENCES mandates (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE competence_uses ADD CONSTRAINT FK_BD0CECC415761DAB FOREIGN KEY (competence_id) REFERENCES competences (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE competence_uses');
    }
}
