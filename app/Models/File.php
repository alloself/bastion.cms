<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class File extends BaseModel
{
    use HasFactory;

    protected $fillable = ['url', 'name', 'extension'];

    public function getUrlAttribute($value)
    {
        return asset(Storage::url($value));
    }

    function deleteEntity(): bool
    {
        Storage::disk('public')->delete($this->url);

        return $this->delete();
    }

    public static function createEntity(array $values): File
    {
        $originalFile = $values['file'];
        $name = $originalFile->getClientOriginalName();
        $url = $originalFile->storeAs('public/files', uniqid() . "." . $originalFile->getClientOriginalExtension());

        $entity = File::create([
            'url' => $url,
            'name' => $name,
            'extension' => $originalFile->getClientOriginalExtension(),
        ]);

        return $entity;
    }
}
