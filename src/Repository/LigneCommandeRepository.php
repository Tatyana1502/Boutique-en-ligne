<?php

namespace App\Repository;

use App\Entity\LigneCommande;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LigneCommande>
 *
 * @method LigneCommande|null find($idLigneCommande, $lockMode = null, $lockVersion = null)
 * @method LigneCommande|null findOneBy(array $criteria, array $orderBy = null)
 * @method LigneCommande[]    findAll()
 * @method LigneCommande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LigneCommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LigneCommande::class);
    }

    public function findAllLigneCommandeWithJointuresByUser(User $id): array
    {
        return $this->createQueryBuilder('l')
 
            ->addSelect('c', 'p')
            ->InnerJoin('l.id', 'c')
            ->InnerJoin('l.idProduit', 'p')
            ->andWhere('c.id = :user')
            ->setParameter('user', $id)
            ->getQuery()
            ->getResult();
            
    }

//    /**
//     * @return LigneCommande[] Returns an array of LigneCommande objects
//     */
//    public function findByUser(User $user): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.user = :user')
//            ->setParameter('user', $user)
//         //    ->orderBy('u.id', 'ASC')
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LigneCommande
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
