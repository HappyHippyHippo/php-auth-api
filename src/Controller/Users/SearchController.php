<?php

namespace App\Controller\Users;

use App\Config\Config;
use App\Service\UserService;
use App\Validator\Users\SearchValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 201;

    /**
     * @param Config $config
     * @param SearchValidator $validator
     * @param UserService $service
     */
    public function __construct(
        Config $config,
        private SearchValidator $validator,
        private UserService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/users", name="users.search", methods={"GET"})
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
