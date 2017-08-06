<?php

namespace Sweetchuck\Robo\Serialize\Test\Helper\Dummy;

class Subject02 extends Subject01 implements \JsonSerializable
{
    protected $array = [];

    public function __construct(array $array)
    {
        $this->array = $array;
    }

    public function jsonSerialize()
    {
        return $this->array;
    }
}
