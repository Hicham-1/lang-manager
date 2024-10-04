<?php

namespace H1ch4m\LangManager;

use H1ch4m\LangManager\Console\LanguageCommand;
use Illuminate\Support\ServiceProvider;

class LangManagerServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->commands([
            LanguageCommand::class
        ]);
    }

    public function register() {}
}
