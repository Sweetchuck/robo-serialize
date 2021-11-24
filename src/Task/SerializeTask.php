<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Serialize\Task;

use Robo\Result;
use Robo\Task\BaseTask;
use Symfony\Component\Console\Output\OutputInterface;

class SerializeTask extends BaseTask
{
    protected string $assetNamePrefix = '';

    public function getAssetNamePrefix(): string
    {
        return $this->assetNamePrefix;
    }

    /**
     * @return $this
     */
    public function setAssetNamePrefix(string $assetNamePrefix)
    {
        $this->assetNamePrefix = $assetNamePrefix;

        return $this;
    }

    /**
     * @var mixed
     */
    protected $value = null;

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @var null|callable
     */
    protected $serializer = null;

    public function getSerializer(): ?callable
    {
        return $this->serializer;
    }

    /**
     * @return $this
     */
    public function setSerializer(?callable $serializer)
    {
        $this->serializer = $serializer;

        return $this;
    }

    protected ?OutputInterface $writer = null;

    public function getWriter(): ?OutputInterface
    {
        return $this->writer;
    }

    /**
     * @return $this
     */
    public function setWriter(?OutputInterface $writer)
    {
        $this->writer = $writer;

        return $this;
    }

    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    /**
     * @return $this
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            switch ($key) {
                case 'assetNamePrefix':
                    $this->setAssetNamePrefix($value);
                    break;

                case 'value':
                    $this->setValue($value);
                    break;

                case 'serializer':
                    $this->setSerializer($value);
                    break;

                case 'writer':
                    $this->setWriter($value);
                    break;
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function run(): Result
    {
        $serialized = call_user_func(
            $this->getSerializer(),
            $this->getValue(),
        );

        $writer = $this->getWriter();
        if ($writer !== null) {
            $this->writer->write($serialized);
        }

        return new Result(
            $this,
            0,
            '',
            [
                $this->getAssetNamePrefix() . 'serialized' => $serialized,
            ],
        );
    }
}
