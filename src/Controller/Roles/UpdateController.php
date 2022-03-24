<?php

namespace App\Controller\Roles;

use App\Config\Config;
use App\Service\RoleService;
use App\Validator\Roles\UpdateValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpdateController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 303;

    /**
     * @param Config $config
     * @param UpdateValidator $validator
     * @param RoleService $service
     */
    public function __construct(
        Config $config,
        private UpdateValidator $validator,
        private RoleService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/roles/{roleId}", name="roles.update", methods={"POST"})
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
