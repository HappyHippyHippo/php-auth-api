<?php

namespace App\Controller\RolePermissions;

use App\Config\Config;
use App\Service\RolePermissionService;
use App\Validator\RolePermissions\GetValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 400;

    /**
     * @param Config $config
     * @param GetValidator $validator
     * @param RolePermissionService $service
     */
    public function __construct(
        Config $config,
        private GetValidator $validator,
        private RolePermissionService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/roles/{roleId}/permissions/{permissionId}", name="role_permissions.get", methods={"GET"})
     */
    public function search(Request $request): Response
    {
        $this->setEndpointCode(self::ENDPOINT_CODE);

        return $this->envelope(
            function () use ($request) {
                return $this->service->get($this->validator->validate($request));
            }
        );
    }
}
