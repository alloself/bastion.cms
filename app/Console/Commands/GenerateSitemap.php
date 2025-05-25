<?php

namespace App\Console\Commands;

use App\Models\Link;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Генерирует sitemap на основе всех ссылок в системе';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Начинаю генерацию sitemap...');

        $sitemap = Sitemap::create();

        // Получаем все активные ссылки
        $links = Link::whereNotNull('url')
            ->where('url', '!=', '')
            ->get();

        $this->info("Найдено {$links->count()} ссылок для добавления в sitemap");

        foreach ($links as $link) {
            $url = $this->buildFullUrl($link->url);
            
            $sitemapUrl = Url::create($url)
                ->setLastModificationDate($link->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority($this->calculatePriority($link->url));

            $sitemap->add($sitemapUrl);
            
            $this->line("Добавлена ссылка: {$url}");
        }

        // Сохраняем sitemap
        $sitemapPath = public_path('sitemap.xml');
        $sitemap->writeToFile($sitemapPath);

        $this->info("Sitemap успешно сгенерирован: {$sitemapPath}");
        $this->info("Всего добавлено ссылок: {$links->count()}");

        return Command::SUCCESS;
    }

    /**
     * Строит полный URL
     */
    private function buildFullUrl(string $path): string
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $path = ltrim($path, '/');
        
        return $baseUrl . '/' . $path;
    }

    /**
     * Вычисляет приоритет для URL
     */
    private function calculatePriority(string $url): float
    {
        // Главная страница - максимальный приоритет
        if ($url === '/' || $url === '') {
            return 1.0;
        }

        // Страницы первого уровня - высокий приоритет
        if (substr_count($url, '/') <= 2) {
            return 0.8;
        }

        // Страницы каталога - средний приоритет
        if (strpos($url, '/katalog') === 0) {
            return 0.6;
        }

        // Остальные страницы - стандартный приоритет
        return 0.5;
    }


}
