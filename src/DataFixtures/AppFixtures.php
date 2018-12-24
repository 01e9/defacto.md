<?php

namespace App\DataFixtures;

use App\Entity\Action;
use App\Entity\Category;
use App\Entity\Institution;
use App\Entity\InstitutionTitle;
use App\Entity\Mandate;
use App\Entity\Politician;
use App\Entity\Power;
use App\Entity\Product;
use App\Entity\Promise;
use App\Entity\Setting;
use App\Entity\Status;
use App\Entity\Title;
use App\Entity\Election;
use App\Repository\SettingRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
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

        $powers = $this->createPowers($manager);

        $title = new Title();
        $title
            ->setName('Președintele Republicii Moldova')
            ->setSlug('președintele-republicii-moldova')
            ->setPowers($powers);
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

        $election = new Election();
        $election
            ->setName('Demo election')
            ->setSlug('demo-election')
            ->setDate(new \DateTime());
        $manager->persist($election);

        $mandate = new Mandate();
        $mandate
            ->setBeginDate(new \DateTime('-1 days'))
            ->setEndDate(new \DateTime('+1 days'))
            ->setPolitician($politician)
            ->setInstitutionTitle($institutionTitle)
            ->setVotesCount(10000)
            ->setVotesPercent(51)
            ->setElection($election);
        $manager->persist($mandate);

        $promise = new Promise();
        $promise
            ->setMandate($mandate)
            ->setStatus(current($statuses))
            ->setName('Demo promisiune')
            ->setSlug('demo-promisiune')
            ->setDescription('Demo descriere')
            ->setMadeTime(new \DateTime())
            ->setPublished(true);
        $manager->persist($promise);

        $promiseNoStatus = new Promise();
        $promiseNoStatus
            ->setMandate($mandate)
            ->setStatus(null)
            ->setName('Demo promisiune fără statut')
            ->setSlug('demo-promisiune-fără-statut')
            ->setDescription('Demo descriere')
            ->setMadeTime(new \DateTime('-10 days'))
            ->setPublished(true);
        $manager->persist($promiseNoStatus);

        $promiseUnpublished = new Promise();
        $promiseUnpublished
            ->setMandate($mandate)
            ->setStatus(null)
            ->setName('Demo promisiune nepublicată')
            ->setSlug('demo-promisiune-nepublicată')
            ->setDescription('Demo descriere')
            ->setMadeTime(new \DateTime('-10 days'))
            ->setPublished(false);
        $manager->persist($promiseUnpublished);

        $action = new Action();
        $action
            ->setMandate($mandate)
            ->setName('Demo acțiune')
            ->setSlug('demo-acțiune')
            ->setDescription('Demo descriere')
            ->setOccurredTime(new \DateTime())
            ->setPublished(true);
        $manager->persist($action);

        $action2 = new Action();
        $action2
            ->setMandate($mandate)
            ->setName('Demo altă acțiune')
            ->setSlug('demo-altă-acțiune')
            ->setDescription('Demo descriere')
            ->setOccurredTime(new \DateTime())
            ->setPublished(true);
        $manager->persist($action2);

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

    private function createPowers(ObjectManager $manager) : ArrayCollection
    {
        $powers = new ArrayCollection();

        foreach ([
            'inițiativă-legislativă' => 'Inițiativă legislativă',
            'promulgarea-abrogarea-legilor' => 'Promulgarea/Abrogarea legilor',
            'mesaje-adresate-instituțiilor' => 'Mesaje adresate instituțiilor',
        ] as $powerSlug => $powerName) {
            $power = new Power();
            $power
                ->setName($powerName)
                ->setSlug($powerSlug);
            $manager->persist($power);

            $powers->add($power);
        }

        return $powers;
    }

    private function createStatuses(ObjectManager $manager) : array
    {
        $statuses = [];

        foreach ([
            'declarații' => [
                'name' => 'Declarație',
                'name_plural' => 'Declarații',
                'effect' => 0,
                'color' => 'violet',
            ],
            'în-proces' => [
                'name' => 'În proces',
                'name_plural' => 'În proces',
                'effect' => 1,
                'color' => 'orange',
            ],
            'îndeplinite' => [
                'name' => 'Îndeplinită',
                'name_plural' => 'Îndeplinite',
                'effect' => 2,
                'color' => 'green',
            ],
            'compromise' => [
                'name' => 'Compromisă',
                'name_plural' => 'Compromise',
                'effect' => -2,
                'color' => 'red',
            ],
            'nemăsurabile' => [
                'name' => 'Nemăsurabilă',
                'name_plural' => 'Nemăsurabile',
                'effect' => -1,
                'color' => 'yellow',
            ],
            'nerealizate' => [
                'name' => 'Nerealizată',
                'name_plural' => 'Nerealizate',
                'effect' => -3,
                'color' => 'grey',
            ],
        ] as $slug => $info) {
            $status = new Status();
            $status
                ->setSlug($slug)
                ->setName($info['name'])
                ->setNamePlural($info['name_plural'])
                ->setEffect($info['effect'])
                ->setColor($info['color']);
            $manager->persist($status);

            $statuses[$slug] = $status;
        }

        return $statuses;
    }
}