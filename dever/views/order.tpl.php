<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/css/style.css?<?= time() ?>" rel="stylesheet" media="all"/>
    <title><?= $order['title'] ?> - Заказ №<?= $order['id'] ?></title>
</head>
<body>
<div class="header">
    <div class="logo"></div>
    <h2>DEVER</h2>
</div>

<div class="container">

    <div class="left">
        <div class="state"></div>
        <div class="state_text">
            <span class="bold">Оценка:</span>
            <span class="light">Выше среднего</span>
        </div>
    </div>

    <div class="right">
        <div class="tabs">
            <div class="head">
                <a href="#order">Заказ</a>
                <a href="#status" class="active">Статус</a>
                <a href="#courier">Курьер</a>
            </div>
            <div class="body">
                <div id="order" class="tab">

                </div>
                <div id="status" class="tab active">
                    <canvas id="temperature"></canvas>
                    <canvas id="humidity"></canvas>
                    <canvas id="overload"></canvas>
                </div>
                <div id="courier" class="tab">
                    <div id="courier_map"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
	echo '
<script>

window.chartColors = {
	red: \'rgb(255, 99, 132)\',
	orange: \'rgb(255, 159, 64)\',
	yellow: \'rgb(255, 205, 86)\',
	green: \'rgb(75, 192, 192)\',
	blue: \'rgb(54, 162, 235)\',
	purple: \'rgb(153, 102, 255)\',
	grey: \'rgb(201, 203, 207)\'
};
    var uluru = {lat: ' . $order["position"]["lat"] . ', lng: ' . $order["position"]["lon"] . '};
    var temp_data={
        labels: [\'' . implode("','", $l) . '\'],
        datasets: [{
            data: [' . implode(",", $t) . '],
            borderColor: \'rgba(255, 110, 64, 1)\',
            borderWidth: 1
        }]
    };
    var hum_data={
        labels: [\'' . implode("','", $l) . '\'],
        datasets: [{
            data: [' . implode(",", $h) . '],
            borderColor: \'rgba(110, 255, 64, 1)\',
            borderWidth: 1
        }]
    };
    var over_data={
        labels: [\'' . implode("','", $l) . '\'],
        datasets: [{
            data: [' . implode(",", $o) . '],
					fill: false,
					backgroundColor: window.chartColors.blue,
					borderColor: window.chartColors.blue,
            borderWidth: 1
        }]
    };
    </script>'; ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBrrqIGvmbrhu05yak4mqtmI74M5nHFQeM"></script>
<script src="/js/Chart.min.js"></script>
<script src="/js/main.js?<?= time() ?>"></script>
</body>
</html>
