<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-{{setting('theme_contrast')}}-{{setting('theme_color')}} elevation-4">
    <!-- Brand Logo -->
    <a href="{{route('home')}}" class="brand-link {{setting('logo_bg_color','bg-white')}}">
        <?php

        use App\Helper\Helper;
        use App\Models\CollectionHub;

        if (in_array(4, Auth::user()->users_roles_id()) || in_array(5, Auth::user()->users_roles_id())) {
            $hub_id = Helper::getCollectionHubId();
            $hub = CollectionHub::find($hub_id)->image;
            if (!$hub)
                $hub = 'user_account.jpg';
        ?>
            <img id="hub_logo" src="{{asset('nucycle-admin/images/hub_logo').'/'.$hub}}" alt="{{env('APP_NAME')}}" class="brand-image">
        <?php

        }  else { ?>
            <img src="{{asset('nucycle-admin/icon.png')}}" alt="{{env('APP_NAME')}}" class="brand-image">
        <?php } ?>
        <span class="brand-text font-weight-light">NuCycle</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @include('layouts.menu',['icons'=>true])
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>