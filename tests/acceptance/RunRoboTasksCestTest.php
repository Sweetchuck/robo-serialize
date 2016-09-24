<?php

// @codingStandardsIgnoreStart
class RunRoboTasksCestTest extends \Codeception\Test\Unit
{
    // @codingStandardsIgnoreEnd

    /**
     * @var \AcceptanceTester
     */
    protected $tester;

    public function testSerializeYaml()
    {
        $this
            ->tester
            ->runRoboTask('serialize:yaml')
            ->expectTheExitCodeToBe(0)
            ->seeThisTextInTheStdOutput(implode("\n", [
                '---',
                'a: b',
                '...',
                '',
            ]));
    }

    public function testSerializeJson()
    {
        $this
            ->tester
            ->runRoboTask('serialize:json')
            ->expectTheExitCodeToBe(0)
            ->seeThisTextInTheStdOutput(implode("\n", [
                '{',
                '    "a": "b"',
                '}',
                '',
            ]));
    }
}
