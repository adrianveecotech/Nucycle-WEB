    @if(in_array(1, auth()->user()->users_roles_id()))
    <li class="nav-item">
        <a class="nav-link {{ Request::is('collection/*') ||  Request::is('collection') ? 'active' : '' }}" href="{!! route('collection.index') !!}">
            <i class="nav-icon fa fa-truck"></i>
            <p>{{ __('Collection')}}</p>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ Request::is('waste-clearance/*') ||  Request::is('waste-clearance') ? 'active' : '' }}" href="{!! route('waste_clearance.index') !!}">
            <i class="nav-icon fa fa-trash"></i>
            <p>{{ __('Waste Clearance')}}</p>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ Request::is('waste-clearance-statement/*') ||  Request::is('waste-clearance-statement') ? 'active' : '' }}" href="{!! route('waste_clearance_statement.index') !!}">
            <i class="nav-icon fa fa-file-text-o"></i>
            <p>{{ __('Waste Clearance Statement')}}</p>
        </a>
    </li>


    <li class="nav-item">
        <a class="nav-link {{ Request::is('collection-hub-bin/*') ||  Request::is('collection-hub-bin') ? 'active' : '' }}" href="{!! route('collection_hub_bin.index') !!}">
            <i class="nav-icon fa fa-trash"></i>
            <p>{{ __('Collection Hub Bin')}}</p>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ Request::is('collection-hub-bin-activity/*') ||  Request::is('collection-hub-bin-activity') ? 'active' : '' }}" href="{!! route('collection_hub_bin_activity.index') !!}">
            <i class="nav-icon fa fa-list"></i>
            <p>{{ __('Collection Hub Bin Activities')}}</p>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ Request::is('collection-hub/*') ||  Request::is('collection-hub') ? 'active' : '' }}" href="{!! route('collection_hub.index') !!}">
            <i class="nav-icon fa fa-home"></i>
            <p>{{ __('Collection Hub')}}</p>
        </a>
    </li>


    <li class="nav-item">
        <a class="nav-link {{ Request::is('collection-hub-recycle-type/*') ||  Request::is('collection-hub-recycle-type') ? 'active' : '' }}" href="{!! route('collection_hub_recycle_type.index') !!}">
            <i class="nav-icon fa fa-recycle"></i>
            <p>{{__('Hub Recycle Type')}}</p>
        </a>
    </li>


    <li class="nav-item">
        <a class="nav-link {{Request::is('collection-hub-admin/*') ||  Request::is('collection-hub-admin') ? 'active' : '' }}" href="{!! route('collection_hub_admin.index') !!}">
            <i class="nav-icon fa fa-lock"></i>
            <p>{{ __('Collection Hub Admin')}}</p>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ Request::is('collection-hub-collector/*') ||  Request::is('collection-hub-collector') ? 'active' : '' }}" href="{!! route('collection_hub_collector.index') !!}">
            <i class="nav-icon fa fa-truck"></i>
            <p>{{__('Collection Hub Collector') }}</p>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ Request::is('all-users/*') ||  Request::is('all-users') ? 'active' : '' }}" href="{!! route('user.index') !!}">
            <i class="nav-icon fa fa-users"></i>
            <p>{{__('All Users Role') }}</p>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ Request::is('user/*') ||  Request::is('user') ? 'active' : '' }}" href="{!! route('customer.index') !!}">
            <i class="nav-icon fa fa-users"></i>
            <p>{{__('Users') }}</p>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ Request::is('reward/*') ||  Request::is('reward') ? 'active' : '' }}" href="{!! route('reward.index') !!}">
            <i class="nav-icon fa fa-star"></i>
            <p>{{__('Reward')}}</p>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::is('activity/*') ||  Request::is('activity') ? 'active' : '' }}" href="{!! route('activity.index') !!}">
            <i class="nav-icon fa fa-file-text-o"></i>
            <p>{{__('Activity') }}</p>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{Request::is('promotion/*') ||  Request::is('promotion') ? 'active' : '' }}" href="{!! route('promotion.index')  !!}">
            <i class="nav-icon fa fa-file-text-o"></i>
            <p>{{__('Promotion') }}</p>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ Request::is('guideline/*') ||  Request::is('guideline') ? 'active' : '' }}" href="{!! route('guideline.index')  !!}">
            <i class="nav-icon fa fa-file-text-o"></i>
            <p>{{__('Guideline') }}</p>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ Request::is('article/*') ||  Request::is('article') ? 'active' : '' }}" href="{!! route('article.index')  !!}">
            <i class="nav-icon fa fa-file-text-o"></i>
            <p>{{__('Article') }}</p>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{Request::is('faq/*') ||  Request::is('faq') ? 'active' : '' }}" href="{!! route('faq.index')  !!}">
            <i class="nav-icon fa fa-question"></i>
            <p>{{__('FAQ') }}</p>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{Request::is('be_our_partner/enquiry/*') ||  Request::is('be_our_partner/enquiry') ? 'active' : '' }}" href="{!! route('be_our_partner.enquiry.index')  !!}">
            <i class="nav-icon fa fa-handshake-o"></i>
            <p>{{__('Be Our Partner Enquiry') }}</p>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{Request::is('contact_us/enquiry/*') ||  Request::is('contact_us/enquiry')? 'active' : '' }}" href="{!! route('contact_us.enquiry.index')  !!}">
            <i class="nav-icon fa fa-phone"></i>
            <p>{{__('Contact Us Enquiry') }}</p>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{Request::is('notfication/*') ||  Request::is('notfication') ? 'active' : '' }}" href="{!! route('notification.index')  !!}">
            <i class="nav-icon fa fa-envelope"></i>
            <p>{{__('Notification') }}</p>
        </a>
    </li>

    <li class="nav-item has-treeview {{  Request::is('report/collection/total*') ||  Request::is('report/collection/total') || Request::is('report/collection/on-site*') ||  Request::is('report/collection/on-site') || Request::is('report/collection/mobile*') ||  Request::is('report/collection/mobile') || Request::is('report/app-performance/*') ||  Request::is('report/app-performance') || Request::is('report/reward-performance/*') ||  Request::is('report/reward-performance') || Request::is('report/ads-click/*') ||  Request::is('report/ads-click') || Request::is('report/accounting/nuppurchase*') ||  Request::is('report/accounting/nuppurchase') || Request::is('report/accounting/epoint*') ||  Request::is('report/accounting/epoint') || Request::is('report/accounting/evoucher*') ||  Request::is('report/accounting/evoucher') || Request::is('report/accounting/nupsales*') ||  Request::is('report/accounting/nupsales') || Request::is('report/accounting/inventory*') ||  Request::is('report/accounting/inventory') || Request::is('report/accounting/closingstock*') ||  Request::is('report/accounting/closingstock') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ Request::is('report/collection/total*') ||  Request::is('report/collection/total') || Request::is('report/collection/on-site*') ||  Request::is('report/collection/on-site') || Request::is('report/collection/mobile*') ||  Request::is('report/collection/mobile') || Request::is('report/app-performance/*') ||  Request::is('report/app-performance') || Request::is('report/reward-performance/*') ||  Request::is('report/reward-performance') || Request::is('report/ads-click/*') ||  Request::is('report/ads-click') || Request::is('report/accounting/nuppurchase*') ||  Request::is('report/accounting/nuppurchase') || Request::is('report/accounting/epoint*') ||  Request::is('report/accounting/epoint') || Request::is('report/accounting/evoucher*') ||  Request::is('report/accounting/evoucher') || Request::is('report/accounting/nupsales*') ||  Request::is('report/accounting/nupsales') || Request::is('report/accounting/inventory*') ||  Request::is('report/accounting/inventory') || Request::is('report/accounting/closingstock*') ||  Request::is('report/accounting/closingstock') ? 'active' : '' }}">
            <i class="nav-icon fa fa-area-chart"></i>
            <p>Report <i class="right fa fa-angle-down"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item has-treeview {{ Request::is('report/collection/total*') ||  Request::is('report/collection/total') || Request::is('report/collection/on-site*') ||  Request::is('report/collection/on-site') || Request::is('report/collection/mobile*') ||  Request::is('report/collection/mobile') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{Request::is('report/collection/total*') ||  Request::is('report/collection/total') ||  Request::is('report/collection/on-site*') ||  Request::is('report/collection/on-site') || Request::is('report/collection/mobile*') ||  Request::is('report/collection/mobile') ? 'active' : '' }}">
                    <p>Collection <i class="right fa fa-angle-down"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a class="nav-link {{Request::is('report/collection/total*') ||  Request::is('report/collection/total') ? 'active' : '' }}" href="{!! route('report.collection.total') !!}">
                            <p>Total</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{Request::is('report/collection/on-site*') ||  Request::is('report/collection/on-site') ? 'active' : '' }}" href="{!! route('report.collection.on_site') !!}">
                            <p>On Site</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{Request::is('report/collection/mobile*') ||  Request::is('report/collection/mobile') ? 'active' : '' }}" href="{!! route('report.collection.mobile') !!}" href="{!! route('report.collection.mobile') !!}">
                            <p>Mobile</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link {{Request::is('report/app-performance/*') ||  Request::is('report/app-performance') ? 'active' : '' }}" href="{!! route('report.app_performance.demography') !!}">
                    <p>App Performance</p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{Request::is('report/reward-performance/*') ||  Request::is('report/reward-performance') || Request::is('report/ads-click/*') ||  Request::is('report/ads-click') ? 'active' : '' }}" href="{!! route('report.reward_performance') !!}">
                    <p>Reward Performance</p>
                </a>
            </li>
            <li class="nav-item has-treeview {{ Request::is('report/accounting/nuppurchase*') ||  Request::is('report/accounting/nuppurchase') || Request::is('report/accounting/epoint*') ||  Request::is('report/accounting/epoint') || Request::is('report/accounting/evoucher*') ||  Request::is('report/accounting/evoucher') || Request::is('report/accounting/nupsales*') ||  Request::is('report/accounting/nupsales') || Request::is('report/accounting/inventory*') ||  Request::is('report/accounting/inventory') || Request::is('report/accounting/closingstock*') ||  Request::is('report/accounting/closingstock') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{Request::is('report/accounting/nuppurchase*') ||  Request::is('report/accounting/nuppurchase') ||  Request::is('report/accounting/epoint*') ||  Request::is('report/accounting/epoint') || Request::is('report/accounting/evoucher*') ||  Request::is('report/accounting/evoucher') || Request::is('report/accounting/nupsales*') ||  Request::is('report/accounting/nupsales') || Request::is('report/accounting/inventory*') ||  Request::is('report/accounting/inventory') || Request::is('report/accounting/closingstock*') ||  Request::is('report/accounting/closingstock') ? 'active' : '' }}">
            <p>Accounting <i class="right fa fa-angle-down"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a class="nav-link {{Request::is('report/accounting/nuppurchase*') ||  Request::is('report/accounting/nuppurchase') ? 'active' : '' }}" href="{!! route('report.accounting.nuppurchase') !!}">
                    <p>NUP Purchase</p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{Request::is('report/accounting/epoint*') ||  Request::is('report/accounting/epoint') ? 'active' : '' }}" href="{!! route('report.accounting.epoint') !!}">
                    <p>E-Point</p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{Request::is('report/accounting/evoucher*') ||  Request::is('report/accounting/evoucher') ? 'active' : '' }}" href="{!! route('report.accounting.evoucher') !!}">
                    <p>E-Voucher</p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{Request::is('report/accounting/nupsales*') ||  Request::is('report/accounting/nupsales') ? 'active' : '' }}" href="{!! route('report.accounting.nupsales') !!}">
                    <p>NUP Sales</p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{Request::is('report/accounting/inventory*') ||  Request::is('report/accounting/inventory') ? 'active' : '' }}" href="{!! route('report.accounting.inventory') !!}">
                    <p>Inventory</p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{Request::is('report/accounting/closingstock*') ||  Request::is('report/accounting/closingstock') ? 'active' : '' }}" href="{!! route('report.accounting.closingstock') !!}">
                    <p>Closing Stocks</p>
                </a>
            </li>
    </li>
        </ul>
    </li>
    </ul>
    </li>

    <!-- <li class="nav-item">
        <a class="nav-link {{ Request::is('report/*') ||  Request::is('report') ? 'active' : '' }}" href="{!! route('report.index')  !!}">
            <i class="nav-icon fa fa-area-chart"></i>
            <p>{{__('Report') }}</p>
        </a>
    </li> -->

    <li class="nav-header">Settings</li>
    <li class="nav-item">
        <a class="nav-link {{ Request::is('settings/user-role/*') ||  Request::is('settings/user-role') ? 'active' : '' }}" href="{!! route('settings.user_role') !!}">
            <i class="nav-icon fa fa-users"></i>
            <p>{{__('User role') }} </p>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::is('settings/recycle-category/*') ||  Request::is('settings/recycle-category') ? 'active' : '' }}" href="{!! route('settings.recycle_category') !!}">
            <i class="nav-icon fa fa-recycle"></i>
            <p>{{__('Recycling category') }} </p>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::is('settings/indicator_name/*') ||  Request::is('settings/indicator_name') ? 'active' : '' }}" href="{!! route('settings.statistic_indicator') !!}">
            <i class="nav-icon fa fa-list-ol"></i>
            <p>{{__('Statistic Indicator Name') }} </p>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{Request::is('settings/recycle-type/*') ||  Request::is('settings/recycle-type') ? 'active' : '' }}" href="{!! route('settings.recycle_type') !!}">
            <i class="nav-icon fa fa-recycle"></i>
            <p>{{__('Recycling type') }} </p>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::is('settings/merchant/*') ||  Request::is('settings/merchant') ? 'active' : '' }}" href="{!! route('settings.merchant') !!}">
            <i class="nav-icon fa fa-home"></i>
            <p>{{__('Merchant management') }} </p>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::is('settings/banner_tag/*') ||  Request::is('settings/banner_tag') ? 'active' : '' }}" href="{!! route('settings.banner_tag') !!}">
            <i class="nav-icon fa fa-tags"></i>
            <p>{{__('Tags') }} </p>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::is('settings/rewards_category/*') ||  Request::is('settings/rewards_category') ? 'active' : '' }}" href="{!! route('settings.rewards_category') !!}">
            <i class="nav-icon fa fa-star"></i>
            <p>{{__('Rewards category') }} </p>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{Request::is('settings/be_our_partner/content/*') ||  Request::is('settings/be_our_partner/content') ? 'active' : '' }}" href="{!! route('be_our_partner.content.index')  !!}">
            <i class="nav-icon fa fa-handshake-o"></i>
            <p>{{__('Be Our Partner Content') }}</p>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{Request::is('settings/contact_us/content/*') ||  Request::is('settings/contact_us/content') ? 'active' : '' }}" href="{!! route('contact_us.content.index')  !!}">
            <i class="nav-icon fa fa-phone"></i>
            <p>{{__('Contact Us Content') }}</p>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{Request::is('level/*') ||  Request::is('level') ? 'active' : '' }}" href="{!! route('level.index')  !!}">
            <i class="nav-icon fa fa-level-up"></i>
            <p>{{__('Level') }}</p>
        </a>
    </li>
    @endif
    @if(in_array(4, auth()->user()->users_roles_id()))
    <li class="nav-item">
        <a class="nav-link {{ Request::is('collection/*') ||  Request::is('collection') ? 'active' : '' }}" href="{!! route('collection.index') !!}">
            <i class="nav-icon fa fa-list"></i>
            <p>{{ __('Collection')}}</p>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ Request::is('report/collection-hub*') ? 'active' : '' }}" href="{!! route('report.collectionhub_collected_transaction')  !!}">
            <i class="nav-icon fa fa-area-chart"></i>
            <p>{{__('Report') }}</p>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ Request::is('collection-hub-bin/*') ||  Request::is('collection-hub-bin') ? 'active' : '' }}" href="{!! route('collection_hub_bin.index') !!}">
            <i class="nav-icon fa fa-trash"></i>
            <p>{{ __('Collection Hub Bin')}}</p>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ Request::is('collection-hub-recycle-type/*') ||  Request::is('collection-hub-recycle-type') ? 'active' : '' }}" href="{!! route('collection_hub_recycle_type.index') !!}">
            <i class="nav-icon fa fa-recycle"></i>
            <p>{{__('Hub Recycle Type')}}</p>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ Request::is('collection-hub-collector/*') ||  Request::is('collection-hub-collector') ? 'active' : '' }}" href="{!! route('collection_hub_collector.index') !!}">
            <i class="nav-icon fa fa-truck"></i>
            <p>{{__('Collection Hub Collector') }}</p>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ Request::is('collection-hub-reader/*') ||  Request::is('collection-hub-reader') ? 'active' : '' }}" href="{!! route('collection_hub_reader.index') !!}">
            <i class="nav-icon fa fa-eye"></i>
            <p>{{__('Collection Hub Reader') }}</p>
        </a>
    </li>
    @endif