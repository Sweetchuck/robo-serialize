<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Serialize\Test\Helper\RoboFiles;

use Robo\Contract\TaskInterface;
use Robo\Tasks;
use Sweetchuck\Robo\Serialize\SerializeTaskLoader;

class RoboFileSerialize extends Tasks
{
    use SerializeTaskLoader;

    /**
     * {@inheritdoc}
     */
    protected function output()
    {
        return $this->getContainer()->get('output');
    }

    public function serialize(string $serializerName): TaskInterface
    {
        $value = ['a' => 'b'];
        $serializer = $this->getSerializer($serializerName);

        return $this
            ->taskSerialize()
            ->setValue($value)
            ->setSerializer($serializer)
            ->setWriter($this->output());
    }
}
