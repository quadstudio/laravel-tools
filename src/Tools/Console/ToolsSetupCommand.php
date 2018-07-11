<?php

namespace QuadStudio\Tools\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Symfony\Component\Console\Input\InputOption;

abstract class ToolsSetupCommand extends Command
{
    /**
     * @var string
     */
    protected $alias = '';
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The Composer instance.
     *
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    /**
     * @var bool
     */
    protected $publishTranslations = true;

    /**
     * @var bool
     */
    protected $publishConfig = true;

    /**
     * @var bool
     */
    protected $publishAssets = true;

    /**
     * @var bool
     */
    protected $publishResources = true;

    /**
     * SetupCommand constructor.
     * @param Filesystem $files
     * @param Composer $composer
     */
    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();
        $this->composer = $composer;
        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->handle();
    }

    public function handle()
    {
        $this->publishTranslations();
        $this->publishConfig();
        $this->publishAssets();
        $this->publishResources();

    }

    public function publishTranslations()
    {
        if ($this->publishTranslations === true) {
            if ($this->option('force') || $this->confirm(trans('tools::commands.translation.proceed'), 'yes')) {
                if ($this->files->copyDirectory($this->packagePath('resources/lang'), resource_path('lang/vendor/' . $this->alias))) {
                    $this->info(trans('tools::commands.translation.success', ['directory' => 'resources/lang/vendor/' . $this->alias]));
                } else {
                    $this->error(trans('tools::commands.translation.error'));
                }
            }
            $this->line('');
        }

    }

    /**
     * @param $path
     * @return string
     */
    abstract function packagePath($path): string;

    public function publishConfig()
    {
        if ($this->publishConfig === true) {
            if ($this->option('force') || $this->confirm(trans('tools::commands.config.proceed'), 'yes')) {
                if ($this->files->copy($this->packagePath('config/' . $this->alias . '.php'), config_path($this->alias . '.php'))) {
                    $this->info(trans('tools::commands.config.success', ['directory' => 'config', 'file' => $this->alias . '.php']));
                } else {
                    $this->error(trans('tools::commands.config.error'));
                }
            }
            $this->line('');
        }
    }

    public function publishAssets()
    {
        if ($this->publishAssets === true) {
            if ($this->option('force') || $this->confirm(trans('tools::commands.assets.proceed'), 'yes')) {
                if ($this->files->copyDirectory($this->packagePath('resources/assets'), resource_path('assets'))) {
                    $this->info(trans('tools::commands.assets.success', ['directory' => 'resources/assets']));
                } else {
                    $this->error(trans('tools::commands.assets.error'));
                }
            }
        }
    }

    public function publishResources()
    {
        if ($this->publishResources === true) {
            if ($this->option('force') || $this->confirm(trans('tools::commands.views.proceed'), 'yes')) {
                $this->call($this->alias . ':resource', ['--force' => $this->option('force')]);
                $this->info(trans('tools::commands.views.success'));
            }
        }
    }

    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Quick setup without any questions'],
        ];
    }

}