<?php

namespace App\Controller;

use App\Entity\LeaveType;

use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Routing\Annotation\Route;

use JMS\Serializer\ContextFactory\SerializationContextFactoryInterface;
use JMS\Serializer\SerializerInterface;

/**
 * @Route(path="api/leave-types", name="leave_types")
 */
class LeaveTypeController extends BaseController {
    /**
     * @Route(methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns leave types",
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=LeaveType::class, groups={"leave_type"})))
     * )
     */
    public function cgetAction(Request $request): JsonResponse {
        $leaveTypes = $this->entityManager->getRepository(LeaveType::class)
            ->findBy([], ["type" => "ASC"]);

        return $this->returnResponse($leaveTypes, ["leave_type"]);
    }
}
