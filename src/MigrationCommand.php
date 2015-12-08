<?php


namespace Buzz\Authorization;


use Illuminate\Console\Command;

class MigrationCommand extends Command
{
    protected $packageTables = ['roles', 'permissons', 'permission_role', 'role_user'];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'authorization:migration';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a migration for package advanced authorization.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->line('');
        $this->info(sprintf('Tables: %s', implode(', ', $this->packageTables)));
        $this->comment('A migration that creates tables will be created in database/migrations directory.');
        $this->line('');
        if ($this->confirm('Proceed with the migration creation?', 'yes')) {
            $this->info('Creating migration...');
            $this->publishMigration();
            $this->line('');
            $this->info('Migration successfully created!');
            $this->line('');
        }
    }

    /*
     * Created in database/migrations directory.
     * @return boolean
     * */
    private function publishMigration()
    {
        $migrationPath = realpath(__DIR__ . sprintf('%s..%smigrations', DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR)) . DIRECTORY_SEPARATOR;
        $outputPath = base_path(sprintf('database%smigrations%s', DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR));
        foreach ($this->packageTables as $table) {
            $tmpPath = $outputPath;
            $tmpPath .= sprintf('%s_create_%s_table.php', date('Y_m_d_His'), $table);
            file_put_contents($tmpPath,
                file_get_contents(sprintf('%s/create_%s_table.php', $migrationPath, $table))
            );
            $this->info(sprintf('Created migration table: %s', $table));
        }
    }
}