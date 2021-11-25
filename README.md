# Robo task to serialize data structures

[![CircleCI](https://circleci.com/gh/Sweetchuck/robo-serialize/tree/2.x.svg?style=svg)](https://circleci.com/gh/Sweetchuck/robo-serialize/?branch=2.x)
[![codecov](https://codecov.io/gh/Sweetchuck/robo-serialize/branch/2.x/graph/badge.svg?token=M7avP9BiV1)](https://app.codecov.io/gh/Sweetchuck/robo-serialize/branch/2.x)


## Install

Run `composer require sweetchuck/robo-serialize`


## Usage example

```php
<?php

declare(strict_types = 1);

use Robo\Contract\TaskInterface;
use Robo\State\Data as RoboStateData;
use Robo\Tasks;
use Sweetchuck\Robo\Serialize\SerializeTaskLoader;
use Symfony\Component\Console\Output\StreamOutput;

class RoboFileExample extends Tasks
{
    use SerializeTaskLoader;

    /**
     * @command serialize:example:1
     */
    public function serializeExample1(): TaskInterface
    {
        // The file name can be my_config.json as well.
        $dstFileName = 'php://stdout';
        $serializer = $this->getSerializer('json');
        $writer = new StreamOutput(fopen($dstFileName, 'w+'));

        return $this
            ->collectionBuilder()
            ->addCode(function (RoboStateData $data): int {
                $data['my_config.json'] = [
                    'description' => 'this is the initial value of the my_config.json',
                ];

                return 0;
            })
            ->addCode($this->getTaskIndependentConfigManipulator1('my_config.json'))
            ->addCode($this->getTaskIndependentConfigManipulator2('my_config.json'))
            ->addTask($this
                ->taskSerialize()
                ->setSerializer($serializer)
                ->setWriter($writer)
                ->deferTaskConfiguration('setValue', 'my_config.json')
            );
    }

    protected function getTaskIndependentConfigManipulator1(string $stateKey): \Closure
    {
        return function (RoboStateData $data) use ($stateKey): int {
            $data[$stateKey]['manipulator_1'] = 'foo';

            return 0;
        };
    }

    protected function getTaskIndependentConfigManipulator2(string $stateKey): \Closure
    {
        return function (RoboStateData $data) use ($stateKey): int {
            $data[$stateKey]['manipulator_2'] = 'bar';

            return 0;
        };
    }
}
```
