<?php

namespace Sweetchuck\Robo\Serialize\Tests\Acceptance;

use Sweetchuck\Robo\Serialize\Test\AcceptanceTester;
use Sweetchuck\Robo\Serialize\Test\Helper\RoboFiles\SerializeTaskRoboFile;

class SerializeTaskCest
{
    public function testSerializeYaml(AcceptanceTester $I): void
    {
        $id = 'serialize:yaml';
        $expectedStdOutput = implode("\n", [
            '---',
            'a: b',
            '...',
            '',
        ]);
        $I->runRoboTask($id, SerializeTaskRoboFile::class, 'serialize:yaml');
        $I->assertEquals(0, $I->getRoboTaskExitCode($id));
        $I->assertEquals($expectedStdOutput, $I->getRoboTaskStdOutput($id));
    }

    public function testSerializeJson(AcceptanceTester $I): void
    {
        $id = 'serialize:json';
        $expectedStdOutput = implode("\n", [
            '{',
            '    "a": "b"',
            '}',
            '',
        ]);
        $I->runRoboTask($id, SerializeTaskRoboFile::class, 'serialize:json');
        $I->assertEquals(0, $I->getRoboTaskExitCode($id));
        $I->assertEquals($expectedStdOutput, $I->getRoboTaskStdOutput($id));
    }
}
