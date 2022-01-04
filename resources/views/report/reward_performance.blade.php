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

                    <h5 class="text-center mt-5">Total Redeemed Coins by Users</h5>
                    <canvas id="chartTotalCollectedTransaction" width="300"></canvas>

                    <br>
                    <br>
                    <h5 class="text-center mt-3">Total Redeemed Coins Across States</h5>
                    <canvas id="chartTotalCollectedTransactionState" width="300"></canvas>

                    <br>
                    <br>
                    <label for="state">State:</label>

                    <select class="ml-2" name="state" id="state">
                        <option value="">Select a state</option>
                        @foreach ($states as $value)
                        <option value="{{$value['id']}}">{{$value['name']}}</option>
                        @endforeach
                    </select>
                    <h5 class="text-center mt-3">Total Redeemed Coins Across District</h5>
                    <canvas id="chartTotalCollectedTransactionDistrict" width="300"></canvas>

                    <br>
                    <br>
                    <h5 class="text-center mt-3">Total Number of Redemption by Categories</h5>
                    <canvas id="chartRedemptionByCategory" width="300"></canvas>

                    <br>
                    <br>
                    <label for="state">State:</label>
                    <select class="ml-2" name="state_for_redemption" id="state_for_redemption">
                        <option value="">Select a state</option>
                        @foreach ($states as $value)
                        <option value="{{$value['id']}}">{{$value['name']}}</option>
                        @endforeach
                    </select>
                    <h5 class="text-center mt-3">Total Number of Redemption by Categories in State</h5>
                    <canvas id="chartRedemptionByCategoryState" width="300"></canvas>


                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    var myChart2 = '';
    var myChart = '';
    var myChart1 = '';
    var myChart3 = '';
    var myChart4 = '';
    var format = 'Month';
    var labels = [];
    var datasets = [];
    var dataset = [];
    var dataset2 = [];
    var dataset3 = [];
    var dataset_category = [];
    var state = '';
    var date_from = '';
    var date_to = '';
    var tableData = [];

    function exportCsv() {
        state = $('#state').find('option:selected').text();
        state_for_redemption = $('#state_for_redemption').find('option:selected').text();
        labelsArr = [];
        labelsArr.push(labels);

        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "Redemption - " + format + "\r\n";

        if (date_from != '' && date_to != '') {
            csvContent += date_from + ' to ' + date_to + "\r\n";
        }

        csvContent += "\r\n" + "Total Redeemed Coins by Users " + "\r\n";
        labelsArr.forEach(function(rowArray) {
            let row = rowArray.join(",");
            csvContent += ' ,' + row + "\r\n";
        });

        datasets.forEach(function(rowArray) {
            let row = rowArray.label + ',' + rowArray.data.join(",");
            csvContent += row + "\r\n";
        });

        csvContent += "\r\n" + "Total Redeemed Coins Across States " + "\r\n";
        dataset.forEach(function(rowArray) {
            let row = rowArray.label + ',' + rowArray.data.join(",");
            csvContent += row + "\r\n";
        });

        if (state != 'Select a state') {
            csvContent += "\r\n";
            csvContent += state + "\r\n";
            dataset2.forEach(function(rowArray) {
                let row = rowArray.label + ',' + rowArray.data.join(",");
                csvContent += row + "\r\n";
            });

        }

        csvContent += "\r\n" + "Total Number of Redemption by Categories" + "\r\n ";
        dataset_category.forEach(function(rowArray) {
            let row = rowArray.label + ',' + rowArray.data.join(",");
            csvContent += row + "\r\n";
        });

        if (state_for_redemption != 'Select a state') {
            csvContent += "\r\n";
            csvContent += state_for_redemption + "\r\n";
            dataset3.forEach(function(rowArray) {
                let row = rowArray.label + ',' + rowArray.data.join(",");
                csvContent += row + "\r\n";
            });

        }

        csvContent += "\r\n" + "User with points redeemed" + "\r\nEmail,Total Points Redeemed\r\n";

        tableData.forEach(function(rowArray) {
            let row = rowArray.email + ',' + rowArray.total;
            csvContent += row + "\r\n";
        });

        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "redemption.csv");
        document.body.appendChild(link);
        link.click()
    }

    function showData() {
        date_from = $('#date_from').val();
        date_to = $('#date_to').val();
        var chartType = '';
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            url: '{{ route("report.get_reward_performance_data") }}',
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                format: format,
                date_from: date_from,
                date_to: date_to
            },
            success: function(data) {
                var state_id = $('#state').find(":selected").val();
                if (state_id != '')
                    showStateData();
                var state_id1 = $('#state_for_redemption').find(":selected").val();
                if (state_id1 != '')
                    showStateRedemptionData();
                if (format == 'Month' && (date_from == '' && date_to == '')) {
                    var companyMonth = data[0];
                    var individualMonth = data[1];
                    var userMonth = data[2];
                    labels = data[3];
                    var allStatesName = data[4];
                    var data_only = data[5];
                    var colors = data[6];
                    var transactionMonthAllDataOnly = data[7];
                    var categoryLabel = data[8];
                    var redemptionByMonthInCategory = data[9];
                    var colors_category = data[10];
                    tableData = data[11];
                    var ctx = document.getElementById('chartTotalCollectedTransaction');
                    datasets = [];
                    chartType = 'line'
                    datasets = [{
                            data: companyMonth,
                            label: 'B2B',
                            borderColor: '#ff7605',
                            fill: 'transparent',
                            pointBackgroundColor: '#ff7605',
                            backgroundColor: '#ff7605',
                            hoverOffset: 10,


                        },
                        {
                            data: individualMonth,
                            label: 'B2C',
                            fill: 'transparent',
                            pointBackgroundColor: '#035AA6',
                            backgroundColor: '#035AA6',
                            borderColor: '#035AA6',
                            hoverOffset: 10,


                        },
                        {
                            data: userMonth,
                            label: 'Total',
                            fill: 'transparent',
                            pointBackgroundColor: '#BDBDBD',
                            backgroundColor: '#BDBDBD',
                            borderColor: '#BDBDBD',
                            hoverOffset: 10,


                        }
                    ]

                    if (myChart) myChart.destroy();
                    myChart = new Chart(ctx, {
                        type: chartType,
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
                    dataset = [];
                    for (var i = 0; i < data_only.length; i++) {
                        dataset.push({
                            label: allStatesName[i],
                            borderColor: colors[i],
                            pointBackgroundColor: colors[i],
                            backgroundColor: colors[i],
                            data: Object.values(data_only[i]),
                            fill: 'transparent',
                            minBarLength: 7,
                        })
                    }
                    dataset.push({
                        label: 'Total',
                        borderColor: '#bdbdbd',
                        pointBackgroundColor: '#bdbdbd',
                        backgroundColor: '#bdbdbd',
                        data: transactionMonthAllDataOnly,
                        fill: 'transparent',
                        minBarLength: 7,
                    })
                    var ctx1 = document.getElementById('chartTotalCollectedTransactionState');
                    if (myChart1) myChart1.destroy();
                    myChart1 = new Chart(ctx1, {
                        type: chartType,
                        data: {
                            labels: labels,
                            datasets: dataset

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
                    dataset_category = [];
                    var newArray = [];
                    for (let x = 0; x < categoryLabel.length; x++) {
                        var newArray1 = [];
                        for (let y = 0; y < redemptionByMonthInCategory.length; y++) {
                            newArray1.push(redemptionByMonthInCategory[y][x]);
                        }
                        newArray.push(newArray1);
                    }

                    for (let i = 0; i < newArray.length; i++) {
                        dataset_category.push({
                            label: categoryLabel[i],
                            borderColor: colors[i],
                            pointBackgroundColor: colors[i],
                            backgroundColor: colors[i],
                            data: Object.values(newArray[i]),
                            fill: 'transparent',
                            minBarLength: 7,
                        })
                    }

                    var ctx3 = document.getElementById('chartRedemptionByCategory');
                    if (myChart3) myChart3.destroy();
                    myChart1 = new Chart(ctx3, {
                        type: chartType,
                        data: {
                            labels: labels,
                            datasets: dataset_category

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
                } else if (format == 'Week' || (date_to != '' && date_from != '')) {
                    var companyMonth = data[0];
                    var individualMonth = data[1];
                    var userMonth = data[2];
                    labels = data[3];
                    var allStatesName = data[4];
                    var dataMonth = data[5];
                    var colors = data[6];
                    var categoryLabel = data[7];
                    var redemptionByWeekInCategory = data[8];
                    tableData = data[9];
                    var ctx = document.getElementById('chartTotalCollectedTransaction');
                    datasets = [];
                    chartType = 'line'
                    datasets = [{
                            data: companyMonth,
                            label: 'B2B',
                            borderColor: '#ff7605',
                            fill: 'transparent',
                            pointBackgroundColor: '#ff7605',
                            backgroundColor: '#ff7605',
                            hoverOffset: 10,


                        },
                        {
                            data: individualMonth,
                            label: 'B2C',
                            fill: 'transparent',
                            pointBackgroundColor: '#035AA6',
                            backgroundColor: '#035AA6',
                            borderColor: '#035AA6',
                            hoverOffset: 10,


                        },
                        {
                            data: userMonth,
                            label: 'Total',
                            fill: 'transparent',
                            pointBackgroundColor: '#BDBDBD',
                            backgroundColor: '#BDBDBD',
                            borderColor: '#BDBDBD',
                            hoverOffset: 10,


                        }
                    ]

                    if (myChart) myChart.destroy();
                    myChart = new Chart(ctx, {
                        type: chartType,
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
                    dataset = [];
                    for (var i = 0; i < dataMonth.length; i++) {

                        dataset.push({
                            label: allStatesName[i],
                            borderColor: colors[i],
                            pointBackgroundColor: colors[i],
                            backgroundColor: colors[i],
                            data: Object.values(dataMonth[i]),
                            fill: 'transparent',
                            minBarLength: 7,


                        })
                    }
                    dataset.push({
                        label: 'Total',
                        borderColor: '#bdbdbd',
                        pointBackgroundColor: '#bdbdbd',
                        backgroundColor: '#bdbdbd',
                        data: userMonth,
                        fill: 'transparent',
                        minBarLength: 7,


                    })

                    var ctx1 = document.getElementById('chartTotalCollectedTransactionState');
                    if (myChart1) myChart1.destroy();
                    myChart1 = new Chart(ctx1, {
                        type: chartType,
                        data: {
                            labels: labels,
                            datasets: dataset

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

                    dataset_category = [];
                    var newArray = [];
                    for (let x = 0; x < categoryLabel.length; x++) {
                        var newArray1 = [];
                        for (let y = 0; y < redemptionByWeekInCategory.length; y++) {
                            newArray1.push(redemptionByWeekInCategory[y][x]);
                        }
                        newArray.push(newArray1);
                    }

                    for (let i = 0; i < newArray.length; i++) {
                        dataset_category.push({
                            label: categoryLabel[i],
                            borderColor: colors[i],
                            pointBackgroundColor: colors[i],
                            backgroundColor: colors[i],
                            data: Object.values(newArray[i]),
                            fill: 'transparent',
                            minBarLength: 7,
                        })
                    }

                    var ctx3 = document.getElementById('chartRedemptionByCategory');
                    if (myChart3) myChart3.destroy();
                    myChart1 = new Chart(ctx3, {
                        type: chartType,
                        data: {
                            labels: labels,
                            datasets: dataset_category

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


                }
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

    jQuery('#state').change(function() {
        showStateData();
    });

    jQuery('#state_for_redemption').change(function() {
        showStateRedemptionData();
    });


    function showStateData() {
        var date_from = $('#date_from').val();
        var date_to = $('#date_to').val();
        var state_id = $('#state').find(":selected").val();
        if (state_id == '') {
            myChart2.destroy();
            return;
        }
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            url: '{{ route("report.get_reward_performance_district_data") }}',
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                state_id: state_id,
                format: format,
                date_from: date_from,
                date_to: date_to
            },
            success: function(data) {
                console.log(data);
                var city = data[0];
                var colors = data[1];
                var chartData = data[2];
                var labels = data[3];
                var allCityData = data[4];
                dataset2 = [];
                for (var i = 0; i < chartData.length; i++) {
                    dataset2.push({
                        label: city[i],
                        borderColor: colors[i],
                        pointBackgroundColor: colors[i],
                        backgroundColor: colors[i],
                        data: Object.values(chartData[i]),
                        fill: 'transparent',
                        minBarLength: 7,
                    })
                }
                dataset2.push({
                    label: 'Total',
                    borderColor: '#bdbdbd',
                    pointBackgroundColor: '#bdbdbd',
                    backgroundColor: '#bdbdbd',
                    data: allCityData,
                    fill: 'transparent',
                    minBarLength: 7,

                })

                var ctx1 = document.getElementById('chartTotalCollectedTransactionDistrict');
                if (myChart2) myChart2.destroy();

                myChart2 = new Chart(ctx1, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: dataset2
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
    };

    function showStateRedemptionData() {
        var date_from = $('#date_from').val();
        var date_to = $('#date_to').val();
        var state_id = $('#state_for_redemption').find(":selected").val();
        if (state_id == '') {
            myChart4.destroy();
            return;
        }
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            url: '{{ route("report.get_redemption_by_category_state") }}',
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                state_id: state_id,
                format: format,
                date_from: date_from,
                date_to: date_to
            },
            success: function(data) {
                console.log(data);
                var labels = data[0];
                var chartData = data[1];
                var categoryLabel = data[2];
                var colors = data[3];

                dataset3 = [];
                var newArray = [];
                for (let x = 0; x < categoryLabel.length; x++) {
                    var newArray1 = [];
                    for (let y = 0; y < chartData.length; y++) {
                        newArray1.push(chartData[y][x]);
                    }
                    newArray.push(newArray1);
                }

                for (let i = 0; i < newArray.length; i++) {
                    dataset3.push({
                        label: categoryLabel[i],
                        borderColor: colors[i],
                        pointBackgroundColor: colors[i],
                        backgroundColor: colors[i],
                        data: Object.values(newArray[i]),
                        fill: 'transparent',
                        minBarLength: 7,
                    })
                }


                var ctx4 = document.getElementById('chartRedemptionByCategoryState');
                if (myChart4) myChart4.destroy();

                myChart4 = new Chart(ctx4, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: dataset3
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
    };

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