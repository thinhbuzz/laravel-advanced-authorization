<?php


namespace Buzz\Authorization\Interfaces;


interface UserLevelInterface
{
    /**
     * Get all level of user
     * @return array
     */
    public function allLevel();
    /**
     * Get avg level of user
     * @return float|int
     */
    public function avgLevel($round = false);

    /**
     * Get smallest level of user
     *
     * @return int|null
     */
    public function level();

    /**
     * Return true if user has all levels
     *
     * @param $level
     * @param bool $any
     * @return bool
     */
    public function matchLevel($level, $any = false);

    /**
     * Return true if user has one in any levels
     *
     * @param $levels
     * @return bool
     */
    public function matchAnyLevel($levels);

    /**
     * Get greatest level of user
     *
     * @return int|null
     */
    public function maxLevel();
}