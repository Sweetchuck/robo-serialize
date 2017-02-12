<?php

use Robo\Contract\TaskInterface;

// @codingStandardsIgnoreStart
class SerializeTaskRoboFile extends \Robo\Tasks
{
    // @codingStandardsIgnoreEnd
    use \Cheppers\Robo\Serialize\SerializeTaskLoader;

    public function serializeYaml(): TaskInterface
    {
        return $this->taskSerialize()
            ->setSubject(['a' => 'b'])
            ->setDestination($this->output())
            ->setSerializer('yaml');
    }

    public function serializeJson(): TaskInterface
    {
        return $this->taskSerialize()
            ->setSubject(['a' => 'b'])
            ->setDestination($this->output())
            ->setSerializer('json');
    }
}
