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

    public function level()
    {
        return $this->getLevel('max');
    }

    public function minLevel()
    {
        return $this->getLevel('min');
    }

    public function allLevel()
    {
        return $this->getLevel('all');
    }

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