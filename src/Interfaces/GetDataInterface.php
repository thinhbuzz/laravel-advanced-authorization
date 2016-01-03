<?php


namespace Buzz\Authorization\Interfaces;


interface GetDataInterface
{
    public function getRoles();
    public function getPermission();
    public function getLevels();
}