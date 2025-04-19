<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;


function getItemByPivotKey($array, $findKey)
{
    return $array?->first(function ($value, $key) use ($findKey) {
        return $value->pivot->key === $findKey;
    });
}

function getItemsByPivotKey($array, $findKey)
{
    return $array->filter(function ($value, $key) use ($findKey) {
        return $value->pivot->key === $findKey;
    });
}

function getAttributeByKey($item, $findKey)
{
    return $item?->attributes?->first(function ($value, $key) use ($findKey) {
        return $value->key === $findKey;
    });
}

function getItemImage($item, $key)
{
    return getItemByPivotKey($item->images, $key)?->path;
}

function activeMenu($uri = '')
{
    $active = '';
    if (Request::is(Request::segment(1) . '/' . $uri . '/*') || Request::is(Request::segment(1) . '/' . $uri) || Request::is($uri)) {
        $active = 'is-active-link';
    }
    return $active;
}

function getAttributeByName($array, $findKey)
{
    if ($array instanceof Collection) {
        $item = $array->first(function ($value, $key) use ($findKey) {
            return $value->name === $findKey;
        });
        if ($item) {
            return $item->pivot->value;
        } else {
            return '';
        }
    }
}
