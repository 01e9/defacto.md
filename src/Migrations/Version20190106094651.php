<?php declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Promise;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190106094651 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE promises ADD politician_id VARCHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE promises ADD election_id VARCHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE promises ADD CONSTRAINT FK_EB9A506CA708DAFF FOREIGN KEY (election_id) REFERENCES elections (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE promises ADD CONSTRAINT FK_EB9A506C899F0176 FOREIGN KEY (politician_id) REFERENCES politicians (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_EB9A506CA708DAFF ON promises (election_id)');
        $this->addSql('CREATE INDEX IDX_EB9A506C899F0176 ON promises (politician_id)');

        $promises = $this->connection->query(
            "SELECT p.id, m.politician_id, m.election_id FROM promises AS p ".
            "INNER JOIN mandates AS m ON m.id = p.mandate_id"
        );
        foreach ($promises as $promise) {
            $this->addSql(
                "UPDATE promises SET politician_id = :politician_id, election_id = :election_id WHERE id = :id",
                $promise
            );
        }

        $this->addSql('ALTER TABLE promises DROP CONSTRAINT fk_eb9a506c6c1129cd');
        $this->addSql('DROP INDEX idx_eb9a506c6c1129cd');
        $this->addSql('ALTER TABLE promises DROP mandate_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        throw new \Exception("No way back");
    }
}
