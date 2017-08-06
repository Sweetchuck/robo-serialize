<?php

namespace Sweetchuck\Robo\Serialize;

use Sweetchuck\Robo\Serialize\Task\SerializeTask;
use Robo\Collection\CollectionBuilder;

trait SerializeTaskLoader
{
    /**
     * @return \Sweetchuck\Robo\Serialize\Task\SerializeTask|\Robo\Collection\CollectionBuilder
     */
    protected function taskSerialize(array $options = []): CollectionBuilder
    {
        return $this->task(SerializeTask::class, $options);
    }
}
