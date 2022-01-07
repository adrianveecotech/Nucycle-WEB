<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class EVoucherTableExport implements FromCollection,WithHeadings
{
    public function headings():array{
        return[
            'Reward Category',
            'Voucher Name',
            'Supplier ID',
            'Supplier Name',
            'Entitlement Date',
            'NUP generation code',
            'Merchant',
            'Voucher Value',
            'Voucher Date',
            'Voucher Code'
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
        $evouchers = DB::table('customer_reward')
        ->join('rewards','customer_reward.reward_id','=','rewards.id')
        ->join('reward_category','rewards.reward_category_id','=','reward_category.id')
        ->join('customer','customer_reward.customer_id','=','customer.id')
        ->join('merchant','rewards.merchant_id','=','merchant.id')
        ->select('reward_category.name AS reward_category','rewards.title AS reward_name','customer.membership_id AS user_id','customer.name AS user_name','customer_reward.redeem_date','customer_reward.id AS redemption_id','merchant.name as merchant_name','rewards.point as voucher_value','customer_reward.created_at AS voucher_date','customer_reward.voucher_id AS voucher_code')
        ->where('rewards.reward_category_id',9)
        ->get();

        foreach($evouchers as $e)
        {
                $e->redeem_date = Carbon::parse($e->redeem_date)->format('Y-m-d');
                $e->redemption_id = "NUP".$e->redemption_id;
                $voucher_value = (float)$e->voucher_value/100;
                $voucher_value = round($voucher_value,2);
                $voucher_value = number_format($voucher_value, 2, '.', '');
                $e->voucher_value = $voucher_value;
                $e->voucher_date = Carbon::parse($e->voucher_date)->format('Y-m-d');
        }
        
        return collect($evouchers);
    }
}
