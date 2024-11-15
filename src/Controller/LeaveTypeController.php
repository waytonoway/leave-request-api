<?php

namespace App\Controller;

use App\Entity\LeaveType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Routing\Annotation\Route;

/** @Route(path="leave_types") */
class LeaveTypeController extends AbstractController {
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $em) {
        $this->entityManager = $em;
    }

    /**
     * @Route(methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns leave types",
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=LeaveType::class, groups={"leave_type"})))
     * )
     */
    public function cgetAction(Request $request) {
        return $this->entityManager->getRepository(LeaveType::class)->findAll();
    }
}
