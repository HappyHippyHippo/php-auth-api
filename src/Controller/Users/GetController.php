<?php

namespace App\Controller\Users;

use App\Config\Config;
use App\Service\UserService;
use App\Validator\Users\GetValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 200;

    /**
     * @param Config $config
     * @param GetValidator $validator
     * @param UserService $service
     */
    public function __construct(
        Config $config,
        private GetValidator $validator,
        private UserService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/users/{userId}", name="users.get", methods={"GET"})
     */
    public function user(Request $request): Response
    {
        $this->setEndpointCode(self::ENDPOINT_CODE);

        return $this->envelope(
            function () use ($request) {
                return $this->service->get($this->validator->validate($request));
            }
        );
    }
}
