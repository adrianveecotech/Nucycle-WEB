@if (\Session::has('successMsg'))
<div class="alert alert-success">
    <ul>
        <li>{!! \Session::get('successMsg') !!}</li>
    </ul>
</div>
@endif
<div class="row justify-content-center">
    <div class="col">
        <div id="wrapper">
            <div class="tbl-header">
                <table class="table table-striped" id="tableMain">
                    <thead>
                        <tr>
                            <th><span>Reward Category</span></th>
                            <th><span>Voucher Name</span></th>
                            <th><span>Supplier ID</span></th>
                            <th><span>Supplier Name</span></th>
                            <th><span>Entitlement Date</span></th>
                            <th><span>NUP generation code</span></th>
                            <th><span>Merchant</span></th>
                            <th><span>Voucher Value</span></th>
                            <th><span>Voucher Date</span></th> 
                            <th><span>Voucher Code</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evouchers as $evoucher)
                        <tr>
                            <td>{{$evoucher->reward_category}}</td>
                            <td>{{$evoucher->reward_name}}</td>
                            <td>{{$evoucher->user_id}}</td>
                            <td>{{$evoucher->user_name}}</td>
                            <td>{{Carbon\Carbon::parse($evoucher->redeem_date)->format('Y-m-d')}}</td>
                            <td>NUP{{$evoucher->redemption_id}}</td>
                            <td>{{$evoucher->merchant_name}}</td>
                            @php 
                            $voucher_value = (float)$evoucher->voucher_value/100;
                            $voucher_value = round($voucher_value,2);
                            $voucher_value = number_format($voucher_value, 2, '.', '');
                            @endphp
                            <td>{{$voucher_value}}</td>
                            <td>{{Carbon\Carbon::parse($evoucher->voucher_date)->format('Y-m-d')}}</td>
                            <td>{{$evoucher->voucher_code}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>