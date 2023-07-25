<?php

declare(strict_types=1);

namespace App\Component\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

abstract class AbstractBaseController extends AbstractController
{
    private Request $request;

    public function __construct(RequestStack $requestStack)
    {
        $request = $requestStack->getMainRequest();

        if (!$request) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Request is null.');
        }

        $this->request = $request;
    }

    public function getCookie(string $key): ?string
    {
        $value = (string) $this->request->cookies->get($key);
        
        if (empty($value)) {
            return null;
        }
        
        return $value;
    }
    
    public function getQueryParameterInt(string $name): ?int
    {
        $queryParameter = $this->request->query->get($name);

        if (empty($queryParameter)) {
            return null;
        }

        return (int) $queryParameter;
    }

    public function getQueryParameterString(string $name): ?string
    {
        $queryParameter = $this->request->query->get($name);

        if (empty($queryParameter)) {
            return null;
        }

        return (string) $queryParameter;
    }
    
    public function getCurrentUser(): User
    {
        /** @var ?User $user */
        $user = $this->getUser();
        
        if (!$user) {
            throw new UnauthorizedHttpException('', 'Current user is unknown.');
        }
        
        return $user;
    }
}
