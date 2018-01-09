<?php


namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class GenusRepository extends EntityRepository
{
    /**
     * @return Genus[]
     */
    public function findAllByPublishedOrderedBySize()
    {
        return $this->createQueryBuilder('genus')
            ->andWhere('genus.isPublished = :isPublished')
            ->setParameter('isPublished', true)
            ->addOrderBy('genus.speciesCount', 'DESC ')
            ->getQuery()
            ->execute();
    }

    /**
     * @return Genus[]
     */
    public function findAllByPublishedOrderedByRecentlyActive()
    {
        return $this->createQueryBuilder('genus')
            ->andWhere('genus.isPublished = :isPublished')
            ->setParameter('isPublished', true)
            ->leftJoin('genus.notes', 'genus_note')
            ->addOrderBy('genus_note.createdAt', 'DESC ')
            ->leftJoin('genus.genusScientists', 'genusScientist')
            ->addSelect('genusScientist')
            ->getQuery()
            ->execute();
    }
}