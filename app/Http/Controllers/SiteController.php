<?php

namespace App\Http\Controllers;

use App\Http\Guards\Authenticate;
use App\Http\Guards\Inverse;
use App\Models\Link;
use App\Models\DataEntity;
use App\Models\Page;
use App\Models\DataCollection;
use App\Models\Pivot\DataEntityable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SiteController extends Controller
{
    public static $guards = [
        'auth' => Authenticate::class,
        'not' => Inverse::class
    ];

    public function admin()
    {
        return view('admin');
    }

    public function show404()
    {
        return view('errors.404', [
            'title' => 'Страница не найдена',
            'description' => 'Страница не найдена',
            'keywords' => 'Страница не найдена',
            'canonical' => request()->url(),
            'robots' => 'noindex, nofollow',
            'code' => 404,
        ]);
    }

    protected function renderContent($model, Request $request): string
    {
        return $model?->render($request);
    }

    public function site(Request $request, string $url = '')
    {
        $slug = '/' . trim($url, '/');

        try {
            $link = Link::where('url', $slug)->with([
                'linkable' => function ($query) {
                    $query->morphWith([
                        Page::class => Page::$renderRelations,
                        DataCollection::class => DataCollection::$renderRelations,
                        DataEntity::class => DataEntity::$renderRelations,
                        DataEntityable::class => DataEntityable::$renderRelations,
                    ]);
                }
            ])->first();

            if (!$link?->linkable) {
                return $this->show404();
            }

            $html = $this->renderContent($link->linkable, $request);

            return response($html, 200, [
                'Content-Type'   => 'text/html',
                'X-Robots-Tag'   => 'index,follow'
            ]);
        } catch (\Throwable $e) {
            Log::error('Rendering error: ' . $slug . ' : ' . $e->getMessage(), ['exception' => $e]);
            return $this->show404();
        }
    }
}
