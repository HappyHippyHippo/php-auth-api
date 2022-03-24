<?php

namespace App\Controller\Users;

use App\Config\Config;
use App\Service\UserService;
use App\Validator\Users\WarmupValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WarmupController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 206;

    /**
     * @param Config $config
     * @param WarmupValidator $validator
     * @param UserService $service
     */
    public function __construct(
        Config $config,
        private WarmupValidator $validator,
        private UserService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/users/{userId}/warmup", name="users.warmup", methods={"POST"})
     */
    public function user(Request $request): Response
    {
        $this->setEndpointCode(self::ENDPOINT_CODE);

        return $this->envelope(
            function () use ($request) {
                return $this->service->warmup($this->validator->validate($request));
            }
        );
    }
}
