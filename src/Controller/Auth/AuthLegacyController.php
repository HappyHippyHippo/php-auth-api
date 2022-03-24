<?php

namespace App\Controller\Auth;

use App\Config\Config;
use App\Service\AuthLegacyService;
use App\Validator\Auth\AuthLegacyValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthLegacyController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 104;

    /**
     * @param Config $config
     * @param AuthLegacyValidator $validator
     * @param AuthLegacyService $service
     */
    public function __construct(
        Config $config,
        private AuthLegacyValidator $validator,
        private AuthLegacyService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/auth/legacy", name="auth.legacy", methods={"POST"})
     */
    public function auth(Request $request): Response
    {
        $this->setEndpointCode(self::ENDPOINT_CODE);

        return $this->envelope(
            function () use ($request) {
                return $this->service->authenticate($this->validator->validate($request));
            },
            Response::HTTP_CREATED
        );
    }

    /**
     * @return bool
     */
    protected function isEnabled(): bool
    {
        return $this->config instanceof Config && !!$this->config->isAuthLegacyEnabled();
    }
}
