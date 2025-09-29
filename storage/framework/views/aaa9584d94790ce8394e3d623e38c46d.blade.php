<section id='block-9ffeb8f8-6844-4140-8e34-f905574e6626' class="app-text-row pt-8">
    <div class="centered">
        <div class="flex flex-col gap-4 pb-2 border-b border-brand border-opacity-20 mb-5 md:items-end md:flex-row">
            <h2 class="h3 mb-0 md:w-1/2 lg:w-1/3">{{$contentBlock->link->title}}</h2>
            <div class="text-label text-brand text-opacity-60
             md:w-1/2 md:text-right lg:text-center lg:w-1/3">{{$contentBlock->link->subtitle}}</div>
        </div>
        <div class="text-simple text-simple--dark lg:ml-auto lg:w-1/3">
            {!!$contentBlock->content!!}
        </div>
    </div>
</section>