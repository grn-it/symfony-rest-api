<?php

declare(strict_types=1);

namespace App\Bundle\JsonLogin\Controller\WebService;

use App\Bundle\JsonLogin\Service\Authorization\Dto\AuthorizationDto;
use App\Bundle\JsonLogin\Service\Authorization\Token\Token;
use App\Bundle\JsonLogin\Service\User\Event\UserAuthorizedEvent;
use App\Component\Controller\AbstractBaseController;
use App\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route('/service/json-login')]
class JsonLoginAuthorizationWebServiceController extends AbstractBaseController
{
    #[Route(
        path: '/authorize',
        name: 'app_web-service_json-login_authorize',
        methods: ['POST']
    )]
    public function authorize(Serializer $serializer, EventDispatcherInterface $dispatcher): JsonResponse
    {
        $user = $this->getCurrentUser();
        $session = $this->getCookie('PHPSESSID');
        
        $dispatcher->dispatch(
            new UserAuthorizedEvent(new Token($user, $session)),
            UserAuthorizedEvent::NAME
        );

        $authorizationDto = new AuthorizationDto(true, sprintf('Logged In "%s".', $user->getUserIdentifier()));

        return new JsonResponse($serializer->serialize($authorizationDto), json: true);
    }
}
