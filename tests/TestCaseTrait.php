<?php

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Tests\traits\FactoryTrait;

trait TestCaseTrait
{
    use FactoryTrait;

    private static $consoleApplication;

    protected function setUp()
    {
    }

    protected static function resetDb()
    {
        self::runCommand('doctrine:schema:drop --force');
        self::runCommand('doctrine:schema:create');
        self::runCommand('doctrine:fixtures:load --no-interaction');
    }

    protected static function runCommand($command)
    {
        $command = sprintf('%s --quiet', $command);

        return self::getConsoleApplication()->run(new StringInput($command));
    }

    protected static function getConsoleApplication()
    {
        if (null === self::$consoleApplication) {
            $client = self::createClient();
            $client->insulate();

            self::$consoleApplication = new Application($client->getKernel());
            self::$consoleApplication->setAutoExit(false);
        }

        return self::$consoleApplication;
    }

    protected static function langs() : array
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

    protected static function cleanup(EntityManagerInterface $em = null)
    {
        if ($em) {
            $em->close();
            $em = null;
        }
        static::$kernel->shutdown();
    }

    //region Shortcuts

    protected static function getDoctrine(Client $client): EntityManagerInterface
    {
        return $client->getContainer()->get('doctrine.orm.default_entity_manager');
    }

    protected static function getLocale(Client $client): string
    {
        return $client->getContainer()->getParameter('locale');
    }

    //endregion

    //region User utils

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

    protected static function createAdminClient(): Client
    {
        $client = self::createClient();
        $client->insulate();
        $client->followRedirects(false);
        self::logInClientAsRole($client, 'ROLE_ADMIN');
        return $client;
    }

    //endregion

    //region Assertions

    protected static function assertOnlyAdminCanAccess(string $pathWithoutLang, Client $client = null)
    {
        $isPassed = true;

        if (!$client) {
            $client = self::createClient();
            $client->insulate();
        }

        $rolesExpectations = [
            '' => false,
            'ROLE_USER' => false,
            'ROLE_ADMIN' => true,
        ];
        foreach ($rolesExpectations as $role => $expectSuccess) {
            foreach (self::langs() as $lang) {
                $client->restart();
                if ($role) {
                    self::logInClientAsRole($client, $role);
                }

                $client->request('GET', "/${lang}${pathWithoutLang}");
                $response = $client->getResponse();

                if ($expectSuccess) {
                    if (200 !== $response->getStatusCode()) {
                        $isPassed = false;
                        break 2;
                    }
                } elseif ($role) {
                    if (403 !== $response->getStatusCode()) {
                        $isPassed = false;
                        break 2;
                    }
                } else {
                    $redirectPath = parse_url($response->headers->get('location'), PHP_URL_PATH);

                    if (302 !== $response->getStatusCode() || "/${lang}/login" !== $redirectPath) {
                        $isPassed = false;
                        break 2;
                    }
                }
            }
        }

        static::assertThat($isPassed, static::isTrue(), "Only admin can access ${pathWithoutLang}");
    }

    protected static function assertHasFormErrors(Response $response)
    {
        self::assertEquals(200, $response->getStatusCode());
        self::assertContains('is-invalid', $response->getContent());
    }

    protected static function assertRedirectsToRoute(Response $response, string $routeName) : array
    {
        self::assertEquals(302, $response->getStatusCode());

        $router = self::createClient()->getContainer()->get("router");
        $route = $router->match($response->getTargetUrl());

        self::assertEquals($routeName, $route['_route']);

        return $route;
    }

    //endregion
}