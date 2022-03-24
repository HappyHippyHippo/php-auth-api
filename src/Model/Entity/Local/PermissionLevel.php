<?php

namespace App\Model\Entity\Local;

// phpcs:ignoreFile
enum PermissionLevel: string
{
    case NONE = 'none';
    case self = 'self';
    case GROUP = 'group';
    case ALL = 'all';
}
