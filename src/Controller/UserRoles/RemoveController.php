<?php

namespace App\Controller\UserRoles;

use App\Config\Config;
use App\Service\UserRoleService;
use App\Validator\UserRoles\RemoveValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RemoveController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 602;

    /**
     * @param Config $config
     * @param RemoveValidator $validator
     * @param UserRoleService $service
     */
    public function __construct(
        Config $config,
        private RemoveValidator $validator,
        private UserRoleService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/users/{userId}/roles/{roleId}", name="users.roles.remove", methods={"DELETE"})
     */
    public function user(Request $request): Response
    {
        $this->setEndpointCode(self::ENDPOINT_CODE);

        return $this->envelope(
            function () use ($request) {
                return $this->service->remove($this->validator->validate($request));
            }
        );
    }
}
