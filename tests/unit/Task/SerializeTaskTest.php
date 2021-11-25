<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Serialize\Tests\Unit\Task;

use Codeception\Test\Unit;
use League\Container\Container as LeagueContainer;
use Robo\Collection\CollectionBuilder;
use Robo\Config\Config as RoboConfig;
use Robo\Robo;
use Psr\Container\ContainerInterface;
use Sweetchuck\Codeception\Module\RoboTaskRunner\DummyOutput;
use Sweetchuck\Robo\Serialize\Test\Helper\Dummy\DummyTaskBuilder;
use Sweetchuck\Robo\Serialize\Test\UnitTester;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\ErrorHandler\BufferingLogger;

/**
 * @covers \Sweetchuck\Robo\Serialize\Task\SerializeTask
 * @covers \Sweetchuck\Robo\Serialize\SerializeTaskLoader
 */
class SerializeTaskTest extends Unit
{
    protected UnitTester $tester;

    protected ContainerInterface $container;

    protected RoboConfig $config;

    protected CollectionBuilder $builder;

    protected DummyTaskBuilder $taskBuilder;

    protected function _before()
    {
        parent::_before();

        Robo::unsetContainer();

        $this->container = new LeagueContainer();
        $application = new SymfonyApplication('Sweetchuck - Robo Git', '1.0.0');
        $this->config = new RoboConfig();
        $input = null;
        $output = new DummyOutput([
            'verbosity' => DummyOutput::VERBOSITY_DEBUG,
        ]);

        $this->container->add('container', $this->container);

        Robo::configureContainer($this->container, $application, $this->config, $input, $output);
        $this->container->share('logger', BufferingLogger::class);

        $this->builder = CollectionBuilder::create($this->container, null);
        $this->taskBuilder = new DummyTaskBuilder();
        $this->taskBuilder->setContainer($this->container);
        $this->taskBuilder->setBuilder($this->builder);
    }

    public function casesRun(): array
    {
        return [
            'basic pecl_yaml' => [
                implode("\n", [
                    '---',
                    'foo: bar',
                    '...',
                    '',
                ]),
                'pecl_yaml',
                [
                    'foo' => 'bar',
                ],
            ],
            'basic symfony_yaml' => [
                implode("\n", [
                    'foo: bar',
                    '',
                ]),
                'symfony_yaml',
                [
                    'foo' => 'bar',
                ],
            ],
            'basic json' => [
                implode("\n", [
                    '{',
                    '    "foo": "bar"',
                    '}',
                    '',
                ]),
                'json',
                [
                    'foo' => 'bar',
                ],
            ],
        ];
    }

    /**
     * @dataProvider casesRun
     */
    public function testRun(string $expected, string $serializerName, $value): void
    {
        $writer = new BufferedOutput();

        $task = $this->taskBuilder->taskSerialize([
            'assetNamePrefix' => 'my_config.',
            'value' => $value,
            'serializer' => $this->taskBuilder->getSerializer($serializerName),
            'writer' => $writer,
        ]);

        $result = $task->run();
        $this->tester->assertEquals($expected, $writer->fetch(), 'writer usage');
        $this->tester->assertEquals($expected, $result['my_config.serialized'], 'provided asset: serialized');
    }
}
