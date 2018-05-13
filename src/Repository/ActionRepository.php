<?php

namespace App\Repository;

use App\Entity\Action;
use App\Entity\Power;
use App\Entity\Promise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;

class ActionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Action::class);
    }

    public function getAdminList(Request $request)
    {
        return $this->findBy([], ['occurredTime' => 'DESC']);
    }

    public function getAdminListByPromise(Promise $promise)
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.promiseUpdates', 'pu')
            ->where('pu.promise = :promise')
            ->orderBy('a.occurredTime', 'DESC')
            ->setParameter('promise', $promise)
            ->getQuery()
            ->getResult();
    }

    public function getAdminPowerChoices(Action $action)
    {
        $choices = [];

        foreach (
            $action->getMandate()->getInstitutionTitle()->getTitle()->getPowers()
            as $power /** @var Power $power */
        ) {
            $choices[ $power->getName() ] = $power;
        }

        return $choices;
    }
}
