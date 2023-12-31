<?php

declare(strict_types=1);

namespace App\Repository;

use App\Component\Exception\EntityNotExistException;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method array<User> findAll()
 * @method array<User> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        // phpcs:ignore
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        // phpcs:ignore
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    public function findUser(?int $id = null, ?string $email = null, ?string $session = null): ?User
    {
        $qb = $this->createQueryBuilder('u');

        if ($id) {
            $qb->andWhere('u.id = :id')
                ->setParameter('id', $id);
        }

        if ($email) {
            $qb->andWhere('u.email = :email')
                ->setParameter('email', $email);
        }

        if ($session) {
            $qb->andWhere('u.session = :session')
                ->setParameter('session', $session);
        }

        /** @var ?User $user */
        $user = $qb->getQuery()->getOneOrNullResult();

        return $user;
    }

    public function get(?int $id = null, ?string $email = null, ?string $session = null): User
    {
        $user = $this->findUser($id, $email, $session);

        if (!$user) {
            throw new EntityNotExistException(
                sprintf('User "%d" does not exist.', $user)
            );
        }

        return $user;
    }
}
