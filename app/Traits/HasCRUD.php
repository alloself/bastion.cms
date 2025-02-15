<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait HasCRUD
{
  abstract public function model();


  public function index(Request $request)
  {
    $with = $request->has('with') ? $request->input('with') : [];
    if ($request->has('items_per_page') && $request->has('page')) {
      return $this->model()::getPaginateList($request->all(), $with);
    }

    return $this->model()::getList($request->all(), $with);
  }

  public function store(Request $request)
  {
    $with = $request->with ?? [];
    $values = $request->all();

    DB::beginTransaction();

    try {
      $entity = $this->model()::createEntity($values);
      DB::commit();
      return $entity->load($with);
    } catch (Exception $error) {
      DB::rollback();

      return $error;
    }
  }

  public function show(string $id, Request $request)
  {
    return $this->model()::showEntity($id, $request->with ?? []);
  }

  public function update(Request $request, string $id)
  {
    $with = $request->with ?? [];
    $values = $request->all();
    DB::beginTransaction();

    try {
      $entity = $this->model()::findOrFail($id);
      $entity->updateEntity($values);
      DB::commit();
      return $entity->load($with);
    } catch (Exception $error) {
      DB::rollback();

      return $error;
    }
  }

  public function destroy(string $id)
  {
    DB::beginTransaction();

    try {
      $entity = $this->model()::findOrFail($id);
      $entity->deleteEntity();
      DB::commit();
      return;
    } catch (Exception $error) {
      DB::rollback();

      return $error;
    }
  }

  public function deleteMany(Request $request)
  {
    DB::beginTransaction();

    try {
      $this->model()::destroy($request->toArray());
      DB::commit();
      return;
    } catch (Exception $error) {
      DB::rollback();

      return $error;
    }
  }
}
