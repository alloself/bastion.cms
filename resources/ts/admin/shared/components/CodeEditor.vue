<script setup lang="ts">
import { ref, onMounted } from "vue";
import * as monaco from "monaco-editor";

const editorRef = ref();
const editor = ref();

onMounted(() => {
    self.MonacoEnvironment = {
        getWorker(_, lang) {
            if (lang === "html") {
                return new Worker(
                    new URL(
                        "@monaco/language/html/html.worker.js",
                        import.meta.url
                    ),
                    { type: "module" }
                );
            }
            return new Worker(
                new URL("@monaco/editor/editor.worker.js", import.meta.url),
                { type: "module" }
            );
        },
    };

    editor.value = monaco.editor.create(editorRef.value, {
        value: `<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="{{ $page->meta->keywords ?? '' }}" />
    <meta name="description" content="{{ $page->meta->description ?? '' }}" />
    <title>{{ $page->link->title ?? '' }}</title>
    <link rel="canonical" href="https://<?=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?>"/>
    <meta property="og:url" content="https://<?=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?=$page->link->title?>">
    <meta property="og:description" content="<?=$page->meta->description?>">
    <meta property="og:image" content="/public/files/logo-dark.svg">
</head>

<body id="app">
    @csrf
    @vite(['resources/scss/index.scss', 'resources/js/index.js'])
    <link rel="stylesheet" href="/css/travelline-style.css">
    <x-svg-sprite />
    <main id="main">
        {!! $header !!}
        <div id='block-search' class="{{$page->link->path !== '/' ? 'no-index-page' : ''}}">
            <div id='tl-search-form' class='tl-container'>
            </div>
        </div>

        <section class="main-section">
            @if ($page->link->path && $page->link->path !== '/')
                <x-breadcrumbs :breadcrumbs="$page->ancestors" :page="$page"></x-breadcrumbs>
                <div class="container mx-auto px-5 my-5">
                    <h1>{{ $page->link->subtitle }}</h1>
                </div>
            @endif
            @foreach ($blocks as $block)
                {!! $block !!}
            @endforeach
        </section>

        {!! $footer !!}
    </main>
</body>

</html>`,
        language: "php",
        theme: "vs-dark",
        automaticLayout: true,
    });

    editor.value.getModel().updateOptions({ tabSize: 2 });

    editor.value.onKeyDown((e: KeyboardEvent) => {
        if (e.keyCode === 49 && e.ctrlKey) {
            e.preventDefault();
            console.log("hello world");
        }
    });
});
</script>

<template>
    <div id="editor" ref="editorRef"></div>
</template>

<style scoped>
#editor {
    width: 100%;
    height: 100%;
}
</style>
