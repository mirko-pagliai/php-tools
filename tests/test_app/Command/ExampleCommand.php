<?php
declare(strict_types=1);

/**
 * This file is part of php-tools.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/php-tools
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExampleCommand extends Command
{
    protected function configure(): void
    {
        $this->addOption('failure', null, InputOption::VALUE_NONE, 'This option causes the command to fail');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('failure')) {
            return Command::FAILURE;
        }

        $output->writeln('hello!');

        return Command::SUCCESS;
    }
}
