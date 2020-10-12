<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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
        $modelName = $this->ask('name');
        $tableName = $this->ask('table');
        $namespace = ucfirst($this->option('namespace'));


        $validator = Validator::make([
            'model_name' => $modelName,
            'table_name' => $tableName,
        ], [
            'model_name' => ['required'],
            'table_name' => ['required']
        ]);

        if ($validator->fails()) {
            $this->info('Model are not created.');

            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        $this->makeModel($modelName, $tableName, $namespace);
        $this->makeRepo($modelName, $namespace);
    }

    public function makeModel($modelName, $tableName = null, $namespace = null)
    {
        $model = ucfirst($modelName);
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

        $written = Storage::disk('app')->put('Models/' . $namespace . '/'. $modelName . '.php', $content);

        if ($written) {
            $this->info('Created new model ' . $modelName . '.php in App\Models\\' . $namespace);
        } else {
            $this->info('Something went wrong');
        }
    }

    public function makeRepo($modelName, $namespace)
    {
        $model = ucfirst($modelName);
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

        $written = Storage::disk('app')->put('Repositories/' . $namespace . '/'. $modelName . 'Repo.php', $content);

        if ($written) {
            $this->info('Created new Repo ' . $modelName . 'Repo.php in App\Repositories\\' . $namespace);
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
