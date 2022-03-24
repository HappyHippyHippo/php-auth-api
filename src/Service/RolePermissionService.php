<?php

namespace App\Service;

use App\Config\Config;
use App\Error\ErrorCode;
use App\Model\Controller\RolePermissions as Models;
use App\Model\Entity\Local\Role;
use App\Model\Entity\Local\RolePermission;
use App\Repository\RepositoryFactory;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Hippy\Api\Repository\ListResult;
use Symfony\Component\HttpFoundation\Response;

class RolePermissionService extends AbstractService
{
    /**
     * @param Config $config
     * @param RepositoryFactory $repoFactory
     */
    public function __construct(
        private Config $config,
        private RepositoryFactory $repoFactory,
    ) {
    }

    /**
     * @param Models\GetRequest $request
     * @return Models\SingleResponse
     */
    public function get(Models\GetRequest $request): Models\SingleResponse
    {
        // check if the requested role exists
        $role = $this->getRole($request->getRoleId());

        // return the requested role permission to the client
        return new Models\SingleResponse($this->getRolePermission($role, $request->getPermissionId()));
    }

    /**
     * @param Models\SearchRequest $request
     * @return Models\ListResponse
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function search(Models\SearchRequest $request): Models\ListResponse
    {
        // check if the requested role exists
        $role = $this->getRole($request->getRoleId());

        // compose the search parameters
        $max = $this->config->getListingMaxRecords();
        $search = $request->getSearch();
        $start = (int) $request->getStart();
        $count = (int) min($request->getCount() ?? $max, $max);

        // search for the list of role permissions
        $result = $this->repoFactory->getRolePermissions()->search($role, $search, $start, $count);

        // send the search result to the client
        return new Models\ListResponse(
            new ListResult(
                $result->getCollection(),
                $result->getReport()->getSearch(),
                $start,
                $count,
                $result->getReport()->getTotal()
            )
        );
    }

    /**
     * @param Models\CreateRequest $request
     * @return Models\SingleResponse
     */
    public function create(Models\CreateRequest $request): Models\SingleResponse
    {
        // check if the requested role exists
        $role = $this->getRole($request->getRoleId());

        // check for duplicate directory
        $repo = $this->repoFactory->getRolePermissions();
        $dup = $repo->findByDirectory($role, $request->getDirectory());
        if (!is_null($dup)) {
            $this->throws(Response::HTTP_CONFLICT, ErrorCode::DUPLICATE_ROLE_PERMISSION_DIRECTORY);
        }

        // persist the role permission
        $now = new DateTime();
        $creator = $this->repoFactory->getUsers()->findById($request->getHeaderAuthUserId());
        return new Models\SingleResponse(
            $repo->persist(new RolePermission([
                'enabled' => $request->isEnabled(),
                'role' => $role,
                'directory' => $request->getDirectory(),
                'level' => $request->getLevel(),
                'description' => $request->getDescription(),
                'createdAt' => $now,
                'createdBy' => $creator,
                'updatedAt' => $now,
                'updatedBy' => $creator,
            ]))
        );
    }

    /**
     * @param Models\UpdateRequest $request
     * @return Models\SingleResponse
     */
    public function update(Models\CreateRequest $request): Models\SingleResponse
    {
        // check if the requested role and permission exists
        $role = $this->getRole($request->getRoleId());
        $permission = $this->getRolePermission($role, $request->getPermissionId());

        // check for duplicate directory
        $repo = $this->repoFactory->getRolePermissions();
        $dup = $repo->findByDirectory($role, $request->getDirectory());
        if (!is_null($dup) && $dup->getId() != $permission->getId()) {
            $this->throws(Response::HTTP_CONFLICT, ErrorCode::DUPLICATE_ROLE_PERMISSION_DIRECTORY);
        }

        // update the role permission
        return new Models\SingleResponse($repo->update(
            $permission
                ->setEnabled($request->isEnabled())
                ->setDirectory($request->getDirectory())
                ->setLevel($request->getLevel())
                ->setDescription($request->getDescription())
                ->setUpdatedAt(new DateTime())
                ->setUpdatedBy($this->repoFactory->getUsers()->findById($request->getHeaderAuthUserId()))
        ));
    }

    /**
     * @param Models\EnableRequest $request
     * @return Models\SingleResponse
     */
    public function enable(Models\EnableRequest $request): Models\SingleResponse
    {
        // check if the requested role exists
        $role = $this->getRole($request->getRoleId());

        // update the role permission
        $repo = $this->repoFactory->getRolePermissions();
        return new Models\SingleResponse($repo->update(
            $this->getRolePermission($role, $request->getPermissionId())
                ->setEnabled(true)
                ->setUpdatedAt(new DateTime())
                ->setUpdatedBy($this->repoFactory->getUsers()->findById($request->getHeaderAuthUserId()))
        ));
    }

    /**
     * @param Models\DisableRequest $request
     * @return Models\SingleResponse
     */
    public function disable(Models\DisableRequest $request): Models\SingleResponse
    {
        // check if the requested role exists
        $role = $this->getRole($request->getRoleId());

        // update the role permission
        $repo = $this->repoFactory->getRolePermissions();
        return new Models\SingleResponse($repo->update(
            $this->getRolePermission($role, $request->getPermissionId())
                ->setEnabled(false)
                ->setUpdatedAt(new DateTime())
                ->setUpdatedBy($this->repoFactory->getUsers()->findById($request->getHeaderAuthUserId()))
        ));
    }

    /**
     * @param int $roleId
     * @return Role
     */
    private function getRole(int $roleId): Role
    {
        $role = $this->repoFactory->getRoles()->findById($roleId);
        if (is_null($role)) {
            $this->throws(Response::HTTP_NOT_FOUND, ErrorCode::ROLE_NOT_FOUND);
        }
        return $role;
    }

    /**
     * @param Role $role
     * @param int $permissionId
     * @return RolePermission
     */
    private function getRolePermission(Role $role, int $permissionId): RolePermission
    {
        $role = $this->repoFactory->getRolePermissions()->findById($role, $permissionId);
        if (is_null($role)) {
            $this->throws(Response::HTTP_NOT_FOUND, ErrorCode::ROLE_PERMISSION_NOT_FOUND);
        }
        return $role;
    }
}
