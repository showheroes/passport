<?php

namespace ShowHeroes\Passport\Console\Commands\Test;

use Illuminate\Console\Command;


/**
 * Class TestingDbSnaphotUpdate
 * @package ShowHeroes\IFactory\Console\Tests
 *
 * Console Artisan command for the updating testing database snapshot
 * from the original.
 */
class TestingDbSnaphotUpdate extends Command
{
    /** @var string */
    protected $signature = 'tests:db-snapshot';

    /** @var string */
    protected $description = 'Updates snapshot of testing database';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connections = config('database.connections');
        foreach ($connections as $config) {
            if (isset($config['testing_database'])) {
                /*
                 * Shell command:
                 * - get source database dump with DROP TABLES
                 * - cut of from this dump AUTO_INCREMENT directives
                 * - load back this dump to testing database.
                 */
                exec("mysqldump -u ".$config['username']." -p".$config['password']." --add-drop-table -d ".$config['database']." | sed 's/ AUTO_INCREMENT=[0-9]*//g' | mysql -u ".$config['username']." -p".$config['password']." -D".$config['testing_database']);
            }
        }
        $this->info('Snapshots updated.');
    }
}
