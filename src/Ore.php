<?php
namespace App;

class Ore implements OreInterface
{
    private $val;

    public function __construct($val = null)
    {
        $this->val = $val;
    }

    public function get()
    {
        return $this->val;
    }

    public function set($val)
    {
        $this->val = $val;
    }

    public function say()
    {
        if ($this->val !== null) {
            return "Ore($this->val)";
        } else {
            return "Ore";
        }
    }
}
