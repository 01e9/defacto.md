<?php

namespace App\Tests\Controller;

use App\Consts;
use App\Entity\Constituency;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestCaseTrait;
use Doctrine\Persistence\ObjectManager;

class AdminConstituenciesControllerTest extends WebTestCase
{
    use TestCaseTrait;

    //region List

    public function testIndexActionAccess()
    {
        $this->assertOnlyAdminCanAccess('/admin/constituencies');
    }

    //endregion

    //region Add

    public function testAddActionAccess()
    {
        $this->assertOnlyAdminCanAccess('/admin/constituencies/add');
    }

    public function testAddActionSubmitAddEmptyForm()
    {
        $client = self::createAdminClient();
        $locale = self::getLocale($client);

        $form = $client
            ->request('GET', "/${locale}/admin/constituencies/add")
            ->filter('form')->form();
        $client->submit($form, []);
        $this->assertHasFormErrors($client->getResponse());
    }

    public function testAddActionSubmitAddValidData()
    {
        $client = self::createAdminClient();
        $locale = self::getLocale($client);
        $em = self::getDoctrine($client);
        $random = self::randomNumber();

        $formData = [
            'constituency[name]' => "Test ${random}",
            'constituency[slug]' => "test-${random}",
            'constituency[link]' => "https://test.test/test-${random}",
            'constituency[number]' => "${random}",
        ];

        $form = $client
            ->request('GET', "/${locale}/admin/constituencies/add")
            ->filter('form')->form();
        $client->submit($form, $formData);
        $route = $this->assertRedirectsToRoute($client->getResponse(), 'admin_constituency_edit');

        /** @var Constituency $constituency */
        $constituency = $em->getRepository('App:Constituency')->find($route['id']);

        $this->assertNotNull($constituency);
        $this->assertEquals($formData['constituency[name]'], $constituency->getName());
        $this->assertEquals($formData['constituency[slug]'], $constituency->getSlug());

        self::cleanup($em);
    }

    //endregion

    //region Edit

    public function testEditActionAccess()
    {
        static::ensureKernelShutdown();
        $client = static::createClient();
        $client->insulate();

        /** @var ObjectManager $manager */
        $manager = $client->getContainer()->get('doctrine')->getManager();
        $constituency = $this->makeConstituency($manager);

        $this->assertOnlyAdminCanAccess("/admin/constituencies/{$constituency->getId()}", $client);
    }

    public function testEditActionSubmitInvalidData()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $constituency = $this->makeConstituency($em);
        $form = $client
            ->request('GET', "/${locale}/admin/constituencies/{$constituency->getId()}")
            ->filter('form')->form();
        $client->submit($form, ['constituency[name]' => '?',]);

        $this->assertHasFormErrors($client->getResponse());
    }

    public function testEditActionSubmitNoAddableFields()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);
        $random = self::randomNumber();

        $formData = [
            'constituency[name]' => "Test ${random}",
            'constituency[slug]' => "test-${random}",
            'constituency[link]' => "https://test.test/test-${random}",
        ];

        $constituency = $this->makeConstituency($em);
        $form = $client
            ->request('GET', "/${locale}/admin/constituencies/{$constituency->getId()}")
            ->filter('form')->form();
        $client->submit($form, $formData);

        $this->assertRedirectsToRoute($client->getResponse(), 'admin_constituency_edit');

        $em->clear();
        /** @var Constituency $constituency */
        $constituency = $em->getRepository('App:Constituency')->find($constituency->getId());

        $this->assertNotNull($constituency);
        $this->assertEquals($constituency->getName(), $formData['constituency[name]']);
    }

    public function testEditActionSubmitAddProblems()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $constituency = $this->makeConstituency($em);
        $this->assertCount(0, $constituency->getProblems());

        $form = $client
            ->request('GET', "/${locale}/admin/constituencies/{$constituency->getId()}")
            ->filter('form')->form();

        $formPhpValues = $form->getPhpValues();

        {
            $election1 = $this->makeElection($em);
            $election2 = $this->makeElection($em);
            $problem1 = $this->makeProblem($em);
            $problem2 = $this->makeProblem($em);

            $formPhpValues['constituency']['problems'] = [
                [
                    'constituency' => $constituency->getId(),
                    'election' => $election1->getId(),
                    'problem' => $problem1->getId(),
                ],
                [
                    'constituency' => $constituency->getId(),
                    'election' => $election2->getId(),
                    'problem' => $problem1->getId(),
                    'type' => 'national',
                ],
                [
                    'constituency' => $constituency->getId(),
                    'election' => $election1->getId(),
                    'problem' => $problem2->getId(),
                    'respondents' => 123,
                    'percentage' => 67,
                    'type' => 'local',
                ],
            ];
        }

        $client->request($form->getMethod(), $form->getUri(), $formPhpValues);

        $this->assertRedirectsToRoute($client->getResponse(), 'admin_constituency_edit');

        $em->clear();
        /** @var Constituency $constituency */
        $constituency = $em->getRepository('App:Constituency')->find($constituency->getId());

        $this->assertNotNull($constituency);
        $this->assertCount(3, $constituency->getProblems());
    }

    public function testEditActionSubmitRemoveProblems()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $constituency = $this->makeConstituency($em);
        $constituency->setProblems(new ArrayCollection([$this->makeConstituencyProblem($em, $constituency)]));
        $em->flush();

        $form = $client
            ->request('GET', "/${locale}/admin/constituencies/{$constituency->getId()}")
            ->filter('form')->form();

        $formPhpValues = $form->getPhpValues();
        $this->assertCount(1, $formPhpValues['constituency']['problems']);
        $formPhpValues['constituency']['problems'] = [];

        $client->request($form->getMethod(), $form->getUri(), $formPhpValues);

        $this->assertRedirectsToRoute($client->getResponse(), 'admin_constituency_edit');

        $em->clear();
        /** @var Constituency $constituency */
        $constituency = $em->getRepository('App:Constituency')->find($constituency->getId());

        $this->assertNotNull($constituency);
        $this->assertCount(0, $constituency->getProblems());
    }

    public function testEditActionSubmitAddCandidates()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $constituency = $this->makeConstituency($em);
        $this->assertCount(0, $constituency->getCandidates());

        $form = $client
            ->request('GET', "/${locale}/admin/constituencies/{$constituency->getId()}")
            ->filter('form')->form();

        $formPhpValues = $form->getPhpValues();

        {
            $election1 = $this->makeElection($em);
            $election2 = $this->makeElection($em);
            $politician1 = $this->makePolitician($em);
            $politician2 = $this->makePolitician($em);
            $party1 = $this->makeParty($em);

            $formPhpValues['constituency']['candidates'] = [
                [
                    'constituency' => $constituency->getId(),
                    'election' => $election1->getId(),
                    'politician' => $politician1->getId(),
                ],
                [
                    'constituency' => $constituency->getId(),
                    'election' => $election1->getId(),
                    'politician' => $politician2->getId(),
                    'party' => $party1->getId(),
                    'registrationDate' => (new \DateTime())->format(Consts::DATE_FORMAT_PHP),
                    'registrationNote' => 'Test note',
                    'registrationLink' => 'http://test.link/registration',
                    'electoralPlatform' => 'Test platform',
                    'electoralPlatformLink' => 'http://test.link/platform',
                ],
                [
                    'constituency' => $constituency->getId(),
                    'election' => $election2->getId(),
                    'politician' => $politician2->getId(),
                ],
            ];
        }

        $client->request($form->getMethod(), $form->getUri(), $formPhpValues);

        $this->assertRedirectsToRoute($client->getResponse(), 'admin_constituency_edit');

        $em->clear();
        /** @var Constituency $constituency */
        $constituency = $em->getRepository('App:Constituency')->find($constituency->getId());

        $this->assertNotNull($constituency);
        $this->assertCount(3, $constituency->getCandidates());
    }

    public function testEditActionSubmitRemoveCandidates()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $constituency = $this->makeConstituency($em);
        $constituency->setCandidates(new ArrayCollection([$this->makeConstituencyCandidate($em, $constituency)]));
        $em->flush();

        $form = $client
            ->request('GET', "/${locale}/admin/constituencies/{$constituency->getId()}")
            ->filter('form')->form();

        $formPhpValues = $form->getPhpValues();
        $this->assertCount(1, $formPhpValues['constituency']['candidates']);
        $formPhpValues['constituency']['candidates'] = [];

        $client->request($form->getMethod(), $form->getUri(), $formPhpValues);

        $this->assertRedirectsToRoute($client->getResponse(), 'admin_constituency_edit');

        $em->clear();
        /** @var Constituency $constituency */
        $constituency = $em->getRepository('App:Constituency')->find($constituency->getId());

        $this->assertNotNull($constituency);
        $this->assertCount(0, $constituency->getCandidates());
    }

    public function testEditActionSubmitAddCandidateProblemOpinions()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $constituency = $this->makeConstituency($em);
        $this->assertCount(0, $constituency->getCandidateProblemOpinions());

        $form = $client
            ->request('GET', "/${locale}/admin/constituencies/{$constituency->getId()}")
            ->filter('form')->form();

        $formPhpValues = $form->getPhpValues();

        {
            $election1 = $this->makeElection($em);
            $election2 = $this->makeElection($em);
            $politician1 = $this->makePolitician($em);
            $politician2 = $this->makePolitician($em);
            $problem1 = $this->makeProblem($em);
            $problem2 = $this->makeProblem($em);

            $formPhpValues['constituency']['candidateProblemOpinions'] = [
                [
                    'constituency' => $constituency->getId(),
                    'politician' => $politician1->getId(),
                    'election' => $election1->getId(),
                    'problem' => $problem1->getId(),
                    'opinion' => 'Foo'
                ],
                [
                    'constituency' => $constituency->getId(),
                    'politician' => $politician1->getId(),
                    'election' => $election1->getId(),
                    'problem' => $problem2->getId(),
                    'opinion' => 'Bar'
                ],
                [
                    'constituency' => $constituency->getId(),
                    'politician' => $politician2->getId(),
                    'election' => $election1->getId(),
                    'problem' => $problem1->getId(),
                    'opinion' => 'Baz'
                ],
                [
                    'constituency' => $constituency->getId(),
                    'politician' => $politician2->getId(),
                    'election' => $election2->getId(),
                    'problem' => $problem2->getId(),
                    'opinion' => 'Test'
                ],
            ];
        }

        $client->request($form->getMethod(), $form->getUri(), $formPhpValues);

        $this->assertRedirectsToRoute($client->getResponse(), 'admin_constituency_edit');

        $em->clear();
        /** @var Constituency $constituency */
        $constituency = $em->getRepository('App:Constituency')->find($constituency->getId());

        $this->assertNotNull($constituency);
        $this->assertCount(4, $constituency->getCandidateProblemOpinions());
    }

    public function testEditActionSubmitRemoveCandidateProblemOpinions()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $constituency = $this->makeConstituency($em);
        $constituency->setCandidateProblemOpinions(
            new ArrayCollection([$this->makeConstituencyCandidateProblemOpinion($em, $constituency)])
        );
        $em->flush();

        $form = $client
            ->request('GET', "/${locale}/admin/constituencies/{$constituency->getId()}")
            ->filter('form')->form();

        $formPhpValues = $form->getPhpValues();
        $this->assertCount(1, $formPhpValues['constituency']['candidateProblemOpinions']);
        $formPhpValues['constituency']['candidateProblemOpinions'] = [];

        $client->request($form->getMethod(), $form->getUri(), $formPhpValues);

        $this->assertRedirectsToRoute($client->getResponse(), 'admin_constituency_edit');

        $em->clear();
        /** @var Constituency $constituency */
        $constituency = $em->getRepository('App:Constituency')->find($constituency->getId());

        $this->assertNotNull($constituency);
        $this->assertCount(0, $constituency->getCandidateProblemOpinions());
    }

    //endregion

    //region Delete

    public function testDeleteActionAccess()
    {
        static::ensureKernelShutdown();
        $client = static::createClient();
        $client->insulate();

        $em = self::getDoctrine($client);
        $constituency = $this->makeConstituency($em);

        $this->assertOnlyAdminCanAccess("/admin/constituencies/{$constituency->getId()}/d", $client);

        self::cleanup($em);
    }

    public function testDeleteActionSubmit()
    {
        $client = self::createAdminClient();
        $em = self::getDoctrine($client);
        $locale = self::getLocale($client);

        $constituency = $this->makeConstituency($em);

        $form = $client
            ->request('GET', "/${locale}/admin/constituencies/{$constituency->getId()}/d")
            ->filter('form')->form();
        $client->submit($form);
        $this->assertRedirectsToRoute($client->getResponse(), 'admin_constituencies');

        $em->clear();
        /** @var Constituency $constituency */
        $constituency = $em->getRepository('App:Constituency')->find($constituency->getId());

        $this->assertNull($constituency);

        self::cleanup($em);
    }

    //endregion
}