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
            <div class="tbl-header table-responsive">
                <table class="table table-striped" id="tableMain">
                    <thead>
                        <tr>
                            <th><span>Stock Category<span></th>
                            <th><span>Bal B/F Qty</span></th>
                            <th><span>Bal B/F Amt</span></th>
                            <th><span>Purchase Qty</span></th>
                            <th><span>Purchase Amt</span></th>
                            <th><span>Average Cost</span></th>
                            <th><span>Sales Qty</span></th>
                            <th><span>Sales Amt</span></th>
                            <th><span>Adjustment Qty</span></th>
                            <th><span>Adjustment Amt</span></th> 
                            <th><span>Current Qty</span></th>
                            <th><span>Current Amt</span></th>
                    </thead>
                    <tbody>
                        @foreach($category as $c)
                        <tr>
                            <td>{{$c->name}}</td>
                            <td>{{number_format($c->balqty, 2, '.', '')}}</td>
                            <td>RM{{number_format($c->balamt, 2, '.', '')}}</td>
                            <td>{{number_format($c->purchaseqty, 2, '.', '')}}</td>
                            <td>RM{{number_format($c->purchaseamt, 2, '.', '')}}</td>
                            <td>RM{{number_format($c->avgcost, 2, '.', '')}}</td>
                            <td>{{number_format($c->salesqty, 2, '.', '')}}</td>
                            <td>RM{{number_format($c->salesamt, 2, '.', '')}}</td>
                            <td>{{number_format($c->adjustqty, 2, '.', '')}}</td>
                            <td>RM{{number_format($c->adjustamt, 2, '.', '')}}</td>
                            <td>{{number_format($c->currentqty, 2, '.', '')}}</td>
                            <td>RM{{number_format($c->currentamt, 2, '.', '')}}</td>
                        </tr>
                      
                            
                        @endforeach 
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>