<?php


namespace Buzz\Authorization;


use Illuminate\Console\Command;

class ModelCommand extends Command
{
    protected $packageModels = ['Role', 'Permission'];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'authorization:model';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish models for package.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $config = $this->laravel->config->get('authorization');
        $this->line('');
        if ($this->confirm('Do you want publish default models?', 'yes')) {
            /*publish role model*/
            $this->publishModel($config['model_role'], 'Role.stub');
            /*publish permission model*/
            $this->publishModel($config['model_permission'], 'Permission.stub');
        }
    }

    protected function getModelData($path, $stub)
    {
        $packageModelPath = realpath(__DIR__ . '/../models') . DIRECTORY_SEPARATOR;
        $tmpData = file_get_contents($packageModelPath . $stub);

        return sprintf($tmpData, dirname($path), basename($path));
    }

    protected function getPath($model)
    {
        $pathToPer = base_path(lcfirst($model) . 'php');
        $return ['dirname'] = dirname($pathToPer);
        $return ['realpath'] = realpath($return ['dirname']);

        return $return;
    }

    protected function publishModel($model, $stub)
    {
        $path = $this->getPath($model);
        if ($path['realpath'] === false) {
            $this->error(sprintf('Directory %s not exist', $path['dirname']));
        } else {
            if (file_put_contents(
                $path['realpath'] . DIRECTORY_SEPARATOR . basename($model) . '.php',
                $this->getModelData($model, $stub))
            ) {
                $this->info(sprintf('Published model: %s', $model));
            } else {
                $this->error(sprintf('Something went wrong when publish model %s', $model));
            }
        }
    }
}