<?php

namespace App\Controller\UserRoles;

use App\Config\Config;
use App\Service\UserRoleService;
use App\Validator\UserRoles\PriorityValidator;
use Hippy\Api\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PriorityController extends AbstractController
{
    /** @var int */
    private const ENDPOINT_CODE = 605;

    /**
     * @param Config $config
     * @param PriorityValidator $validator
     * @param UserRoleService $service
     */
    public function __construct(
        Config $config,
        private PriorityValidator $validator,
        private UserRoleService $service
    ) {
        parent::__construct($config);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/users/{userId}/roles/{roleId}/priority", name="users.roles.priority", methods={"POST"})
     */
    public function user(Request $request): Response
    {
        $this->setEndpointCode(self::ENDPOINT_CODE);

        return $this->envelope(
            function () use ($request) {
                return $this->service->priority($this->validator->validate($request));
            }
        );
    }
}
