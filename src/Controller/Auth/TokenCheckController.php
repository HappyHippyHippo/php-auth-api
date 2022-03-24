<?php

namespace App\Controller\Auth;

use App\Config\Config;
use App\Service\AuthTokenService;
use App\Validator\Auth\TokenCheckValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TokenCheckController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 100;

    /**
     * @param Config $config
     * @param TokenCheckValidator $validator
     * @param AuthTokenService $service
     */
    public function __construct(
        Config $config,
        private TokenCheckValidator $validator,
        private AuthTokenService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/auth", name="auth.token.check", methods={"GET"})
     */
    public function checkToken(Request $request): Response
    {
        $this->setEndpointCode(self::ENDPOINT_CODE);

        return $this->envelope(
            function () use ($request) {
                $this->service->check($this->validator->validate($request));
            },
            Response::HTTP_NO_CONTENT
        );
    }
}
