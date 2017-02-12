<?php

namespace Cheppers\Robo\Serialize\Tests\Acceptance;

use \AcceptanceTester;

class SerializeTaskCest
{
    public function testSerializeYaml(AcceptanceTester $I): void
    {
        $expectedStdOutput = implode("\n", [
            '---',
            'a: b',
            '...',
            '',
        ]);
        $I->runRoboTask(\SerializeTaskRoboFile::class, 'serialize:yaml');
        $I->assertEquals(0, $I->getRoboTaskExitCode());
        $I->assertEquals($expectedStdOutput, $I->getRoboTaskStdOutput());
    }

    public function testSerializeJson(AcceptanceTester $I): void
    {
        $expectedStdOutput = implode("\n", [
            '{',
            '    "a": "b"',
            '}',
            '',
        ]);
        $I->runRoboTask(\SerializeTaskRoboFile::class, 'serialize:json');
        $I->assertEquals(0, $I->getRoboTaskExitCode());
        $I->assertEquals($expectedStdOutput, $I->getRoboTaskStdOutput());
    }
}
