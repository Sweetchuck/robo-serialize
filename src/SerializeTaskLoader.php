<?php

namespace Cheppers\Robo\Serialize;

use Cheppers\Robo\Serialize\Task\SerializeTask;
use Robo\Collection\CollectionBuilder;

trait SerializeTaskLoader
{
    /**
     * @return \Cheppers\Robo\Serialize\Task\SerializeTask|\Robo\Collection\CollectionBuilder
     */
    protected function taskSerialize(array $options = []): CollectionBuilder
    {
        return $this->task(SerializeTask::class, $options);
    }
}
