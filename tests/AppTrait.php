<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Persistence\ObjectManager;

trait AppTrait
{
    protected static function getLangs()
    {
        return ['ro'];
    }

    protected static function getFirewallContext()
    {
        return 'main';
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
}