<?php

namespace App\Controller\Users;

use App\Config\Config;
use App\Service\UserService;
use App\Validator\Users\DisableValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DisableController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 205;

    /**
     * @param Config $config
     * @param DisableValidator $validator
     * @param UserService $service
     */
    public function __construct(
        Config $config,
        private DisableValidator $validator,
        private UserService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/users/{userId}/disable", name="users.disable", methods={"POST"})
     */
    public function user(Request $request): Response
    {
        $this->setEndpointCode(self::ENDPOINT_CODE);

        return $this->envelope(
            function () use ($request) {
                return $this->service->disable($this->validator->validate($request));
            }
        );
    }
}
