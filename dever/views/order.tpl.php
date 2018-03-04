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
        <div class="state">
            <span class="state_percent"><?= $order['state'] ?></span>
        </div>
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
                    <h3>Температура</h3>
                    <canvas id="temperature"></canvas>
                    <h3>Влажность</h3>
                    <canvas id="humidity"></canvas>
                    <h3>Сохранность</h3>
                    <canvas id="overload"></canvas>
                </div>
                <div id="courier" class="tab">
                    <div class="space"></div>
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
    var uluru = {lat: ' . $order["position"][1] . ', lng: ' . $order["position"][0] . '};
    var temp_data={
        labels: [\'' . implode("','", $l) . '\'],
        datasets: [{
            data: [' . implode(",", $t) . '],
            borderColor: window.chartColors.red,
            borderWidth: 1
        }]
    };
    var hum_data={
        labels: [\'' . implode("','", $l) . '\'],
        datasets: [{
            data: [' . implode(",", $h) . '],
            borderColor: window.chartColors.green,
            borderWidth: 1
        }]
    };
    var over_data={
        labels: [\'' . implode("','", $l) . '\'],
        datasets: [{
            data: [' . implode(",", $a) . '],
            fill: false,
            borderColor: window.chartColors.blue,
            borderWidth: 1
        },{
            data: [' . implode(",", $o) . '],
            fill: true,
            backgroundColor: window.chartColors.red,
            borderColor: window.chartColors.red,
            borderWidth: 3
        }]
    };
    </script>'; ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBrrqIGvmbrhu05yak4mqtmI74M5nHFQeM"></script>
<script src="/js/Chart.min.js"></script>
<script src="/js/main.js?<?= time() ?>"></script>
</body>
</html>
