@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">Report - Reward Performance</div>
                <div class="card-body">
                    <div class="row justify-content-between hide-print">
                        <div class="col-lg-6 col-6">
                            <a href="{{route('report.reward_performance')}}" class="small-box bg-info p-4">
                                <div class="inner">
                                    <h4>Redemption</h4>

                                </div>
                            </a>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-6 col-6">
                            <a href="{{route('report.ads_click')}}" class="small-box bg-success p-4">
                                <div class="inner">
                                    <h4>Ads Click</h4>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="row most-left">
                        <div class="row most-left hide-print">
                            <label class="mr-2">Date</label>
                            <input type="date" id="date_from" name="date_from">
                            <label class="ml-2 mr-2">to</label>
                            <input type="date" id="date_to" name="date_to">

                            <label class="ml-4" for="format">Format:</label>

                            <select class="ml-2" name="format" id="format">
                                <option value="Month">Month</option>
                                <option value="Week">Week</option>
                            </select>

                            <a href="#" onclick="showData()" id="btn_show" class="btn btn-xs btn-info ml-2">View</a>

                            <a href="#" onclick="printReport()" id="btn_export" class="btn btn-xs btn-success ml-2">PDF</a>
                            <a href="#" onclick="exportCsv()" id="btn_csv" class="btn btn-xs btn-success ml-2">CSV</a>
                        </div>
                    </div>

                    <h5 class="text-center mt-5">Total User Click For Each Promotions </h5>
                    <canvas id="chartTotalClicksPromotion" width="300"></canvas>

                    <br>
                    <br>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    var myChart = '';
    var format = 'Month';
    var labels = [];
    var datasets = [];
    var date_from = '';
    var date_to = '';

    function exportCsv() {
        labelsArr = [];
        labelsArr.push(labels);

        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "Total User Click For Each Promotions - " + format + "\r\n";

        if (date_from != '' && date_to != '') {
            csvContent += date_from + ' to ' + date_to + "\r\n";
        }

        labelsArr.forEach(function(rowArray) {
            let row = ' ,' + rowArray.join(",");
            console.log(rowArray)

            csvContent += row + "\r\n";
        });
        datasets.forEach(function(rowArray) {
            let row = rowArray.label + ',' + rowArray.data.join(",");
            csvContent += row + "\r\n";
        });




        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "ads_click.csv");
        document.body.appendChild(link);
        link.click()
    }

    function showData() {
        date_from = $('#date_from').val();
        date_to = $('#date_to').val();
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            url: '{{ route("report.get_ads_click_data") }}',
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                format: format,
                date_from: date_from,
                date_to: date_to
            },
            success: function(data) {
                datasets = [];

                if (format == 'Month') {
                    labels = data[0];
                    var dataTotal = data[1];
                    var colors = data[2];
                    var promotion_ids = data[3];
                    var promotion_title = data[4];
                    var newArray = [];
                    datasets = [];
                    for (let x = 0; x < promotion_ids.length; x++) {
                        var newArray1 = [];
                        for (let y = 0; y < dataTotal.length; y++) {
                            if (dataTotal[y][x] == undefined)
                                newArray1.push(0);
                            else
                                newArray1.push(dataTotal[y][x]);
                        }
                        newArray.push(newArray1);
                    }
                    for (let i = 0; i < newArray.length; i++) {
                        datasets.push({
                            label: promotion_title[i],
                            borderColor: colors[i],
                            pointBackgroundColor: colors[i],
                            backgroundColor: colors[i],
                            data: Object.values(newArray[i]),
                            fill: 'transparent',
                            minBarLength: 7,
                        })
                    }


                } else if (format == 'Week') {
                    var promotionWeekClick = data[0];
                    labels = data[1];
                    var promotion_ids = data[2];
                    var promotion_title = data[3];
                    var colors = data[4];
                    var newArray = [];
                    datasets = [];
                    for (let x = 0; x < promotion_ids.length; x++) {
                        var newArray1 = [];
                        for (let y = 0; y < promotionWeekClick.length; y++) {
                            if (promotionWeekClick[y][x] == undefined)
                                newArray1.push(0);
                            else
                                newArray1.push(promotionWeekClick[y][x]);
                        }
                        newArray.push(newArray1);
                    }
                    for (let i = 0; i < newArray.length; i++) {
                        datasets.push({
                            label: promotion_title[i],
                            borderColor: colors[i],
                            pointBackgroundColor: colors[i],
                            backgroundColor: colors[i],
                            data: Object.values(newArray[i]),
                            fill: 'transparent',
                            minBarLength: 7,
                        })
                    }
                }
                var ctx = document.getElementById('chartTotalClicksPromotion');

                if (myChart) myChart.destroy();
                myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: {
                        plugins: {
                            labels: false
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                },
                            }],

                        },

                    }
                });
            },
            error: function(data) {
                console.log(data.responseJSON.message);
            }
        });
    }

    $(document).ready(function() {
        showData();
    })


    jQuery('#format').change(function() {
        format = $('#format').find(":selected").val();
    });

    function beforePrint() {
        for (const id in Chart.instances) {
            Chart.instances[id].resize()
        }
    }

    if (window.matchMedia) {
        let mediaQueryList = window.matchMedia('print')
        mediaQueryList.addListener((mql) => {
            if (mql.matches) {
                beforePrint()
            }
        })
    }

    window.onbeforeprint = beforePrint;

    function printReport() {
        window.print();
    }
</script>
@endsection