<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Serialize\Tests\Acceptance\Task;

use Sweetchuck\Robo\Serialize\Tests\AcceptanceTester;
use Sweetchuck\Robo\Serialize\Tests\Helper\RoboFiles\RoboFileSerialize;

class SerializeTaskCest
{
    public function testSerializeJson(AcceptanceTester $I): void
    {
        $id = 'serialize:json';
        $expectedStdOutput = implode("\n", [
            '{',
            '    "a": "b"',
            '}',
            '',
        ]);
        $I->runRoboTask($id, RoboFileSerialize::class, 'serialize', 'json');
        $I->assertSame(0, $I->getRoboTaskExitCode($id));
        $I->assertSame($expectedStdOutput, $I->getRoboTaskStdOutput($id));
    }
}
