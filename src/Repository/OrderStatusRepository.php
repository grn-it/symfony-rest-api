<?php

declare(strict_types=1);

namespace App\Repository;

use App\Component\Exception\EntityNotExistException;
use App\Entity\OrderStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderStatus>
 * @method OrderStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method array<OrderStatus> findAll()
 * @method array<OrderStatus> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderStatus::class);
    }

    public function save(OrderStatus $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        // phpcs:ignore
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(OrderStatus $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        // phpcs:ignore
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOrderStatus(?int $id = null): ?OrderStatus
    {
        $qb = $this->createQueryBuilder('os');

        if ($id) {
            $qb->andWhere('os.id = :id')
                ->setParameter('id', $id);
        }

        /** @var ?OrderStatus $orderStatus */
        $orderStatus = $qb->getQuery()->getOneOrNullResult();

        return $orderStatus;
    }

    public function get(?int $id = null): OrderStatus
    {
        $orderStatus = $this->findOrderStatus($id);

        if (!$orderStatus) {
            throw new EntityNotExistException(
                sprintf('Order status with id "%d" does not exist.', $id)
            );
        }

        return $orderStatus;
    }
}
