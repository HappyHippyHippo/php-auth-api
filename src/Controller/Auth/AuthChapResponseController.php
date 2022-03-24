<?php

namespace App\Controller\Auth;

use App\Config\Config;
use App\Service\AuthChapService;
use App\Validator\Auth\AuthChapResponseValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthChapResponseController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE_RESPONSE = 103;

    /**
     * @param Config $config
     * @param AuthChapResponseValidator $validator
     * @param AuthChapService $service
     */
    public function __construct(
        Config $config,
        private AuthChapResponseValidator $validator,
        private AuthChapService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/auth/chap", name="auth.chap.response", methods={"POST"})
     */
    public function challengeResponse(Request $request): Response
    {
        $this->setEndpointCode(self::ENDPOINT_CODE_RESPONSE);

        return $this->envelope(
            function () use ($request) {
                return $this->service->response($this->validator->validate($request));
            },
            Response::HTTP_CREATED
        );
    }

    /**
     * @return bool
     */
    protected function isEnabled(): bool
    {
        return $this->config instanceof Config && !!$this->config->isAuthChapEnabled();
    }
}
