<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Client\Factory as HttpFactory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Регистрируем сервис изображений
        $this->app->singleton(ImageService::class, function ($app) {
            return new ImageService();
        });
        
        // Создаем алиас для упрощения доступа к сервису
        $this->app->alias(ImageService::class, 'image');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Отключаем SQL-логирование 
        DB::disableQueryLog();
        
        // Регистрируем директивы для работы с изображениями
        Blade::directive('image', function ($expression) {
            return "<?php echo app('image')->optimizeAndConvert($expression); ?>";
        });
        
        Blade::directive('imageResized', function ($expression) {
            return "<?php echo app('image')->getResized($expression); ?>";
        });
        
        // Добавление заголовков для статических ресурсов
        $this->configureResourceHeaders();
        
        // Добавляем глобальные переменные для шаблонов
        View::composer('*', function ($view) {
            $view->with('siteConfig', [
                'site_name' => config('app.name'),
                'site_description' => config('app.description', 'Bastion CMS'),
                'site_keywords' => config('app.keywords', 'cms, content, management, system'),
                'social_links' => [
                    'twitter' => config('services.twitter.url', '#'),
                    'facebook' => config('services.facebook.url', '#'),
                    'instagram' => config('services.instagram.url', '#'),
                ],
                'contact_email' => config('app.contact_email', 'info@bastioncms.com'),
                'footer_text' => config('app.footer_text', '© ' . date('Y') . ' Bastion CMS'),
            ]);
        });
        
        // Оптимизации для продакшн-среды
        if (app()->isProduction()) {
            // Включаем отложенную загрузку изображений
            View::composer('*', function ($view) {
                $view->with('lazyLoadImages', true);
            });
        }
    }
    
    /**
     * Настройка заголовков для статических ресурсов
     */
    protected function configureResourceHeaders(): void
    {
        // Добавляем заголовки для статических ресурсов
        Route::prefix('/')
            ->group(function () {
                $longTerm = now()->addYear();
                $mediumTerm = now()->addMonth();
            
                Route::get('js/{file}', function ($file) use ($longTerm) {
                    $path = public_path("js/$file");
                    if (!file_exists($path)) {
                        abort(404);
                    }
                    
                    return Response::file($path, [
                        'Cache-Control' => 'public, max-age=31536000, immutable',
                        'Expires' => $longTerm->format('D, d M Y H:i:s').' GMT'
                    ]);
                })->where('file', '.*\.js');
                
                Route::get('css/{file}', function ($file) use ($longTerm) {
                    $path = public_path("css/$file");
                    if (!file_exists($path)) {
                        abort(404);
                    }
                    
                    return Response::file($path, [
                        'Cache-Control' => 'public, max-age=31536000, immutable',
                        'Expires' => $longTerm->format('D, d M Y H:i:s').' GMT'
                    ]);
                })->where('file', '.*\.css');
                
                Route::get('fonts/{file}', function ($file) use ($longTerm) {
                    $path = public_path("fonts/$file");
                    if (!file_exists($path)) {
                        abort(404);
                    }
                    
                    return Response::file($path, [
                        'Cache-Control' => 'public, max-age=31536000, immutable',
                        'Expires' => $longTerm->format('D, d M Y H:i:s').' GMT'
                    ]);
                })->where('file', '.*');
                
                Route::get('images/{file}', function ($file) use ($mediumTerm) {
                    $path = public_path("images/$file");
                    if (!file_exists($path)) {
                        abort(404);
                    }
                    
                    return Response::file($path, [
                        'Cache-Control' => 'public, max-age=2592000',
                        'Expires' => $mediumTerm->format('D, d M Y H:i:s').' GMT'
                    ]);
                })->where('file', '.*');
            });
    }
}
