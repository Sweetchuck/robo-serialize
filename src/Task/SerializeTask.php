<?php

namespace Sweetchuck\Robo\Serialize\Task;

use PackageVersions\Versions;
use Robo\Result;
use Robo\Task\BaseTask;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class SerializeTask extends BaseTask
{
    /**
     * File handler.
     *
     * @var resource
     */
    protected $destinationResource = null;

    /**
     * @var OutputInterface
     */
    protected $destinationOutput = null;

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
                case 'subject':
                    $this->setSubject($value);
                    break;

                case 'serializer':
                    $this->setSerializer($value);
                    break;

                case 'destination':
                    $this->setDestination($value);
                    break;
            }
        }

        return $this;
    }

    //region Option - subject.
    /**
     * The variable to serialize.
     *
     * @var mixed
     */
    protected $subject = null;

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }
    //endregion

    //region Option - serializer.
    /**
     * @var string
     */
    protected $serializer = 'json';

    public function getSerializer(): string
    {
        return $this->serializer;
    }

    /**
     * @return $this
     */
    public function setSerializer(string $serializer)
    {
        if (!in_array($serializer, ['json', 'yml', 'yaml'])) {
            throw new \InvalidArgumentException();
        }

        $this->serializer = $serializer;

        return $this;
    }
    //endregion

    //region Option - destination.
    /**
     * @var string|resource|OutputInterface
     */
    protected $destination = STDOUT;

    /**
     * @return string|resource|OutputInterface
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param string|resource|OutputInterface $destination
     *
     * @return $this
     */
    public function setDestination($destination)
    {
        if ($destination instanceof OutputInterface
          || (is_resource($destination) && get_resource_type($destination) === 'stream')
          || is_string($destination)
        ) {
            $this->destination = $destination;

            return $this;
        }

        throw new \InvalidArgumentException();
    }
    //endregion

    /**
     * {@inheritdoc}
     */
    public function run(): Result
    {
        $this
            ->initOutput()
            ->release($this->serialize())
            ->closeStreamOutput();

        return new Result($this, 0);
    }

    /**
     * Initialize the destination Output.
     *
     * @return $this
     */
    protected function initOutput()
    {
        $destination = $this->getDestination();
        if ($destination instanceof OutputInterface) {
            $this->destinationOutput = $destination;
        } elseif (is_string($destination)) {
            $fs = new Filesystem();
            $fs->mkdir(dirname($this->destination));

            $this->destinationResource = fopen($destination, 'w');
            $this->destinationOutput = $this->createStreamOutput($this->destinationResource);
        } elseif (is_resource($destination)) {
            $this->destinationOutput = $this->createStreamOutput($destination);
        }

        return $this;
    }

    protected function serialize(): string
    {
        $serialized = '';
        $subject = $this->toFlat($this->getSubject());
        switch ($this->getSerializer()) {
            case 'json':
                $serialized = json_encode($subject, JSON_PRETTY_PRINT) . "\n";
                break;

            case 'yml':
            case 'yaml':
                if (function_exists('yaml_emit')) {
                    $serialized = yaml_emit($subject);
                } else {
                    $yamlVersion = ltrim(Versions::getShortVersion('symfony/yaml'), 'v');
                    if (version_compare($yamlVersion, '3.1.0', '>=')) {
                        $serialized = Yaml::dump($subject, PHP_INT_MAX, 2);
                    } else {
                        $serialized = Yaml::dump($subject, PHP_INT_MAX, 2, true, true);
                    }

                    $serialized = "---\n{$serialized}...\n";
                }
                break;
        }

        return $serialized;
    }

    /**
     * @return $this
     */
    protected function release(string $serialized)
    {
        $this->destinationOutput->write($serialized);

        return $this;
    }

    /**
     * @param resource $resource
     *
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    protected function createStreamOutput($resource): OutputInterface
    {
        return new StreamOutput($resource, OutputInterface::VERBOSITY_NORMAL, false);
    }

    /**
     * Close the destination resource if it was opened here.
     *
     * @return $this
     */
    protected function closeStreamOutput()
    {
        if ($this->destinationResource) {
            fclose($this->destinationResource);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    protected function toFlat($subject)
    {
        if (is_null($subject) || is_scalar($subject)) {
            return $subject;
        }

        if (is_object($subject)) {
            $result = [];
            if ($subject instanceof \JsonSerializable) {
                $result = $subject->jsonSerialize();
            } else {
                foreach (get_object_vars($subject) as $key => $value) {
                    $result[$key] = $this->toFlat($value);
                }
            }

            return $result;
        }

        if (is_array($subject)) {
            $result = [];
            foreach ($subject as $key => $value) {
                $result[$key] = $this->toFlat($value);
            }

            return $result;
        }

        // @todo It can be a "resource" or a "\Closure" as well.
        return null;
    }
}
