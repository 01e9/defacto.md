<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200603141328 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX competence_unique_code');
        $this->addSql('ALTER TABLE competences ADD title_id VARCHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE competences ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE competences DROP description');
        $this->addSql('ALTER TABLE competences ALTER code SET NOT NULL');
        $this->addSql('ALTER TABLE competences ADD CONSTRAINT FK_DB2077CEA9F87BD FOREIGN KEY (title_id) REFERENCES titles (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_DB2077CEA9F87BD ON competences (title_id)');
        $this->addSql('CREATE UNIQUE INDEX competence_unique_title_code ON competences (title_id, code)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE competences DROP CONSTRAINT FK_DB2077CEA9F87BD');
        $this->addSql('DROP INDEX IDX_DB2077CEA9F87BD');
        $this->addSql('DROP INDEX competence_unique_title_code');
        $this->addSql('ALTER TABLE competences ADD description TEXT NOT NULL');
        $this->addSql('ALTER TABLE competences DROP title_id');
        $this->addSql('ALTER TABLE competences DROP name');
        $this->addSql('ALTER TABLE competences ALTER code DROP NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX competence_unique_code ON competences (code)');
    }
}
