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
                            <th><span>Supplier ID</span></th>
                            <th><span>Supplier Name</span></th>
                            <th><span>Balance B/F</span></th>
                            <th><span>Monthly Earned</span></th>
                            <th><span>Monthly Redeemed</span></th>
                            <th><span>Current</span></th>
                            <th><span>Estimated Unit Value</span></th>
                            <th><span>Estimated Total Value</span></th>
                            <th><span>Cost of Redeemed</span></th> 
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($epoints as $epoint)
                        <tr>
                            <td>{{$epoint->user_id}}</td>
                            <td>{{$epoint->user_name}}</td>
                            <td>{{$epoint->previousBalance}}</td>
                            <td>{{$epoint->monthlyearn}}</td>
                            <td>{{$epoint->currentMonthRedeem}}</td>
                            <td>{{$epoint->current}}</td>
                            <td>{{$epoint->unitvalue}}</td>
                            <td>{{$epoint->totalvalue}}</td>
                            <td>{{$epoint->costredeem}}</td>                    
                        </tr>
                      
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>