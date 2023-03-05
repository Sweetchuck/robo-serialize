<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Serialize\Task;

use Robo\Task\BaseTask as RoboBaseTask;

abstract class BaseTask extends RoboBaseTask
{
    protected string $taskName = 'Serialize';

    /**
     * @var array<string, mixed>
     */
    protected array $assets = [];

    protected string $assetNamePrefix = '';

    public function getAssetNamePrefix(): string
    {
        return $this->assetNamePrefix;
    }

    public function setAssetNamePrefix(string $assetNamePrefix): static
    {
        $this->assetNamePrefix = $assetNamePrefix;

        return $this;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function setOptions(array $options): static
    {
        foreach ($options as $key => $value) {
            switch ($key) {
                case 'assetNamePrefix':
                    $this->setAssetNamePrefix($value);
                    break;
            }
        }

        return $this;
    }
}
