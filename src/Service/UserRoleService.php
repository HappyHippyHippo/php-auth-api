<?php

namespace App\Service;

use App\Error\ErrorCode;
use App\Model\Controller\UserRoles as Models;
use App\Model\Entity\Local\Role;
use App\Model\Entity\Local\User;
use App\Model\Entity\Local\UserRole;
use App\Repository\RepositoryFactory;
use DateTime;
use Symfony\Component\HttpFoundation\Response;

class UserRoleService extends AbstractService
{
    /**
     * @param RepositoryFactory $repoFactory
     */
    public function __construct(
        private RepositoryFactory $repoFactory,
    ) {
    }

    /**
     * @param Models\GetRequest $request
     * @return Models\ListResponse
     */
    public function get(Models\GetRequest $request): Models\ListResponse
    {
        // get all the user roles relations
        return new Models\ListResponse(
            $this->repoFactory->getUserRoles()->searchByUser($this->getUser($request->getUserId()))
        );
    }

    /**
     * @param Models\AddRequest $request
     * @return Models\SingleResponse
     */
    public function add(Models\AddRequest $request): Models\SingleResponse
    {
        // check if the user and role are present
        $user = $this->getUser($request->getUserId());
        $role = $this->getRole($request->getRoleId());

        // check if the association as not been created yet
        $repo = $this->repoFactory->getUserRoles();
        $dup = $repo->findByUserAndRole($user, $role, true);
        if (!is_null($dup)) {
            // check if the conflict hit is on a non-deleted record
            if (is_null($dup->getDeletedAt())) {
                $this->throws(Response::HTTP_CONFLICT, ErrorCode::DUPLICATE_USER_ROLE);
            }

            // shift priorities forcing the selected priority to be unique
            $repo->shiftPriority($user, $request->getPriority());

            // un-delete the user role association
            return new Models\SingleResponse($repo->update(
                $dup
                    ->setEnabled($request->isEnabled())
                    ->setPriority($request->getPriority())
                    ->setUpdatedAt(new DateTime())
                    ->setUpdatedBy($this->repoFactory->getUsers()->findById($request->getHeaderAuthUserId()))
                    ->setDeletedAt(null)
                    ->setDeletedBy(null)
            ));
        }

        // shift priorities forcing the selected priority to be unique
        $repo->shiftPriority($user, $request->getPriority());

        // create the new user role association
        $now = new DateTime();
        $creator = $this->repoFactory->getUsers()->findById($request->getHeaderAuthUserId());
        return new Models\SingleResponse($repo->persist(
            new UserRole([
                'user' => $user,
                'role' => $role,
                'enabled' => $request->isEnabled(),
                'priority' => $request->getPriority(),
                'createdAt' => $now,
                'createdBy' => $creator,
                'updatedAt' => $now,
                'updatedBy' => $creator,
            ])
        ));
    }

    /**
     * @param Models\RemoveRequest $request
     * @return Models\SingleResponse
     */
    public function remove(Models\RemoveRequest $request): Models\SingleResponse
    {
        // check if the user role association is present
        $rel = $this->getUserRole($request->getUserId(), $request->getRoleId());

        // delete the founded relation
        $now = new DateTime();
        $creator = $this->repoFactory->getUsers()->findById($request->getHeaderAuthUserId());
        $repo = $this->repoFactory->getUserRoles();
        return new Models\SingleResponse($repo->update(
            $rel
                ->setDeletedAt($now)
                ->setDeletedBy($creator)
        ));
    }

    /**
     * @param Models\EnableRequest $request
     * @return Models\SingleResponse
     */
    public function enable(Models\EnableRequest $request): Models\SingleResponse
    {
        // check if the user role association is present
        $rel = $this->getUserRole($request->getUserId(), $request->getRoleId());

        // enable the founded relation
        $now = new DateTime();
        $creator = $this->repoFactory->getUsers()->findById($request->getHeaderAuthUserId());
        $repo = $this->repoFactory->getUserRoles();
        return new Models\SingleResponse($repo->update(
            $rel
                ->setEnabled(true)
                ->setUpdatedAt($now)
                ->setUpdatedBy($creator)
        ));
    }

    /**
     * @param Models\DisableRequest $request
     * @return Models\SingleResponse
     */
    public function disable(Models\DisableRequest $request): Models\SingleResponse
    {
        // check if the user role association is present
        $rel = $this->getUserRole($request->getUserId(), $request->getRoleId());

        // disable the founded relation
        $now = new DateTime();
        $creator = $this->repoFactory->getUsers()->findById($request->getHeaderAuthUserId());
        $repo = $this->repoFactory->getUserRoles();
        return new Models\SingleResponse($repo->update(
            $rel
                ->setEnabled(false)
                ->setUpdatedAt($now)
                ->setUpdatedBy($creator)
        ));
    }

    /**
     * @param Models\PriorityRequest $request
     * @return Models\SingleResponse
     */
    public function priority(Models\PriorityRequest $request): Models\SingleResponse
    {
        // check if the user role association is present
        $rel = $this->getUserRole($request->getUserId(), $request->getRoleId());

        // shift priorities forcing the selected priority to be unique
        $repo = $this->repoFactory->getUserRoles();
        $repo->shiftPriority($this->getUser($request->getUserId()), $request->getPriority());

        // assign th priority to the founded relation
        $now = new DateTime();
        $creator = $this->repoFactory->getUsers()->findById($request->getHeaderAuthUserId());
        return new Models\SingleResponse($repo->update(
            $rel
                ->setPriority($request->getPriority())
                ->setUpdatedAt($now)
                ->setUpdatedBy($creator)
        ));
    }

    /**
     * @param int $id
     * @return User
     */
    private function getUser(int $id): User
    {
        $repo = $this->repoFactory->getUsers();
        $user = $repo->findById($id);
        if (is_null($user)) {
            $this->throws(Response::HTTP_NOT_FOUND, ErrorCode::USER_NOT_FOUND);
        }
        return $user;
    }

    /**
     * @param int $id
     * @return Role
     */
    private function getRole(int $id): Role
    {
        $repo = $this->repoFactory->getRoles();
        $role = $repo->findById($id);
        if (is_null($role)) {
            $this->throws(Response::HTTP_NOT_FOUND, ErrorCode::ROLE_NOT_FOUND);
        }
        return $role;
    }

    /**
     * @param int $userId
     * @param int $roleId
     * @return UserRole
     */
    private function getUserRole(int $userId, int $roleId): UserRole
    {
        // check if the user and role are present
        $user = $this->getUser($userId);
        $role = $this->getRole($roleId);

        // check if the association is present
        $repo = $this->repoFactory->getUserRoles();
        $rel = $repo->findByUserAndRole($user, $role);
        if (is_null($rel)) {
            $this->throws(Response::HTTP_CONFLICT, ErrorCode::USER_ROLE_NOT_FOUND);
        }
        return $rel;
    }
}
