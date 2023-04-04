<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Project;
use App\Form\SearchType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function save(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function paginationQuery(SearchData $search): Query
    {
        $query =  $this->createQueryBuilder('p');

        if (!empty($search->projectName)) {
            $query = $query
                ->andWhere('p.title LIKE :projectName')
                ->setParameter('projectName', "%{$search->projectName}%");
        }
        if (!empty($search->projectStatus)) {
            $query = $query
                ->andWhere('p.status LIKE :projectStatus')
                ->setParameter('projectStatus', "%{$search->projectStatus}%");
        }

        if (!empty($search->filenameOrUrl)) {
            $query = $query
                ->andWhere('p.filenameOrUrl LIKE :filenameOrUrl')
                ->setParameter('filenameOrUrl', "%{$search->filenameOrUrl}%");
        }

        return $query->getQuery();
    }

    //    /**
    //     * @return Project[] Returns an array of Project objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Project
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
