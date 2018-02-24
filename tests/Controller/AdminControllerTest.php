<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\AppTrait;

class AdminControllerTest extends WebTestCase
{
    use AppTrait;

    public function testIndexAction()
    {
        $client = static::createClient();

        $rolesExpectations = [
            '' => false,
            'ROLE_USER' => false,
            'ROLE_ADMIN' => true,
        ];

        foreach (self::getLangs() as $lang) {
            foreach ($rolesExpectations as $role => $expectSuccess) {
                $client->restart();

                if ($role) {
                    self::logInClientAsRole($client, $role);
                }

                $client->request('GET', '/'. $lang .'/admin/');
                $response = $client->getResponse();

                if ($expectSuccess) {
                    $this->assertEquals(200, $response->getStatusCode(), $role .' is allowed');
                } elseif ($role) {
                    $this->assertEquals(403, $response->getStatusCode(), $role .' is not allowed');
                } else {
                    $this->assertEquals(302, $response->getStatusCode(), $role .' is not allowed');

                    $redirectPath = parse_url($response->headers->get('location'), PHP_URL_PATH);
                    $this->assertEquals('/' . $lang . '/login', $redirectPath);
                }
            }
        }
    }
}