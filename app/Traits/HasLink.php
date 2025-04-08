<?php

namespace App\Traits;

use App\Models\Link;
use App\Services\LinkUrlGenerator;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Validator;

trait HasLink
{
  public function link(): MorphOne
  {
    return $this->morphOne(Link::class, 'linkable');
  }

  public function addLink(array $values): self
  {
    Validator::make($values, [
      'title' => 'required|string',
    ])->validate();

    $link = new Link();
    $link->fill($values);

    $this->link()->save($link);

    app(LinkUrlGenerator::class)->generate($link);

    return $this;
  }

  public function updateLink(array $values): self
  {
    if (empty($values['title']) && $this->link) {
      $this->link->delete();
      return $this;
    }

    Validator::make($values, [
      'title' => 'required|string',
    ])->validate();

    $link = $this->link;

    if (!$link) {
      $this->addLink($values);
    } else {
      $link->fill($values);
      $link->save();

      app(LinkUrlGenerator::class)->generate($link);
    }

    return $this;
  }

  protected function syncLink(array $data): self
  {
    if (isset($data['id'])) {
      $this->updateLink($data);
    } else {
      $this->addLink($data);
    }

    return $this;
  }
}
