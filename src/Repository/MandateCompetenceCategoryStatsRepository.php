<?php

namespace App\Repository;

use App\Entity\Mandate;
use App\Entity\MandateCompetenceCategoryStats;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MandateCompetenceCategoryStats|null find($id, $lockMode = null, $lockVersion = null)
 * @method MandateCompetenceCategoryStats|null findOneBy(array $criteria, array $orderBy = null)
 * @method MandateCompetenceCategoryStats[]    findAll()
 * @method MandateCompetenceCategoryStats[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MandateCompetenceCategoryStatsRepository extends ServiceEntityRepository
{
    private CompetenceCategoryRepository $competenceCategoryRepository;

    public function __construct(ManagerRegistry $registry, CompetenceCategoryRepository $competenceCategoryRepository)
    {
        parent::__construct($registry, MandateCompetenceCategoryStats::class);

        $this->competenceCategoryRepository = $competenceCategoryRepository;
    }

    public function findStatsByParentCategory(Mandate $mandate, ?Collection $categoryStats = null): Collection
    {
        $categoryStats = $categoryStats ?: $mandate->getCompetenceCategoryStats();

        /** @var MandateCompetenceCategoryStats[] $stats {parentCategoryId: Stats} */
        $stats = [];

        foreach ($this->competenceCategoryRepository->findBy(['parent' => null]) as $parentCategory) {
            $stat = new MandateCompetenceCategoryStats();
            $stat->setCompetenceUsesPoints(0);
            $stat->setCompetenceUsesCount(0);
            $stat->setCompetenceCategory($parentCategory);

            $stats[ $parentCategory->getId() ] = $stat;
        }

        foreach ($categoryStats as $categoryStat /** @var MandateCompetenceCategoryStats $categoryStat */) {
            $parentCategory = $categoryStat->getCompetenceCategory()->getParent() ?: $categoryStat->getCompetenceCategory();
            $parentCategoryStats = $stats[ $parentCategory->getId() ];

            $parentCategoryStats->setCompetenceUsesPoints(
                $parentCategoryStats->getCompetenceUsesPoints() + $categoryStat->getCompetenceUsesPoints()
            );
            $parentCategoryStats->setCompetenceUsesCount(
                $parentCategoryStats->getCompetenceUsesCount() + $categoryStat->getCompetenceUsesCount()
            );
        }

        usort($stats, function (MandateCompetenceCategoryStats $a, MandateCompetenceCategoryStats $b) {
            if ($a->getCompetenceUsesPoints() === $b->getCompetenceUsesPoints()) {
                return 0;
            }
            return ($a->getCompetenceUsesPoints() > $b->getCompetenceUsesPoints()) ? -1 : 1;
        });

        return new ArrayCollection($stats);
    }
}
