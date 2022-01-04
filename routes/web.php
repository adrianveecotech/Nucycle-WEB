<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();


Route::get('/home', 'HomeController@index')->name('home');
Route::post('get-home-data', 'HomeController@getHomeData')->name('get_home_data');
Route::get('merchant-report', 'ReportController@printPartnerReport')->name('report.print_parter_report');

Route::group(['middleware' => ['auth', 'admin']], function () {
    Route::post('summernoteUploadImage', 'GeneralController@summernoteUploadImage')->name('summernoteUploadImage');
    Route::post('get_city_by_state', 'GeneralController@get_city_by_state')->name('get_city_by_state');

    Route::group(['prefix' => 'collection-hub-bin-activity'], function () {
        Route::get('/', 'CollectionHubBinActivityController@index')->name('collection_hub_bin_activity.index');
    });

    Route::group(['prefix' => 'report'], function () {
        Route::group(['prefix' => 'collection'], function () {
            Route::get('total', 'ReportController@collection_total')->name('report.collection.total');
            Route::get('total-collected-waste', 'ReportController@total_collected_waste')->name('report.collection.total_collected_waste');
            Route::get('total-waste-selling', 'ReportController@total_waste_selling')->name('report.collection.total_waste_selling');
            Route::get('on-site', 'ReportController@collection_on_site')->name('report.collection.on_site');
            Route::get('on-site-collected-waste', 'ReportController@on_site_collected_waste')->name('report.collection.on_site_collected_waste');
            Route::get('on-site-waste-selling', 'ReportController@on_site_waste_selling')->name('report.collection.on_site_waste_selling');
            Route::get('mobile', 'ReportController@collection_mobile')->name('report.collection.mobile');
            Route::get('mobile-collected-waste', 'ReportController@mobile_collected_waste')->name('report.collection.mobile_collected_waste');
            Route::get('mobile-waste-selling', 'ReportController@mobile_waste_selling')->name('report.collection.mobile_waste_selling');
        });
        Route::group(['prefix' => 'app-performance'], function () {
            Route::get('demography', 'ReportController@demography')->name('report.app_performance.demography');
            Route::get('growth-and-population', 'ReportController@growth_and_population')->name('report.app_performance.growth_and_population');
            Route::post('get-registered-user', 'ReportController@get_registered_user')->name('report.app_performance.get_registered_user');
            Route::post('get-registered-user-district', 'ReportController@get_registered_user_district')->name('report.app_performance.get_registered_user_district');
            Route::post('get-new-registered-user', 'ReportController@get_new_registered_user')->name('report.app_performance.get_new_registered_user');
            Route::post('get-new-registered-user-district', 'ReportController@get_new_registered_user_district')->name('report.app_performance.get_new_registered_user_district');
            Route::post('get-thirty-days-login', 'ReportController@get_thirty_days_login')->name('report.app_performance.get_thirty_days_login');
            Route::post('get-thirty-days-login-district', 'ReportController@get_thirty_days_login_district')->name('report.app_performance.get_thirty_days_login_district');
            Route::post('get-active-transaction', 'ReportController@get_active_transaction')->name('report.app_performance.get_active_transaction');
            Route::post('get-active-transaction-district', 'ReportController@get_active_transaction_district')->name('report.app_performance.get_active_transaction_district');
            Route::post('get-membership-tier', 'ReportController@get_membership_tier')->name('report.app_performance.get_membership_tier');
            Route::post('get-user-preference', 'ReportController@get_user_preference')->name('report.app_performance.get_user_preference');
        });

        Route::get('reward-performance', 'ReportController@reward_performance')->name('report.reward_performance');
        Route::get('ads-click', 'ReportController@ads_click')->name('report.ads_click');
        Route::post('get-ads-click-data', 'ReportController@get_ads_click_data')->name('report.get_ads_click_data');
        Route::get('get-ads-click-data/{format}', 'ReportController@get_ads_click_data_get')->name('report.get_ads_click_data_get');
        Route::post('get-collection-on-site-collected-transaction-district', 'ReportController@get_collection_collected_transaction_district')->name('report.get_collection_collected_transaction_district');
        Route::post('get-collection-on-site-collected-waste-district', 'ReportController@get_collection_collected_waste_district')->name('report.get_collection_collected_waste_district');
        Route::post('get-collection-on-site-waste-selling-district', 'ReportController@get_collection_waste_selling_district')->name('report.get_collection_waste_selling_district');
        Route::get('individual-vs-company', 'ReportController@individual_vs_company')->name('report.individual_vs_company');
        Route::get('user-by-state-city', 'ReportController@user_by_state_city')->name('report.user_by_state_city');
        Route::post('get-city-by-state', 'ReportController@get_city_by_state')->name('report.get_city_by_state');
        Route::get('user-by-membership-tier', 'ReportController@user_by_membership_tier')->name('report.user_by_membership_tier');
        Route::get('point-redeemed', 'ReportController@point_redeemed')->name('report.point_redeemed');
        Route::get('active-user', 'ReportController@active_user')->name('report.active_user');
        Route::post('get-active-user-by-state', 'ReportController@get_active_user_by_state')->name('report.get_active_user_by_state');
        Route::get('point-hub-weekly-monthly', 'ReportController@point_hub_weekly_monthly')->name('report.point_hub_weekly_monthly');
        Route::post('get-point-hub-weekly-monthly', 'ReportController@get_point_hub_weekly_monthly')->name('report.get_point_hub_weekly_monthly');
        Route::post('get-collected-recyling-type', 'ReportController@get_collected_recyling_type')->name('report.get_collected_recyling_type');
        Route::get('collected-recyling-type', 'ReportController@collected_recyling_type')->name('report.collected_recyling_type');
        Route::post('get-waste-recyling-type', 'ReportController@get_waste_recyling_type')->name('report.get_waste_recyling_type');
        Route::get('waste-recyling-type', 'ReportController@waste_recyling_type')->name('report.waste_recyling_type');
        Route::post('get-reward-redeemed', 'ReportController@get_reward_redeemed')->name('report.get_reward_redeemed');
        Route::get('reward-redeemed', 'ReportController@reward_redeemed')->name('report.reward_redeemed');
        Route::get('new-vs-exisiting-recycling-frequency', 'ReportController@new_vs_exisiting_recycling_frequency')->name('report.new_vs_exisiting_recycling_frequency');
        Route::post('get-new-vs-exisiting-recycling-frequency', 'ReportController@get_new_vs_exisiting_recycling_frequency')->name('report.get_new_vs_exisiting_recycling_frequency');

        Route::get('individual-vs-company-visited-center', 'ReportController@individual_vs_company_visited_center')->name('report.individual_vs_company_visited_center');

        Route::get('new-vs-existing-visited-collection', 'ReportController@new_vs_existing_visited_center')->name('report.new_vs_existing_visited_center');

        Route::post('get-new-vs-existing-visited-collection', 'ReportController@get_new_vs_existing_visited_center')->name('report.get_new_vs_existing_visited_center');
        Route::post('get-collection-on-site-collected-transaction-hub-data', 'ReportController@get_collection_collected_transaction_data')->name('report.get_collection_collected_transaction_data');
        Route::post('get-collection-on-site-collected-waste-hub-data', 'ReportController@get_collection_collected_waste_data')->name('report.get_collection_collected_waste_data');
        Route::post('get-collection-on-site-waste-selling-hub-data', 'ReportController@get_collection_waste_selling_data')->name('report.get_collection_waste_selling_data');
        Route::post('get-reward-performance-data', 'ReportController@get_reward_performance_data')->name('report.get_reward_performance_data');
        Route::get('get-reward-performance-data-get/{format}+{state1}+{state2}', 'ReportController@get_reward_performance_data_get')->name('report.get_reward_performance_data_get');

        Route::post('get-reward-performance-district-data', 'ReportController@get_reward_performance_district_data')->name('report.get_reward_performance_district_data');
        Route::post('get-redemption-by-category-state', 'ReportController@get_redemption_by_category_state')->name('report.get_redemption_by_category_state');
    });

    Route::group(['prefix' => 'waste-clearance'], function () {
        Route::get('/', 'WasteClearanceController@index')->name('waste_clearance.index');
        Route::get('create', 'WasteClearanceController@create')->name('waste_clearance.create');
        Route::post('insert', 'WasteClearanceController@insert')->name('waste_clearance.insert_db');
        Route::get('edit/{id}', 'WasteClearanceController@edit')->name('waste_clearance.edit');
        Route::post('edit', 'WasteClearanceController@edit_db')->name('waste_clearance.edit_db');
        Route::get('cancel/{id}', 'WasteClearanceController@cancel')->name('waste_clearance.cancel');
        Route::get('view/{id}', 'WasteClearanceController@view')->name('waste_clearance.view');
        Route::get('view-statement/{id}', 'WasteClearanceController@view_statement')->name('waste_clearance.view_statement');
        Route::post('get-hub-info', 'WasteClearanceController@getHubInfo')->name('waste_clearance.get_hub_info');
    });

    Route::group(['prefix' => 'waste-clearance-statement'], function () {
        Route::get('/', 'WasteClearanceStatementController@index')->name('waste_clearance_statement.index');
        Route::get('view/{id}', 'WasteClearanceStatementController@view')->name('waste_clearance_statement.view');
    });

    Route::group(['prefix' => 'notfication'], function () {
        Route::get('/', 'NotificationController@index')->name('notification.index');
        Route::get('create', 'NotificationController@create')->name('notification.create');
        Route::get('edit/{id}', 'NotificationController@content_edit')->name('notification.edit');
        Route::post('edit', 'NotificationController@content_edit_db')->name('notification.edit_db');
        Route::get('view/{id}', 'NotificationController@content_view')->name('notification.view');
        Route::post('insert', 'NotificationController@insert')->name('notification.insert_db');
    });
    Route::group(['prefix' => 'be_our_partner'], function () {
        Route::get('/', 'BeOurPartnerController@index')->name('be_our_partner.index');
        Route::group(['prefix' => 'content'], function () {
            Route::get('edit/{id}', 'BeOurPartnerController@content_edit')->name('be_our_partner.content.edit');
            Route::post('edit', 'BeOurPartnerController@content_edit_db')->name('be_our_partner.content.edit_db');
            Route::get('view/{id}', 'BeOurPartnerController@content_view')->name('be_our_partner.content.view');
            Route::get('index', 'BeOurPartnerController@content_index')->name('be_our_partner.content.index');
        });

        Route::group(['prefix' => 'enquiry'], function () {
            Route::get('view/{id}', 'BeOurPartnerController@enquiry_view')->name('be_our_partner.enquiry.view');
            Route::get('index', 'BeOurPartnerController@enquiry_index')->name('be_our_partner.enquiry.index');
        });
    });

    Route::group(['prefix' => 'contact_us'], function () {
        Route::get('/', 'ContactUsController@index')->name('contact_us.index');
        Route::group(['prefix' => 'content'], function () {
            Route::get('edit', 'ContactUsController@content_edit')->name('contact_us.content.edit');
            Route::post('edit_db', 'ContactUsController@content_edit_db')->name('contact_us.content.edit_db');
            Route::get('view/{id}', 'ContactUsController@content_view')->name('contact_us.content.view');
            Route::get('index', 'ContactUsController@content_index')->name('contact_us.content.index');
        });

        Route::group(['prefix' => 'enquiry'], function () {
            Route::get('view/{id}', 'ContactUsController@enquiry_view')->name('contact_us.enquiry.view');
            Route::get('index', 'ContactUsController@enquiry_index')->name('contact_us.enquiry.index');
        });
    });

    Route::group(['prefix' => 'level'], function () {
        Route::get('/', 'LevelController@index')->name('level.index');
        Route::get('create', 'LevelController@create')->name('level.create');
        Route::get('edit/{id}', 'LevelController@edit')->name('level.edit');
        Route::get('view/{id}', 'LevelController@view')->name('level.view');
        Route::get('delete/{id}', 'LevelController@delete')->name('level.delete');
        Route::post('edit', 'LevelController@edit_db')->name('level.edit_db');
        Route::post('insert', 'LevelController@insert')->name('level.insert_db');
    });

    Route::group(['prefix' => 'reward'], function () {
        Route::get('/', 'RewardController@index')->name('reward.index');
        Route::post('get_reward_by_merchant', 'RewardController@get_reward_by_merchant')->name('reward.get_reward_by_merchant');
        Route::get('edit/{id}', 'RewardController@edit')->name('reward.edit');
        Route::get('view/{id}', 'RewardController@view')->name('reward.view');
        Route::get('edit_voucher/{id}', 'VoucherController@edit')->name('voucher.edit');
        Route::get('view_voucher/{id}', 'VoucherController@view')->name('voucher.view');
        Route::post('insert_voucher', 'VoucherController@insert')->name('voucher.insert');
        Route::get('delete_voucher/{id}/{reward_id}', 'VoucherController@delete')->name('voucher.delete');
        Route::get('delete/{id}', 'RewardController@delete')->name('reward.delete');
        Route::post('edit', 'RewardController@edit_db')->name('reward.edit_db');
        Route::post('edit_voucher', 'VoucherController@edit_db')->name('voucher.edit_db');
        Route::get('create', 'RewardController@create')->name('reward.create');
        Route::post('insert', 'RewardController@insert')->name('reward.insert_db');
    });

    Route::group(['prefix' => 'user'], function () {
        Route::get('/', 'CustomerController@index')->name('customer.index');
        Route::get('create', 'CustomerController@create')->name('customer.create');
        Route::get('edit/{id}', 'CustomerController@edit')->name('customer.edit');
        Route::get('view/{id}', 'CustomerController@view')->name('customer.view');
        // Route::get('delete/{id}', 'CustomerController@delete')->name('customer.delete');
        Route::post('edit', 'CustomerController@edit_db')->name('customer.edit_db');
        Route::post('insert', 'CustomerController@insert')->name('customer.insert_db');
    });

    Route::group(['prefix' => 'all-users'], function () {
        Route::get('/', 'UserController@index')->name('user.index');
        Route::get('create', 'UserController@create')->name('user.create');
        Route::get('edit/{id}', 'UserController@edit')->name('user.edit');
        Route::post('edit', 'UserController@edit_db')->name('user.edit_db');
        Route::post('insert', 'UserController@insert')->name('user.insert_db');
        Route::get('delete/{id}', 'UserController@delete')->name('user.delete');
    });

    Route::group(['prefix' => 'activity'], function () {
        Route::get('/', 'ActivityController@index')->name('activity.index');
        Route::get('create', 'ActivityController@create')->name('activity.create');
        Route::get('edit/{id}', 'ActivityController@edit')->name('activity.edit');
        Route::get('view/{id}', 'ActivityController@view')->name('activity.view');
        Route::get('delete/{id}', 'ActivityController@delete')->name('activity.delete');
        Route::post('edit', 'ActivityController@edit_db')->name('activity.edit_db');
        Route::post('insert', 'ActivityController@insert')->name('activity.insert_db');
    });

    Route::group(['prefix' => 'promotion'], function () {
        Route::get('/', 'PromotionController@index')->name('promotion.index');
        Route::get('create', 'PromotionController@create')->name('promotion.create');
        Route::get('edit/{id}', 'PromotionController@edit')->name('promotion.edit');
        Route::get('view/{id}', 'PromotionController@view')->name('promotion.view');
        Route::get('delete/{id}', 'PromotionController@delete')->name('promotion.delete');
        Route::post('edit', 'PromotionController@edit_db')->name('promotion.edit_db');
        Route::post('insert', 'PromotionController@insert')->name('promotion.insert_db');
    });

    Route::group(['prefix' => 'article'], function () {
        Route::get('/', 'ArticleController@index')->name('article.index');
        Route::get('create', 'ArticleController@create')->name('article.create');
        Route::get('edit/{id}', 'ArticleController@edit')->name('article.edit');
        Route::get('view/{id}', 'ArticleController@view')->name('article.view');
        Route::get('delete/{id}', 'ArticleController@delete')->name('article.delete');
        Route::post('edit', 'ArticleController@edit_db')->name('article.edit_db');
        Route::post('insert', 'ArticleController@insert')->name('article.insert_db');
    });

    Route::group(['prefix' => 'guideline'], function () {
        Route::get('/', 'GuidelineController@index')->name('guideline.index');
        Route::get('create', 'GuidelineController@create')->name('guideline.create');
        Route::get('edit/{id}', 'GuidelineController@edit')->name('guideline.edit');
        Route::get('view/{id}', 'GuidelineController@view')->name('guideline.view');
        Route::get('delete/{id}', 'GuidelineController@delete')->name('guideline.delete');
        Route::post('edit', 'GuidelineController@edit_db')->name('guideline.edit_db');
        Route::post('insert', 'GuidelineController@insert')->name('guideline.insert_db');
    });

    Route::group(['prefix' => 'faq'], function () {
        Route::get('/', 'FaqController@index')->name('faq.index');
        Route::get('create', 'FaqController@create')->name('faq.create');
        Route::get('edit/{id}', 'FaqController@edit')->name('faq.edit');
        Route::get('view/{id}', 'FaqController@view')->name('faq.view');
        Route::get('delete/{id}', 'FaqController@delete')->name('faq.delete');
        Route::post('edit', 'FaqController@edit_db')->name('faq.edit_db');
        Route::post('insert', 'FaqController@insert')->name('faq.insert_db');
    });

    Route::group(['prefix' => 'collection-hub'], function () {
        Route::get('/', 'CollectionHubController@index')->name('collection_hub.index');
        Route::get('create', 'CollectionHubController@create')->name('collection_hub.create');
        Route::post('insert', 'CollectionHubController@insert')->name('collection_hub.insert_db');
        Route::get('edit/{id}', 'CollectionHubController@edit')->name('collection_hub.edit');
        Route::get('view/{id}', 'CollectionHubController@view')->name('collection_hub.view');
        // Route::post('delete/{id}', 'CollectionHubController@delete')->name('collection_hub.delete');
        Route::post('edit', 'CollectionHubController@edit_db')->name('collection_hub.edit_db');
    });

    Route::group(['prefix' => 'collection-hub-admin'], function () {
        Route::get('/', 'CollectionHubAdminController@index')->name('collection_hub_admin.index');
        Route::get('create', 'CollectionHubAdminController@create')->name('collection_hub_admin.create');
        Route::get('edit/{id}', 'CollectionHubAdminController@edit')->name('collection_hub_admin.edit');
        Route::get('view/{id}', 'CollectionHubAdminController@view')->name('collection_hub_admin.view');
        Route::post('insert', 'CollectionHubAdminController@insert')->name('collection_hub_admin.insert_db');
        Route::post('edit', 'CollectionHubAdminController@edit_db')->name('collection_hub_admin.edit_db');
        Route::get('delete/{id}', 'CollectionHubAdminController@delete')->name('collection_hub_admin.delete');
    });



    Route::group(['prefix' => 'settings'], function () {
        Route::get('/', 'SettingsController@index')->name('settings');

        Route::group(['prefix' => 'membership_info'], function () {
            Route::get('/', 'MembershipInfoController@index')->name('membership_info.index');
            Route::get('/edit', 'MembershipInfoController@edit')->name('membership_info.edit');
            Route::post('/edit', 'MembershipInfoController@edit_db')->name('membership_info.edit_db');
        });

        Route::group(['prefix' => 'user-role'], function () {
            Route::get('/', 'SettingsController@user_role')->name('settings.user_role');
            Route::get('edit/{id}', 'SettingsController@user_role_edit')->name('settings.user_role.edit');
            Route::get('view/{id}', 'SettingsController@user_role_view')->name('settings.user_role.view');
            Route::post('edit', 'SettingsController@user_role_edit_db')->name('settings.user_role.edit_db');
        });
        Route::group(['prefix' => 'recycle-category'], function () {
            Route::get('/', 'SettingsController@recycle_category')->name('settings.recycle_category');
            Route::get('create', 'SettingsController@recycle_category_create')->name('settings.recycle_category.create');
            Route::post('insert', 'SettingsController@recycle_category_insert')->name('settings.recycle_category.insert_db');
            Route::get('edit/{id}', 'SettingsController@recycle_category_edit')->name('settings.recycle_category.edit');
            Route::get('view/{id}', 'SettingsController@recycle_category_view')->name('settings.recycle_category.view');
            Route::post('edit', 'SettingsController@recycle_category_edit_db')->name('settings.recycle_category.edit_db');
            Route::get('delete/{id}', 'SettingsController@recycle_category_delete')->name('settings.recycle_category.delete');
        });
        Route::group(['prefix' => 'recycle-type'], function () {
            Route::get('/', 'SettingsController@recycle_type')->name('settings.recycle_type');
            Route::get('create', 'SettingsController@recycle_type_create')->name('settings.recycle_type.create');
            Route::post('insert', 'SettingsController@recycle_type_insert')->name('settings.recycle_type.insert_db');
            Route::get('edit/{id}', 'SettingsController@recycle_type_edit')->name('settings.recycle_type.edit');
            Route::get('view/{id}', 'SettingsController@recycle_type_view')->name('settings.recycle_type.view');
            Route::post('edit', 'SettingsController@recycle_type_edit_db')->name('settings.recycle_type.edit_db');
            Route::get('delete/{id}', 'SettingsController@recycle_type_delete')->name('settings.recycle_type.delete');
        });

        Route::group(['prefix' => 'merchant'], function () {
            Route::get('/', 'SettingsController@merchant')->name('settings.merchant');
            Route::get('create', 'SettingsController@merchant_create')->name('settings.merchant.create');
            Route::post('insert', 'SettingsController@merchant_insert')->name('settings.merchant.insert_db');
            Route::get('edit/{id}', 'SettingsController@merchant_edit')->name('settings.merchant.edit');
            Route::get('view/{id}', 'SettingsController@merchant_view')->name('settings.merchant.view');
            Route::post('edit', 'SettingsController@merchant_edit_db')->name('settings.merchant.edit_db');
        });

        Route::group(['prefix' => 'banner_tag'], function () {
            Route::get('/', 'SettingsController@banner_tag')->name('settings.banner_tag');
            Route::get('create', 'SettingsController@banner_tag_create')->name('settings.banner_tag.create');
            Route::post('insert', 'SettingsController@banner_tag_insert')->name('settings.banner_tag.insert_db');
            Route::get('edit/{id}', 'SettingsController@banner_tag_edit')->name('settings.banner_tag.edit');
            Route::get('view/{id}', 'SettingsController@banner_tag_view')->name('settings.banner_tag.view');
            Route::post('edit', 'SettingsController@banner_tag_edit_db')->name('settings.banner_tag.edit_db');
            Route::get('delete/{id}', 'SettingsController@banner_tag_delete')->name('settings.banner_tag.delete');
        });

        Route::group(['prefix' => 'rewards_category'], function () {
            Route::get('/', 'SettingsController@rewards_category')->name('settings.rewards_category');
            Route::get('create', 'SettingsController@rewards_category_create')->name('settings.rewards_category.create');
            Route::post('insert', 'SettingsController@rewards_category_insert')->name('settings.rewards_category.insert_db');
            Route::get('edit/{id}', 'SettingsController@rewards_category_edit')->name('settings.rewards_category.edit');
            Route::get('view/{id}', 'SettingsController@rewards_category_view')->name('settings.rewards_category.view');
            Route::post('edit', 'SettingsController@rewards_category_edit_db')->name('settings.rewards_category.edit_db');
            Route::get('delete/{id}', 'SettingsController@rewards_category_delete')->name('settings.rewards_category.delete');
        });

        Route::group(['prefix' => 'statistic_indicator'], function () {
            Route::get('/', 'SettingsController@statistic_indicator')->name('settings.statistic_indicator');
            Route::get('edit/{id}', 'SettingsController@statistic_indicator_edit')->name('settings.statistic_indicator.edit');
            Route::get('view/{id}', 'SettingsController@statistic_indicator_view')->name('settings.statistic_indicator.view');
            Route::post('edit', 'SettingsController@statistic_indicator_edit_db')->name('settings.statistic_indicator.edit_db');
        });
    });
});

Route::group(['middleware' => ['auth', 'admin_hubadmin']], function () {
    Route::group(['prefix' => 'collection'], function () {
        Route::get('/', 'CollectionController@index')->name('collection.index');
        Route::get('edit/{id}', 'CollectionController@edit')->name('collection.edit');
        Route::post('edit', 'CollectionController@edit_db')->name('collection.edit_db');
        Route::get('view/{id}', 'CollectionController@view')->name('collection.view');
        Route::get('cancel/{id}', 'CollectionController@cancel')->name('collection.cancel');
    });

    Route::group(['prefix' => 'collection-hub-collector'], function () {
        Route::get('/', 'CollectionHubCollectorController@index')->name('collection_hub_collector.index');
        Route::get('create', 'CollectionHubCollectorController@create')->name('collection_hub_collector.create');
        Route::get('edit/{id}', 'CollectionHubCollectorController@edit')->name('collection_hub_collector.edit');
        Route::get('view/{id}', 'CollectionHubCollectorController@view')->name('collection_hub_collector.view');
        Route::post('insert', 'CollectionHubCollectorController@insert')->name('collection_hub_collector.insert_db');
        Route::post('edit', 'CollectionHubCollectorController@edit_db')->name('collection_hub_collector.edit_db');
        Route::get('delete/{id}', 'CollectionHubCollectorController@delete')->name('collection_hub_collector.delete');
    });

    Route::group(['prefix' => 'collection-hub-recycle-type'], function () {
        Route::get('/', 'CollectionHubRecycleTypeController@index')->name('collection_hub_recycle_type.index');
        Route::get('create', 'CollectionHubRecycleTypeController@create')->name('collection_hub_recycle_type.create');
        Route::post('insert', 'CollectionHubRecycleTypeController@insert')->name('collection_hub_recycle_type.insert_db');
        Route::post('edit', 'CollectionHubRecycleTypeController@edit_db')->name('collection_hub_recycle_type.edit_db');
        Route::post('get_hub_recycle', 'CollectionHubRecycleTypeController@get_hub_recycle')->name('collection_hub_recycle_type.get_hub_recycle');
        Route::get('delete/id={id}&hub_id={hub_id}', 'CollectionHubRecycleTypeController@delete')->name('collection_hub_recycle_type.delete');
        Route::get('edit/id={id}&hub_id={hub_id}', 'CollectionHubRecycleTypeController@edit')->name('collection_hub_recycle_type.edit');
        Route::get('view/id={id}&hub_id={hub_id}', 'CollectionHubRecycleTypeController@view')->name('collection_hub_recycle_type.view');
    });

    Route::group(['prefix' => 'report'], function () {
        Route::get('/', 'ReportController@index')->name('report.index');
    });

    Route::group(['prefix' => 'collection-hub-bin'], function () {
        Route::get('/', 'CollectionHubBinController@index')->name('collection_hub_bin.index');
        Route::get('edit/{id}', 'CollectionHubBinController@edit')->name('collection_hub_bin.edit');
        Route::get('reset/{id}', 'CollectionHubBinController@reset')->name('collection_hub_bin.reset');
        Route::post('edit', 'CollectionHubBinController@edit_db')->name('collection_hub_bin.edit_db');
    });


    // Route::group(['prefix' => 'report'], function () {
    //     Route::get('/', 'ReportController@index')->name  ('report.index');
    //     Route::get('collection_hub', 'ReportController@collection_hub')->name('report_collection_hub.index');
    //     Route::get('collected_waste', 'ReportController@collected_waste')->name('report_collected_waste.index');
    //     Route::get('redeemed_reward', 'ReportController@redeemed_reward')->name('report_redeemed_reward.index');
    //     Route::group(['middleware' => ['auth', 'admin_and_collectoradmin']], function () {
    //         Route::get('collected_waste_by_hub_admin', 'ReportController@collected_waste_by_hub_admin')->name('report_collected_waste_by_hub_admin.index');
    //     });
    // });
});

Route::group(['middleware' => ['auth', 'hubadmin_reader']], function () {
    Route::group(['prefix' => 'report'], function () {
        Route::get('collection-hub/collected-transaction', 'ReportController@collectionhub_collected_transaction')->name('report.collectionhub_collected_transaction');
        Route::post('collection-hub/collected-transaction-data', 'ReportController@collectionhub_collected_transaction_data')->name('report.collectionhub_collected_transaction_data');
        Route::get('collection-hub/collected-waste', 'ReportController@collectionhub_collected_waste')->name('report.collectionhub_collected_waste');
        Route::post('collection-hub/collected-waste-data', 'ReportController@collectionhub_collected_waste_data')->name('report.collectionhub_collected_waste_data');
        Route::get('collection-hub/waste-selling', 'ReportController@collectionhub_waste_selling')->name('report.collectionhub_waste_selling');
        Route::post('collection-hub/waste-selling-data', 'ReportController@collectionhub_waste_selling_data')->name('report.collectionhub_waste_selling_data');
    });
});

Route::group(['middleware' => ['auth', 'hubadmin']], function () {
    Route::group(['prefix' => 'collection-hub-reader'], function () {
        Route::get('/', 'CollectionHubReaderController@index')->name('collection_hub_reader.index');
        Route::get('create', 'CollectionHubReaderController@create')->name('collection_hub_reader.create');
        Route::get('edit/{id}', 'CollectionHubReaderController@edit')->name('collection_hub_reader.edit');
        Route::get('view/{id}', 'CollectionHubReaderController@view')->name('collection_hub_reader.view');
        Route::post('insert', 'CollectionHubReaderController@insert')->name('collection_hub_reader.insert_db');
        Route::post('edit', 'CollectionHubReaderController@edit_db')->name('collection_hub_reader.edit_db');
        Route::get('delete/{id}', 'CollectionHubReaderController@delete')->name('collection_hub_reader.delete');
    });
});
