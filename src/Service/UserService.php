<?php

namespace App\Service;

use App\Config\Config;
use App\Error\ErrorCode;
use App\Model\Controller\Users as Models;
use App\Model\Entity\Local\User;
use App\Repository\RepositoryFactory;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Hippy\Api\Repository\ListResult;
use Symfony\Component\HttpFoundation\Response;

class UserService extends AbstractService
{
    /**
     * @param Config $config
     * @param RepositoryFactory $repoFactory
     * @param HasherService $hasher
     */
    public function __construct(
        private Config $config,
        private RepositoryFactory $repoFactory,
        private HasherService $hasher,
    ) {
    }

    /**
     * @param Models\GetRequest $request
     * @return Models\SingleResponse
     */
    public function get(Models\GetRequest $request): Models\SingleResponse
    {
        // send the requested user information to the client
        return new Models\SingleResponse($this->getUser($request->getUserId()));
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

        // search for the list of users
        $result = $this->repoFactory->getUsers()->search($search, $start, $count);

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
        $repo = $this->repoFactory->getUsers();

        // check for email duplicate
        $dup = $repo->findByEmail($request->getEmail());
        if (!is_null($dup)) {
            $this->throws(Response::HTTP_CONFLICT, ErrorCode::USER_DUPLICATE_EMAIL);
        }

        // generate a salt and hash the password
        $salt = $this->hasher->string();
        $password = $this->hasher->hash($request->getPassword(), $salt);

        // create the new user
        $now = new DateTime();
        $creator = $repo->findById($request->getHeaderAuthUserId());
        return new Models\SingleResponse($repo->persist(
            new User([
                'enabled' => $request->isEnabled(),
                'email' => $request->getEmail(),
                'password' => $password,
                'salt' => $salt,
                'tries' => $this->config->getAuthTries(),
                'coolDown' => new DateTime('1900-01-01 00:00:00'),
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
        $repo = $this->repoFactory->getUsers();

        // check if the user is present
        $user = $this->getUser($request->getUserId());

        // check for email duplicate
        $dup = $repo->findByEmail($request->getEmail());
        if (!is_null($dup) && $user->getId() != $dup->getId()) {
            $this->throws(Response::HTTP_CONFLICT, ErrorCode::USER_DUPLICATE_EMAIL);
        }

        // persist updates
        return new Models\SingleResponse($repo->update(
            $user
                ->setEnabled($request->isEnabled())
                ->setEmail($request->getEmail())
                ->setUpdatedAt(new DateTime())
                ->setUpdatedBy($repo->findById($request->getHeaderAuthUserId()))
        ));
    }

    /**
     * @param Models\EnableRequest $request
     * @return Models\SingleResponse
     */
    public function enable(Models\EnableRequest $request): Models\SingleResponse
    {
        $repo = $this->repoFactory->getUsers();

        // set user as enabled
        return new Models\SingleResponse($repo->update(
            $this->getUser($request->getUserId())
                ->setEnabled(true)
                ->setUpdatedAt(new DateTime())
                ->setUpdatedBy($repo->findById($request->getHeaderAuthUserId()))
        ));
    }

    /**
     * @param Models\DisableRequest $request
     * @return Models\SingleResponse
     */
    public function disable(Models\DisableRequest $request): Models\SingleResponse
    {
        $repo = $this->repoFactory->getUsers();

        // set user as disabled
        return new Models\SingleResponse($repo->update(
            $this->getUser($request->getUserId())
                ->setEnabled(false)
                ->setUpdatedAt(new DateTime())
                ->setUpdatedBy($repo->findById($request->getHeaderAuthUserId()))
        ));
    }

    /**
     * @param Models\WarmupRequest $request
     * @return Models\SingleResponse
     */
    public function warmup(Models\WarmupRequest $request): Models\SingleResponse
    {
        $repo = $this->repoFactory->getUsers();

        // remove the cool down status from the user
        return new Models\SingleResponse($repo->update(
            $this->getUser($request->getUserId())
                ->setTries($this->config->getAuthTries())
                ->setCoolDown(new DateTime('1900-01-01 00:00:00'))
                ->setUpdatedAt(new DateTime())
                ->setUpdatedBy($repo->findById($request->getHeaderAuthUserId()))
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
}
