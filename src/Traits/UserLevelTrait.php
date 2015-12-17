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
     * @return int|null
     */
    public function level()
    {
        return $this->getLevel('min');
    }

    /**
     * @return int|null
     */
    public function maxLevel()
    {
        return $this->getLevel('max');
    }

    /**
     * @return array
     */
    public function allLevel()
    {
        return $this->getLevel('all');
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
                if ($this->getLevel('contains', $item) === false) {
                    return false;
                } elseif ($any === true) {
                    return true;
                }
            }

            return true;
        }

        return $this->getLevel('contains', $level);
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

    protected function getLevel($method, $value = null)
    {
        if (is_null($this->levels)) {
            $this->levels = $this->roles->lists('level');
        }

        return $this->levels->{$method}($value);
    }
}
