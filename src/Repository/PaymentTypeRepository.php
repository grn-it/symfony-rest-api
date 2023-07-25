<?php

declare(strict_types=1);

namespace App\Repository;

use App\Component\Exception\EntityNotExistException;
use App\Entity\PaymentType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PaymentType>
 * @method PaymentType|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentType|null findOneBy(array $criteria, array $orderBy = null)
 * @method array<PaymentType> findAll()
 * @method array<PaymentType> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentType::class);
    }

    public function save(PaymentType $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        // phpcs:ignore
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PaymentType $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        // phpcs:ignore
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findPaymentType(?int $id = null): ?PaymentType
    {
        $qb = $this->createQueryBuilder('pt');

        if ($id) {
            $qb->andWhere('pt.id = :id')
                ->setParameter('id', $id);
        }

        /** @var ?PaymentType $paymentType */
        $paymentType = $qb->getQuery()->getOneOrNullResult();

        return $paymentType;
    }

    public function get(?int $id = null): PaymentType
    {
        $paymentType = $this->findPaymentType(id: $id);

        if (!$paymentType) {
            throw new EntityNotExistException(
                sprintf('Payment type with id "%d" does not exist.', $id)
            );
        }

        return $paymentType;
    }
}
