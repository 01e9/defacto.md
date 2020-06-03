<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200603172746 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql("INSERT INTO competence_categories (id, parent_id, slug, name) VALUES ('069a3ac6-2ed4-4ff1-a118-9cad76f3b7fa', null, 'parlament', 'Parlament')");
        $this->addSql("INSERT INTO competence_categories (id, parent_id, slug, name) VALUES ('169a3ac6-2ed4-4ff1-a118-9cad76f3b7fa', '069a3ac6-2ed4-4ff1-a118-9cad76f3b7fa', 'legislativ', 'Legislativ')");
        $this->addSql("INSERT INTO competence_categories (id, parent_id, slug, name) VALUES ('269a3ac6-2ed4-4ff1-a118-9cad76f3b7fa', '069a3ac6-2ed4-4ff1-a118-9cad76f3b7fa', 'plen', 'Plen')");
        $this->addSql("INSERT INTO competence_categories (id, parent_id, slug, name) VALUES ('369a3ac6-2ed4-4ff1-a118-9cad76f3b7fa', '069a3ac6-2ed4-4ff1-a118-9cad76f3b7fa', 'comisie', 'Comisie')");
        $this->addSql("INSERT INTO competence_categories (id, parent_id, slug, name) VALUES ('469a3ac6-2ed4-4ff1-a118-9cad76f3b7fa', '069a3ac6-2ed4-4ff1-a118-9cad76f3b7fa', 'initiativa-proprie', 'Inițiativă proprie')");
        $this->addSql("INSERT INTO competence_categories (id, parent_id, slug, name) VALUES ('05218c0d-56cd-4a51-bca0-f7b07396f0cf', null, 'cu-cetateni', 'CU/Cetățeni')");
        $this->addSql("INSERT INTO competence_categories (id, parent_id, slug, name) VALUES ('0ee40a86-df73-4784-ac72-e5ea3bde20a5', null, 'apc-apl', 'APC/APL')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql("TRUNCATE TABLE competence_categories");
    }
}
