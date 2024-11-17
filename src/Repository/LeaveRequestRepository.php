<?php
namespace App\Repository;

use App\Entity\LeaveRequest;
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

    public function findByFilter(array $filter, int $offset = 0, ?int $limit = null): array {
        $query = $this->createQueryBuilder("lr");

        $this->buildFilterConditions($query, $filter);

        return $query
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countByFilter(array $filter): int {
        $totalCountQuery = $this->createQueryBuilder("lr")
            ->select("COUNT(lr.id)");
        $this->buildFilterConditions($totalCountQuery, $filter);

        return $totalCountQuery
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function buildFilterConditions($query, array $filter = []) {
        $conditions = [];
        $params = [];

        if (isset($filter["start_date"])) {
            $conditions[] = "lr.startDate >= :startDate";
            $params["startDate"] = $filter["start_date"];
        }

        if (isset($filter["end_date"])) {
            $conditions[] = "lr.endDate <= :endDate";
            $params["endDate"] = $filter["end_date"];
        }

        if (isset($filter["user"])) {
            $conditions[] = "lr.user = :user";
            $params["user"] = $filter["user"];
        }

        if (isset($filter["search_query"])) {
            $conditions[] = "lr.reason LIKE :reason";
            $params["reason"] = "%" . $filter["search_query"] . "%";
        }

        if (!empty($conditions)) {
            $query->where(join(" AND ", $conditions));
            $query->setParameters($params);
        }
    }
}

