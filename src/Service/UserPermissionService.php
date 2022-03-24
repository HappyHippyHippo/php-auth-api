<?php

namespace App\Service;

use App\Config\Config;
use App\Error\ErrorCode;
use App\Model\Controller\UserPermissions as Models;
use App\Model\Entity\Local\User;
use App\Model\Entity\Local\UserPermission;
use App\Repository\RepositoryFactory;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Hippy\Api\Repository\ListResult;
use Symfony\Component\HttpFoundation\Response;

class UserPermissionService extends AbstractService
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
        // check if the requested user exists
        $user = $this->getUser($request->getUserId());

        // return the requested user permission to the client
        return new Models\SingleResponse($this->getUserPermission($user, $request->getPermissionId()));
    }

    /**
     * @param Models\SearchRequest $request
     * @return Models\ListResponse
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function search(Models\SearchRequest $request): Models\ListResponse
    {
        // check if the requested user exists
        $user = $this->getUser($request->getUserId());

        // compose the search parameters
        $max = $this->config->getListingMaxRecords();
        $search = $request->getSearch();
        $start = (int) $request->getStart();
        $count = (int) min($request->getCount() ?? $max, $max);

        // search for the list of user permissions
        $result = $this->repoFactory->getUserPermissions()->search($user, $search, $start, $count);

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
        // check if the requested user exists
        $user = $this->getUser($request->getUserId());

        // check for duplicate directory
        $repo = $this->repoFactory->getUserPermissions();
        $dup = $repo->findByDirectory($user, $request->getDirectory());
        if (!is_null($dup)) {
            $this->throws(Response::HTTP_CONFLICT, ErrorCode::DUPLICATE_USER_PERMISSION_DIRECTORY);
        }

        // persist the user permission
        $now = new DateTime();
        $creator = $this->repoFactory->getUsers()->findById($request->getHeaderAuthUserId());
        return new Models\SingleResponse(
            $repo->persist(new UserPermission([
                'enabled' => $request->isEnabled(),
                'user' => $user,
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
        // check if the requested user and permission exists
        $user = $this->getUser($request->getUserId());
        $permission = $this->getUserPermission($user, $request->getPermissionId());

        // check for duplicate directory
        $repo = $this->repoFactory->getUserPermissions();
        $dup = $repo->findByDirectory($user, $request->getDirectory());
        if (!is_null($dup) && $dup->getId() != $permission->getId()) {
            $this->throws(Response::HTTP_CONFLICT, ErrorCode::DUPLICATE_USER_PERMISSION_DIRECTORY);
        }

        // update the user permission
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
        // check if the requested user exists
        $user = $this->getUser($request->getUserId());

        // update the user permission
        $repo = $this->repoFactory->getUserPermissions();
        return new Models\SingleResponse($repo->update(
            $this->getUserPermission($user, $request->getPermissionId())
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
        // check if the requested user exists
        $user = $this->getUser($request->getUserId());

        // update the user permission
        $repo = $this->repoFactory->getUserPermissions();
        return new Models\SingleResponse($repo->update(
            $this->getUserPermission($user, $request->getPermissionId())
                ->setEnabled(false)
                ->setUpdatedAt(new DateTime())
                ->setUpdatedBy($this->repoFactory->getUsers()->findById($request->getHeaderAuthUserId()))
        ));
    }

    /**
     * @param int $userId
     * @return User
     */
    private function getUser(int $userId): User
    {
        $user = $this->repoFactory->getUsers()->findById($userId);
        if (is_null($user)) {
            $this->throws(Response::HTTP_NOT_FOUND, ErrorCode::USER_NOT_FOUND);
        }
        return $user;
    }

    /**
     * @param User $user
     * @param int $permissionId
     * @return UserPermission
     */
    private function getUserPermission(User $user, int $permissionId): UserPermission
    {
        $user = $this->repoFactory->getUserPermissions()->findById($user, $permissionId);
        if (is_null($user)) {
            $this->throws(Response::HTTP_NOT_FOUND, ErrorCode::USER_PERMISSION_NOT_FOUND);
        }
        return $user;
    }
}
