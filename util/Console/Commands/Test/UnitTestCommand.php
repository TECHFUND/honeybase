<?php namespace Util\Console\Commands\Test;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class UnitTestCommand extends Command {

    protected $name = 'test:unit';
    protected $description = "Run unit test in /tests";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->info("Unit test executing in /tests by vendor/bin/phpunit command");
        chdir(base_path());
        passthru("vendor/bin/phpunit");
    }

}
