<?php

declare(strict_types=1);

namespace App\Repository;

use App\Component\Exception\EntityNotExistException;
use App\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Payment>
 * @method Payment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payment|null findOneBy(array $criteria, array $orderBy = null)
 * @method array<Payment> findAll()
 * @method array<Payment> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function save(Payment $payment, bool $flush = true): void
    {
        $this->getEntityManager()->persist($payment);

        // phpcs:ignore
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Payment $payment, bool $flush = true): void
    {
        $this->getEntityManager()->remove($payment);

        // phpcs:ignore
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findPayment(
        ?int $id = null,
        ?string $uuid = null,
        ?int $order = null,
        ?int $status = null,
        ?int $type = null,
        ?int $amount = null,
        ?int $user = null
    ): ?Payment
    {
        $qb = $this->createQueryBuilder('p');

        if ($id) {
            $qb->andWhere('p.id = :id')
                ->setParameter('id', $id);
        }

        if ($uuid) {
            $qb->andWhere('p.uuid = :uuid')
                ->setParameter('uuid', $uuid);
        }
        
        if ($order) {
            $qb->andWhere('p.ord = :order')
                ->setParameter('order', $order);
        }

        if ($status) {
            $qb->andWhere('p.status = :status')
                ->setParameter('status', $status);
        }

        if ($type) {
            $qb->andWhere('p.type = :type')
                ->setParameter('type', $type);
        }

        if ($amount) {
            $qb->andWhere('p.amount = :amount')
                ->setParameter('amount', $amount);
        }

        if ($user) {
            $qb->andWhere('p.usr = :user')
                ->setParameter('user', $user);
        }

        /** @var ?Payment $result */
        $result = $qb->getQuery()->getOneOrNullResult();
        
        return $result;
    }
    
    public function get(
        ?int $id = null,
        ?string $uuid = null,
        ?int $order = null,
        ?int $status = null,
        ?int $type = null,
        ?int $amount = null,
        ?int $user = null
    ): Payment
    {
        $payment = $this->findPayment($id, $uuid, $order, $status, $type, $amount, $user);
        
        if (!$payment) {
            throw new EntityNotExistException('Payment does not exist.');
        }

        return $payment;
    }

    public function isExist(?int $id = null, ?int $order = null, ?int $status = null, ?int $type = null): bool
    {
        $qb = $this->createQueryBuilder('p');

        $qb->select('p.id');

        if ($id) {
            $qb->andWhere('p.id = :id')
                ->setParameter('id', $id);
        }

        if ($order) {
            $qb->andWhere('p.ord = :order')
                ->setParameter('order', $order);
        }

        if ($status) {
            $qb->andWhere('p.status = :status')
                ->setParameter('status', $status);
        }

        if ($type) {
            $qb->andWhere('p.type = :type')
                ->setParameter('type', $type);
        }

        return (bool) $qb->getQuery()->getScalarResult();
    }
}
