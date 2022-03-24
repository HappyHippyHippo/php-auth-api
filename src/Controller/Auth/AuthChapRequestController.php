<?php

namespace App\Controller\Auth;

use App\Config\Config;
use App\Service\AuthChapService;
use App\Validator\Auth\AuthChapRequestValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthChapRequestController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE_REQUEST = 102;

    /**
     * @param Config $config
     * @param AuthChapRequestValidator $validator
     * @param AuthChapService $service
     */
    public function __construct(
        Config $config,
        private AuthChapRequestValidator $validator,
        private AuthChapService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/auth/chap", name="auth.chap.request", methods={"GET"})
     */
    public function challengeRequest(Request $request): Response
    {
        $this->setEndpointCode(self::ENDPOINT_CODE_REQUEST);

        return $this->envelope(
            function () use ($request) {
                return $this->service->request($this->validator->validate($request));
            }
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
