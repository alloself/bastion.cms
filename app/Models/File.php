<?php

namespace App\Models;

use App\Models\Pivot\Fileable;
use App\Traits\HasCRUDMethods;
use App\Traits\HasList;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory, HasUuids, HasList, HasCRUDMethods;

    protected $fillable = ['url', 'name', 'extension'];

    public function getUrlAttribute($value)
    {
        return asset(Storage::url($value));
    }

    public function fileables()
    {
        return $this->hasMany(Fileable::class);
    }

    function deleteEntity(): bool
    {
        $this->fileables()->delete();

        Storage::disk('public')->delete($this->getRawOriginal('url'));

        return $this->delete();
    }

    public static function createEntity(array $values): File
    {
        $originalFile = $values['file'];
        $name = $originalFile->getClientOriginalName();
        $url = $originalFile->storeAs('files', uniqid() . "." . $originalFile->getClientOriginalExtension(), 'public');

        $entity = File::create([
            'url' => $url,
            'name' => $name,
            'extension' => $originalFile->getClientOriginalExtension(),
        ]);

        return $entity;
    }
}
