<?php

namespace App\Repository;

use App\Entity\Participants;
use App\Entity\Sorties;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sorties|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sorties|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sorties[]    findAll()
 * @method Sorties[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sorties::class);
    }

    // @return Sorties[] Returns an array of Sorties objects
    // Les sorties réalisées depuis plus d’un mois ne sont pas consultables.
    public function findAllFilteredByDateAndState()
    {
        $em = $this->getEntityManager();

        $today = date("Y-m-d");
        $plusOneMonth = strtotime(date("Y-m-d", strtotime($today)) . "+1 month");

        $query = $em->createQuery('
            SELECT sorties
            FROM App\Entity\Sorties sorties
            WHERE sorties.noSortie NOT IN
                (SELECT s.noSortie
                FROM App\Entity\Sorties s
                WHERE 
                    (s.etatsNoEtat = 5 or s.etatsNoEtat = 6) 
                    and s.datedebut < :date)
            ORDER BY sorties.datedebut DESC'
            )->setParameter('date', $plusOneMonth);

            return $query->getResult();
    }

    public function findByFilteredByDateAndState(Participants $participants)
    {
        $em = $this->getEntityManager();

        $today = date("Y-m-d");
        $plusOneMonth = strtotime(date("Y-m-d", strtotime($today)) . "+1 month");
        $organisateur = $participants->getNoParticipant();

        $query = $em->createQuery('
            SELECT sorties
            FROM App\Entity\Sorties sorties
            WHERE sorties.organisateur = :organisateur AND sorties.noSortie NOT IN
                (SELECT s.noSortie
                FROM App\Entity\Sorties s
                WHERE 
                    (s.etatsNoEtat = 5 or s.etatsNoEtat = 6) 
                    and s.datedebut < :date)
            ORDER BY sorties.datedebut DESC'
        )->setParameter('date', $plusOneMonth)->setParameter('organisateur', $organisateur);

        return $query->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Sorties
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
