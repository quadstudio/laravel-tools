<?php

namespace QuadStudio\Tools\Console;

use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Support\Composer;

abstract class ToolsResourceMakeCommand extends Command
{

    use DetectsApplicationNamespace;

    /**
     * The views that need to be exported.
     *
     * @var array
     */
    protected $views = [

    ];

    /**
     * @var array
     */
    protected $controllers = [

    ];

    /**
     * @var array
     */
    protected $directories = [

    ];

    /**
     * @var array
     */
    protected $seeds = [

    ];

    /**
     * @var array
     */
    protected $assets = [

    ];
    protected $routes = [

    ];

    /**
     * The Composer instance.
     *
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

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
    public function handle()
    {
        $this->createDirectories();

        $this->exportAssets();

        $this->exportViews();

        $this->exportControllers();

        $this->exportSeeds();

        $this->exportRoutes();

    }

    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function createDirectories()
    {
        foreach ($this->directories as $directory) {
            if (!is_dir($directory = base_path($directory))) {
                mkdir($directory, 0755, true);
            }
        }

    }

    /**
     * Export the authentication views.
     *
     * @return void
     */
    protected function exportAssets()
    {
        foreach ($this->assets as $value) {

            if (file_exists($asset = resource_path('assets/' . $value)) && !$this->option('force')) {
                if (!$this->confirm(trans('tools::commands.asset.exists', ['asset' => $value]))) {
                    continue;
                }
            }

            copy(
                $this->getAsset() . $value,
                $asset
            );
        }
    }

    abstract function getAsset();

    abstract function getStub();

    /**
     * Export the authentication views.
     *
     * @return void
     */
    protected function exportViews()
    {
        foreach ($this->views as $key => $value) {

            if (file_exists($view = resource_path('views/' . $value)) && !$this->option('force')) {
                if (!$this->confirm(trans('tools::commands.views.exists', ['view' => $value]))) {
                    continue;
                }
            }

            copy(
                $this->getStub() . 'views/' . $key,
                $view
            );
        }
    }

    public function exportControllers()
    {
        foreach ($this->controllers as $key => $value) {

            if (file_exists($controller = app_path("Http/Controllers/{$value}")) && !$this->option('force')) {
                if (!$this->confirm(trans('tools::commands.controller.exists', ['controller' => $value]))) {
                    continue;
                }
            }

            file_put_contents(
                app_path("Http/Controllers/{$value}.php"),
                $this->compileControllerStub($key)
            );
        }

    }

    /**
     * Compiles the Controller stub.
     *
     * @return string
     */
    protected function compileControllerStub($name)
    {
        return str_replace(
            '{{namespace}}',
            $this->getAppNamespace(),
            file_get_contents($this->getStub() . 'controllers/' . $name)
        );
    }

    public function exportSeeds()
    {

        foreach ($this->seeds as $key => $value) {

            if (file_exists($seed = database_path('seeds/' . $value . '.php')) && !$this->option('force')) {
                if (!$this->confirm(trans('tools::commands.seeder.exists', ['seed' => $value]))) {
                    continue;
                }
            }

            copy(
                $this->getStub() . 'seeds/' . $key,
                $seed
            );
        }

        $this->composer->dumpAutoloads();


    }

    private function exportRoutes()
    {
        foreach ($this->routes as $route => $mode) {
            file_put_contents(
                base_path('routes/web.php'),
                //file_get_contents(__DIR__ . '/stubs/routes/web.stub'),
                file_get_contents($this->getStub() . 'routes/' . $route),
                $mode
            );
        }

    }

}