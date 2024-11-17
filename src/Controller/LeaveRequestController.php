<?php
namespace App\Controller;

use App\Entity\LeaveRequest;
use App\Form\LeaveRequestType;


use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

/**
 * @Route("/api/leave-requests", name="leave_requests")
 */
class LeaveRequestController extends BaseController {

    /**
     * @Route("", name="get", methods={"GET"})
     *
     * @OA\Parameter(name="order", in="query", description="Order of results", @OA\Schema(type="string"))
     * @OA\Parameter(name="page", in="query", description="Page result in paginated response", @OA\Schema(type="int"))
     * @OA\Parameter(name="limit", in="query", description="Limit in paginated response", @OA\Schema(type="int"))
     * @OA\Parameter(name="search_query", in="query", description="Limit in paginated response", @OA\Schema(type="int"))
     * @OA\Parameter(name="start_date", in="query", description="Filter by start date", @OA\Schema(type="int"))
     * @OA\Parameter(name="end_date", in="query", description="Filter by end date", @OA\Schema(type="int"))
     * @OA\Parameter(name="user", in="query", description="Filter by end date", @OA\Schema(type="int"))
     *
     * @OA\Response(
     *      response=200,
     *      description="Returns leave requests",
     *      @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=LeaveRequest::class, groups={"leave_request", "leave_type", "user"})))
     *  )
     */
    public function getLeaveRequests(Request $request): JsonResponse {
        $page = (int) $request->query->get("page", 1);
        $limit = (int) $request->query->get("limit", 10);
        $startDate = $request->query->get("start_date");
        $endDate = $request->query->get("end_date");
        $userId = $request->query->get("user");
        $offset = ($page - 1) * $limit;

        $criteria = [];
        if ($startDate) {
            $criteria["startDate"] = new \DateTime($startDate);
        }

        if ($endDate) {
            $criteria["endDate"] = new \DateTime($endDate);
        }

        if ($userId) {
            $criteria["user"] = $userId;
        }

        $items = $this->entityManager->getRepository(LeaveRequest::class)
            ->findBy($criteria, [], $limit, $offset);

        $totalCountQuery = $this->entityManager->getRepository(LeaveRequest::class)
            ->createQueryBuilder("lr")
            ->select("COUNT(lr.id)");
        $this->buildFilterConditions($totalCountQuery, $criteria);
        $totalCount = $totalCountQuery
            ->getQuery()
            ->getSingleScalarResult();

        $headers = ["X-Total-Count" => $totalCount];

        return $this->returnResponse($items, ["leave_request", "leave_type", "user"], Response::HTTP_OK, $headers);
    }

    /**
     * @Route("", name="create", methods={"POST"})
     *
     * @OA\RequestBody(
     *      description="The full JSON of the LeaveRequest",
     *      @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(ref=@Model(type=LeaveRequest::class))))
     * @OA\Response(
     *       response=201,
     *       description="Returns leave request",
     *       @OA\JsonContent(ref=@Model(type=LeaveRequest::class, groups={"leave_request", "leave_type", "user"}))
     *   )
     */
    public function createLeaveRequest(Request $request): JsonResponse {
        $leaveRequest = new LeaveRequest();
        $form = $this->createForm(LeaveRequestType::class, $leaveRequest);

        $formData = json_decode($request->getContent(), true);
        $errors = [];

        try {
            $form->submit($formData);

            if ($form->isSubmitted() && $form->isValid()) {
                if ($leaveRequest->getStartDate() <= (new \DateTime())) {
                    throw new \Exception("Leave request start date can't be in the past");
                }

                $existingLeaveRequest = $this->entityManager->getRepository(LeaveRequest::class)
                    ->findLeaveRequestsByDates($leaveRequest->getStartDate(), $leaveRequest->getEndDate(), $leaveRequest->getUser());

                if (!empty($existingLeaveRequest)) {
                    throw new \Exception("Leave request overlaps with an existing one.");
                }

                if (count($form->getErrors()) === 0) {
                    $this->entityManager->persist($leaveRequest);
                    $this->entityManager->flush();

                    return $this->returnResponse($leaveRequest, ["leave_request", "leave_type", "user"], Response::HTTP_CREATED);
                }
            }
        } catch (\Exception $exception) {
            $errors[] = $exception->getMessage();
        }

        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        return new JsonResponse(["errors" => $errors], Response::HTTP_BAD_REQUEST);
    }

    private function buildFilterConditions($query, array $criteria): array {
        $conditions = [];
        $params = [];

        if (isset($criteria["startDate"])) {
            $conditions[] = "lr.startDate >= :startDate";
            $params["startDate"] = $criteria["startDate"];
        }

        if (isset($criteria["endDate"])) {
            $conditions[] = "lr.endDate <= :endDate";
            $params["endDate"] = $criteria["endDate"];
        }

        if (isset($criteria["user"])) {
            $conditions[] = "lr.user = :user";
            $params["user"] = $criteria["user"];
        }

        $query->where(join(" AND ", $conditions));
        $query->setParameters($params);

        return $conditions;
    }
}
