<?php

use \PHPUnit_Framework_Assert as Assert;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    /**
     * @param string $taskName
     *
     * @return $this
     */
    public function runRoboTask($taskName, array $args = [], array $options = [])
    {
        $cmdPattern = 'cd tests/_data && ../../bin/robo %s';
        $cmdArgs = [
            escapeshellarg($taskName)
        ];

        foreach ($options as $option => $value) {
            $cmdPattern .= " --$option";
            if ($value !== null) {
                $cmdPattern .= '=%s';
                $cmdArgs[] = escapeshellarg($value);
            }
        }

        $cmdPattern .= str_repeat(' %s', count($args));
        foreach ($args as $arg) {
            $cmdArgs[] = escapeshellarg($arg);
        }

        $this->runShellCommand(vsprintf($cmdPattern, $cmdArgs));

        return $this;
    }

    /**
     * @param string $expected
     *
     * @return $this
     */
    public function seeThisTextInTheStdOutput($expected)
    {
        Assert::assertContains($expected, $this->getStdOutput());

        return $this;
    }

    /**
     * @param string $expected
     *
     * @return $this
     */
    public function seeThisTextInTheStdError($expected)
    {
        Assert::assertContains($expected, $this->getStdError());

        return $this;
    }

    /**
     * @param int $expected
     *
     * @return $this
     */
    public function expectTheExitCodeToBe($expected)
    {
        Assert::assertEquals($expected, $this->getExitCode());

        return $this;
    }
}
