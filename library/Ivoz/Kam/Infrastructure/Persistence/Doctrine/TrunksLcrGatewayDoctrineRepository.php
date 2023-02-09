<?php

namespace Ivoz\Kam\Infrastructure\Persistence\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Ivoz\Kam\Domain\Model\TrunksLcrGateway\TrunksLcrGateway;
use Ivoz\Kam\Domain\Model\TrunksLcrGateway\TrunksLcrGatewayRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * TrunksLcrGatewayDoctrineRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TrunksLcrGatewayDoctrineRepository extends ServiceEntityRepository implements TrunksLcrGatewayRepository
{
    public const DUMMY_LCR_GATEWAY_ID = 0;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrunksLcrGateway::class);
    }

    /**
     * @return object|null
     */
    public function findDummyGateway()
    {
        return $this
            ->find(self::DUMMY_LCR_GATEWAY_ID);
    }
}
