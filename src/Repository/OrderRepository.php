<?php

declare(strict_types=1);

namespace App\Repository;

use App\Component\Exception\EntityNotExistException;
use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method array<Order> findAll()
 * @method array<Order> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function save(Order $order, bool $flush = true): void
    {
        $this->getEntityManager()->persist($order);

        // phpcs:ignore
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Order $order, bool $flush = true): void
    {
        $this->getEntityManager()->remove($order);

        // phpcs:ignore
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param array<int>|int|null $statuses
     * @return array<Order>
     */
    public function findOrders(?int $id = null, array|int|null $statuses = null, ?int $user = null): array
    {
        $qb = $this->createQueryBuilder('o');

        if ($id) {
            $qb->andWhere('o.id = :id')
                ->setParameter('id', $id);
        }

        if (!empty($statuses)) {
            $qb->andWhere('o.status IN (:statuses)')
                ->setParameter('statuses', $statuses);
        }

        if ($user) {
            $qb->andWhere('o.usr = :user')
                ->setParameter('user', $user);
        }

        /** @var array<Order> $result */
        $result = $qb->getQuery()->getResult();

        return $result;
    }
    
    /**
     * @param array<int>|int|null $statuses
     */
    public function findOrder(?int $id = null, array|int|null $statuses = null, ?int $user = null): ?Order
    {
        $result = $this->findOrders($id, $statuses, $user);

        if (empty($result)) {
            return null;
        }

        return array_pop($result);
    }

    public function get(?int $id = null): Order
    {
        $order = $this->findOrder($id);
        
        if (!$order) {
            throw new EntityNotExistException(
                sprintf('Order "%d" does not exist.', $id)
            );
        }

        return $order;
    }

    /**
     * @param array<int>|int|null $statuses
     */
    public function isExist(?int $id = null, array|int|null $statuses = null, ?int $user = null): bool
    {
        $qb = $this->createQueryBuilder('o');

        $qb->select('o.id');

        if ($id) {
            $qb->andWhere('o.id = :id')
                ->setParameter('o.id', $id);
        }

        if (!empty($statuses)) {
            $qb->andWhere('o.status IN (:statuses)')
                ->setParameter('statuses', $statuses);
        }

        if ($user) {
            $qb->andWhere('o.usr = :user')
                ->setParameter('user', $user);
        }

        return (bool) $qb->getQuery()->getScalarResult();
    }
}
