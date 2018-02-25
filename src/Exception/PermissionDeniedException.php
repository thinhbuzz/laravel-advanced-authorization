<?php


namespace Buzz\Authorization\Exception;


class PermissionDeniedException extends \Exception
{
    /**
     * @var string
     */
    private $permissions;

    /**
     * PermissionDeniedException constructor.
     * @param string[] $permissions
     */
    public function __construct(array $permissions = [])
    {
        $this->permissions = $permissions;
    }
}
