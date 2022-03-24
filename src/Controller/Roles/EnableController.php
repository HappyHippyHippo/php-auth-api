<?php

namespace App\Controller\Roles;

use App\Config\Config;
use App\Service\RoleService;
use App\Validator\Roles\EnableValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EnableController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 304;

    /**
     * @param Config $config
     * @param EnableValidator $validator
     * @param RoleService $service
     */
    public function __construct(
        Config $config,
        private EnableValidator $validator,
        private RoleService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/roles/{roleId}/enable", name="roles.enable", methods={"POST"})
     */
    public function user(Request $request): Response
    {
        $this->setEndpointCode(self::ENDPOINT_CODE);

        return $this->envelope(
            function () use ($request) {
                return $this->service->enable($this->validator->validate($request));
            }
        );
    }
}
