<?php

namespace App\Repository;

use App\Entity\Banner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Banner>
 */
class BannerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Banner::class);
    }


   /**
    * @return Banner[]
    */
    public function findActiveBanners(): array
    {
        $now = new \DateTimeImmutable(); 

        return $this->createQueryBuilder('b')
            ->andWhere('b.active = :active')

            ->andWhere('(b.start_date IS NULL OR b.start_date <= :now)')
            ->andWhere('(b.end_date IS NULL OR b.end_date >= :now)')
            
            ->setParameter('active', true)
            ->setParameter('now', $now)
            
            ->orderBy('b.created_at', 'DESC') 
            ->getQuery()
            ->getResult();
    }

}
