<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
/**
 * @extends ServiceEntityRepository<User>
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        // Check if the user is an instance of the User class
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }
        // Set the new hashed password for the user
        $user->setPassword($newHashedPassword);

        // Persist the updated user entity
        $this->getEntityManager()->persist($user);
        
        // Commit changes to the database
        $this->getEntityManager()->flush();
    }

    public function loadUserByIdentifier(string $usernameOrNom): ?User
    {
        $entityManager = $this->getEntityManager();
        // Query to find a user by username or nom (name)
        return $entityManager->createQuery(
                'SELECT u
                FROM App\Entity\User u
                WHERE u.username = :query
                OR u.nom = :query'
            )
            ->setParameter('query', $usernameOrNom)
            ->getOneOrNullResult();
    }
    
    public function findAllUserWithJointuresByUser(User $id): array
    {
        // Query to find all user data with joins by user id
        return $this->createQueryBuilder('l')
 
            ->addSelect('c', 'p')
            ->InnerJoin('l.id', 'c')
            ->InnerJoin('l.idProduit', 'p')
            ->andWhere('c.id = :user')
            ->setParameter('user', $id)
            ->getQuery()
            ->getResult();
            
    }
        
//     public function getJsonData($entityManager, $favoriteProduct)
//     {
//     $User = $this->entityManager->getRepository(User::class)->find($favoriteProduct);
//     if ($User) {
//         return json_decode($User->getFavoriteProduct(), true);
//     }

//     return null;
// }
//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
