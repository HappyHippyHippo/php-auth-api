<?php

namespace App\Controller\Users;

use App\Config\Config;
use App\Service\UserService;
use App\Validator\Users\UpdateValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpdateController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 203;

    /**
     * @param Config $config
     * @param UpdateValidator $validator
     * @param UserService $service
     */
    public function __construct(
        Config $config,
        private UpdateValidator $validator,
        private UserService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/users/{userId}", name="users.update", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $this->setEndpointCode(self::ENDPOINT_CODE);

        return $this->envelope(
            function () use ($request) {
                return $this->service->update($this->validator->validate($request));
            }
        );
    }
}
