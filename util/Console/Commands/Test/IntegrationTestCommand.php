<?php namespace Util\Console\Commands\Test;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class IntegrationTestCommand extends Command {

    protected $name = 'test:integration';
    protected $description = "Run integration test in /lib/honeybase/spec";

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
        // lib/honeybas/spec/server.php にこの関数からアクセスしserve,
        // lib/honeybas/spec/app.php を実行する
        // lib/honeybas/spec/routes.php の /integration を開く
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
