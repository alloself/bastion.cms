<?php

namespace App\Services;


class InstagramParser
{
/**
 * Получить посты с открытого профиля Instagram
 *
 * @param string $username  — имя пользователя Instagram (без @)
 * @param int    $limit     — сколько постов вернуть (по умолчанию 12)
 * @return array            — массив постов, каждый элемент содержит:
 *                            • id            — внутренний ID публикации  
 *                            • shortcode     — короткий код для ссылки  
 *                            • display_url   — URL основного изображения/видео  
 *                            • thumbnail_url — URL превью (для видео)  
 *                            • is_video      — булево, это видео или нет  
 *                            • caption       — текст подписи (если есть)  
 *
 * @throws Exception       — если не удалось получить или распарсить данные
 */
function getInstagramPosts(string $username, int $limit = 12): array
{
    // 1) Формируем URL и User-Agent
    $url = "https://www.instagram.com/{$username}/";
    $ua  = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) '
         . 'AppleWebKit/537.36 (KHTML, like Gecko) '
         . 'Chrome/99.0.4844.51 Safari/537.36';

    // 2) Получаем HTML (cURL)
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER     => ["User-Agent: {$ua}"],
        CURLOPT_TIMEOUT        => 10,
    ]);
    $html = curl_exec($ch);
    if (curl_errno($ch)) {
        throw new Exception('cURL error: ' . curl_error($ch));
    }
    curl_close($ch);

    // 3) Ищем JSON-данные в <script>
    if (!preg_match(
        '/<script type="text\/javascript">window\._sharedData = (.+?);<\/script>/s',
        $html,
        $m
    )) {
        throw new Exception('Не удалось найти JSON в разметке, возможно, Instagram изменил структуру страницы.');
    }

    // 4) Декодируем и проходим по ленте
    $data = json_decode($m[1], true);
    $edges = $data['entry_data']['ProfilePage'][0]
                  ['graphql']['user']
                  ['edge_owner_to_timeline_media']['edges'] ?? [];

    // 5) Собираем результаты
    $posts = [];
    foreach (array_slice($edges, 0, $limit) as $edge) {
        $node = $edge['node'];
        $posts[] = [
            'id'            => $node['id'],
            'shortcode'     => $node['shortcode'],
            'display_url'   => $node['display_url'],
            'thumbnail_url' => $node['thumbnail_src'] ?? null,
            'is_video'      => (bool)$node['is_video'],
            'caption'       => $node['edge_media_to_caption']['edges'][0]['node']['text'] ?? '',
        ];
    }

    return $posts;
}


}