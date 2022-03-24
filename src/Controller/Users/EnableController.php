<?php

namespace App\Controller\Users;

use App\Config\Config;
use App\Service\UserService;
use App\Validator\Users\EnableValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EnableController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 204;

    /**
     * @param Config $config
     * @param EnableValidator $validator
     * @param UserService $service
     */
    public function __construct(
        Config $config,
        private EnableValidator $validator,
        private UserService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/users/{userId}/enable", name="users.enable", methods={"POST"})
     */
    public function user(Request $request): Response
    {
        $this->setEndpointCode(self::ENDPOINT_CODE);

        return $this->envelope(
            function () use ($request) {
                return $this->service->enable($this->validator->validate($request));
            }
        );
    }
}
