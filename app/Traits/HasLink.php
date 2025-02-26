<?php

namespace App\Traits;

use App\Models\Link;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

trait HasLink
{

  public function link(): MorphOne
  {
    return $this->morphOne(Link::class, 'linkable');
  }

  function addLink(array $values): self
  {
    Validator::make($values, [
      'title' => 'required|string',
    ])->validate();

    $link = new Link();
    $link->fill($values);

    $this->link()->save($link);
    $this->link->refreshURL();

    return $this;
  }

  public function updateLink(array $values)
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
      $this->link->refreshURL();
    }

    return $this;
  }
}
