<?php

namespace App\Repository;

use App\Entity\BlogPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Knp\Component\Pager\Pagination\AbstractPagination;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method BlogPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogPost[]    findAll()
 * @method BlogPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogPostRepository extends ServiceEntityRepository
{
    private $paginator;

    public function __construct(RegistryInterface $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, BlogPost::class);

        $this->paginator = $paginator;
    }

    public function getAdminListPaginated(Request $request): AbstractPagination
    {
        $query = $this->createQueryBuilder('p')->orderBy('p.publishTime', 'DESC');

        return $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // fixme: hardcode
            10 // fixme: hardcode
        );
    }

    public function hasConnections(string $id) : bool
    {
        return false;
    }
}
