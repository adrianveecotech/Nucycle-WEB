@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card" id="card">
                <div class="card-header">Partner Report for merchant {{$merchant->name}}</div>
                <div class="card-body">

                    <div class="row most-left">
                        <a href="#" onclick="printReport()" id="btn_export" class="btn btn-xs btn-success ml-2 hide-print">PDF</a>
                    </div>
                    <div class="row">
                        <div class="test col-6">
                            <h5 class="text-center mt-3">Total redeemed vouchers for past 3 months (Monthly)</h5>
                            <div class="test"> <canvas id="chartTotalRedeemedVoucher3MonthsMonthly" width="300"></canvas> </div>
                        </div>
                        <br>
                        <br>
                        <div class="test col-6">
                            <h5 class="text-center mt-3">Total redeemed vouchers for past 3 months (Weekly)</h5>
                            <div class="test"> <canvas id="chartTotalRedeemedVoucher3MonthsWeekly" width="300"></canvas></div>
                        </div>
                        <br>
                        <br>
                    </div>
                    <div class="row">
                        <div class="test col-6">
                            <h5 class="text-center mt-3">Total number of distinct users redeemed for past 3 months (Monthly)</h5>
                            <div class="test"> <canvas id="chartTotalDistinctUsersRedeemedMonthly" width="300"></canvas></div>
                        </div>
                        <br>
                        <br>
                        <div class="test col-6">
                            <h5 class="text-center mt-3">Total number of distinct users redeemed for past 3 months (Weekly)</h5>
                            <div class="test"> <canvas id="chartTotalDistinctUsersRedeemedWeekly" width="300"></canvas></div>
                        </div>
                        <br>
                        <br>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <h5 class="text-center mt-3">Top 3 states of redemption</h5>
                            <div class="col">
                                <div class="tbl-header">
                                    <table class="table table-striped" id="tableMain">
                                        <thead>
                                            <tr>
                                                <th><span>States</span></th>
                                                <th><span>Number of Redemption</span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($topStates as $state)
                                            <tr>
                                                <td class="lalign">{{$state->name}}</td>
                                                <td>{{$state->total}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <h5 class="text-center mt-3">Top 3 districts of redemption</h5>
                            <div class="col">
                                <div class="tbl-header">
                                    <table class="table table-striped" id="tableMain">
                                        <thead>
                                            <tr>
                                                <th><span>District</span></th>
                                                <th><span>Number of Redemption</span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($topDistricts as $district)
                                            <tr>
                                                <td class="lalign">{{$district->name}}</td>
                                                <td>{{$district->total}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <h5 class="text-center mt-3">Total number of redemption for previous month</h5>
                        <div class="test"> <canvas id="chartTotalNumberOfRedemptionPreviousMonth" width="300"></canvas></div>
                    </div>
                </div>
                <br>
                <br>
                <h3 class="text-center mt-3">Ads Campaign Report</h3>
                <div class="row">
                    <div class="test col-6">
                        <div class="test">
                            <h5 class="text-center mt-3">Total Reached and User Click (1 week)</h5>
                            <div class="test"> <canvas id="chartTotalReachAndUserClick1Week" width="300"></canvas></div>
                        </div>
                    </div>
                    <br>
                    <br>
                    <div class="test col-6">
                        <h5 class="text-center mt-3">Total Reached and User Click (2 weeks)</h5>
                        <canvas id="chartTotalReachAndUserClick2Weeks" width="300"></canvas>
                    </div>
                    <br>
                    <br>
                </div>
                <div class="row">
                    <div class="test col-6">
                        <h5 class="text-center mt-3">Total Reached and User Click (1 month)</h5>
                        <div class="test"> <canvas id="chartTotalReachAndUserMonth" width="300"></canvas></div>
                    </div>
                </div>
                <br>
                <br>
                <div class="row">
                    <div class="col-6">
                        <h5 class="text-center mt-3">Total 5 states of user clicks</h5>
                        <div class="tbl-header">
                            <table class="table table-striped" id="tableMain">
                                <thead>
                                    <tr>
                                        <th><span>States</span></th>
                                        <th><span>Number of clicks</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topStatesUserClicks as $click)
                                    <tr>
                                        <td class="lalign">{{$click->name}}</td>
                                        <td>{{$click->total}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <br>
                        <br>
                    </div>
                    <div class="col-6">
                        <h5 class="text-center mt-3">Total 5 districts of user clicks</h5>
                        <div class="tbl-header">
                            <table class="table table-striped" id="tableMain">
                                <thead>
                                    <tr>
                                        <th><span>Districts</span></th>
                                        <th><span>Number of clicks</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topDistrictsUserClicks as $click)
                                    <tr>
                                        <td class="lalign">{{$click->name}}</td>
                                        <td>{{$click->total}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-center mt-3">Subscription Based Report</h3>
                    <div class="tbl-header">
                        <div class="row">
                            @foreach($merchantsData as $key=>$value)
                            <div class="col-6">
                                <p class="text-center mt-3">{{$merchantCategory[$key]->name}}</p>
                                <table class="table table-striped" id="tableMain">
                                    <thead>
                                        <tr>
                                            <th><span>Ranking</span></th>
                                            <th><span>Number of redemption</span></th>
                                            <th><span>Merchant Location</span></th>
                                            <th><span>Highest User State</span></th>
                                            <th><span>Highest User District</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($value as $index=>$state)
                                        <tr>
                                            <td class="lalign">{{$index+1}}</td>
                                            <td>{{$state->total}}</td>
                                            <td>{{$state->merchant_location}}</td>
                                            <td>{{$state->highestUserByState}}</td>
                                            <td>{{$state->highestUserByCity}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
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
    var myChart1 = '';
    var myChart2 = '';
    var myChart3 = '';
    var myChart4 = '';
    var myChart5 = '';
    var myChart6 = '';
    var myChart7 = '';

    function showData() {
        var monthLabels = <?php echo json_encode($monthLabels); ?>;
        var weekLabel = <?php echo json_encode($weekLabel); ?>;
        var yearMonthWeekLabel = <?php echo json_encode($yearMonthWeekLabel); ?>;
        var colors = <?php echo json_encode($colors); ?>;
        var voucherRedeemedMonth = <?php echo json_encode($voucherRedeemedMonth); ?>;
        var userRedeemedMonth = <?php echo json_encode($userRedeemedMonth); ?>;
        var voucherRedeemedWeek = <?php echo json_encode($voucherRedeemedWeek); ?>;
        var userRedeemedWeek = <?php echo json_encode($userRedeemedWeek); ?>;
        var userRedeemedWeekLastMonth = <?php echo json_encode($userRedeemedWeekLastMonth); ?>;
        var forteenDaysLabel = <?php echo json_encode($forteenDaysLabel); ?>;
        var userReachForteenDays = <?php echo json_encode($userReachForteenDays); ?>;
        var sevenDaysLabel = <?php echo json_encode($sevenDaysLabel); ?>;
        var userReachSevenDays = <?php echo json_encode($userReachSevenDays); ?>;
        var userReachMonth = <?php echo json_encode($userReachMonth); ?>;
        var userClickMonth = <?php echo json_encode($userClickMonth); ?>;
        var userClickSevenDays = <?php echo json_encode($userClickSevenDays); ?>;
        var userClickForteenDays = <?php echo json_encode($userClickForteenDays); ?>;
        var twoDaysDifferentInMonth = <?php echo json_encode($twoDaysDifferentInMonth); ?>;
        var numberOfRedemptionIn2days = <?php echo json_encode($numberOfRedemptionIn2days); ?>;
        var userReachIn2days = <?php echo json_encode($userReachIn2days); ?>;
        var userClickIn2days = <?php echo json_encode($userClickIn2days); ?>;

        var ctx = document.getElementById('chartTotalRedeemedVoucher3MonthsMonthly');
        if (myChart) myChart.destroy();
        myChart = new Chart(ctx, {
            plugins: [ChartDataLabels],
            type: 'bar',
            data: {
                labels: monthLabels,
                datasets: [{
                    data: voucherRedeemedMonth,
                    label: 'Number of voucher redeemed',
                    borderColor: '#ff7605',
                    fill: 'transparent',
                    pointBackgroundColor: '#ff7605',
                    backgroundColor: '#ff7605',
                    hoverOffset: 10,
                    maxBarThickness: 70,
                }]
            },
            options: {
                plugins: {
                    labels: false,
                    datalabels: {
                        backgroundColor: function(context) {
                            return context.dataset.backgroundColor;
                        },
                        borderRadius: 4,
                        color: 'white',
                        font: {
                            weight: 'bold'
                        },
                        formatter: Math.round,
                        padding: 6
                    }
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
        var dataset = [];

        var ctx1 = document.getElementById('chartTotalRedeemedVoucher3MonthsWeekly');
        if (myChart1) myChart1.destroy();
        yearMonthWeekLabel.forEach((element, index) => {
            dataset.push({
                data: voucherRedeemedWeek[index],
                label: monthLabels[index],
                borderColor: colors[index],
                fill: 'transparent',
                pointBackgroundColor: colors[index],
                backgroundColor: colors[index],
                hoverOffset: 10,
                maxBarThickness: 70,
            })
        });
        myChart1 = new Chart(ctx1, {
            plugins: [ChartDataLabels],
            type: 'bar',
            data: {
                labels: weekLabel,
                datasets: dataset
            },
            options: {
                plugins: {
                    labels: false,
                    datalabels: {
                        backgroundColor: function(context) {
                            return context.dataset.backgroundColor;
                        },
                        borderRadius: 4,
                        color: 'white',
                        font: {
                            weight: 'bold'
                        },
                        formatter: Math.round,
                        padding: 6
                    }
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

        var ctx2 = document.getElementById('chartTotalDistinctUsersRedeemedMonthly');
        if (myChart2) myChart2.destroy();
        myChart2 = new Chart(ctx2, {
            plugins: [ChartDataLabels],
            type: 'bar',
            data: {
                labels: monthLabels,
                datasets: [{
                    data: userRedeemedMonth,
                    label: 'Number of users',
                    borderColor: '#ff7605',
                    fill: 'transparent',
                    pointBackgroundColor: '#ff7605',
                    backgroundColor: '#ff7605',
                    hoverOffset: 10,
                    maxBarThickness: 70,
                }]
            },
            options: {
                plugins: {
                    labels: false,
                    datalabels: {
                        backgroundColor: function(context) {
                            return context.dataset.backgroundColor;
                        },
                        borderRadius: 4,
                        color: 'white',
                        font: {
                            weight: 'bold'
                        },
                        formatter: Math.round,
                        padding: 6
                    }
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

        var dataset = [];

        var ctx3 = document.getElementById('chartTotalDistinctUsersRedeemedWeekly');
        if (myChart3) myChart3.destroy();
        yearMonthWeekLabel.forEach((element, index) => {
            dataset.push({
                data: userRedeemedWeek[index],
                label: monthLabels[index],
                borderColor: colors[index],
                fill: 'transparent',
                pointBackgroundColor: colors[index],
                backgroundColor: colors[index],
                hoverOffset: 10,
                maxBarThickness: 70,
            })
        });
        myChart3 = new Chart(ctx3, {
            plugins: [ChartDataLabels],
            type: 'bar',
            data: {
                labels: weekLabel,
                datasets: dataset
            },
            options: {
                plugins: {
                    labels: false,
                    datalabels: {
                        backgroundColor: function(context) {
                            return context.dataset.backgroundColor;
                        },
                        borderRadius: 4,
                        color: 'white',
                        font: {
                            weight: 'bold'
                        },
                        formatter: Math.round,
                        padding: 6
                    }
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
        var ctx4 = document.getElementById('chartTotalNumberOfRedemptionPreviousMonth');
        if (myChart4) myChart4.destroy();
        myChart4 = new Chart(ctx4, {
            plugins: [ChartDataLabels],
            type: 'line',
            data: {
                labels: twoDaysDifferentInMonth[0],
                datasets: [{
                    data: numberOfRedemptionIn2days,
                    label: 'Number of redemption',
                    borderColor: '#ff7605',
                    fill: 'transparent',
                    pointBackgroundColor: '#ff7605',
                    backgroundColor: '#ff7605',
                    hoverOffset: 10,
                    lineTension: 0,
                }]
            },
            options: {
                plugins: {
                    labels: false,
                    datalabels: {
                        backgroundColor: function(context) {
                            return context.dataset.backgroundColor;
                        },
                        borderRadius: 4,
                        color: 'white',
                        font: {
                            weight: 'bold'
                        },
                        formatter: Math.round,
                        padding: 6
                    }
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

        var ctx5 = document.getElementById('chartTotalReachAndUserClick1Week');
        if (myChart5) myChart5.destroy();
        myChart5 = new Chart(ctx5, {
            plugins: [ChartDataLabels],
            type: 'line',
            data: {
                labels: sevenDaysLabel,
                datasets: [{
                        data: userReachSevenDays,
                        label: 'User Reach',
                        borderColor: '#ff7605',
                        fill: 'transparent',
                        pointBackgroundColor: '#ff7605',
                        backgroundColor: '#ff7605',
                        hoverOffset: 10,
                        lineTension: 0,
                    },
                    {
                        data: userClickSevenDays,
                        label: 'User Clicks',
                        borderColor: '#b9b9b9',
                        fill: 'transparent',
                        pointBackgroundColor: '#b9b9b9',
                        backgroundColor: '#b9b9b9',
                        hoverOffset: 10,
                        lineTension: 0,
                    }
                ]
            },
            options: {
                plugins: {
                    datalabels: {
                        backgroundColor: function(context) {
                            return context.dataset.backgroundColor;
                        },
                        borderRadius: 4,
                        color: 'white',
                        font: {
                            weight: 'bold'
                        },
                        formatter: Math.round,
                        padding: 6
                    }
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

        var ctx6 = document.getElementById('chartTotalReachAndUserClick2Weeks');
        if (myChart6) myChart6.destroy();
        myChart6 = new Chart(ctx6, {
            plugins: [ChartDataLabels],
            type: 'line',
            data: {
                labels: forteenDaysLabel,
                datasets: [{
                        data: userReachForteenDays,
                        label: 'User Reach',
                        borderColor: '#ff7605',
                        fill: 'transparent',
                        pointBackgroundColor: '#ff7605',
                        backgroundColor: '#ff7605',
                        hoverOffset: 10,
                        lineTension: 0,

                    },
                    {
                        data: userClickForteenDays,
                        label: 'User Clicks',
                        borderColor: '#b9b9b9',
                        fill: 'transparent',
                        pointBackgroundColor: '#b9b9b9',
                        backgroundColor: '#b9b9b9',
                        hoverOffset: 10,
                        lineTension: 0,
                    }
                ]
            },
            options: {
                plugins: {
                    labels: false,
                    datalabels: {
                        backgroundColor: function(context) {
                            return context.dataset.backgroundColor;
                        },
                        borderRadius: 4,
                        color: 'white',
                        font: {
                            weight: 'bold'
                        },
                        formatter: Math.round,
                        padding: 6
                    }
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

        var ctx7 = document.getElementById('chartTotalReachAndUserMonth');
        if (myChart7) myChart7.destroy();
        myChart7 = new Chart(ctx7, {
            plugins: [ChartDataLabels],
            type: 'line',
            data: {
                labels: twoDaysDifferentInMonth[0],
                datasets: [{
                        data: userReachIn2days,
                        label: 'User Reach',
                        borderColor: '#ff7605',
                        fill: 'transparent',
                        pointBackgroundColor: '#ff7605',
                        backgroundColor: '#ff7605',
                        hoverOffset: 10,
                        lineTension: 0,

                    },
                    {
                        data: userClickIn2days,
                        label: 'User Clicks',
                        borderColor: '#b9b9b9',
                        fill: 'transparent',
                        pointBackgroundColor: '#b9b9b9',
                        backgroundColor: '#b9b9b9',
                        hoverOffset: 10,
                        lineTension: 0,
                    }
                ]
            },
            options: {
                plugins: {
                    labels: false,
                    datalabels: {
                        backgroundColor: function(context) {
                            return context.dataset.backgroundColor;
                        },
                        borderRadius: 4,
                        color: 'white',
                        font: {
                            weight: 'bold'
                        },
                        formatter: Math.round,
                        padding: 6
                    }
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

    $(document).ready(function() {
        showData();
        //avoid page break after the element
        document.getElementById("card").style.pageBreakAfter = "avoid";
        //avoid page break before the element
        document.getElementById("card").style.pageBreakBefore = "avoid";
        //avoid page break inside the element
        document.getElementById("card").style.pageBreakInside = "avoid";
    })

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

    function printReport() {
        window.print();
    }
</script>
@endsection