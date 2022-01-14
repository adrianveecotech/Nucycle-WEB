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
                            <th><span>Status</span></th>
                            <th><span>NUP GRN</span></th>
                            <th><span>NUP GRN Date</span></th>
                            <th><span>Hub Location</span></th>
                            <th><span>Supplier ID</span></th>
                            <th><span>Supplier Name</span></th>
                            <th><span>Product Type</span></th>
                            <th><span>Product Des</span></th>
                            <th><span>DO Qty</span></th> 
                            <th><span>DO UOM</span></th>
                            <th><span>Unit Cost</span></th>
                            <th><span>Total Cost</span></th>
                            <th><span>e-Coins rewarded</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($collections as $collection)
                        <tr>
                            <td>{{$collection->status == 0 ? 'Cancelled' : 'Successful'}}</td>
                            <td class="lalign">PO{{$collection->collection_id}}</td>
                            <td>{{Carbon\Carbon::parse($collection->created_at)->format('Y-m-d')}}</td>
                            <td>{{$collection->location}}</td>
                            <td>{{$collection->customer_id}}</td>
                            <td>{{$collection->name}}</td>
                            <td>{{$collection->category_name}}</td>
                            <td>{{$collection->type_name}}</td>
                            <td>{{$collection->weight}}</td>
                            <td>KG</td>
                            @php 
                            $point = $collection->point/100;
                            $point = number_format($point, 2, '.', '');
                            $total_point = $collection->total_point/100;
                            $total_point = round($total_point,2);
                            $total_point = number_format($total_point, 2, '.', '');
                            @endphp
                            <td>{{$point}}</td>
                            <td>{{$total_point}}</td>
                            <td>{{$collection->total_point}}</td>
                        </tr>
                      
                            
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>