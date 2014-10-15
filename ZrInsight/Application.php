<?php

/*
 * This file is part of the ZrInsight package.
 *
 * (c) Lhassan Baazzi <baazzilhassan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZrInsight;

use SensioLabs\Insight\Cli\Application as BaseApplication;

class Application extends BaseApplication
{

    const APPLICATION_NAME    = 'ZrInsight CLI';
    const APPLICATION_VERSION = '1.0';

    protected function getDefaultCommands()
    {
        $defaultCommands   = parent::getDefaultCommands();

        if (in_array($selfUpdateCommand = new \SensioLabs\Insight\Cli\Command\SelfUpdateCommand(), $defaultCommands)) {
            unset($defaultCommands[array_search($selfUpdateCommand, $defaultCommands)]);
        }

        $defaultCommands[] = new Command\CheckViolationsCommand();
        $defaultCommands[] = new Command\SelfUpdateCommand();

        return $defaultCommands;
    }
}
