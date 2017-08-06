<?php

namespace Sweetchuck\Robo\Serialize\Tests\Unit;

use Sweetchuck\Robo\Serialize\Task\SerializeTask;
use Sweetchuck\Robo\Serialize\Test\Helper\Dummy\Subject01;
use Sweetchuck\Robo\Serialize\Test\Helper\Dummy\Subject02;
use Codeception\Test\Unit;
use Codeception\Util\Stub;
use Robo\Robo;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @covers \Sweetchuck\Robo\Serialize\Task\SerializeTask
 */
class SerializeTaskTest extends Unit
{
    /**
     * @var \Sweetchuck\Robo\Serialize\Test\UnitTester
     */
    protected $tester;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $tmpDir = codecept_output_dir('tmp');
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0777 - umask(), true);
        }

        parent::setUp();
    }

    // @codingStandardsIgnoreStart
    /**
     * {@inheritdoc}
     */
    public function _after()
    {
        // @codingStandardsIgnoreEnd
        parent::_after();

        $tmpDir = codecept_output_dir('tmp');
        if (is_dir($tmpDir)) {
            (new Filesystem())->remove($tmpDir);
        }
    }

    public function testGetSetSerializer(): void
    {
        $task = new SerializeTask();

        $this->tester->assertEquals('json', $task->getSerializer(), 'Default value: json');

        $task->setSerializer('yaml');
        $this->tester->assertEquals('yaml', $task->getSerializer(), 'New value: yaml');

        $task->setSerializer('yml');
        $this->tester->assertEquals('yml', $task->getSerializer(), 'New value: yml');

        try {
            $task->setSerializer('none');
            $this->tester->fail("Invalid serializer name wasn't detected");
        } catch (\InvalidArgumentException $e) {
            $this->tester->assertTrue(true, 'Invalid serializer name was detected');
        }
    }

    public function testGetSetDestination(): void
    {
        $task = new SerializeTask();

        $this->tester->assertEquals(STDOUT, $task->getDestination(), 'Default value: STDOUT');

        $task->setDestination('foo.yml');
        $this->tester->assertEquals('foo.yml', $task->getDestination(), 'New value: foo.yml');

        $task->setDestination(STDERR);
        $this->tester->assertEquals(STDERR, $task->getDestination(), 'New value: STDERR');

        $bufferedOutput = new BufferedOutput();
        $task->setDestination($bufferedOutput);
        $this->tester->assertEquals($bufferedOutput, $task->getDestination(), 'New value: BufferedOutput');

        try {
            $task->setDestination([]);
            $this->tester->fail("Invalid destination wasn't detected");
        } catch (\InvalidArgumentException $e) {
            $this->tester->assertTrue(true, 'Invalid destination was detected');
        }
    }

    public function testOptions(): void
    {
        $options = [
            'subject' => ['a' => 'b'],
            'serializer' => 'yaml',
            'destination' => 'foo.yml',
        ];

        $task = new SerializeTask($options);
        $this->tester->assertEquals($options['subject'], $task->getSubject());
        $this->tester->assertEquals($options['serializer'], $task->getSerializer());
        $this->tester->assertEquals($options['destination'], $task->getDestination());
    }

    public function casesRun(): array
    {
        $subjectSimple = ['foo' => 'bar'];
        $subjectDeep = new \stdClass();
        $subjectDeep->foo = new \stdClass();
        $subjectDeep->foo->bar = new \stdClass();
        $subjectDeep->foo->bar->baz = new \stdClass();
        $subjectDeep->foo->bar->baz->a = 'b';
        $subjectObject = [
            'normal' => new Subject01(),
            'serializable' => new Subject02(['d' => 'e']),
            'r1' => STDERR,
        ];

        $null = function_exists('yaml_emit') ? '~' : 'null';

        return [
            'simple - json;' => [
                implode("\n", [
                    '{',
                    '    "foo": "bar"',
                    '}',
                    '',
                ]),
                'json',
                $subjectSimple,
            ],
            'simple - yaml' => [
                implode("\n", [
                    '---',
                    'foo: bar',
                    '...',
                    '',
                ]),
                'yaml',
                $subjectSimple,
            ],
            'deep - json;' => [
                implode("\n", [
                    '{',
                    '    "foo": {',
                    '        "bar": {',
                    '            "baz": {',
                    '                "a": "b"',
                    '            }',
                    '        }',
                    '    }',
                    '}',
                    '',
                ]),
                'json',
                $subjectDeep,
            ],
            'deep - yaml' => [
                implode("\n", [
                    '---',
                    'foo:',
                    '  bar:',
                    '    baz:',
                    '      a: b',
                    '...',
                    '',
                ]),
                'yaml',
                $subjectDeep,
            ],
            'object - json' => [
                implode("\n", [
                    '{',
                    '    "normal": {',
                    '        "myPublic": "a"',
                    '    },',
                    '    "serializable": {',
                    '        "d": "e"',
                    '    },',
                    '    "r1": null',
                    '}',
                    '',
                ]),
                'json',
                $subjectObject,
            ],
            'object - yaml' => [
                implode("\n", [
                    '---',
                    'normal:',
                    '  myPublic: a',
                    'serializable:',
                    '  d: e',
                    "r1: $null",
                    '...',
                    '',
                ]),
                'yaml',
                $subjectObject,
            ],
        ];
    }

    /**
     * @dataProvider casesRun
     */
    public function testRunOutput(string $expected, string $serializer, $subject): void
    {
        $destination = new BufferedOutput();

        $task = $this->getTask();
        $result = $task
            ->setSubject($subject)
            ->setSerializer($serializer)
            ->setDestination($destination)
            ->run();

        $this->tester->assertEquals(0, $result->getExitCode());
        $this->tester->assertEquals($expected, $destination->fetch());
    }

    /**
     * @dataProvider casesRun
     */
    public function testRunString(string $expected, string $serializer, $subject): void
    {
        $destination = codecept_output_dir('tmp/destination.txt');
        $this->tester->assertFileNotExists($destination);

        $task = $this->getTask();
        $result = $task
            ->setSubject($subject)
            ->setSerializer($serializer)
            ->setDestination($destination)
            ->run();

        $this->tester->assertEquals(0, $result->getExitCode());
        $this->tester->assertFileExists($destination);
        $this->tester->assertEquals($expected, file_get_contents($destination));
    }

    /**
     * @dataProvider casesRun
     */
    public function testRunResource(string $expected, string $serializer, $subject): void
    {
        $fileName = codecept_output_dir('tmp/destination.txt');
        $this->tester->assertFileNotExists($fileName);
        $destination = fopen($fileName, 'w');

        $task = $this->getTask();
        $result = $task
            ->setSubject($subject)
            ->setSerializer($serializer)
            ->setDestination($destination)
            ->run();

        $this->tester->assertEquals(0, $result->getExitCode());
        $this->tester->assertFileExists($fileName);
        $this->tester->assertEquals($expected, file_get_contents($fileName));
    }

    protected function getTask(): SerializeTask
    {
        $container = Robo::createDefaultContainer();
        Robo::setContainer($container);

        /** @var \Sweetchuck\Robo\Serialize\Task\SerializeTask $task */
        $task = Stub::construct(SerializeTask::class, [[], []]);

        $task->setLogger($container->get('logger'));

        return $task;
    }
}
