<?php

namespace App\Controller;

use App\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Routing\Annotation\Route;

/** @Route(path="api/users") */
class UserController extends BaseController {
    /**
     * @Route(methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns users",
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=User::class, groups={"user"})))
     * )
     */
    public function cgetAction(Request $request) {
        $data = $this->entityManager->getRepository(User::class)
            ->findBy([], ["lastName" => "ASC"]);

        return $this->returnResponse($data, ["user"]);
    }
}
