<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Serialize\Tests\Unit\Task;

use Codeception\Attribute\DataProvider;
use Sweetchuck\Robo\Serialize\Task\SerializeTask;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @covers \Sweetchuck\Robo\Serialize\Task\SerializeTask
 * @covers \Sweetchuck\Robo\Serialize\Task\BaseTask
 * @covers \Sweetchuck\Robo\Serialize\SerializeTaskLoader
 *
 * @method SerializeTask createTask()
 */
class SerializeTaskTest extends TaskTestBase
{
    protected function createTaskInstance(): SerializeTask
    {
        return new SerializeTask();
    }

    /**
     * @return array<string, mixed>
     */
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
     * @param mixed $value
     */
    #[DataProvider('casesRun')]
    public function testRun(string $expected, string $serializerName, $value): void
    {
        $writer = new BufferedOutput();

        $task = $this->createTask();
        $task->setOptions([
            'assetNamePrefix' => 'my_config.',
            'value' => $value,
            'serializer' => $this->taskBuilder->getSerializer($serializerName),
            'writer' => $writer,
        ]);

        $result = $task->run();
        $this->tester->assertSame($expected, $writer->fetch(), 'writer usage');
        $this->tester->assertSame($expected, $result['my_config.serialized'], 'provided asset: serialized');
    }
}
