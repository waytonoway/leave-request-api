<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class BaseController extends AbstractController {
    protected EntityManagerInterface $entityManager;
    protected SerializerInterface $serializer;

    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer) {
        $this->entityManager = $em;
        $this->serializer = $serializer;
    }

    public function returnResponse(array $data, array $groups): JsonResponse {
        $jsonContent = $this->serializer->serialize(
            $data, "json", SerializationContext::create()->setGroups($groups)
        );

        return new JsonResponse($jsonContent, 200, [], true);
    }
}
