<?php

namespace App\Controller\UserPermissions;

use App\Config\Config;
use App\Service\UserPermissionService;
use App\Validator\UserPermissions\UpdateValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpdateController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 503;

    /**
     * @param Config $config
     * @param UpdateValidator $validator
     * @param UserPermissionService $service
     */
    public function __construct(
        Config $config,
        private UpdateValidator $validator,
        private UserPermissionService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/users/{userId}/permissions/{permissionId}", name="user_permissions.update", methods={"POST"})
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
