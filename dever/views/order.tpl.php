<!DOCTYPE html>
<html>
<head>

    <link href="/css/materialize.css" rel="stylesheet" media="all"/>
    <link href="/css/style.css" rel="stylesheet" media="all"/>
    <title><?= $order['title'] ?> - Заказ №<?= $order['id'] ?></title>

    <style>
        #map {
            height: 100vh;
            width: 100%;
        }
    </style>
</head>
<body onload="initMap()">
<nav class="cyan">
    <div class="nav-wrapper">
        <a href="#" class="brand-logo">Dever</a>
        <ul id="nav-mobile" class="right hide-on-med-and-down">
        </ul>
    </div>
</nav>
<div class="progress" style="height:12vmin;">
    <div class="indeterminate orange"></div>
    <div class="determinate red" style="width: 70%"></div>
    <div style="position: absolute;">
        <h1 class="" style="margin:0; font-size:10vmin;"><?= $order['title'] ?></h1>
    </div>
</div>


<div class="row">
    <div class="center-align">
            <h5><?= $order['from'] ?> ------> <?= $order['to'] ?></h5>
    </div>
    <div class="center-align">
            <h5><иконка курьера> <?= $courier['name'] ?> <?= $courier['rating'] ?> </h5>
    </div>
</div>
<div id="map"></div>
<script>
    function initMap() {
        var uluru = {lat: <?=$order["position"]["lat"]?>, lng: <?=$order["position"]["lon"]?>};
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 17,
            center: uluru
        });
        var marker = new google.maps.Marker({
            position: uluru,
            map: map
        });
    }

</script>
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBrrqIGvmbrhu05yak4mqtmI74M5nHFQeM&callback=initMap">
</script>
</body>
</html>
