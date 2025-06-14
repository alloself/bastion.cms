<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Page;
use App\Models\Template;
use Illuminate\Foundation\Application;

// Загружаем Laravel приложение
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Тестирование PageObserver...\n";

try {
    // Получаем первый доступный шаблон
    $template = Template::first();
    if (!$template) {
        echo "Ошибка: Не найдено ни одного шаблона\n";
        exit(1);
    }

    echo "Создаем тестовую страницу...\n";
    
    // Создаем тестовую страницу
    $page = new Page();
    $page->template_id = $template->id;
    $page->meta = (object)['test' => true];
    $page->save();

    echo "Страница создана с ID: {$page->id}\n";

    // Создаем ссылку для страницы
    $page->addLink([
        'title' => 'Тестовая страница для Observer',
        'url' => '/test-observer-page'
    ]);

    echo "Ссылка создана: {$page->link->url}\n";

    // Обновляем страницу
    echo "Обновляем страницу...\n";
    $page->meta = (object)['test' => true, 'updated' => true];
    $page->save();

    echo "Страница обновлена\n";

    // Удаляем тестовую страницу
    echo "Удаляем тестовую страницу...\n";
    $page->delete();

    echo "Страница удалена\n";
    echo "Тест завершен! Проверьте логи Laravel для сообщений от PageObserver\n";

} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
    echo "Трассировка: " . $e->getTraceAsString() . "\n";
} 