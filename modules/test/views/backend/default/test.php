<?php

use app\modules\admin\Module;
use app\modules\user\Module as UserModule;
use app\modules\url\UrlModule as UrlModule;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \app\modules\user\models\backend\User */

$this->title = Module::t('module', 'ADMIN');
?>
<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=68531ae1-9ce3-44cf-95f9-a2a922bf7358" type="text/javascript"></script>
<script src="/js/geodesy-master/latlon-spherical.js"></script>
<style>

    #map {
        width: 100%;
        height: 90%;
    }
</style>

<div id="map"></div>

<script>
    var myMap;


    // Дождёмся загрузки API и готовности DOM.
    ymaps.ready(init);

    function init () {
        lat0 = 58.665843;
        lon0 = 49.545688;
        var r = 250;
        var color_fill = "#DB709333";
        var c = 1;

        myMap = new ymaps.Map('map', {

            center: [58.603269, 49.636136],
            zoom: 13
        }, {
            searchControlProvider: 'yandex#search'
        });

        myMap.events.add('click', function (e) {

            var coords = e.get('coords');

            console.log('check', check_point(coords[0],coords[1] , curr_bounds[0][0],curr_bounds[0][1], curr_bounds[1][0],curr_bounds[1][1] ) );

        });


        var myCircle = new ymaps.Circle( [ [lat0,lon0], r ], {
        }, { draggable: false, fillColor: color_fill,strokeColor: "#990066",strokeOpacity: 0.6,strokeWidth: 2,idTarget:c });


        myMap.geoObjects.add(myCircle);


        var curr_bounds = myCircle.geometry.getBounds();


        var rectangle = new ymaps.Rectangle(curr_bounds, { hintContent:  c}, {
            coordRendering: "boundsPath",
            fillColor: color_fill,
            strokeWidth: 2,
            strokeOpacity: 0.6,
            idTarget:c
        });

        rectangle.events.add(['click',], function (e) {
          var coords = e.get('coords');

          console.log('check', check_point(coords[0],coords[1] , curr_bounds[0][0],curr_bounds[0][1], curr_bounds[1][0],curr_bounds[1][1] ) );

        });

        myMap.geoObjects.add(rectangle);

        function check_point(p_lat,p_lon, r0_lat,r0_lon,r1_lat,r1_lon ) {
            return ( p_lon > r0_lon && p_lon < r1_lon) && ( p_lat > r0_lat && p_lat < r1_lat );
        }





    }
</script>