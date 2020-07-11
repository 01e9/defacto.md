<?php

namespace App\Repository;

use App\Consts;
use App\Entity\Constituency;
use App\Entity\Election;
use App\Entity\Mandate;
use App\Repository\Vo\ConstituencyElectionVo;
use App\Repository\Vo\ElectionDataVo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\AbstractQuery;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Election|null find($id, $lockMode = null, $lockVersion = null)
 * @method Election|null findOneBy(array $criteria, array $orderBy = null)
 * @method Election[]    findAll()
 * @method Election[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ElectionRepository extends ServiceEntityRepository
{
    private SettingRepository $settingRepository;
    private ConstituencyRepository $constituencyRepository;
    private MandateRepository $mandateRepository;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Election::class);
    }

    /**
     * @required
     */
    public function setRequirements(
        SettingRepository $settingRepository,
        ConstituencyRepository $constituencyRepository,
        MandateRepository $mandateRepository
    )
    {
        $this->settingRepository = $settingRepository;
        $this->constituencyRepository = $constituencyRepository;
        $this->mandateRepository = $mandateRepository;
    }

    public function getAdminChoices() : array
    {
        $choices = [];

        foreach ($this->findBy([], ['date' => 'DESC']) as $election) {
            $choices[
                $election->getDate()->format(Consts::DATE_FORMAT_PHP) . ' | ' . $election->getName()
            ] = $election;
        }

        return $choices;
    }

    public function findWithSubElections(Election $election): array
    {
        $parentElection = $election->getParent() ?: $election;

        return $this->createQueryBuilder('e')
            ->where('e.id = :parent')
            ->orWhere('e.parent = :parent')
            ->orderBy('e.date', 'ASC') // required in some cases to override old mandates
            ->setParameter('parent', $parentElection)
            ->getQuery()
            ->getResult();
    }

    public function getCurrentElection(): ?Election
    {
        $settingId = SettingRepository::CURRENT_ELECTION_ID;

        /** @var Election $election */
        $election = $this->settingRepository->get($settingId);

        return $election ?: null;
    }

    public function getElectionData(Election $election) : ?ElectionDataVo
    {
        $elections = $this->findWithSubElections($election);

        $constituencies = [];
        foreach ($elections as $el /** @var Election $el */) {
            foreach (
                $this->constituencyRepository->createQueryBuilder('con')
                    ->innerJoin(
                        'con.candidates', 'can', 'WITH',
                        'can.election = :election'
                    )
                    ->orderBy('con.number', 'ASC')
                    ->groupBy('con.id')
                    ->setParameters(['election' => $el])
                    ->getQuery()
                    ->getResult()
                as $constituency /** @var Constituency $constituency */
            ) {
                $constituencyElection = new ConstituencyElectionVo();
                $constituencyElection->constituency = $constituency;
                $constituencyElection->election = $el;

                $constituencies[ $constituency->getId() ] = $constituencyElection;
            }
        }

        if (count($constituencies)) {
            foreach (
                $this->mandateRepository->createQueryBuilder('m')
                    ->join('m.constituency', 'c')
                    ->join('m.election', 'e')
                    ->where('m.election IN (:elections)')
                    ->andWhere('m.constituency IN (:constituencies)')
                    ->setParameter('elections', $elections)
                    ->setParameter('constituencies', array_keys($constituencies))
                    ->getQuery()
                    ->getResult()
                as $mandate /** @var Mandate $mandate */
            ) {
                $constituencyId = $mandate->getConstituency()->getId();
                $electionId = $mandate->getElection()->getId();

                if (
                    isset($constituencies[ $constituencyId ]) &&
                    $constituencies[ $constituencyId ]->election->getId() === $electionId
                ) {
                    $constituencies[ $constituencyId ]->mandate = $mandate;
                }
            }
        }

        $electionData = new ElectionDataVo();
        $electionData->election = $election;
        $electionData->constituencies = $constituencies;

        return $electionData;
    }
}
