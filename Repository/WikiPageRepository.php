<?php
namespace IndyDevGuy\WikiBundle\Repository;

use IndyDevGuy\WikiBundle\Entity\WikiPage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;

/**
 * @method WikiPage|null find($id, $lockMode = null, $lockVersion = null)
 * @method WikiPage|null findOneBy(array $criteria, array $orderBy = null)
 * @method WikiPage[]    findAll()
 * @method WikiPage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WikiPageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WikiPage::class);
    }

    public function findWikiPageByNameWithDashes(string $name)
    {
        $newName = str_replace('-',' ', $name);
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
        //return $this->findOneBy(['name' => $newName]);
    }

//    /**
//     * @return WikiPage[] Returns an array of WikiPage objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WikiPage
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findByWikiId($wikiId)
    {
        return $this->findBy(['wiki' => $wikiId]);
    }

    public function findOneByWikiIdAndName($wikiId, $name)
    {
        return $this->findOneBy(['wiki' => $wikiId, 'name' => $name]);
    }
}
