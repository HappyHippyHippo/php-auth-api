<?php

namespace App\Controller\UserRoles;

use App\Config\Config;
use App\Service\UserRoleService;
use App\Validator\UserRoles\GetValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 600;

    /**
     * @param Config $config
     * @param GetValidator $validator
     * @param UserRoleService $service
     */
    public function __construct(
        Config $config,
        private GetValidator $validator,
        private UserRoleService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/users/{userId}/roles", name="users.roles", methods={"GET"})
     */
    public function user(Request $request): Response
    {
        $this->setEndpointCode(self::ENDPOINT_CODE);

        return $this->envelope(
            function () use ($request) {
                return $this->service->get($this->validator->validate($request));
            }
        );
    }
}
