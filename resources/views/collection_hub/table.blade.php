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
                    <th><span>Name</span></th>
                    <th><span>Address</span></th>
                    <th><span>Postcode</span></th>
                    <th><span>State</span></th>
                    <th><span>Contact Number</span></th>
                    <th><span>Operating Day</span></th>
                    <th><span>Operating Hours</span></th>
                    <th><span>Active Status</span></th>
                    <th><span>Hub Recycle Type Read Only</span></th>
                    <th><span>Type</span></th>
                    <th><span>Action</span></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($hubs as $hub)
                  <tr>
                    <td class="lalign">{{$hub->hub_name}}</td>
                    <td>{{$hub->hub_address}}</td>
                    <td>{{$hub->hub_postcode}}</td>
                    <td>{{$hub->state_name}}</td>
                    <td>{{$hub->contact_number}}</td>
                    <td>{{$hub->operating_day}}</td>
                    <?php $operatinghours = str_replace(',', ' - ', $hub->operating_hours) ?>
                    <td>{{$operatinghours}}</td>
                    <td>{{$hub->is_active == 1? 'Active' : 'Inactive'}}</td>
                    <td>{{$hub->read_only == 1? 'Yes' : 'No'}}</td>
                    <td>{{$hub->type == 0? 'Station' : 'Mobile'}}</td>
                    <td>
                      <a href="{{route('collection_hub.view', ['id' => $hub->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-eye"></i></i></a>
                      <a href="{{route('collection_hub.edit', ['id' => $hub->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-pencil"></i></a>
                      <!-- <a href="{{ URL('collection-hub/delete/'. $hub->id) }}" class="btn btn-xs btn-danger button-float-right"><i class="nav-icon fa fa-trash"></i></a> -->
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>