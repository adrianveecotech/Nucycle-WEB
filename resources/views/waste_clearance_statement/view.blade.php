@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col">

            <div class="a4-print" style="padding : 50px ;width: 210mm;height: 297mm; background-color:white;">
                <div class="row most-left">
                    <a href="#" onclick="printReport()" id="btn_export" class="btn btn-xs btn-success ml-2 hide-print">PDF</a>
                </div>
                <p style="text-align: center;">Nuplas Solutions Sdn. Bhd.</p>
                <p style="text-align: center;">Address: {{$company_info->address}}</p>
                <p style="text-align: center;">Tel: {{$company_info->phone}}</p>
                <p>&nbsp;</p>
                <p style="text-align: center;">{{Helper::lpadClearanceId($schedule->id)}}</p>
                <p style="text-align: right;">Date: {{explode(' ',$schedule->completed_at)[0]}}</p>
                <p>Sold To: {{$schedule->buyer_name}}</p>
                <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {{$schedule->buyer_phone_number}}</p>
                <p>&nbsp;</p>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td>
                                <p><strong>Item No</strong></p>
                            </td>
                            <td>
                                <p><strong>Description</strong></p>
                            </td>
                            <td>
                                <p><strong>UOM</strong></p>
                            </td>
                            <td>
                                <p><strong>Qty</strong></p>
                            </td>
                        </tr>
                        @foreach($items as $index => $item)
                        <tr>
                            <td>
                                <p>{{$index + 1}}</p>
                            </td>
                            <td>
                                <p>{{$item->name}}</p>
                            </td>
                            <td>
                                <p>Kg</p>
                            </td>
                            <td>
                                <p>{{number_format((float)$item->weight, 2, '.', '')}}</p>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <p>&nbsp;</p>
                <p>Remarks:</p>
                <ul>
                    <li>Goods must be fully inspected at the time of delivery or pick up.</li>
                    <li>No return allowed after goods received.</li>
                    <li>This is digitally generated order with pin code. No Signature required.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">
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