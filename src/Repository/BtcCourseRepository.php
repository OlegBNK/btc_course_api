<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\BtcCourse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BtcCourse>
 *
 * @method BtcCourse|null find($id, $lockMode = null, $lockVersion = null)
 * @method BtcCourse|null findOneBy(array $criteria, array $orderBy = null)
 * @method BtcCourse[]    findAll()
 * @method BtcCourse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BtcCourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BtcCourse::class);
    }

    /**
     * @param BtcCourse $entity
     * @param bool $flush
     */
    public function add(BtcCourse $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param BtcCourse $entity
     * @param bool $flush
     */
    public function remove(BtcCourse $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return BtcCourse
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLastCourse(): BtcCourse
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('b')
            ->from('\App\Entity\BtcCourse', 'b')
            ->orderBy('b.time', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @return bool
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isEmptyTable(): bool
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $result = $qb->select('COUNT(b)')
            ->from('\App\Entity\BtcCourse', 'b')
            ->getQuery()
            ->getSingleScalarResult();

        return (int)$result === 0;
    }

    /**
     * @param string $currencyTo
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getDateOldAddedCourse(string $currencyTo): BtcCourse
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('b')
            ->from('\App\Entity\BtcCourse', 'b')
            ->andWhere('b.currency = :currency')
            ->setParameter('currency', $currencyTo)
            ->orderBy('b.time', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @param string $currencyTo
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getDateLastAddedCourse(string $currencyTo): BtcCourse
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('b')
            ->from('\App\Entity\BtcCourse', 'b')
            ->andWhere('b.currency = :currency')
            ->setParameter('currency', $currencyTo)
            ->orderBy('b.time', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @param \DateTimeImmutable $dateTimeFrom
     * @param \DateTimeImmutable $dateTimeTo
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    public function getDataByDateRange(\DateTimeImmutable $dateTimeFrom, \DateTimeImmutable $dateTimeTo): array
    {
        return $this->getEntityManager()
            ->getConnection()
            ->createQueryBuilder()
            ->select('b.currency, b.time, b.high, b.low, b.open, b.close')
            ->from('btc_course', 'b')
            ->andWhere('b.time >= :timeFrom')
            ->setParameter('timeFrom', $dateTimeFrom->format('Y-m-d H:i:s'))
            ->andWhere('b.time <= :timeTo')
            ->setParameter('timeTo', $dateTimeTo->format('Y-m-d H:i:s'))
            ->orderBy('b.time', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();
    }
}
