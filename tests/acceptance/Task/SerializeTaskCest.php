<?php

namespace Cheppers\Robo\Serialize\Tests\Acceptance;

use \AcceptanceTester;

class SerializeTaskCest
{
    public function testSerializeYaml(AcceptanceTester $I): void
    {
        $I->runRoboTask('serialize:yaml');
        $I->expectTheExitCodeToBe(0);
        $I->seeThisTextInTheStdOutput(implode("\n", [
            '---',
            'a: b',
            '...',
            '',
        ]));
    }

    public function testSerializeJson(AcceptanceTester $I): void
    {
        $I->runRoboTask('serialize:json');
        $I->expectTheExitCodeToBe(0);
        $I->seeThisTextInTheStdOutput(implode("\n", [
            '{',
            '    "a": "b"',
            '}',
            '',
        ]));
    }
}
