<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Serialize;

use Sweetchuck\Robo\Serialize\Task\SerializeTask;
use Robo\Collection\CollectionBuilder;
use Symfony\Component\Yaml\Yaml;

trait SerializeTaskLoader
{
    /**
     * @return \Sweetchuck\Robo\Serialize\Task\SerializeTask|\Robo\Collection\CollectionBuilder
     */
    protected function taskSerialize(array $options = []): CollectionBuilder
    {
        return $this->task(SerializeTask::class, $options);
    }

    /**
     * Returns a serializer callable with the default configuration.
     */
    protected function getSerializer(string $name): callable
    {
        assert(in_array($name, ['pecl_yaml', 'symfony_yaml', 'json']));

        switch ($name) {
            case 'pecl_yaml':
                return $this->getPeclYamlSerializer();

            case 'symfony_yaml':
                return $this->getSymfonyYamlSerializer();
        }

        return $this->getJsonSerializer();
    }

    /**
     * @see \yaml_emit()
     */
    protected function getPeclYamlSerializer(int $encoding = 0, int $linebreak = 0, array $callbacks = []): callable
    {
        return function ($data) use ($encoding, $linebreak, $callbacks) {
            // @todo Check extension/function exists.
            /** @noinspection PhpComposerExtensionStubsInspection */
            return yaml_emit($data, $encoding, $linebreak, $callbacks);
        };
    }

    /**
     * @see \Symfony\Component\Yaml\Yaml::dump()
     */
    protected function getSymfonyYamlSerializer(int $inline = 2, int $indent = 4, int $flags = 0): callable
    {
        return function ($data) use ($inline, $indent, $flags) {
            // @todo Check extension/function exists.
            return Yaml::dump($data, $inline, $indent, $flags);
        };
    }

    /**
     * @param int $flags
     *   Note that the $flags are different than the original one.
     *   \JSON_PRETTY_PRINT(128) + \JSON_UNESCAPED_UNICODE(256) = 384.
     *
     * @see \json_encode()
     * @see \JSON_PRETTY_PRINT
     * @see \JSON_UNESCAPED_UNICODE
     */
    protected function getJsonSerializer(int $flags = 384, int $depth = 512): callable
    {
        return function ($data) use ($flags, $depth) {
            // @todo Check extension/function exists.
            /** @noinspection PhpComposerExtensionStubsInspection */
            return json_encode($data, $flags, $depth) . "\n";
        };
    }
}
