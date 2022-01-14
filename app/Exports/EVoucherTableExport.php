<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;

class EVoucherTableExport implements FromCollection,WithHeadings,WithEvents, WithCustomStartCell
{
    public function startCell(): string
    {
        return 'A3';
    }

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

    public function registerEvents(): array {
        
        return [
            AfterSheet::class => function(AfterSheet $event) {
                /** @var Sheet $sheet */
                $sheet = $event->sheet;

                $sheet->mergeCells('A1:G1');
                $sheet->setCellValue('A1', "Company Name - Nuplas Solutions Sdn. Bhd.");

                $sheet->mergeCells('A2:G2');
                $sheet->setCellValue('A2', "e-VOUCHER List");
     
                $cellRange = 'A1:G2'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange);
            },
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
        if(session()->has('monthinnum') && session()->has('year'))
        {
            $monthinnum = session('monthinnum');
            $year = session('year');
            $evouchers = DB::table('customer_reward')
            ->join('rewards','customer_reward.reward_id','=','rewards.id')
            ->join('reward_category','rewards.reward_category_id','=','reward_category.id')
            ->join('customer','customer_reward.customer_id','=','customer.id')
            ->join('merchant','rewards.merchant_id','=','merchant.id')
            ->select('reward_category.name AS reward_category','rewards.title AS reward_name','customer.membership_id AS user_id','customer.name AS user_name','customer_reward.redeem_date','customer_reward.id AS redemption_id','merchant.name as merchant_name','rewards.point as voucher_value','rewards.created_at AS voucher_date','customer_reward.voucher_id AS voucher_code')
            ->whereMonth('customer_reward.redeem_date',$monthinnum)
            ->whereYear('customer_reward.redeem_date',$year)
            ->get();
        }
        else
        {
            $evouchers = DB::table('customer_reward')
            ->join('rewards','customer_reward.reward_id','=','rewards.id')
            ->join('reward_category','rewards.reward_category_id','=','reward_category.id')
            ->join('customer','customer_reward.customer_id','=','customer.id')
            ->join('merchant','rewards.merchant_id','=','merchant.id')
            ->select('reward_category.name AS reward_category','rewards.title AS reward_name','customer.membership_id AS user_id','customer.name AS user_name','customer_reward.redeem_date','customer_reward.id AS redemption_id','merchant.name as merchant_name','rewards.point as voucher_value','rewards.created_at AS voucher_date','customer_reward.voucher_id AS voucher_code')
            ->get();
        }

        foreach($evouchers as $e)
        {
                $e->redeem_date = Carbon::parse($e->redeem_date)->format('Y-m-d');
                $e->redemption_id = "NUP".$e->redemption_id;
                $voucher_value = (float)$e->voucher_value/100;
                $voucher_value = round($voucher_value,2);
                $voucher_value = number_format($voucher_value, 2, '.', '');
                $e->voucher_value = "RM".$voucher_value;
                $e->voucher_date = Carbon::parse($e->voucher_date)->format('Y-m-d');
        }
        
        return collect($evouchers);
    }
}
