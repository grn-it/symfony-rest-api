<?php

declare(strict_types=1);

namespace App\Controller\WebService;

use App\Component\Serializer\Serializer;
use App\Service\User\Dto\CreatedGuestUserDto;
use App\Service\User\UserFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/service/user')]
class UserWebServiceController extends AbstractController
{
    #[Route(
        path: '/guest/create',
        name: 'app_web-service_user_guest_create',
        methods: ['GET']
    )]
    public function createGuest(
        Request $request,
        Security $security,
        UserFactory $userFactory,
        Serializer $serializer
    ): JsonResponse
    {
        $request->getSession()->start();
        
        $user = $userFactory->create('ROLE_GUEST', ['session' => $request->getSession()->getId()]);
        
        $security->login($user);
        
        $createdGuestUserDto = new CreatedGuestUserDto($user->getId(), $user->getEmail());

        return new JsonResponse(
            $serializer->serialize($createdGuestUserDto),
            Response::HTTP_CREATED,
            json: true
        );
    }
}
