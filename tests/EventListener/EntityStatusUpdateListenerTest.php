<?php

namespace App\Tests\Service;

use App\Entity\Promise;
use App\Entity\StatusUpdate;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EntityStatusUpdateListenerTest extends KernelTestCase
{
    public function testStatusUpdates()
    {
        $em = self::bootKernel()->getContainer()->get('doctrine.orm.default_entity_manager');
        $promiseRepo = $em->getRepository('App:Promise');

        $promise = new Promise();
        $promise
            ->setName('Test')
            ->setSlug('test')
            ->setDescription('Test')
            ->setMadeTime(new \DateTime())
            ->setStatus(null)
            ->setMandate($em->getRepository('App:Mandate')->findOneBy([]));
        $em->persist($promise);
        $this->createdEntities[] = $promise;

        $em->flush();

        $this->assertEquals(null, $promiseRepo->find($promise->getId())->getStatus());

        $statusUpdate = new StatusUpdate();
        $statusUpdate
            ->setPromise($promise)
            ->setAction($em->getRepository('App:Action')->findOneBy([]))
            ->setStatus($em->getRepository('App:Status')->findOneBy([]));
        $em->persist($statusUpdate);
        $this->createdEntities[] = $statusUpdate;

        $em->flush();

        $this->assertEquals(
            $statusUpdate->getStatus()->getId(),
            $promiseRepo->find($promise->getId())->getStatus()->getId()
        );

        $statusUpdate->setStatus(
            $em->getRepository('App:Status')
                ->createQueryBuilder('s')
                ->where('s.id != :status')
                ->setParameter('status', $statusUpdate->getStatus())
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
        );

        $em->flush();

        $this->assertEquals(
            $statusUpdate->getStatus()->getId(),
            $promiseRepo->find($promise->getId())->getStatus()->getId()
        );

        $em->remove($statusUpdate);
        $em->flush();

        $this->assertEquals(null, $promiseRepo->find($promise->getId())->getStatus());

        $em->remove($promise);
        $em->flush();
    }
}
