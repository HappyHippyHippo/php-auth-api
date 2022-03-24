<?php

namespace App\Controller\RolePermissions;

use App\Config\Config;
use App\Service\RolePermissionService;
use App\Validator\RolePermissions\DisableValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DisableController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 405;

    /**
     * @param Config $config
     * @param DisableValidator $validator
     * @param RolePermissionService $service
     */
    public function __construct(
        Config $config,
        private DisableValidator $validator,
        private RolePermissionService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/roles/{roleId}/permissions/{permissionId}/disable", name="role_permissions.disable", methods={"POST"})
     */
    public function search(Request $request): Response
    {
        $this->setEndpointCode(self::ENDPOINT_CODE);

        return $this->envelope(
            function () use ($request) {
                return $this->service->disable($this->validator->validate($request));
            }
        );
    }
}
