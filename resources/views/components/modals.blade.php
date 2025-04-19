<appmodal name="testModal" v-cloak>
    <div class="h2">Заголовок</div>
    <p>
        Lorem, ipsum dolor sit amet consectetur adipisicing elit. Omnis enim quo at fugiat ab quaerat quas odit, tenetur suscipit esse placeat quidem delectus doloribus dolorum. Obcaecati quia tenetur molestiae aspernatur.
    </p>
</appmodal>
<appmodal name="callbackModal" v-cloak>
    <div class="h2">Напишите нам</div>
    <callbackform></callbackform>
</appmodal>
<appmodal name="geoLocationModal" v-cloak>
    <div class="h2">Выберите город</div>
    <geolocationlist></geolocationlist>
</appmodal>
<appmodal
    name="orderDetailsModal" 
    :width="1080"
>
    <!-- <x-order-details></x-order-details> -->
</appmodal>