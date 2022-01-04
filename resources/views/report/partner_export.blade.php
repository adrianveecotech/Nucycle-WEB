<!DOCTYPE html>
<html lang="{{setting('language','en')}}" dir="ltr">

<head>
    <meta charset="UTF-8">
    <title>NuCycle Admin Panel</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link rel="icon" type="image/png" href="{{asset('nucycle-admin/icon.png')}}" />
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('plugins/font-awesome/css/font-awesome.min.css')}}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Ionicons -->
    {{--<link href="https://unpkg.com/ionicons@4.1.2/dist/css/ionicons.min.css" rel="stylesheet">--}}
    {{--<!-- iCheck -->--}}
    {{--<link rel="stylesheet" href="{{asset('plugins/iCheck/flat/blue.css')}}">--}}
    {{--<!-- select2 -->--}}
    {{--<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">--}}
    <!-- Morris chart -->
    {{--<link rel="stylesheet" href="{{asset('plugins/morris/morris.css')}}">--}}
    <!-- jvectormap -->
    {{--<link rel="stylesheet" href="{{asset('plugins/jvectormap/jquery-jvectormap-1.2.2.css')}}">--}}
    <!-- Date Picker -->
    <link rel="stylesheet" href="{{asset('plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')}}">
    <!-- Daterange picker -->
    {{--<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker-bs3.css')}}">--}}
    {{--<!-- bootstrap wysihtml5 - text editor -->--}}


    @stack('css_lib')
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('dist/css/adminlte.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/bootstrap-sweetalert/sweetalert.css')}}">
    {{--<!-- Bootstrap -->--}}
    {{--<link rel="stylesheet" href="{{asset('plugins/bootstrap/css/bootstrap.min.css')}}">--}}

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,600" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/custom.css')}}">
    <link rel="stylesheet" href="{{asset('css/primary.css')}}">
    @yield('css_custom')

    <!-- jQuery -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.23/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.js"></script>

    <!-- <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script> -->

    <link href="{{asset('plugins/summernote-0.8.18-dist/summernote.min.css')}}" rel="stylesheet">
    <script src="{{asset('plugins/summernote-0.8.18-dist/summernote.min.js')}}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" integrity="sha512-d9xgZrVZpmmQlfonhQUvTR7lMPtO7NkZMkA0ABN3PHCbKA5nqylQ/yWlFAyY6hYgdF1Qh6nYiuADWwKB4C2WSw==" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.bundle.js" integrity="sha512-zO8oeHCxetPn1Hd9PdDleg5Tw1bAaP0YmNvPY8CwcRyUk7d7/+nyElmFrB6f7vg4f7Fv4sui1mcep8RIEShczg==" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.bundle.min.js" integrity="sha512-SuxO9djzjML6b9w9/I07IWnLnQhgyYVSpHZx0JV97kGBfTIsUYlWflyuW4ypnvhBrslz1yJ3R+S14fdCWmSmSA==" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.css" integrity="sha512-C7hOmCgGzihKXzyPU/z4nv97W0d9bv4ALuuEbSf6hm93myico9qa0hv4dODThvCsqQUmKmLcJmlpRmCaApr83g==" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js" integrity="sha512-hZf9Qhp3rlDJBvAKvmiG+goaaKRZA6LKUO35oK6EsM0/kjPK32Yw7URqrq3Q+Nvbbt8Usss+IekL7CRn83dYmw==" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.css" integrity="sha512-/zs32ZEJh+/EO2N1b0PEdoA10JkdC3zJ8L5FTiQu82LR9S/rOQNfQN7U59U9BC12swNeRAz3HSzIL2vpp4fv3w==" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/gh/emn178/chartjs-plugin-labels/src/chartjs-plugin-labels.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@1"></script>
    <!-- jQuery UI 1.11.4 -->
    {{--<script src="{{asset('https://code.jquery.com/ui/1.12.1/jquery-ui.min.js')}}"></script>--}}
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    {{--<script>--}}
    {{--$.widget.bridge('uibutton', $.ui.button)--}}
    {{--</script>--}}
    <!-- Bootstrap 4 -->
    <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    <!-- Sparkline -->
    {{--<script src="{{asset('plugins/sparkline/jquery.sparkline.min.js')}}"></script>--}}
    {{--<!-- iCheck -->--}}
    {{--<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>--}}
    {{--<!-- select2 -->--}}
    {{--<script src="{{asset('plugins/select2/select2.min.js')}}"></script>--}}
    <!-- jQuery Knob Chart -->
    {{--<script src="{{asset('plugins/knob/jquery.knob.js')}}"></script>--}}
    <!-- daterangepicker -->
    {{--<script src="{{asset('https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js')}}"></script>--}}
    {{--<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>--}}
    <!-- datepicker -->
    <script src="{{asset('plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <!-- Bootstrap WYSIHTML5 -->
    {{--<script src="{{asset('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')}}"></script>--}}
    <!-- Slimscroll -->
    <script src="{{asset('plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
    <script src="{{asset('plugins/bootstrap-sweetalert/sweetalert.min.js')}}"></script>
    <!-- FastClick -->
    {{--<script src="{{asset('plugins/fastclick/fastclick.js')}}"></script>--}}
    @stack('scripts_lib')
    <!-- AdminLTE App -->
    <script src="{{asset('dist/js/adminlte.js')}}"></script>

    <script src="{{asset('js/scripts.js')}}"></script>
    @stack('scripts')
</head>

<body style="height: 100%; background-color: #f9f9f9;" class="hold-transition sidebar-mini {{setting('theme_color')}}">
    <div id="page-content-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col">
                                <div class="card" id="card">
                                    <div class="card-header">Partner Report for merchant {{$merchant->name}}</div>
                                    <div class="card-body">

                                        <div class="row most-left">
                                            <a href="{{route('report.print_parter_report')}}" class="btn btn-xs btn-success ml-2 hide-print" onclick="event.preventDefault(); document.getElementById('submit-form').submit();">
                                                PDF
                                            </a>

                                            <form id="submit-form" action="{{ route('report.print_parter_report') }}" method="POST" class="hidden">
                                                @csrf

                                                @method('POST')
                                            </form>
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
        </div>
    </div>
    </div>
    </div>
</body>
</html>
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