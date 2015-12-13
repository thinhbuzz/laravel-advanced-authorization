<?php


namespace Buzz\Authorization;


use Illuminate\Console\Command;
use Illuminate\Foundation\Composer;

class SeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'authorization:seeder';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish seeder for Authorization package.';

    /**
     * Create a new command instance.
     *
     * @param  \Illuminate\Foundation\Composer $composer
     * @return void
     */
    public function __construct(Composer $composer)
    {
        parent::__construct();

        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $config = $this->laravel->config->get('authorization');
        $this->line('');
        if ($this->confirm('Do you want publish default seeder?', 'yes')) {
            $stub = realpath(__DIR__ . '/../seeder') . DIRECTORY_SEPARATOR . 'AuthorizationSeeder.stub';
            $data = file_get_contents($stub);
            $data = str_replace('%n_role%', $config['model_role'], $data);
            $data = str_replace('%n_permission%', $config['model_permission'], $data);
            $data = str_replace('%role%', basename($config['model_role']), $data);
            $data = str_replace('%permission%', basename($config['model_permission']), $data);
            if (file_exists($seedPath = database_path('seeds')) === false) {
                $this->error(sprintf('Directory %s not exist.', $seedPath));

                return;
            }
            if (file_put_contents($seedPath . DIRECTORY_SEPARATOR . 'AuthorizationSeeder.php', $data)) {
                $this->info('Publish seeder successfully.');
                $this->composer->dumpAutoloads();
            } else {
                $this->error('Some thing went wrong when publish seeder');
            }
        }
    }
}
