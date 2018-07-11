<?php

namespace QuadStudio\Tools;

use Illuminate\Support\ServiceProvider;


class ToolsServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     */
    public function register()
    {

    }

    private function packagePath($path)
    {
        return __DIR__ . "/../{$path}";
    }

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->publishTranslations();
    }

    /**
     * Publish Lock Screen translations
     *
     * @return $this
     */
    private function publishTranslations()
    {

        $this->loadTranslations();

        $this->publishes([
            $this->packagePath('resources/lang') => resource_path('lang/vendor/tools'),
        ], 'translations');

        return $this;
    }

    /**
     * Load Lock Screen translations
     *
     * @return $this
     */
    private function loadTranslations()
    {
        $this->loadTranslationsFrom($this->packagePath('resources/lang'), 'tools');

        return $this;
    }

}