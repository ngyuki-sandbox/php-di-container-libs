<?php
namespace App;

class Sore
{
    /**
     * @var Ore
     */
    private $ore;

    public function __construct(OreInterface $ore)
    {
        $this->ore = $ore;
    }

    public function say()
    {
        return "Sore -> " . $this->ore->say();
    }
}
