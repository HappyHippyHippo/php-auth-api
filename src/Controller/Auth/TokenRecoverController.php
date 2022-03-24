<?php

namespace App\Controller\Auth;

use App\Config\Config;
use App\Service\AuthTokenService;
use App\Validator\Auth\TokenRecoverValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TokenRecoverController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 101;

    /**
     * @param Config $config
     * @param TokenRecoverValidator $validator
     * @param AuthTokenService $service
     */
    public function __construct(
        Config $config,
        private TokenRecoverValidator $validator,
        private AuthTokenService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/auth", name="auth.token.recover", methods={"PUT"})
     */
    public function recoverToken(Request $request): Response
    {
        $this->setEndpointCode(self::ENDPOINT_CODE);

        return $this->envelope(
            function () use ($request) {
                return $this->service->recover($this->validator->validate($request));
            },
            Response::HTTP_CREATED
        );
    }
}
