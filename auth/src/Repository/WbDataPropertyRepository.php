<?php

namespace App\Repository;

use App\Entity\WbDataEntity\WbDataProperty;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WbDataProperty|null find($id, $lockMode = null, $lockVersion = null)
 * @method WbDataProperty|null findOneBy(array $criteria, array $orderBy = null)
 * @method WbDataProperty[]    findAll()
 * @method WbDataProperty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WbDataPropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WbDataProperty::class);
    }

    public function removeAllProp($id)
    {
        $arrayPropNames = ["wbDataSale", "wbDataIncome","wbDataOrder", "wbDataStock", "wbDataReport", "wbDataExcise"];
        $dql = $this
            ->createQueryBuilder('p')
            ->delete();
        foreach ($arrayPropNames as $name){
            $dql
                ->orWhere("p.$name = :id")
            ;
        }
        $dql
            ->setParameter("id", $id)
            ->getQuery()
            ->execute()
        ;
    }

    public function getProperty($name, $wbId)
    {
        return $this->createQueryBuilder('p')
            ->select("p.property")
            ->where("p.$name = :id")
            ->setParameter("id", $wbId)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(WbDataProperty $entity, bool $flush = true): void
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
    public function remove(WbDataProperty $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return WbDataProperty[] Returns an array of WbDataProperty objects
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
    public function findOneBySomeField($value): ?WbDataProperty
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
