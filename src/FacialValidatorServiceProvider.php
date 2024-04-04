<?php

namespace FacialValidator;

use Illuminate\Support\ServiceProvider;
use App\Repositories\FacialValidatorRepository;
use App\Repositories\FacialValidatorRepositoryInterface;

class FacialValidatorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([__DIR__.'/../config/facial.php' => config_path('facial.php')], 'config');
        $this->mergeConfigFrom(__DIR__.'/../config/facial.php', 'facial');

        $facialConfig = config('facial');

        if ($facialConfig && $facialConfig['mode'] === 'laravel') {
            $this->publishLaravelResources();
//            $this->registerRoutes();
//            $this->registerBindings();
        } elseif ($facialConfig) {
            $this->publishLegacyResources();
        }
    }

    protected function publishLaravelResources()
    {
        // php artisan vendor:publish --tag=facial-laravel
        $this->publishes([
            __DIR__.'/Controllers/FacialValidationController.php' => app_path('Http/Controllers/FacialValidator/FacialValidationController.php'),
            __DIR__.'/Enums/FacialValidatorClientsEnum.php' => app_path('Enums/FacialValidator/FacialValidatorClientsEnum.php'),
            __DIR__.'/Repositories' => app_path('Repositories/FacialValidator')
        ], 'facial-legacy');
    }

    protected function publishLegacyResources()
    {
        // php artisan vendor:publish --tag=facial-legacy
        $this->publishes([
            __DIR__.'/Controllers/facial.ctrl.php' => app_path('modules/facialvalidator/facial.ctrl.php'),
            __DIR__.'/Enums/FacialValidatorLegacyClientsEnum.php' => app_path('enum/FacialValidatorLegacyClientsEnum.php')
        ], 'facial-legacy');
    }

    // Register routes in api.php
    protected function registerRoutes()
    {
        $facialRoutesPath = __DIR__.'/../routes/facial.php';
        $apiRoutesPath = 'routes/api.php';

        $facialRoutesContent = file_get_contents($facialRoutesPath);
        $apiRoutesContent = file_get_contents($apiRoutesPath);

        preg_match_all('/Route::group\s*\(\s*\[.*?\]\s*,\s*function\s*\(\s*\).*?\s*}\s*\);/s', $facialRoutesContent, $facialRoutesMatches);
        $facialRoutes = $facialRoutesMatches[0];

        // Verificar se as rotas já existem no arquivo api.php
        foreach ($facialRoutes as $route) {
            if (strpos($apiRoutesContent, $route) === false) {
                // Adicionar a rota ao arquivo api.php
                file_put_contents($apiRoutesPath, $route . PHP_EOL, FILE_APPEND);
            } else {
                echo "A rota já existe no arquivo api.php: $route\n";
            }
        }
    }

    // Register repository bind for AppServiceProvider
    protected function registerBindings()
    {
        $path = 'app/Providers/AppServiceProvider.php';

        // Bind a ser adicionado
        $newContent = <<<PHP
            // Bind repository with interface
            \$this->app->bind(
                \App\Repositories\FacialValidator\FacialValidatorRepositoryInterface::class,
                \App\Repositories\FacialValidator\FacialValidatorRepository::class
            );
        PHP;

        // Lê o conteúdo atual do arquivo
        $currentContent = file_get_contents($path);

        // Verifica se o bind já está presente
        if (strpos($currentContent, $newContent) === false) {
            // Verifica se o método register() está presente
            if (preg_match('/public\s+function\s+register\s*\(\s*\)\s*{/', $currentContent, $matches)) {
                $startPos = strpos($currentContent, $matches[0]) + strlen($matches[0]);
                $endPos = strpos($currentContent, '}', $startPos);

                // Adiciona o novo conteúdo dentro do método register()
                $updatedContent = substr_replace($currentContent, $newContent . PHP_EOL, $endPos, 0);

                // Escreve o conteúdo atualizado de volta ao arquivo
                $result = file_put_contents($path, $updatedContent);

                if ($result !== false) {
                    echo "Bind adicionado com sucesso ao método register() no arquivo AppServiceProvider.php.";
                } else {
                    echo "Erro ao escrever no arquivo.";
                }
            } else {
                echo "Método register() não encontrado no arquivo AppServiceProvider.php.";
            }
        } else {
            echo "O bind já foi adicionado anteriormente ao método register() no arquivo AppServiceProvider.php.";
        }
    }

    public function register()
    {}
}
