<?php

namespace Pradility\Seedera;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Pradility\Seedera\Filesystem\Filesystem;

class SeederGeneratorCommand extends Command
{
    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * SeederGeneratorCommand constructor.
     * @param Filesystem $fileSystem
     */
    public function __construct(Filesystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;

        parent::__construct();
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'build:seeder {table=all : The table name for seeder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build seeder for pre filled table in database.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->confirm('Do you wish to continue?')) {
            // logic for seeder generation
            $this->generateSeeder();
        } else {
            $this->line('Ok no problem.');
        }
    }

    public function generateSeeder()
    {
        // path where seeder will be placed
        $seederPath = $this->getFileGenerationPath();

        if( file_exists($seederPath) ) {
            if( $this->confirm('Seeder already present ! Do you want to replace it?') ) {
                $this->seederProcess($seederPath);
            } else {
                $this->line('Ok no problem.');
            }
        } else {
            $this->seederProcess($seederPath);
        }
    }

    /**
     * Seeder generation process
     * @param  $seederPath
     */
    public function seederProcess($seederPath)
    {        
        $code = $this->getSeederCode();

        // create seeder file
        $this->fileSystem->create($seederPath, $code);

        $this->info("Built Seeder : {$seederPath}");
    }

    /**
     * Get code insider seeder
     */
    public function getSeederCode()
    {
        // path where seeder template is placed
        $templatePath = $this->getTemplatePath();

        // get data required by template
        $templateData = $this->getTemplateData();

        // compiling the template
        return $this->compile($templatePath, $templateData, new Compiler);
    }

    /**
     * The path where the file will be created
     *
     * @return mixed
     */
    protected function getFileGenerationPath()
    {
        $path = base_path('database/seeds');// Project/app/database/seeds

        $className = $this->getClassName();

        return "{$path}/{$className}.php";
    }

    /**
     * Fetch the template data
     *
     * @return array
     */
    protected function getTemplateData()
    {
        $tableName = $this->getTableName();

        $records = DB::table($tableName)->get();

        $columns = DB::select("SHOW FULL COLUMNS FROM $tableName");

        $seedData = "DB::table('$tableName')\n\t\t->insert([\n";
        foreach ($records as $record) {

            $seedData .= "\t\t\t[\n";

            foreach ($columns as $column) {
                $seedData .= "\t\t\t\t'" . $column->Field . "' => '" . $record->{$column->Field} . "',\n";
            }

            $seedData .= "\t\t\t],\n";
        }

        $seedData .= "\n\t\t]);";

        return [
            'CLASS' => $this->getClassName(),
            'CODE'  => $seedData
        ];
    }
    /**
     * Get path to template for generator
     *
     * @return mixed
     */
    protected function getTemplatePath()
    {
        return base_path('vendor/pradility/seedera/src/Templates/seeder.txt');
    }

    /**
     * Get table name passed as arguments
     */
    protected function getTableName()
    {
        return $this->argument('table');
    }

    /**
     * Get table name passed as arguments
     */
    protected function getClassName()
    {
        return studly_case($this->getTableName()) . "TableSeeder";
    }

    /**
     * Compile the file
     *
     * @param $templatePath
     * @param array $data
     * @param Compiler $compiler
     * @return mixed
     */
    public function compile($templatePath, array $data, Compiler $compiler)
    {
        return $compiler->compile($this->fileSystem->get($templatePath), $data);
    }
}