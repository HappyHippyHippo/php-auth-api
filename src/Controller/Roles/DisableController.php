<?php

namespace App\Controller\Roles;

use App\Config\Config;
use App\Service\RoleService;
use App\Validator\Roles\DisableValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DisableController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 305;

    /**
     * @param Config $config
     * @param DisableValidator $validator
     * @param RoleService $service
     */
    public function __construct(
        Config $config,
        private DisableValidator $validator,
        private RoleService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/roles/{roleId}/disable", name="roles.disable", methods={"POST"})
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
