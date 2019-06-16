<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190616143754 extends AbstractMigration
{
    private $tablesWithSlug = [
        'actions',
        'blog_posts',
        'categories',
        'constituencies',
        'elections',
        'institutions',
        'parties',
        'politicians',
        'powers',
        'problems',
        'promises',
        'statuses',
        'titles'
    ];

    public function up(Schema $schema) : void
    {
        foreach ($this->tablesWithSlug as $table) {
            $this->addSql("UPDATE ${table} SET slug = LOWER(TRANSLATE(slug, "
            ."'àáäâãåăæçèéëêǵḧìíïîḿńǹñòóöôœøṕŕßśșțùúüûǘẃẍÿź', "
            ."'aaaaaaaaceeeeghiiiimnnnooooooprssstuuuuuwxyz'));");
        }
    }

    public function down(Schema $schema) : void
    {
    }
}
