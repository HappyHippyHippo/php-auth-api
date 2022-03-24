<?php

namespace App\Controller\UserPermissions;

use App\Config\Config;
use App\Service\UserPermissionService;
use App\Validator\UserPermissions\DisableValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DisableController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 505;

    /**
     * @param Config $config
     * @param DisableValidator $validator
     * @param UserPermissionService $service
     */
    public function __construct(
        Config $config,
        private DisableValidator $validator,
        private UserPermissionService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/users/{userId}/permissions/{permissionId}/disable", name="user_permissions.disable", methods={"POST"})
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
