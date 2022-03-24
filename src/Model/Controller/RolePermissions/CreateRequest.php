<?php

namespace App\Model\Controller\RolePermissions;

use App\Model\Entity\Local\PermissionLevel;
use Hippy\Api\Model\Controller\AuthRequestModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method int getRoleId()
 * @method bool isEnabled()
 * @method string getDirectory()
 * @method string|null getDescription()
 */
class CreateRequest extends AuthRequestModel
{
    /**
     * @var mixed
     * @Assert\Type(type="int", message="roleId parameter must be of type int")
     * @Assert\NotBlank(message="roleId parameter must be present")
     * @Assert\Positive(message="roleId parameter must be a positive integer")
     */
    protected mixed $roleId;

    /**
     * @var mixed
     * @Assert\Type(type="bool", message="enabled parameter must be of type bool")
     * @Assert\NotBlank(message="enabled parameter must be present")
     */
    protected mixed $enabled;

    /**
     * @var mixed
     * @Assert\Type(type="string", message="directory parameter must be of type string")
     * @Assert\NotBlank(message="directory parameter must be present")
     */
    protected mixed $directory;

    /**
     * @var mixed
     * @Assert\Type(type="string", message="level parameter must be of type string")
     * @Assert\NotBlank(message="level parameter must be present")
     * @Assert\Choice(
     *     choices={"none", "self", "group", "all"},
     *     message="level parameter must be on of [none, self, group, all]"
     * )
     */
    protected mixed $level;

    /**
     * @var mixed
     * @Assert\Type(type="string", message="description parameter must be of type string")
     */
    protected mixed $description;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->roleId = $this->searchBagInt($request->attributes, 'roleId');
        $this->enabled = $this->searchBagBool($request->request, 'enabled');
        $this->directory = $this->searchBag($request->request, 'directory');
        $this->level = $this->searchBag($request->request, 'level');
        $this->description = $this->searchBag($request->request, 'description');
    }

    /**
     * @return PermissionLevel
     */
    public function getLevel(): PermissionLevel
    {
        return PermissionLevel::from($this->level);
    }
}
