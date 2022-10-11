<?php

namespace App\Command;

use App\Classes\OrderCleanup;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class OrderCleanupCommand extends Command{


    /**
     * @var OrderCleanup
     */
    private $orderCleanup;

    protected static $defaultName = 'app:order:cleanup';

    public function __construct(OrderCleanup $orderCleanup)
    {
        $this->orderCleanup = $orderCleanup;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('cancel the waiting orders from the database')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        if ($input->getOption('dry-run')) {
            $io->note('Dry mode enabled');

            $count = $this->orderCleanup->clean();
        } else {
            $count = $this->orderCleanup->clean();
        }

        $io->success(sprintf('cleaned "%d"  orders.', $count));

        return 0;
    }
}