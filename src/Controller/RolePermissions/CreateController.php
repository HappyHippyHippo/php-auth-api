<?php

namespace App\Controller\RolePermissions;

use App\Config\Config;
use App\Service\RolePermissionService;
use App\Validator\RolePermissions\CreateValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 402;

    /**
     * @param Config $config
     * @param CreateValidator $validator
     * @param RolePermissionService $service
     */
    public function __construct(
        Config $config,
        private CreateValidator $validator,
        private RolePermissionService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/roles/{roleId}/permissions", name="role_permissions.create", methods={"POST"})
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
