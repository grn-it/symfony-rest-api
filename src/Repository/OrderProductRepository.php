<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\OrderProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderProduct>
 * @method OrderProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method array<OrderProduct> findAll()
 * @method array<OrderProduct> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderProduct::class);
    }

    public function save(OrderProduct $orderProduct, bool $flush = true): void
    {
        $this->getEntityManager()->persist($orderProduct);

        // phpcs:ignore
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(OrderProduct $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        // phpcs:ignore
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /** @return array<OrderProduct> */
    public function findOrderProducts(?int $order = null, ?int $product = null, ?int $user = null): array
    {
        $qb = $this->createQueryBuilder('op');

        if ($order) {
            $qb->andWhere('op.ord = :order')
                ->setParameter('order', $order);
        }

        if ($product) {
            $qb->andWhere('op.product = :product')
                ->setParameter('product', $product);
        }

        if ($user) {
            $qb->leftJoin('op.ord', 'o')
                ->andWhere('o.usr = :user')
                ->setParameter('user', $user);
        }

        /** @var array<OrderProduct> $result */
        $result = $qb->getQuery()->getResult();

        return $result;
    }
    
    public function isExist(?int $id = null, ?int $order = null, ?int $product = null, ?int $user = null): bool
    {
        $qb = $this->createQueryBuilder('op');

        $qb->select('op.id');

        if ($id) {
            $qb->andWhere('op.id = :id')
                ->setParameter('id', $id);
        }

        if ($order) {
            $qb->leftJoin('op.ord', 'o')
                ->andWhere('op.ord = :order')
                ->setParameter('order', $order);
        }

        if ($product) {
            $qb->andWhere('op.product = :product')
                ->setParameter('product', $product);
        }

        if ($user) {
            $qb->andWhere('o.usr = :user')
                ->setParameter('user', $user);
        }

        return (bool) $qb->getQuery()->getScalarResult();
    }
}
