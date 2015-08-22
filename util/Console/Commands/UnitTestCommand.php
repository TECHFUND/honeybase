<?php namespace Util\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class UnitTestCommand extends Command {

    protected $name = 'unit';
    protected $description = "Run unit test in /tests";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        chdir(base_path('public'));
        $host = $this->input->getOption('host');
        $port = $this->input->getOption('port');
        $base = $this->laravel->basePath();
        $this->info("Lumen development server started on http://{$host}:{$port}/");
        passthru('"'.PHP_BINARY.'"'." -S {$host}:{$port} \"{$base}\"/server.php"); //このファイルがindex.phpおよびapp.phpを読んでいる
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('host', null, InputOption::VALUE_OPTIONAL, 'The host address to serve the application on.', 'localhost'),

            array('port', null, InputOption::VALUE_OPTIONAL, 'The port to serve the application on.', 8000),
        );
    }

}
