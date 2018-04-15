<?php

namespace App\Repository;

use App\Entity\Promise;
use App\Entity\Mandate;
use App\Entity\Status;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;

class PromiseRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Promise::class);
    }

    public function getAdminChoicesByMandate(Mandate $mandate) : array
    {
        $choices = [];

        foreach ($this->findBy(['mandate' => $mandate], ['madeTime' => 'DESC']) as $promise) { /** @var Promise $promise */
            $choices[ $promise->getName() ] = $promise;
        }

        return $choices;
    }

    public function getAdminList(Request $request)
    {
        return $this->findBy([], ['madeTime' => 'DESC']);
    }

    public function getListByStatusGroupByPolitician(?Status $status)
    {
        $promises = [];

        foreach (
            $this->findBy(
                ['status' => $status, 'published' => true],
                ['madeTime' => 'DESC']
            )
            as $promise /** @var Promise $promise */
        ) {
            $politician = $promise->getMandate()->getPolitician();

            if (empty($promises[$politician->getId()])) {
                $promises[$politician->getId()] = [
                    'politician' => $politician,
                    'promises' => [],
                ];
            }

            $promises[$politician->getId()]['promises'][] = $promise;
        }

        return $promises;
    }
}
