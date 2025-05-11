<?php
// Чтение SQL-файла
$content = file_get_contents('laravel (8).sql');

// Находим все INSERT-выражения для таблицы links
$pattern = "/INSERT INTO \`links\` .*?VALUES.*?\\);/s";
preg_match_all($pattern, $content, $matches);
$insertStatements = $matches[0];

echo "Найдено " . count($insertStatements) . " INSERT-выражений для таблицы links\n";

// Парсинг всех записей из INSERT-выражений
$valuePattern = "/\\('([^']+)', '([^']+)', (.*?), '([^']+)', ('.*?'|NULL), ?'([^']*)', '([^']+)', (.*?), '([^']+)', '([^']+)'\\)/";
$allLinks = [];

foreach ($insertStatements as $statement) {
    preg_match_all($valuePattern, $statement, $valueMatches, PREG_SET_ORDER);
    foreach ($valueMatches as $match) {
        $link = [
            'id' => $match[1],
            'title' => $match[2],
            'slug' => $match[4],
            'url' => $match[5] === 'NULL' ? null : trim($match[5], "'"),
            'linkable_id' => $match[6],
            'linkable_type' => $match[7],
            'created_at' => $match[9],
            'updated_at' => $match[10],
            'original_line' => $match[0]
        ];
        $allLinks[] = $link;
    }
}

echo "Всего найдено записей: " . count($allLinks) . "\n";

// Группировка по заголовку (title) для поиска дубликатов
$linksByTitle = [];
foreach ($allLinks as $link) {
    if (!isset($linksByTitle[$link['title']])) {
        $linksByTitle[$link['title']] = [];
    }
    $linksByTitle[$link['title']][] = $link;
}

// Подсчет дубликатов
$duplicateCount = 0;
$linksToKeep = [];
$linksToDelete = [];

foreach ($linksByTitle as $title => $links) {
    if (count($links) > 1) {
        $duplicateCount++;
        
        // Сортировка записей по дате создания (от новых к старым)
        usort($links, function($a, $b) {
            return strcmp($b['created_at'], $a['created_at']);
        });
        
        // Оставляем самую новую запись, остальные удаляем
        $linksToKeep[] = $links[0];
        for ($i = 1; $i < count($links); $i++) {
            $linksToDelete[] = $links[$i];
        }
    } else {
        // Если нет дубликатов, оставляем единственную запись
        $linksToKeep[] = $links[0];
    }
}

echo "Количество заголовков с дубликатами: " . $duplicateCount . "\n";
echo "Записей к сохранению: " . count($linksToKeep) . "\n";
echo "Записей к удалению: " . count($linksToDelete) . "\n";

// Примеры дубликатов
echo "\nПримеры дубликатов:\n";
$shown = 0;
foreach ($linksByTitle as $title => $links) {
    if (count($links) > 1 && $shown < 5) {
        echo "Заголовок: " . $title . ", Количество: " . count($links) . "\n";
        usort($links, function($a, $b) {
            return strcmp($b['created_at'], $a['created_at']);
        });
        foreach ($links as $i => $link) {
            $status = ($i === 0) ? "СОХРАНИТЬ" : "УДАЛИТЬ";
            echo "  [$status] ID: " . $link['id'] . 
                 ", Создано: " . $link['created_at'] . 
                 ", Тип: " . $link['linkable_type'] . 
                 ", linkable_id: " . $link['linkable_id'] . "\n";
        }
        $shown++;
    }
}

// Создаем новый SQL-файл с исправленными данными
$outputFile = 'laravel_fixed.sql';
$newContent = $content;

// Удаляем записи, которые мы хотим удалить
foreach ($linksToDelete as $link) {
    // Экранируем специальные символы в регулярных выражениях
    $escapedLine = preg_quote($link['original_line'], '/');
    // Заменяем строку на пустую
    $newContent = preg_replace('/' . $escapedLine . ',?/', '', $newContent);
}

// Очистка пустых INSERT-операторов и лишних запятых
$newContent = preg_replace('/INSERT INTO `links` \(.*?\) VALUES\s*;/', '', $newContent);
$newContent = preg_replace('/,\s*\);/', ');', $newContent);

// Дополнительное исправление для удаления лишних запятых перед точкой с запятой
$newContent = preg_replace('/,\s*;/', ';', $newContent);

file_put_contents($outputFile, $newContent);
echo "\nФайл с исправленными данными сохранен как: $outputFile\n";
