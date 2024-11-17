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
     * @OA\Response(
     *      response=200,
     *      description="Returns leave requests",
     *      @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=LeaveRequest::class, groups={"leave_request", "leave_type", "user"})))
     *  )
     */
    public function getLeaveRequests(Request $request): JsonResponse {
        $items = $this->entityManager->getRepository(LeaveRequest::class)->findAll();

        return $this->returnResponse($items, ["leave_request", "leave_type", "user"]);
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
        $form->submit($formData);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingLeaveRequest = $this->entityManager->getRepository(LeaveRequest::class)
                ->findLeaveRequestsByDates($leaveRequest->getStartDate(), $leaveRequest->getEndDate(), $leaveRequest->getUser());

            if (!empty($existingLeaveRequest)) {
                $form->addError(new FormError("Leave request overlaps with an existing one."));
            }

            if (count($form->getErrors()) === 0) {
                $this->entityManager->persist($leaveRequest);
                $this->entityManager->flush();

                return $this->returnResponse($leaveRequest, ["leave_request", "leave_type", "user"], Response::HTTP_CREATED);
            }
        }

        $errors = [];

        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        return new JsonResponse(["errors" => $errors], Response::HTTP_BAD_REQUEST);
    }
}
