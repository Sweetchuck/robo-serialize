<?php

use Robo\Collection\CollectionBuilder;

/**
 * Class RoboFile.
 */
// @codingStandardsIgnoreStart
class RoboFile extends \Robo\Tasks
{
    // @codingStandardsIgnoreEnd
    use \Cheppers\Robo\Serialize\SerializeTaskLoader;

    public function serializeYaml(): CollectionBuilder
    {
        return $this->taskSerialize()
            ->setSubject(['a' => 'b'])
            ->setDestination($this->output())
            ->setSerializer('yaml');
    }

    public function serializeJson(): CollectionBuilder
    {
        return $this->taskSerialize()
            ->setSubject(['a' => 'b'])
            ->setDestination($this->output())
            ->setSerializer('json');
    }
}
