<?php

namespace App\Repository;

use App\Classes\Filter;
use App\Classes\Search;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    /**
     * @var PaginatorInterface
     */
    private $paginator;
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Product::class);
        $this->paginator = $paginator;
    }

    /**
     * @param Filter $filter
     * @param Search $search
     * @param int $pages
     * @return PaginationInterface
     */
    public function findSearch(Filter $filter, Search $search, int $pages):PaginationInterface
    {
        $query = $this->getSearchQuery($filter);
        if (!empty($search->string)){
            $query = $query
                ->andWhere('p.name LIKE :string')
                ->setParameter('string', "%{$search->string}%");
        }

        $query = $query->getQuery();
        return $this->paginator->paginate(
            $query,
            $filter->page,
            $pages
        );
    }

    /**
     * @param Filter $filter
     * @return integer[]
     */
    public function findMinMax(Filter $filter): array
    {
        $results = $this->getSearchQuery($filter, true)
            ->select('MIN(p.price) as min, MAX(p.price) as max')
            ->getQuery()
            ->getScalarResult();

        return [$results[0]['min']/100, $results[0]['max']/100];
    }

    private function getSearchQuery(Filter $filter, $ignorePrice = false):QueryBuilder
    {
        $query = $this->createQueryBuilder('p')
            ->select('p','t')
            ->join('p.tags', 't');


        if (!empty($filter->min) && $ignorePrice === false){
            $query = $query
                ->andWhere('p.price >= :min')
                ->setParameter('min', $filter->min * 100);
        }

        if (!empty($filter->max) && $ignorePrice === false){
            $query = $query
                ->andWhere('p.price <= :max')
                ->setParameter('max', $filter->max * 100);
        }

        if (!empty($filter->tags)){
            $query = $query
                ->andWhere('t.id IN (:tags) ')
                ->setParameter('tags', $filter->tags);
        }
        return $query;
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
