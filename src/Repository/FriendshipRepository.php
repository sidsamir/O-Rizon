<?php

namespace App\Repository;

use App\Entity\Friendship;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Friendship>
 *
 * @method Friendship|null find($id, $lockMode = null, $lockVersion = null)
 * @method Friendship|null findOneBy(array $criteria, array $orderBy = null)
 * @method Friendship[]    findAll()
 * @method Friendship[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendshipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Friendship::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findFriendship(User $currentUser, User $profilUser): ?Friendship
    {
        return $this->createQueryBuilder('f')
            ->where('f.requester = :currentUser AND f.receiver = :profileUser')
            ->orWhere('f.requester = :profileUser AND f.receiver = :currentUser')
            ->setParameter('currentUser', $currentUser)
            ->setParameter('profileUser', $profilUser)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findFriendsOfUser(User $userFriend): ?array
    {
        return $this->createQueryBuilder('f')
            ->where('f.requester = :user OR f.receiver = :user')
            ->setParameter('user', $userFriend)
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByRequesterAndReceiver(User $requester, User $receiver): ?Friendship
    {
        return $this->createQueryBuilder('f')
            ->where('f.requester = :requester AND f.receiver = :receiver')
            ->setParameter('requester', $requester)
            ->setParameter('receiver', $receiver)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAcceptedFriendshipsByUser(User $user)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.status = :status')
            ->andWhere('f.requester = :user OR f.receiver = :user')
            ->setParameter('status', Friendship::STATUS_ACCEPTED)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findAcceptedFriendships(User $user)
    {
        $qb = $this->createQueryBuilder('f')
            ->where('f.requester = :user OR f.receiver = :user')
            ->andWhere('f.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', 'accepted') // Assurez-vous que le statut est correctement défini
            ->groupBy('f.requester, f.receiver') // Cette ligne dépend de la structure exacte de votre base
            ->getQuery();

        return $qb->getResult();
    }

    //    /**
    //     * @return Friendship[] Returns an array of Friendship objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Friendship
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
