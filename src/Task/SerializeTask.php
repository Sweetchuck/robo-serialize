<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Serialize\Task;

use Robo\Result;
use Symfony\Component\Console\Output\OutputInterface;

class SerializeTask extends BaseTask
{

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
     * @param mixed $value
     */
    public function setValue($value): static
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

    public function setSerializer(?callable $serializer): static
    {
        $this->serializer = $serializer;

        return $this;
    }

    protected ?OutputInterface $writer = null;

    public function getWriter(): ?OutputInterface
    {
        return $this->writer;
    }

    public function setWriter(?OutputInterface $writer): static
    {
        $this->writer = $writer;

        return $this;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options): static
    {
        parent::setOptions($options);

        foreach ($options as $key => $value) {
            switch ($key) {
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
