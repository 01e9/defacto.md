<?php

namespace App\DataFixtures;

use App\Entity\Action;
use App\Entity\Category;
use App\Entity\Institution;
use App\Entity\InstitutionTitle;
use App\Entity\Mandate;
use App\Entity\Politician;
use App\Entity\Product;
use App\Entity\Promise;
use App\Entity\Setting;
use App\Entity\Status;
use App\Entity\Title;
use App\Repository\SettingRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $institution = new Institution();
        $institution
            ->setName('Președinția Republicii Moldova')
            ->setSlug('președinția-republicii-moldova');
        $manager->persist($institution);

        $title = new Title();
        $title
            ->setName('Președintele Republicii Moldova')
            ->setSlug('președintele-republicii-moldova');
        $manager->persist($title);

        $institutionTitle = new InstitutionTitle();
        $institutionTitle
            ->setInstitution($institution)
            ->setTitle($title);
        $manager->persist($institutionTitle);

        $categories = $this->createCategories($manager);
        $statuses = $this->createStatuses($manager);

        $politician = new Politician();
        $politician
            ->setFirstName('Demo')
            ->setLastName('Testescu')
            ->setSlug('demo-testescu');
        $manager->persist($politician);

        $mandate = new Mandate();
        $mandate
            ->setBeginDate(new \DateTime('-1 days'))
            ->setEndDate(new \DateTime('+1 days'))
            ->setPolitician($politician)
            ->setInstitutionTitle($institutionTitle)
            ->setVotesCount(10000)
            ->setVotesPercent(51);
        $manager->persist($mandate);

        $promise = new Promise();
        $promise
            ->setMandate($mandate)
            ->setStatus(current($statuses))
            ->setTitle('Demo promisiune')
            ->setSlug('demo-promisiune')
            ->setDescription('Demo descriere')
            ->setMadeTime(new \DateTime())
            ->setPublished(true);
        $manager->persist($promise);

        $action = new Action();
        $action
            ->setMandate($mandate)
            ->setName('Demo acțiune')
            ->setSlug('demo-acțiune')
            ->setDescription('Demo descriere')
            ->setOccurredTime(new \DateTime())
            ->setPublished(true);
        $manager->persist($action);

        $manager->flush(); // generate ids

        $setting = new Setting();
        $setting->setId(SettingRepository::PRESIDENT_INSTITUTION_TITLE_ID);
        $setting->setValue($institutionTitle->getId());
        $manager->persist($setting);

        $manager->flush();
    }

    private function createCategories(ObjectManager $manager) : array
    {
        $categories = [];

        foreach ([
            'economie' => 'Economie',
            'educație' => 'Educație',
            'politică-externă' => 'Politică externă',
            'politică-internă' => 'Politică internă',
            'social' => 'Social',
        ] as $slug => $name) {
            $category = new Category();
            $category
                ->setSlug($slug)
                ->setName($name);
            $manager->persist($category);

            $categories[$slug] = $category;
        }

        return $categories;
    }

    private function createStatuses(ObjectManager $manager) : array
    {
        $statuses = [];

        foreach ([
            'declarații' => [
                'name' => 'Declarație',
                'name_plural' => 'Declarații',
                'effect' => 0,
            ],
            'în-proces' => [
                'name' => 'În proces',
                'name_plural' => 'În proces',
                'effect' => 1,
            ],
            'îndeplinite' => [
                'name' => 'Îndeplinită',
                'name_plural' => 'Îndeplinite',
                'effect' => 2,
            ],
            'compromise' => [
                'name' => 'Compromisă',
                'name_plural' => 'Compromise',
                'effect' => -2,
            ],
            'nemăsurabile' => [
                'name' => 'Nemăsurabilă',
                'name_plural' => 'Nemăsurabile',
                'effect' => -1,
            ],
            'nerealizate' => [
                'name' => 'Nerealizată',
                'name_plural' => 'Nerealizate',
                'effect' => -3,
            ],
        ] as $slug => $info) {
            $status = new Status();
            $status
                ->setSlug($slug)
                ->setName($info['name'])
                ->setNamePlural($info['name_plural'])
                ->setEffect($info['effect']);
            $manager->persist($status);

            $statuses[$slug] = $status;
        }

        return $statuses;
    }
}