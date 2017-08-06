<?php

namespace Sweetchuck\Robo\Serialize\Test\Helper\RoboFiles;

use Robo\Contract\TaskInterface;
use Robo\Tasks;
use Sweetchuck\Robo\Serialize\SerializeTaskLoader;

class SerializeTaskRoboFile extends Tasks
{
    use SerializeTaskLoader;

    public function serializeYaml(): TaskInterface
    {
        return $this
            ->taskSerialize()
            ->setSubject(['a' => 'b'])
            ->setDestination($this->output())
            ->setSerializer('yaml');
    }

    public function serializeJson(): TaskInterface
    {
        return $this
            ->taskSerialize()
            ->setSubject(['a' => 'b'])
            ->setDestination($this->output())
            ->setSerializer('json');
    }
}
