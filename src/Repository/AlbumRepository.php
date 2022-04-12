<?php

namespace App\Repository;

use App\Entity\Album;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Album|null find($id, $lockMode = null, $lockVersion = null)
 * @method Album|null findOneBy(array $criteria, array $orderBy = null)
 * @method Album[]    findAll()
 * @method Album[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlbumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Album::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Album $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Album $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Album[] Returns an array of Album objects
    //  */
    public function findAllOrdered($name)
    {
        return $this->createQueryBuilder('al')
            ->innerJoin('al.artists', 'ar')
            ->andWhere('ar.name LIKE :name')
            ->orderBy('al.published_at', 'DESC')
            ->setParameter('name', '%'.$name.'%')
            ->getQuery()
            ->getResult()
        ;
    }


    public function findAlbumFilter($search)
    {
        // Ici je crée un objet QueryBuilder sur la table Album
        $qb = $this->createQueryBuilder('al');
        // SI j'ai effectué une recherche en indiquant le nom de l'artiste. ALORS je peux filtrer par le nom. 
        // Cela m'évite de faire des requête inutile en BDD.
        if ( !empty($search['artiste']) ) {
            $qb->innerJoin('al.artists', 'ar')
            ->andWhere('ar.name LIKE :name')
            ->setParameter('name', '%' . $search['artiste'] . '%');
        }
        if ( !empty($search['before']) ) {
            $qb->andWhere('al.published_at < :before')
            ->setParameter('before', $search['before']);
        }
        if ( !empty($search['after']) ) {
            $qb->andWhere('al.published_at > :after')
            ->setParameter('after', $search['after']);
        }
        return 
            $qb->orderBy('al.published_at', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Album
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
