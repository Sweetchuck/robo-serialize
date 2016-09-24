<?php

use Cheppers\Robo\Serialize\Task\SerializeTask;
use Codeception\Util\Stub;
use Symfony\Component\Console\Output\BufferedOutput;

// @codingStandardsIgnoreStart
/**
 * @covers \Cheppers\Robo\Serialize\Task\SerializeTask
 */
class SerializeTaskTest extends \Codeception\Test\Unit
{
    // @codingStandardsIgnoreEnd

    /**
     * @var \UnitTester
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

        return parent::setUp();
    }

    // @codingStandardsIgnoreStart
    public function _after()
    {
        // @codingStandardsIgnoreEnd
        parent::_after();

        $tmpDir = codecept_output_dir('tmp');
        if (is_dir($tmpDir)) {
            $fs = new \Symfony\Component\Filesystem\Filesystem();
            $fs->remove($tmpDir);
        }
    }

    public function testGetSetSerializer()
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

    public function testGetSetDestination()
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

    public function testOptions()
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

        return [
            'json; simple;' => [
                implode("\n", [
                    '{',
                    '    "foo": "bar"',
                    '}',
                    '',
                ]),
                'json',
                $subjectSimple,
            ],
            'json; deep;' => [
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
            'yaml; simple' => [
                implode("\n", [
                    '---',
                    'foo: bar',
                    '...',
                    '',
                ]),
                'yaml',
                $subjectSimple,
            ],
            'yaml; deep' => [
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
        ];
    }

    /**
     * @dataProvider casesRun
     */
    public function testRunOutput(string $expected, string $serializer, $subject)
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
    public function testRunString(string $expected, string $serializer, $subject)
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
    public function testRunResource(string $expected, string $serializer, $subject)
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
        $container = \Robo\Robo::createDefaultContainer();
        \Robo\Robo::setContainer($container);

        /** @var \Cheppers\Robo\Serialize\Task\SerializeTask $task */
        $task = Stub::construct(SerializeTask::class, [[], []]);

        $task->setLogger($container->get('logger'));

        return $task;
    }
}
