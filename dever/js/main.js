$(function () {
    var options = {
        responsive: true,
        title: {
            display: false
        },
        tooltips: {
            display: false
        },
        hover: {
            mode: 'nearest',
            intersect: false,
            backgroundColor: 'rgba(0, 0, 0, 0)'
        },
        legend:{
            display:false
        },
        axes: {
            display: false
        },
        scales: {
            xAxes: [{
                display: false
            }],
            yAxes: [{
                display: false
            }]
        }
    };
    new Chart(document.getElementById("temperature").getContext("2d"), {type: 'line', data: temp_data, options: options});
    new Chart(document.getElementById("humidity").getContext("2d"), {type: 'line', data: hum_data, options: options});
    new Chart(document.getElementById("overload").getContext("2d"), {type: 'line', data: over_data, options: options});

    var map = new google.maps.Map(document.getElementById('courier_map'), {
        zoom: 17,
        center: uluru
    });
    var marker = new google.maps.Marker({
        position: uluru,
        map: map
    });
    $(".right .head a").click(function () {
        $id = $(this).attr("href");
        $(".right .head a").removeClass("active");
        $(this).addClass("active");
        $(".tab").removeClass("active");
        $($id).addClass("active");
        return false;
    });
});