@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">Report - Total</div>
                <div class="card-body">
                    <div class="row hide-print">
                        <div class="col-lg-4 col-6">
                            <a href="{{route('report.collection.total')}}" class="small-box bg-info p-4">
                                <div class="inner">
                                    <h4>Collected Transaction</h4>

                                </div>
                            </a>
                        </div>
                        <div class="col-lg-4 col-6">
                            <a href="{{route('report.collection.total_collected_waste')}}" class="small-box bg-danger p-4">
                                <div class="inner">
                                    <h4>Collected Waste</h4>

                                </div>
                            </a>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-4 col-6">
                            <!-- small box -->
                            <a href="{{route('report.collection.total_waste_selling')}}" class="small-box bg-success p-4">
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

                    <h5 class="text-center mt-3">Total Weight Collected By Recycling Type</h5>
                    <canvas id="chartTotalCollectedTransaction" width="300"></canvas>

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
    var myChart2 = '';
    var myChart = '';
    var myChart1 = '';
    var type = 'User';
    var format = 'Month';
    var label = [];
    var dataset = [];
    var dataset_total = [];
    var dataset2 = [];
    var newArray2 = [];
    var category = [];
    var date_from = '';
    var date_to = '';

    function exportCsv() {
        labelsArr = [];
        labelsArr.push(label);

        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "On Site Collected Waste- " + type + " - " + format + "\r\n";
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
        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "total_collected_waste.csv");
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
            url: '{{ route("report.get_collection_collected_waste_data") }}',
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                format: format,
                hub_type: '',
                date_from: date_from,
                date_to: date_to
            },
            success: function(data) {
                label = data[0];
                var dataChart = data[1];
                var categoryLabel = data[2];
                var categoryColor = data[3];
                var dataChartTotal = data[4];
                var totalColor = data[5];
                dataset = [];
                var newArray = [];
                for (let x = 0; x < categoryLabel.length; x++) {
                    var newArray1 = [];
                    for (let y = 0; y < dataChart.length; y++) {
                        newArray1.push(dataChart[y][x]);
                    }
                    newArray.push(newArray1);
                }

                for (let i = 0; i < newArray.length; i++) {
                    dataset.push({
                        label: categoryLabel[i].name,
                        borderColor: categoryColor[i],
                        pointBackgroundColor: categoryColor[i],
                        backgroundColor: categoryColor[i],
                        data: Object.values(newArray[i]),
                        fill: 'transparent',
                        minBarLength: 7,
                    })
                }

                dataset.push({
                    label: 'Total',
                    borderColor: totalColor[0],
                    pointBackgroundColor: totalColor[0],
                    backgroundColor: totalColor[0],
                    data: Object.values(dataChartTotal),
                    fill: 'transparent',
                    minBarLength: 7,
                })

                var ctx = document.getElementById('chartTotalCollectedTransaction');
                if (myChart) myChart.destroy();
                myChart = new Chart(ctx, {
                    type: 'line',
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