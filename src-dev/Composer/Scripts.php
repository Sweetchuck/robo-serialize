<?php

namespace Sweetchuck\Robo\Serialize\Composer;

use Composer\Script\Event;
use Sweetchuck\GitHooks\Composer\Scripts as GitHooks;

class Scripts
{
    /**
     * @var \Composer\Script\Event
     */
    protected static $event;

    /**
     * @var string
     */
    protected static $bundlerVersion = '1.13.6';

    public static function postInstallCmd(Event $event): bool
    {
        $return = [];

        if ($event->isDevMode()) {
            $return[] = GitHooks::deploy($event);
        }

        return count(array_keys($return, false, true)) === 0;
    }

    public static function postUpdateCmd(Event $event): bool
    {
        $return = [];

        if ($event->isDevMode()) {
            $return[] = GitHooks::deploy($event);
        }

        return count(array_keys($return, false, true)) === 0;
    }
}
