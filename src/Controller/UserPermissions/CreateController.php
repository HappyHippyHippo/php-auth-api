<?php

namespace App\Controller\UserPermissions;

use App\Config\Config;
use App\Service\UserPermissionService;
use App\Validator\UserPermissions\CreateValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 502;

    /**
     * @param Config $config
     * @param CreateValidator $validator
     * @param UserPermissionService $service
     */
    public function __construct(
        Config $config,
        private CreateValidator $validator,
        private UserPermissionService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/users/{userId}/permissions", name="user_permissions.create", methods={"POST"})
     */
    public function search(Request $request): Response
    {
        $this->setEndpointCode(self::ENDPOINT_CODE);

        return $this->envelope(
            function () use ($request) {
                return $this->service->create($this->validator->validate($request));
            },
            Response::HTTP_CREATED
        );
    }
}
