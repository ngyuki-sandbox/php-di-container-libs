<?php
namespace App;

class Are
{
    /**
     * @var Ore
     */
    private $ore;

    public function __construct(Ore $ore)
    {
        $this->ore = $ore;
    }

    public function say()
    {
        return "Are -> " . $this->ore->say();
    }
}
