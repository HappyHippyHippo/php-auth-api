<?php

namespace App\Controller\UserPermissions;

use App\Config\Config;
use App\Service\UserPermissionService;
use App\Validator\UserPermissions\SearchValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 501;

    /**
     * @param Config $config
     * @param SearchValidator $validator
     * @param UserPermissionService $service
     */
    public function __construct(
        Config $config,
        private SearchValidator $validator,
        private UserPermissionService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/users/{userId}/permissions", name="user_permissions.search", methods={"GET"})
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
