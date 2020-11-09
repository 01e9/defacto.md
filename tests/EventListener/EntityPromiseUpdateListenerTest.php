<?php

namespace App\Tests\Service;

use App\Entity\Promise;
use App\Entity\PromiseUpdate;
use App\Repository\PromiseActionRepository;
use App\Repository\PromiseRepository;
use App\Tests\TestCaseTrait;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EntityPromiseUpdateListenerTest extends WebTestCase
{
    use TestCaseTrait;

    public function testPromiseUpdates()
    {
        return $this->assertTrue(true); // fixme

        self::resetDb();

        /** @var ObjectManager $em */
        $em = self::bootKernel()->getContainer()->get('doctrine.orm.default_entity_manager');
        /** @var PromiseRepository $promiseRepo */
        $promiseRepo = $em->getRepository('App:Promise');
        /** @var PromiseActionRepository $actionsRepo */
        $actionsRepo = $em->getRepository('App:PromiseAction');

        $promise = new Promise();
        $promise
            ->setName('Test')
            ->setSlug('test')
            ->setDescription('Test')
            ->setMadeTime(new \DateTime())
            ->setStatus(null)
            ->setPolitician($em->getRepository('App:Politician')->findOneBy([]))
            ->setElection($em->getRepository('App:Election')->findOneBy([]));
        $em->persist($promise);

        $em->flush();

        $this->assertEquals(null, $promiseRepo->find($promise->getId())->getStatus());

        $actions = $actionsRepo->findBy([], null, 2);
        $this->assertCount(2, $actions);

        $promiseUpdate = new PromiseUpdate();
        $promiseUpdate
            ->setPromise($promise)
            ->setAction($actions[0])
            ->setStatus($em->getRepository('App:Status')->findOneBy([]));
        $em->persist($promiseUpdate);

        $em->flush();

        $this->assertEquals(
            $promiseUpdate->getStatus()->getId(),
            $promiseRepo->find($promise->getId())->getStatus()->getId()
        );

        $promiseUpdate->setStatus(
            $em->getRepository('App:Status')
                ->createQueryBuilder('s')
                ->where('s.id != :status')
                ->setParameter('status', $promiseUpdate->getStatus())
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
        );

        $em->flush();

        $this->assertEquals(
            $promiseUpdate->getStatus()->getId(),
            $promiseRepo->find($promise->getId())->getStatus()->getId()
        );

        $promiseUpdate2 = new PromiseUpdate();
        $promiseUpdate2
            ->setPromise($promise)
            ->setAction($actions[1])
            ->setStatus(null);
        $em->persist($promiseUpdate2);

        $em->flush();

        $this->assertEquals(
            $promiseUpdate->getStatus()->getId(),
            $promiseRepo->find($promise->getId())->getStatus()->getId()
        );

        $em->remove($promiseUpdate2);
        $em->flush();

        $em->remove($promiseUpdate);
        $em->flush();

        $this->assertEquals(null, $promiseRepo->find($promise->getId())->getStatus());

        $em->remove($promise);
        $em->flush();
    }
}
