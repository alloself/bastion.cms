<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DataCollection;
use App\Models\DataEntity;
use App\Models\ContentBlock;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AddContentBlocksToDataEntities extends Command
{
    /**
     * Название консольной команды.
     *
     * @var string
     */
    protected $signature = 'app:add-content-blocks';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Добавление стандартных блоков контента ко всем сущностям данных';

    /**
     * Выполнение консольной команды.
     */
    public function handle()
    {
        $this->info('Начинаем добавление блоков контента...');
        
        $result = $this->addContentBlocksToAllDataEntities();
        
        $this->info($result);
        
        $this->info('Выполнено успешно!');
    }

    /**
     * Добавляет стандартные блоки контента ко всем сущностям данных
     * в коллекциях данных
     * 
     * @return string
     */
    protected function addContentBlocksToAllDataEntities()
    {
        // Получаем все коллекции данных
        $dataCollections = DataCollection::with('dataEntities')->get();
        
        // Предопределенные блоки контента для добавления
        $contentBlocks = [
            [
                'id' => '9ee2ce4d-eabe-4448-92aa-faddd78b17da',
                'key' => 'default',
                'order' => 1
            ],
            [
                'id' => '9ea760b5-d880-421f-8895-3b8ab5d6dff1',
                'key' => 'header',
                'order' => 2
            ],
            [
                'id' => '9ea760c0-92bb-421d-8776-900baff48eac',
                'key' => 'footer',
                'order' => 0
            ]
        ];
        
        // ID шаблона, который нужно установить для всех сущностей
        $templateId = '9ea76132-20fe-4e29-b099-0583dc78e567';
        
        // Счетчики обработанных сущностей
        $processedEntities = 0;
        $updatedTemplates = 0;
        
        // Проходим по всем коллекциям
        foreach ($dataCollections as $collection) {
            // Проходим по всем сущностям в коллекции
            foreach ($collection->dataEntities as $entity) {
                // Показываем прогресс
                $this->output->write('.');
                
                // Устанавливаем template_id для сущности
                if ($entity->template_id !== $templateId) {
                    $entity->template_id = $templateId;
                    $entity->save();
                    $updatedTemplates++;
                    $this->output->write('t');
                }
                
                // Проходим по всем блокам контента
                foreach ($contentBlocks as $blockData) {
                    // Получаем блок контента
                    $contentBlock = ContentBlock::find($blockData['id']);
                    
                    if (!$contentBlock) {
                        $this->output->write('x');
                        continue;
                    }
                    
                    // Проверяем существование связи без использования сортировки по order
                    $exists = DB::table('content_blockables')
                        ->where('content_blockable_id', $entity->id)
                        ->where('content_blockable_type', get_class($entity))
                        ->where('content_block_id', $blockData['id'])
                        ->exists();
                    
                    // Если связи нет, добавляем её
                    if (!$exists) {
                        $entity->contentBlocks()->attach($contentBlock, [
                            'id' => Str::uuid()->toString(),
                            'key' => $blockData['key'],
                            'order' => $blockData['order']
                        ]);
                    }
                }
                
                $processedEntities++;
            }
        }
        
        $this->output->write("\n");
        return "Добавлены блоки контента к {$processedEntities} сущностям данных, обновлен шаблон у {$updatedTemplates} сущностей";
    }
} 