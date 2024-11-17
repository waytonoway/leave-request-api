<?php
namespace App\Repository;

use App\Entity\User;

use Doctrine\ORM\EntityRepository;

class LeaveRequestRepository extends EntityRepository {
    public function findLeaveRequestsByDates(\DateTime $startDate, \DateTime $endDate, ?User $user): array {
        $query = $this->createQueryBuilder("r")
            ->where("r.startDate <= :startDate")
            ->andWhere("r.endDate >= :endDate");

        if ($user) {
            $query->andWhere("r.user = :user");
        }

        $query->setParameter("startDate", $startDate)
            ->setParameter( "endDate", $endDate);

        if ($user) {
            $query->setParameter("user", $user);
        }

        return $query->getQuery()
            ->getResult();
    }
}

