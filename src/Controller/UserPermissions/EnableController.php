<?php

namespace App\Controller\UserPermissions;

use App\Config\Config;
use App\Service\UserPermissionService;
use App\Validator\UserPermissions\EnableValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EnableController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 504;

    /**
     * @param Config $config
     * @param EnableValidator $validator
     * @param UserPermissionService $service
     */
    public function __construct(
        Config $config,
        private EnableValidator $validator,
        private UserPermissionService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/users/{userId}/permissions/{permissionId}/enable", name="user_permissions.enable", methods={"POST"})
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
