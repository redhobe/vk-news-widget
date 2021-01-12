<?php
    $params = [
        'owner_id' => 'YOUR_PUBLIC_GROUP_ID', // Id группы
        'count' => '4', // Кол-во получаемых записей со стены
        'filter' => 'owner', // Фильтр (по умолчанию записи от имени группы)
        'access_token' => 'YOUR_PUBLIC_GROUP_TOKEN', // Ваш токен доступа
        'v' => '5.71'
    ];

    $cacheFile='./cache.json';

    if (!file_exists($cacheFile) || time() - filemtime($cacheFile) > 1800) { // Получение актуальных записей каждые 1800 сек.
        $url = 'https://api.vk.com/method/wall.get?' . http_build_query($params);
        $response = file_get_contents($url);
        file_put_contents($cacheFile, $response);
    }

    $data = json_decode(file_get_contents($cacheFile), true);

    foreach($data['response']['items'] as $items):
        $text = $items['text'];
        $link = 'https://vk.com/YOUR_PUBLIC_GROUP_ADDRESS'.$params['owner_id'].'_'.$items['id']; // Вместо YOUR_PUBLIC_GROUP_ADDRESS  адрес вашей группы вк
        $attachments = $items['attachments']['0'];
        $defaultThumbnail = './defaultThumbnail.jpg'; //Путь к дефолтной картинке-заглушке

        switch($attachments['type']){
            case 'photo' : $thumbnail = $attachments['photo']['photo_604'] ?? $defaultThumbnail;  break;
            case 'video' : $thumbnail = $attachments['video']['photo_800'] ?? $defaultThumbnail; break;
            case 'link' : $thumbnail = $attachments['link']['photo']['photo_604'] ?? $defaultThumbnail; break;
            default : $thumbnail = $defaultThumbnail;
        };

	    echo '
                <div>
                    <a href="'.$link.'">
                        <img src="' . $thumbnail. '" alt=""/>
                    </a>
                </div>
                <div>
                    <div>
                        <h3><a href="'.$link.'"></a></h3>
                    </div>
                </div>
        ';
    endforeach;
    return ob_get_clean();
?>
