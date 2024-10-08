<?php

namespace App\Repository;

use App\Entity\Produits;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProduitsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produits::class);
    }

    public function AllProduit()
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.images', 'i')
            ->addSelect('i')
            ->leftJoin('p.categories', 'c')
            ->addSelect('c')
            ->getQuery()
            ->getResult();
    }

    public function findByCategoryName(string $categoryName)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.categories', 'c')
            ->where('c.name = :name')
            ->setParameter('name', $categoryName)
            ->getQuery()
            ->getResult();
    }

    public function findByCategory(?string $categoryName = null)
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->leftJoin('p.images', 'i')
            ->addSelect('i')
            ->leftJoin('p.categories', 'c')
            ->addSelect('c');
    
        if ($categoryName) {
            $queryBuilder->andWhere('c.name = :categoryName')
                ->setParameter('categoryName', urldecode($categoryName));
        }
    
        return $queryBuilder->getQuery()->getResult();
    }  
    
    public function findProductDetails(int $id)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.images', 'i')
            ->addSelect('i')
            ->leftJoin('p.categories', 'c')
            ->addSelect('c')
            ->leftJoin('p.avis', 'a') // Joindre les avis
            ->addSelect('a') 
            ->leftJoin('p.stocks', 's') // Joindre les stocks
            ->addSelect('s') 
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function findAllOrderedByIdDesc()
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.images', 'i')
            ->addSelect('i')
            ->leftJoin('p.categories', 'c')
            ->addSelect('c')
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
    
    public function searchByNameAndCategory(?string $query, ?int $categoryId)
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.images', 'i')
            ->addSelect('i')
            ->leftJoin('p.categories', 'c')
            ->addSelect('c');
    
        if ($query) {
            $qb->andWhere('p.name LIKE :query')
               ->setParameter('query', '%' . $query . '%');
        }
    
        if ($categoryId) {
            $qb->andWhere(':category MEMBER OF p.categories')
               ->setParameter('category', $categoryId);
        }
    
        return $qb->getQuery()->getResult();
    }
    public function searchByName(?string $query)
{
    return $this->createQueryBuilder('p')
        ->leftJoin('p.images', 'i')
        ->addSelect('i')
        ->where('p.name LIKE :query')
        ->setParameter('query', '%' . $query . '%')
        ->getQuery()
        ->getResult();
}
}