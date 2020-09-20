<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Input\InputArgument;

class MakeRepository extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repo {name} {--namespace=} {--table=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make new repository by models';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $model = ucfirst($this->argument('name'));
        $namespace = ucfirst($this->option('namespace'));
        $table = $this->option('table');
        $this->makeModel($model, $table, $namespace);
        $this->makeRepo($model, $namespace);
    }

    public function makeModel($model, $tableName = null, $namespace = null)
    {
        $mainSpace = $namespace ? "namespace App\Models\\$namespace;" : "namespace App\Models;";
        $tableName = $tableName ? 'protected $table = ' . "'$tableName';" : '';
        $content = "<?php

$mainSpace

use App\Models\Base;
use Illuminate\Database\Eloquent\Model;

class {$model} extends Model
{
    use Base;
    $tableName
}
";
        $folder = "App/Models/$namespace/";

        if (!is_dir($folder)) {
            File::makeDirectory($folder, $mode = 0777, true, true);
        }

        $written = Storage::disk('app')->put('Models/' . $namespace . '/'. $this->argument('name') . '.php', $content);

        if ($written) {
            $this->info('Created new model ' . $this->argument('name') . '.php in App\Models\\' . $namespace);
        } else {
            $this->info('Something went wrong');
        }
    }

    public function makeRepo($model, $namespace)
    {
        $mainSpace = $namespace ? "namespace App\Repositories\\$namespace;" : "namespace App\Repositories;";
        $modelSpace = $namespace ? "use App\Models\\$namespace\\$model;" : "use App\Models\\$model;";
        $content = "<?php

$mainSpace
use App\Repositories\BaseRepo;
$modelSpace

class {$model}Repo extends BaseRepo
{
    public function getModel()
    {
        return $model::class;
    }

}
";
        $folder = "App/Repositories/$namespace/";

        if (!is_dir($folder)) {
            File::makeDirectory($folder, $mode = 0777, true, true);
        }

        $written = Storage::disk('app')->put('Repositories/' . $namespace . '/'. $this->argument('name') . 'Repo.php', $content);

        if ($written) {
            $this->info('Created new Repo ' . $this->argument('name') . 'Repo.php in App\Repositories\\' . $namespace);
        } else {
            $this->info('Something went wrong');
        }
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return app_path() . '/Console/Commands/Stubs/make-repo.stub';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the repository.'],
        ];
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Models';
    }
}
