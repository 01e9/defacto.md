<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200621204117 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $ids = [
            'a3995fd0-5f60-480d-a522-7006a33f5453',
            '48452969-2154-4b9c-9a56-5b4c1dbc6dab',
            '9ba4743b-50e7-4ad9-9fa2-d4dcc6169dcc',
            '3e56c194-8972-44a9-a277-0cb42764031a',
            '1c618d6b-66ce-4c04-b0fd-0300df84fbd0',
            'a1944e80-8c89-4a88-ae9f-4f09a2537c01',
            '6ef3707e-0c57-4468-94aa-e2f4859bf232',
            '87e15f88-b12a-4cc4-baab-8aa83d2d7a58',
            '87a65946-6232-441d-b929-95360a2707bd',
            '52d2ce08-ba7f-4385-b63c-7a2966da19c2',
            '1481370e-9811-4f37-8656-555e0b257d48',
            'b8e7f154-d7b0-44e1-9b22-4bb2699b429a',
            '82997660-bb59-4dc1-8c6a-fcbbb6029c7e',
            '033c0a36-f7d2-4804-b520-604006595092',
            '400af432-b1ec-4d1d-8f2a-ba444c73e21f',
            '483df408-627b-4e64-a3d7-f4cda42cbaf5',
            '056a9437-47a2-4640-91bb-751b38464d9a',
            '9d16a9a1-8faf-44e8-bb80-e7bdccc0a232',
            '9ad546b2-e28b-4581-a663-cc66071200b8',
            '76164fa1-9d53-46bc-b91f-4e106dae9d15',
            'c30e38b5-c85f-489d-9437-1686e56b197f',
            'a300d885-3800-4a86-af3d-a67576a8c2a4',
            'c31ec563-afe6-4590-8846-5b82ae10c6ad',
            '8214f405-829e-46f5-8121-0752e06dddbb',
            '92a0798c-d05b-4bbc-b000-2866819c62bb',
            '271cb72b-2923-4c57-a49a-9480c044d852',
            'ca950146-303c-44cd-b45d-32f4fc27887a',
            'beb614cb-ce3b-4dd4-8cee-8db8cb809a79',
            'd6a5e7d6-861f-4a8e-8840-48809c821a61',
            'ea4e2eb4-d3f1-4eef-a09e-f0e3899df5c7',
            '914875e3-4f8b-439a-833f-ce8d3566178d',
            'a0025cbd-4549-4211-8567-fda0889ca891',
        ];
        $this->addSql('UPDATE politicians SET is_female = \'true\' WHERE id IN (\''. implode("', '", $ids) .'\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('UPDATE politicians SET is_female = \'false\'');
    }
}
