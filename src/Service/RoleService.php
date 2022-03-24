<?php

namespace App\Service;

use App\Config\Config;
use App\Error\ErrorCode;
use App\Model\Controller\Roles as Models;
use App\Model\Entity\Local\Role;
use App\Repository\RepositoryFactory;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Hippy\Api\Repository\ListResult;
use Symfony\Component\HttpFoundation\Response;

class RoleService extends AbstractService
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
        // send the requested role information to the client
        return new Models\SingleResponse($this->getRole($request->getRoleId()));
    }

    /**
     * @param Models\SearchRequest $request
     * @return Models\ListResponse
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function search(Models\SearchRequest $request): Models\ListResponse
    {
        // compose the search parameters
        $max = $this->config->getListingMaxRecords();
        $search = $request->getSearch();
        $start = (int) $request->getStart();
        $count = (int) min($request->getCount() ?? $max, $max);

        // search for the list of roles
        $result = $this->repoFactory->getRoles()->search($search, $start, $count);

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
        $repo = $this->repoFactory->getRoles();

        // check for email duplicate
        $dup = $repo->findByName($request->getName());
        if (!is_null($dup)) {
            $this->throws(Response::HTTP_CONFLICT, ErrorCode::DUPLICATE_ROLE_NAME);
        }

        // create the new role
        $now = new DateTime();
        $creator = $this->repoFactory->getUsers()->findById($request->getHeaderAuthUserId());
        return new Models\SingleResponse($repo->persist(
            new Role([
                'enabled' => $request->isEnabled(),
                'name' => $request->getName(),
                'description' => $request->getDescription(),
                'createdAt' => $now,
                'createdBy' => $creator,
                'updatedAt' => $now,
                'updatedBy' => $creator,
            ])
        ));
    }

    /**
     * @param Models\UpdateRequest $request
     * @return Models\SingleResponse
     */
    public function update(Models\UpdateRequest $request): Models\SingleResponse
    {
        $repo = $this->repoFactory->getRoles();

        // check if the role is present
        $role = $this->getRole($request->getRoleId());

        // check for email duplicate
        $dup = $repo->findByName($request->getName());
        if (!is_null($dup) && $role->getId() != $dup->getId()) {
            $this->throws(Response::HTTP_CONFLICT, ErrorCode::DUPLICATE_ROLE_NAME);
        }

        // persist updates
        return new Models\SingleResponse($repo->update(
            $role
                ->setEnabled($request->isEnabled())
                ->setName($request->getName())
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
        // set role as enabled
        $repo = $this->repoFactory->getRoles();
        return new Models\SingleResponse($repo->update(
            $this->getRole($request->getRoleId())
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
        // set role as disabled
        $repo = $this->repoFactory->getRoles();
        return new Models\SingleResponse($repo->update(
            $this->getRole($request->getRoleId())
                ->setEnabled(false)
                ->setUpdatedAt(new DateTime())
                ->setUpdatedBy($this->repoFactory->getUsers()->findById($request->getHeaderAuthUserId()))
        ));
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
}
