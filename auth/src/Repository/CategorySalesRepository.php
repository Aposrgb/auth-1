<?php

namespace App\Repository;

use App\Entity\CategorySales;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method CategorySales|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategorySales|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategorySales[]    findAll()
 * @method CategorySales[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorySalesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorySales::class);
    }

    public function findCategories($url, $category)
    {
        $qb = $this
            ->createQueryBuilder('s');
        return $qb
            ->where($qb->expr()->like('s.category', ':url'))
            ->andWhere('s.entity = :entity')
            ->setParameter('entity', $category)
            ->setParameter('url', "%".$url."%")
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(CategorySales $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(CategorySales $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return CategorySales[] Returns an array of CategorySales objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CategorySales
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
