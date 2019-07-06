<?php

namespace App\Repository;

use App\Entity\TaskNote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TaskNote|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskNote|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskNote[]    findAll()
 * @method TaskNote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskNoteRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TaskNote::class);
    }

    // /**
    //  * @return TaskNote[] Returns an array of TaskNote objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TaskNote
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
