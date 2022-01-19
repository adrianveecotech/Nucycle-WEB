@if (\Session::has('successMsg'))
<div class="alert alert-success">
    <ul>
        <li>{!! \Session::get('successMsg') !!}</li>
    </ul>
</div>
@endif
<div class="row justify-content-center mb-4 mt-3">
    <div class="justify-content-center">
        <label for="merchant">Merchant</label>
        <select id="merchant" name="merchant" class="ml-1">
            <option value='Select'>Select a merchant</option>
            @foreach ($merchants as $merchant)
            <option value='{{$merchant["id"]}}'>{{$merchant["name"]}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col">
        <div id="wrapper">
            <div class="d-flex mb-1">
                <label for="filter" class="ml-auto mt-2">Status</label>
                <select id="filter" name="filter" class="ml-1">
                    <option value='Active'>Active</option>
                    <option value='Draft'>Draft</option>
                    <option value='Expired'>Expired</option>
                </select>
            </div>
            <div class="tbl-header">
                <table class="table table-striped" id="reward_table">
                <thead>
                        <tr>
                            <th><span>Title</span></th>
                            <th><span>Reward Category</span></th>
                            <th><span>Image</span></th>
                            <th><span>Point</span></th>
                            <th><span>Redemption per User</span></th>
                            <!-- <th><span>Description</span></th> -->
                            <th><span>Tag</span></th>
                            <th><span>Start Date</span></th>
                            <th><span>End Date</span></th>
                            <th><span>Status</span></th>
                            <th><span>Usable Voucher</span></th>
                            <th><span>Action</span></th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <!-- <div class="tbl-content">
                    <table id="reward_table" cellpadding="0" cellspacing="0" border="0">
                        <tbody>

                        </tbody>
                    </table>
                </div> -->
        </div>
    </div>
</div>