<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200814000000 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('UPDATE competence_categories SET description = \'Atribuțiile deputatului ce țin de activitatea sa în Parlament\' WHERE id = \'069a3ac6-2ed4-4ff1-a118-9cad76f3b7fa\'');
        $this->addSql('UPDATE competence_categories SET description = \'Atribuțiile deputatului ce țin de interacțiunea sa cu cetățenii din circumscripția în care a fost ales\' WHERE id = \'05218c0d-56cd-4a51-bca0-f7b07396f0cf\'');
        $this->addSql('UPDATE competence_categories SET description = \'Atribuțiile deputatului ce țin de interacțiunea sa cu alte instituții de stat\' WHERE id = \'0ee40a86-df73-4784-ac72-e5ea3bde20a5\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('UPDATE competence_categories SET description = NULL');
    }
}
