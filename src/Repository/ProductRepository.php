<?php

declare(strict_types=1);

namespace App\Repository;

use App\Component\Exception\EntityNotExistException;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method array<Product> findAll()
 * @method array<Product> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        // phpcs:ignore
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        // phpcs:ignore
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    public function findProduct(?int $id = null): ?Product
    {
        $qb = $this->createQueryBuilder('p');

        if ($id) {
            $qb->andWhere('p.id = :id')
                ->setParameter('id', $id);
        }

        /** @var ?Product $product */
        $product = $qb->getQuery()->getOneOrNullResult();

        return $product;
    }

    public function get(?int $id = null): Product
    {
        $product = $this->findProduct($id);

        if (!$product) {
            throw new EntityNotExistException(
                sprintf('Product "%d" does not exist.', $id)
            );
        }
        
        return $product;
    }
}
