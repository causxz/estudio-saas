<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Storage::extend('google', function($app, $config) {
            $client = new \Google\Client();
            
            // 1. Forçamos o caminho do JSON direto (sem depender de cache)
            $client->setAuthConfig(storage_path('app/google-drive-key.json'));
            $client->addScope(\Google\Service\Drive::DRIVE);
            
            $service = new \Google\Service\Drive($client);
            
            // 2. Forçamos o ID exato da sua pasta e ativamos o suporte a pastas partilhadas
            $adapter = new GoogleDriveAdapter($service, '12jO8tOLfJf_mK1QvLQcJJ88x9-M9h8T1', [
                'useHasDir' => true
            ]);
            
            return new \Illuminate\Filesystem\FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config
            );
        });
    }
}