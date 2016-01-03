<?php


namespace Buzz\Authorization\Traits;


trait UserLevelTrait
{
    /**
     * The levels of user.
     *
     * @var \Illuminate\Support\Collection
     */
    public $levels;

    /**
     * Get all level of user
     * @return array
     */
    public function allLevel()
    {
        return $this->getLevel('all');
    }

    /**
     * Get avg level of user
     * @return float
     */
    public function avgLevel($round = false)
    {
        if ($round === true)
            return round($this->getLevel('avg'));
        return $this->getLevel('avg');
    }

    protected function getLevel($method, $value = null)
    {
        $this->loadDataClass()->getLevels();

        return $this->levels->{$method}($value);
    }

    /**
     * Get smallest level of user
     *
     * @return int|null
     */
    public function level()
    {
        return $this->getLevel('min');
    }

    /**
     * Return true if user has all levels
     *
     * @param $level
     * @param bool $any
     * @return bool
     */
    public function matchLevel($level, $any = false)
    {
        if (is_array($level)) {
            foreach ($level as $item) {
                if ($this->getLevel('search', $item) === false) {
                    return false;
                } elseif ($any === true) {
                    return true;
                }
            }

            return true;
        }

        return $this->getLevel('search', $level);
    }

    /**
     * Return true if user has one in any levels
     *
     * @param $levels
     * @return bool
     */
    public function matchAnyLevel($levels)
    {
        return $this->matchLevel($levels, true);
    }

    /**
     * Get greatest level of user
     *
     * @return int|null
     */
    public function maxLevel()
    {
        return $this->getLevel('max');
    }
}
