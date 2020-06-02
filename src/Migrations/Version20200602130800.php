<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200602130800 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('alter table actions rename to promise_actions');
        $this->addSql('alter table action_sources rename to promise_action_sources');
        $this->addSql('alter table action_power rename to promise_action_power');
        $this->addSql('alter table promise_action_power rename constraint action_power_pkey to promise_action_power_pkey');
        $this->addSql('alter table promise_action_sources rename constraint action_sources_pkey to promise_action_sources_pkey');
        $this->addSql('alter table promise_actions rename constraint actions_pkey to promise_actions_pkey');

        $this->addSql('ALTER INDEX idx_548f1ef6c1129cd RENAME TO IDX_71C151186C1129CD');
        $this->addSql('ALTER INDEX action_unique_slug RENAME TO promise_action_unique_slug');
        $this->addSql('ALTER TABLE promise_action_power DROP CONSTRAINT fk_4c44cd1c9d32f035');
        $this->addSql('DROP INDEX idx_4c44cd1c9d32f035');
        $this->addSql('ALTER TABLE promise_action_power RENAME COLUMN action_id TO promise_action_id');
        $this->addSql('ALTER TABLE promise_action_power ADD CONSTRAINT FK_5E8CC4A91019502E FOREIGN KEY (promise_action_id) REFERENCES promise_actions (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5E8CC4A91019502E ON promise_action_power (promise_action_id)');
        $this->addSql('ALTER INDEX idx_4c44cd1cab4fc384 RENAME TO IDX_5E8CC4A9AB4FC384');
        $this->addSql('ALTER INDEX idx_8ffed139d32f035 RENAME TO IDX_E8911D829D32F035');
        $this->addSql('ALTER INDEX action_source_unique_name RENAME TO promise_action_source_unique_name');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER INDEX promise_action_source_unique_name RENAME TO action_source_unique_name');
        $this->addSql('ALTER INDEX idx_e8911d829d32f035 RENAME TO idx_8ffed139d32f035');
        $this->addSql('ALTER TABLE promise_action_power DROP CONSTRAINT FK_5E8CC4A91019502E');
        $this->addSql('DROP INDEX IDX_5E8CC4A91019502E');
        $this->addSql('ALTER TABLE promise_action_power RENAME COLUMN promise_action_id TO action_id');
        $this->addSql('ALTER TABLE promise_action_power ADD CONSTRAINT fk_4c44cd1c9d32f035 FOREIGN KEY (action_id) REFERENCES promise_actions (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_4c44cd1c9d32f035 ON promise_action_power (action_id)');
        $this->addSql('ALTER INDEX idx_5e8cc4a9ab4fc384 RENAME TO idx_4c44cd1cab4fc384');
        $this->addSql('ALTER INDEX promise_action_unique_slug RENAME TO action_unique_slug');
        $this->addSql('ALTER INDEX idx_71c151186c1129cd RENAME TO idx_548f1ef6c1129cd');

        $this->addSql('alter table promise_action_sources rename constraint promise_action_sources_pkey to action_sources_pkey');
        $this->addSql('alter table promise_actions rename constraint promise_actions_pkey to actions_pkey');
        $this->addSql('alter table promise_action_power rename constraint promise_action_power_pkey to action_power_pkey');
        $this->addSql('alter table promise_actions rename to actions');
        $this->addSql('alter table promise_action_sources rename to action_sources');
        $this->addSql('alter table promise_action_power rename to action_power');
    }
}
