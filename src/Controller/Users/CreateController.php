<?php

namespace App\Controller\Users;

use App\Config\Config;
use App\Service\UserService;
use App\Validator\Users\CreateValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 202;

    /**
     * @param Config $config
     * @param CreateValidator $validator
     * @param UserService $service
     */
    public function __construct(
        Config $config,
        private CreateValidator $validator,
        private UserService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/users", name="users.create", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $this->setEndpointCode(self::ENDPOINT_CODE);

        return $this->envelope(
            function () use ($request) {
                return $this->service->create($this->validator->validate($request));
            },
            Response::HTTP_CREATED
        );
    }
}
