<?php

namespace App\Controller\RolePermissions;

use App\Config\Config;
use App\Service\RolePermissionService;
use App\Validator\RolePermissions\EnableValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EnableController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 404;

    /**
     * @param Config $config
     * @param EnableValidator $validator
     * @param RolePermissionService $service
     */
    public function __construct(
        Config $config,
        private EnableValidator $validator,
        private RolePermissionService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/roles/{roleId}/permissions/{permissionId}/enable", name="role_permissions.enable", methods={"POST"})
     */
    public function search(Request $request): Response
    {
        $this->setEndpointCode(self::ENDPOINT_CODE);

        return $this->envelope(
            function () use ($request) {
                return $this->service->enable($this->validator->validate($request));
            }
        );
    }
}
