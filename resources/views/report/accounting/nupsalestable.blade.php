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
                            <th><span>DO Num</span></th>
                            <th><span>DO Date</span></th>
                            <th><span>From Hub Location</span></th>
                            <th><span>Buyer Name</span></th>
                            <th><span>Product Type</span></th>
                            <th><span>Product Des</span></th>
                            <th><span>DO Qty</span></th>
                            <th><span>DO UOM</span></th>
                            <th><span>Sales Inv Num</span></th> 
                            <th><span>Sales Inv Date</span></th>
                            <th><span>Unit Price</span></th>
                            <th><span>Total Price</span></th>
                            <th><span>Receipt Date</span></th>
                            <th><span>Receipt Num</span></th>
                            <th><span>Receipt Amount</span></th>
                            <th><span>Actual Cash Receive</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($nupsales as $nupsale)
                        <tr>
                            <td>{{$nupsale->do_num}}</td>
                            <td>{{Carbon\Carbon::parse($nupsale->do_date)->format('Y-m-d')}}</td>
                            <td>{{$nupsale->hub_location}}</td>
                            <td>{{$nupsale->buyer_name}}</td>
                            <td>{{$nupsale->product_type}}</td>
                            <td>{{$nupsale->product_des}}</td>
                            <td>{{$nupsale->do_qty}}</td>
                            <td>KG</td>
                            <td>{{$nupsale->sales_inv_num}}</td>
                            @if($nupsale->sales_inv_date == null)
                                <td></td>
                            @else
                            <td>{{Carbon\Carbon::parse($nupsale->sales_inv_date)->format('Y-m-d')}}</td>
                            @endif
                            <td>{{$nupsale->unit_price}}</td>
                            <td>RM{{number_format($nupsale->total_price, 2, '.', '')}}</td>
                            @if($nupsale->receipt_date == null)
                                <td></td>
                            @else
                            <td>{{Carbon\Carbon::parse($nupsale->receipt_date)->format('Y-m-d')}}</td>
                            @endif
                            <td>{{$nupsale->receipt_number}}
                            <td>RM{{number_format($nupsale->receipt_amount, 2, '.', '')}}</td>
                            <td>RM{{number_format($nupsale->actual_cash_receive, 2, '.', '')}}</td>
                        </tr>
                      
                            
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>