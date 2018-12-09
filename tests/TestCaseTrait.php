<?php

namespace App\Tests;

use App\Entity\Action;
use App\Entity\Institution;
use App\Entity\InstitutionTitle;
use App\Entity\Mandate;
use App\Entity\Politician;
use App\Entity\Promise;
use App\Entity\Status;
use App\Entity\Title;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Persistence\ObjectManager;

trait TestCaseTrait
{
    private static $application;

    protected function setUp()
    {
        self::runCommand('doctrine:schema:drop --force');
        self::runCommand('doctrine:schema:create');
        self::runCommand('doctrine:fixtures:load --no-interaction');
    }

    protected static function runCommand($command)
    {
        $command = sprintf('%s --quiet', $command);

        return self::getApplication()->run(new StringInput($command));
    }

    protected static function getApplication()
    {
        if (null === self::$application) {
            $client = static::createClient();

            self::$application = new Application($client->getKernel());
            self::$application->setAutoExit(false);
        }

        return self::$application;
    }

    protected static function getLangs() : array
    {
        return ['ro'];
    }

    protected static function getFirewallContext() : string
    {
        return 'main';
    }

    protected static function getTestsRootDir() : string
    {
        return __DIR__;
    }

    protected static function logInClientAsRole(Client $client, string $role) : void
    {
        $user = self::createUserWithRole(
            $role,
            $client->getContainer()->get('doctrine')->getManager(),
            $client->getContainer()->get('security.password_encoder')
        );
        $session = $client->getContainer()->get('session');
        $cookieJar = $client->getCookieJar();

        self::logInUser($user, $session, $cookieJar);
    }

    protected static function logInUser(UserInterface $user, SessionInterface $session, CookieJar $cookieJar) : void
    {
        $token = new UsernamePasswordToken($user, null, self::getFirewallContext(), $user->getRoles());

        $session->set('_security_' . self::getFirewallContext(), serialize($token));
        $session->save();

        $cookieJar->set(new Cookie($session->getName(), $session->getId()));
    }

    protected static function createUserWithRole(
        string $role,
        ObjectManager $objectManager,
        UserPasswordEncoderInterface $passwordEncoder
    ) : UserInterface
    {
        $user = new User();
        $user->setRoles([$role]);
        $user->setIsActive(true);
        $user->setEmail('u' . md5(uniqid()) . '@test');
        $user->setSalt(md5(uniqid()));
        $user->setPassword($passwordEncoder->encodePassword($user, md5(uniqid())));

        $objectManager->persist($user);
        $objectManager->flush();

        return $user;
    }

    protected static function onlyAdminCanAccess(string $pathWithoutLang, Client $client) : bool
    {
        $rolesExpectations = [
            '' => false,
            'ROLE_USER' => false,
            'ROLE_ADMIN' => true,
        ];
        foreach ($rolesExpectations as $role => $expectSuccess) {
            foreach (self::getLangs() as $lang) {
                $client->restart();
                if ($role) {
                    self::logInClientAsRole($client, $role);
                }

                $client->request('GET', '/'. $lang . $pathWithoutLang);
                $response = $client->getResponse();

                if ($expectSuccess) {
                    if (200 !== $response->getStatusCode()) {
                        return false;
                    }
                } elseif ($role) {
                    if (403 !== $response->getStatusCode()) {
                        return false;
                    }
                } else {
                    $redirectPath = parse_url($response->headers->get('location'), PHP_URL_PATH);

                    if (302 !== $response->getStatusCode() || '/' . $lang . '/login' !== $redirectPath) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    protected function createStatus(ObjectManager $objectManager) : Status
    {
        $status = new Status();
        $status->setColor('blue');
        $status->setEffect(73);
        $status->setName("Test");
        $status->setNamePlural("Tests");
        $status->setSlug("test");

        $objectManager->persist($status);
        $objectManager->flush();

        return $status;
    }

    protected function createInstitution(ObjectManager $objectManager) : Institution
    {
        $institution = new Institution();
        $institution->setName("Test");
        $institution->setSlug("test");

        $objectManager->persist($institution);
        $objectManager->flush();

        return $institution;
    }

    protected function createTitle(ObjectManager $objectManager) : Title
    {
        $title = new Title();
        $title->setName("Test");
        $title->setSlug("test");

        $objectManager->persist($title);
        $objectManager->flush();

        return $title;
    }

    protected function createInstitutionTitle(ObjectManager $objectManager) : InstitutionTitle
    {
        $institutionTitle = new InstitutionTitle();
        $institutionTitle->setTitle($this->createTitle($objectManager));
        $institutionTitle->setInstitution($this->createInstitution($objectManager));

        $objectManager->persist($institutionTitle);
        $objectManager->flush();

        return $institutionTitle;
    }

    protected function createPolitician(ObjectManager $objectManager) : Politician
    {
        $politician = new Politician();
        $politician->setFirstName("Foo");
        $politician->setLastName("Bar");
        $politician->setSlug("foo-bar");

        $objectManager->persist($politician);
        $objectManager->flush();

        return $politician;
    }

    protected function createMandate(ObjectManager $objectManager) : Mandate
    {
        $mandate = new Mandate();
        $mandate->setBeginDate(new \DateTime("-2 years"));
        $mandate->setEndDate(new \DateTime("+2 years"));
        $mandate->setVotesCount(1000000);
        $mandate->setVotesPercent(73);
        $mandate->setInstitutionTitle($this->createInstitutionTitle($objectManager));
        $mandate->setPolitician($this->createPolitician($objectManager));

        $objectManager->persist($mandate);
        $objectManager->flush();

        return $mandate;
    }

    protected function createPromise(ObjectManager $objectManager) : Promise
    {
        $promise = new Promise();
        $promise->setName("Test");
        $promise->setSlug("test");
        $promise->setPublished(true);
        $promise->setMandate($this->createMandate($objectManager));
        $promise->setMadeTime(new \DateTime("-3 days"));

        $objectManager->persist($promise);
        $objectManager->flush();

        return $promise;
    }

    protected function createAction(ObjectManager $objectManager) : Action
    {
        $action = new Action();
        $action->setName("Test");
        $action->setSlug("test");
        $action->setPublished(true);
        $action->setMandate($this->createMandate($objectManager));
        $action->setOccurredTime(new \DateTime());

        $objectManager->persist($action);
        $objectManager->flush();

        return $action;
    }
}