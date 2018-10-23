<?php

namespace Ivoz\Provider\Infrastructure\Persistence\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Ivoz\Provider\Domain\Model\Brand\Brand;
use Ivoz\Provider\Domain\Model\Brand\BrandInterface;
use Ivoz\Provider\Domain\Model\BrandService\BrandServiceRepository;
use Ivoz\Provider\Domain\Model\BrandService\BrandService;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * BrandServiceDoctrineRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BrandServiceDoctrineRepository extends ServiceEntityRepository implements BrandServiceRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BrandService::class);
    }

    public function findByIden(BrandInterface $brand, string $iden)
    {
        $qb = $this->createQueryBuilder('self');
        $query = $qb
            ->select('self')
            ->innerJoin('self.service', 'service')
            ->where(
                $qb->expr()->eq('self.brand', $brand->getId())
            )
            ->andWhere(
                $qb->expr()->eq('service.iden', "'$iden'")
            )
            ->getQuery();

        $result = $query->getResult();
        return array_shift($result);
    }
}
