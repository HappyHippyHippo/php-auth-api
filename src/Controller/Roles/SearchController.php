<?php

namespace App\Controller\Roles;

use App\Config\Config;
use App\Service\RoleService;
use App\Validator\Roles\SearchValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 301;

    /**
     * @param Config $config
     * @param SearchValidator $validator
     * @param RoleService $service
     */
    public function __construct(
        Config $config,
        private SearchValidator $validator,
        private RoleService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/roles", name="roles.search", methods={"GET"})
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
