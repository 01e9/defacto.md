<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200621183121 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE INDEX constituency_problem_index_constituency_percentage ON constituency_problems (constituency_id, percentage)');
        $this->addSql('CREATE INDEX candidate_index_constituency_registration_date ON candidates (constituency_id, registration_date)');
        $this->addSql('CREATE INDEX mandate_index_constituency_begin_date ON mandates (constituency_id, begin_date)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX candidate_index_constituency_registration_date');
        $this->addSql('DROP INDEX mandate_index_constituency_begin_date');
        $this->addSql('DROP INDEX constituency_problem_index_constituency_percentage');
    }
}
