@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">Report - On Site</div>
                <div class="card-body">
                    <div class="row hide-print">
                        <div class="col-lg-4 col-6">
                            <a href="{{route('report.collection.on_site')}}" class="small-box bg-info p-4">
                                <div class="inner">
                                    <h4>Collected Transaction</h4>

                                </div>
                            </a>
                        </div>
                        <div class="col-lg-4 col-6">
                            <a href="{{route('report.collection.on_site_collected_waste')}}" class="small-box bg-danger p-4">
                                <div class="inner">
                                    <h4>Collected Waste</h4>

                                </div>
                            </a>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-4 col-6">
                            <!-- small box -->
                            <a href="{{route('report.collection.on_site_waste_selling')}}" class="small-box bg-success p-4">
                                <div class="inner">
                                    <h4>Waste Selling</h4>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="row most-left hide-print">
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

                    <h5 class="text-center mt-3">Total Waste Collected and Sold</h5>
                    <canvas id="chartTotalCollectedTransaction" width="300"></canvas>

                    <br>
                    <br>

                    <h5 id="chart3" class="text-center mt-3">Total Weight of Different Recycling Category Waste Collected and Sold Across Collection Hub Across District</h5>
                    <br>
                    <div>
                        <label for="state">State:</label>
                        <select class="ml-2" name="state" id="state">
                            <option value="">Select a state</option>
                            @foreach ($allStates as $value)
                            <option value="{{$value['id']}}">{{$value['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="chartDiv">
                    </div>
                    <!-- <canvas id="chartTotalCollectedTransactionDistrict" width="300"></canvas> -->


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
    var format = 'Month';
    var label = [];
    var dataset = [];
    var dataset_total = [];
    var state = '';
    var category = [];
    var date_from = '';
    var date_to = '';

    function exportCsv() {
        state = $('#state').find('option:selected').text();
        labelsArr = [];
        labelsArr.push(label);

        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "On Site Total Waste Collected and Sold - " + format + "\r\n";
        if (date_from != '' && date_to != '') {
            csvContent += date_from + ' to ' + date_to + "\r\n\r\n";
        }

        labelsArr.forEach(function(rowArray) {
            let row = rowArray.join(",");
            csvContent += ' ,' + row + "\r\n";
        });
        dataset.forEach(function(rowArray) {
            let row = rowArray.label + ',' + rowArray.data.join(",");
            csvContent += row + "\r\n";
        });
        if (state != 'Select a state') {
            csvContent += "\r\n" + state + "\r\n";
            dataset_total.forEach(function(rowArray, index) {
                csvContent += "\r\n" + category[index].name + "\r\n";
                rowArray.forEach(function(rowArray1) {
                    let row = rowArray1.label + ',' + rowArray1.data.join(",");
                    csvContent += row + "\r\n";
                });
            });
        }
        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "on_site_collected_waste.csv");
        document.body.appendChild(link);
        link.click()
    }

    function showData() {
        var chartType = '';
        date_from = $('#date_from').val();
        date_to = $('#date_to').val();
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            url: '{{ route("report.get_collection_waste_selling_data") }}',
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                format: format,
                hub_type: 0,
                date_from: date_from,
                date_to: date_to
            },
            success: function(data) {
                var state_id = $('#state').find(":selected").val();
                if (state_id != '')
                    showStateData();
                label = data[0];
                var collectedWaste = data[1];
                var soldWaste = data[2];
                var color = data[3];
                dataset = [];

                dataset.push({
                    label: 'Waste Selling',
                    borderColor: '#ff7605',
                    pointBackgroundColor: '#ff7605',
                    backgroundColor: '#ff7605',
                    data: Object.values(soldWaste),
                    fill: 'transparent',
                })

                dataset.push({
                    label: 'Collected Waste',
                    borderColor: '#035AA6',
                    pointBackgroundColor: '#035AA6',
                    backgroundColor: '#035AA6',
                    data: Object.values(collectedWaste),
                    fill: 'transparent',
                })

                var ctx = document.getElementById('chartTotalCollectedTransaction');
                if (myChart) myChart.destroy();
                myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: label,
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

    function showStateData() {
        $('#chartDiv').empty();
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
            url: '{{ route("report.get_collection_waste_selling_district") }}',
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                state_id: state_id,
                format: format,
                hub_type: 0,
                date_from: date_from,
                date_to: date_to
            },
            success: function(data) {
                var color = data[0];
                var hubs = data[1];
                var labels = data[2];
                var collectedWaste = data[3];
                var soldWaste = data[4];
                category = data[5];
                var newArray = [];
                var dataset_category = [];
                var newArray2 = [];
                var newArray3 = [];
                dataset_total = [];
                collectedWaste.forEach((element, index) => {
                    newArray = [];
                    for (let x = 0; x < hubs.length; x++) {
                        var newArray1 = [];
                        for (let y = 0; y < element.length; y++) {
                            newArray1.push(element[y][x]);
                        }
                        newArray.push(newArray1);
                    }
                    newArray2.push(newArray);
                });

                soldWaste.forEach((element, index) => {
                    newArray = [];
                    for (let x = 0; x < hubs.length; x++) {
                        var newArray1 = [];
                        for (let y = 0; y < element.length; y++) {
                            newArray1.push(element[y][x]);
                        }
                        newArray.push(newArray1);
                    }
                    newArray3.push(newArray);
                });

                newArray2.forEach((element, index) => {
                    dataset_category = [];
                    for (let i = 0; i < element.length; i++) {
                        dataset_category.push({
                            label: hubs[i].hub_name + ' Collected Waste',
                            borderColor: color[i],
                            pointBackgroundColor: color[i],
                            backgroundColor: color[i],
                            data: Object.values(element[i]),
                            fill: 'transparent',

                        })

                        dataset_category.push({
                            label: hubs[i].hub_name + ' Waste Selling',
                            borderColor: color[i + element.length],
                            pointBackgroundColor: color[i + element.length],
                            backgroundColor: color[i + element.length],
                            data: Object.values(newArray3[index][i]),
                            fill: 'transparent',

                        })

                    }
                    dataset_total.push(dataset_category);

                    $('#chartDiv').append('<h6 class="text-center mt-3">' + category[index].name + '</h6>');
                    $('#chartDiv').append(' <canvas id="myChart' + index + '" width="300" height="100"></canvas>');
                    $('#chartDiv').append('<br><br><br>');
                    var ctx = document.getElementById('myChart' + index);
                    var myChart = new Chart(ctx, {
                        type: 'bar',
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