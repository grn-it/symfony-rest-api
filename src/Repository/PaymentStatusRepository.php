<?php

declare(strict_types=1);

namespace App\Repository;

use App\Component\Exception\EntityNotExistException;
use App\Entity\PaymentStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PaymentStatus>
 * @method PaymentStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method array<PaymentStatus> findAll()
 * @method array<PaymentStatus> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentStatus::class);
    }

    public function save(PaymentStatus $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        // phpcs:ignore
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PaymentStatus $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        // phpcs:ignore
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findPaymentStatus(?int $id = null): ?PaymentStatus
    {
        $qb = $this->createQueryBuilder('ps');

        if ($id) {
            $qb->andWhere('ps.id = :id')
                ->setParameter('id', $id);
        }

        /** @var PaymentStatus $paymentStatus */
        $paymentStatus = $qb->getQuery()->getOneOrNullResult();

        return $paymentStatus;
    }

    public function get(?int $id = null): PaymentStatus
    {
        $paymentStatus = $this->findPaymentStatus(id: $id);

        if (!$paymentStatus) {
            throw new EntityNotExistException(
                sprintf('Payment status "%d" does not exist.', $id)
            );
        }

        return $paymentStatus;
    }
}
