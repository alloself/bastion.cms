<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\DataCollection;
use App\Models\DataEntity;
use App\Models\File;
use App\Models\Link;
use App\Models\Pivot\DataEntityable;
use App\Services\LinkUrlGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{

    function transliterateAndSnakeCase(string $text): string
    {
        $map = [
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'e',
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'й' => 'y',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'h',
            'ц' => 'ts',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'sch',
            'ъ' => '',
            'ы' => 'y',
            'ь' => '',
            'э' => 'e',
            'ю' => 'yu',
            'я' => 'ya',
            'А' => 'A',
            'Б' => 'B',
            'В' => 'V',
            'Г' => 'G',
            'Д' => 'D',
            'Е' => 'E',
            'Ё' => 'E',
            'Ж' => 'Zh',
            'З' => 'Z',
            'И' => 'I',
            'Й' => 'Y',
            'К' => 'K',
            'Л' => 'L',
            'М' => 'M',
            'Н' => 'N',
            'О' => 'O',
            'П' => 'P',
            'Р' => 'R',
            'С' => 'S',
            'Т' => 'T',
            'У' => 'U',
            'Ф' => 'F',
            'Х' => 'H',
            'Ц' => 'Ts',
            'Ч' => 'Ch',
            'Ш' => 'Sh',
            'Щ' => 'Sch',
            'Ъ' => '',
            'Ы' => 'Y',
            'Ь' => '',
            'Э' => 'E',
            'Ю' => 'Yu',
            'Я' => 'Ya'
        ];

        // Транслитерация
        $text = strtr($text, $map);

        // Очистка от всего кроме букв, цифр, пробелов
        $text = preg_replace('/[^a-zA-Z0-9\s]/', '', $text);

        // Пробелы в подчёркивания, нижний регистр
        $text = strtolower(trim(preg_replace('/\s+/', '_', $text)));

        return $text;
    }

    function import(Request $request)
    {


        $modelFieldsMap = [
            'name' => ['Наименование ', 'Артикул ']
        ];


        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $file = $request->file('file');
        $rows = Excel::toCollection(null, $file)->first();
        // оставляем только данные о товаре


        $attributes = $rows[0]->map(function ($item) {
            return [
                'name' => trim($item),
                'key' => $this->transliterateAndSnakeCase(trim($item))
            ];
        });

        $attributeModels = [];

        $attributes->each(function ($item, $index) use (&$attributeModels) {
            $existedModel = Attribute::whereName($item['name'])->first();
            if ($existedModel) {
                $attributeModels[$index] =  $existedModel;
                return;
            }
            $attributeModels[$index] = Attribute::createEntity($item);
        });

        $rows->shift();
        /*
        $dataCollectionsRootId = '9eaad9f3-e7f5-4333-841f-88becf72da69';

        $dataCollections = $rows->pluck(1)
            ->filter()
            ->unique()
            ->values()->map(function ($item) {
                return trim($item);
            });

        $dataCollections->each(function ($item) use ($dataCollectionsRootId) {

            $data = [
                'name' => $item,
                'link' => [
                    'title' => $item
                ],
                'parent_id' => $dataCollectionsRootId
            ];

            $dataCollection = DataCollection::createEntity($data);
        });*/




        return $rows->map(function ($item) use ($attributeModels) {
            $allNull = $item->every(fn($el) => is_null($el));

            if ($allNull) {
                return;
            }

            $ignoredIndexes = [0, 1, 40, 41];
            $dataEntityName = trim($item[1]) . " - " . trim($item[0]);
            $dataCollection = DataCollection::whereName(trim($item[1]))->first();

            $images = [];
            if (isset($item[40])) {
                $file = Http::get($item[40])->body();
                $filename = $dataEntityName . '(1).' . pathinfo($item[40], PATHINFO_EXTENSION);

                $url = uniqid() . "." . pathinfo($item[40], PATHINFO_EXTENSION);
                $path = "files/{$url}";
                Storage::disk('public')->put($path, $file);

                $images[] = File::create([
                    'url' => $path,
                    'name' => $filename,
                    'extension' => pathinfo($item[40], PATHINFO_EXTENSION),
                ]);
            }
            if (isset($item[41])) {
                $file = Http::get($item[41])->body();
                $filename = $dataEntityName . '(1).' . pathinfo($item[41], PATHINFO_EXTENSION);

                $url = uniqid() . "." . pathinfo($item[41], PATHINFO_EXTENSION);
                $path = "files/{$url}";
                Storage::disk('public')->put($path, $file);

                $images[] = File::create([
                    'url' => $path,
                    'name' => $filename,
                    'extension' => pathinfo($item[41], PATHINFO_EXTENSION),
                ]);
            }


            $data = [
                'name' => $dataEntityName
            ];

            $dataEntity = DataEntity::createEntity($data);

            foreach ($images as $image) {
                $dataEntity->images()->attach($image->id, [
                    'type' => 'image',
                    'key' => 'image',
                    'order' => 0,
                ]);
            }

            $dataEntityable = new DataEntityable([
                'data_entity_id' => $dataEntity->id,
                'data_entityable_type' => DataCollection::class,
                'data_entityable_id' => $dataCollection->id
            ]);



            $linkData = [
                'title' => $dataEntityName,
                'linkable_id' => $dataEntityable->id,
                'linkable_type' => DataEntityable::class

            ];

            $link = Link::createEntity($linkData);

            $dataEntity->dataEntityables()->save($dataEntityable);

            $dataEntityable->link()->save($link);

            $link;

            app(LinkUrlGenerator::class)->generate($link);

            foreach ($item as $key => $value) {
                if (in_array($key, $ignoredIndexes) || !$value) {
                    continue;
                }

                $dataEntity->attributes()->attach($attributeModels[$key]->id, [
                    'value' => $value,
                    'order' => 0,
                ]);
            }



            return $dataEntity;
        });
    }

    function importPrice(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $file = $request->file('file');
        $rows = Excel::toCollection(null, $file)->first();
        $rows->shift();

        $optPrice = Attribute::where('key', 'opt_price')->first();
        $price = Attribute::where('key', 'price')->first();
        $amount = Attribute::where('key', 'amount')->first();

        return $rows->map(function ($item) use ($optPrice, $price, $amount) {
            $needle = trim(preg_replace('/[\x00-\x1F\x7F\xA0]+/u', '', $item[0]));
            if (!isset($needle)) {
                return;
            }
            $dataEntity = DataEntity::where('name', 'LIKE', "%{$needle}%")->first();

            $dataEntity->attributes()->attach($optPrice->id, [
                'value' => $item[2],
                'order' => 0,
            ]);

            $dataEntity->attributes()->attach($price->id, [
                'value' => $item[3],
                'order' => 0,
            ]);

            $dataEntity->attributes()->attach($amount->id, [
                'value' => $item[1],
                'order' => 0,
            ]);
        });

        return;
    }
}
