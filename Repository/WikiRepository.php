<?php
namespace IndyDevGuy\WikiBundle\Repository;

use IndyDevGuy\WikiBundle\Entity\Wiki;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;

/**
 * @method Wiki|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wiki|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wiki[]    findAll()
 * @method Wiki[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WikiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wiki::class);
    }

    public function findWikiByNameWithDashes(string $wikiName)
    {
        $newName = str_replace('-',' ', $wikiName);
        $qb = $this->createQueryBuilder('w')
            ->andWhere('w.name = :val')
            ->setParameter('val', $newName);
        $query = $qb->getQuery();

        $entities = $query->execute();
        foreach($entities as $entity)
        {
            if($entity->getName() == $newName)
                return $entity;
        }
        return null;
    }

    public function findOneByName($name): ?Wiki
    {
        return $this->findOneBy(['name' => $name]);
    }
}
