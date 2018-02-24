<?php

namespace App\DataFixtures;

use App\Entity\Institution;
use App\Entity\InstitutionTitle;
use App\Entity\Mandate;
use App\Entity\Politician;
use App\Entity\Product;
use App\Entity\Setting;
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

        $manager->flush(); // generate ids

        $setting = new Setting();
        $setting->setId(SettingRepository::PRESIDENT_INSTITUTION_TITLE_ID);
        $setting->setValue($institutionTitle->getId());
        $manager->persist($setting);

        $politician = new Politician();
        $politician
            ->setFirstName('Foo')
            ->setLastName('Bar')
            ->setSlug('foo-bar');
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

        $manager->flush();
    }
}