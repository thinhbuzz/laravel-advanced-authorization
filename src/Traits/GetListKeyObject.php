<?php


namespace Buzz\Authorization\Traits;


use Illuminate\Database\Eloquent\Model;

trait GetListKeyObject
{
    public function getListKey($objects)
    {
        $ids = [];
        if (is_array($objects)) {
            if (head($objects) instanceof Model) {
                foreach ($objects as $object) {
                    $ids[] = $object->getKey();
                }
            } else {
                $ids = $objects;
            }
        } else {
            $ids = [$objects];
        }
        return $ids;
    }
}