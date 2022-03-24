<?php

namespace App\Controller\RolePermissions;

use App\Config\Config;
use App\Service\RolePermissionService;
use App\Validator\RolePermissions\UpdateValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpdateController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 403;

    /**
     * @param Config $config
     * @param UpdateValidator $validator
     * @param RolePermissionService $service
     */
    public function __construct(
        Config $config,
        private UpdateValidator $validator,
        private RolePermissionService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/roles/{roleId}/permissions/{permissionId}", name="role_permissions.update", methods={"POST"})
     */
    public function search(Request $request): Response
    {
        $this->setEndpointCode(self::ENDPOINT_CODE);

        return $this->envelope(
            function () use ($request) {
                return $this->service->update($this->validator->validate($request));
            }
        );
    }
}
