<?php

/*
 * This file is part of the ZrInsight package.
 *
 * (c) Lhassan Baazzi <baazzilhassan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZrInsight\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SelfUpdateCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setDescription('Update zrinsight.phar to the latest version.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command replace your zrinsight.phar by the latest
version.

<info>php zrinsight.phar %command.name%</info>

EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $remoteFilename = 'https://raw.githubusercontent.com/jbinfo/zrinsight/master/build/zrinsight.phar';
        $localFilename = $_SERVER['argv'][0];
        $tempFilename = basename($localFilename, '.phar').'-temp.phar';

        try {
            copy($remoteFilename, $tempFilename);

            if (md5_file($localFilename) == md5_file($tempFilename)) {
                $output->writeln('<info>zrinsight is already up to date.</info>');
                unlink($tempFilename);

                return;
            }

            chmod($tempFilename, 0777 & ~umask());

            // test the phar validity
            $phar = new \Phar($tempFilename);
            // free the variable to unlock the file
            unset($phar);
            rename($tempFilename, $localFilename);

            $output->writeln('<info>zrinsight updated.</info>');
        } catch (\Exception $e) {
            if (!$e instanceof \UnexpectedValueException && !$e instanceof \PharException) {
                throw $e;
            }
            unlink($tempFilename);
            $output->writeln('<error>The download is corrupt ('.$e->getMessage().').</error>');
            $output->writeln('<error>Please re-run the self-update command to try again.</error>');
        }
    }
}
