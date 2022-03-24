<?php

namespace App\Controller\RolePermissions;

use App\Config\Config;
use App\Service\RolePermissionService;
use App\Validator\RolePermissions\SearchValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 401;

    /**
     * @param Config $config
     * @param SearchValidator $validator
     * @param RolePermissionService $service
     */
    public function __construct(
        Config $config,
        private SearchValidator $validator,
        private RolePermissionService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/roles/{roleId}/permissions", name="role_permissions.search", methods={"GET"})
     */
    public function search(Request $request): Response
    {
        $this->setEndpointCode(self::ENDPOINT_CODE);

        return $this->envelope(
            function () use ($request) {
                return $this->service->search($this->validator->validate($request));
            }
        );
    }
}
