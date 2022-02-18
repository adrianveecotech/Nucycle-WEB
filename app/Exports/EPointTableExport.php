<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;

class EPointTableExport implements FromCollection,WithHeadings,WithEvents, WithCustomStartCell
{
    public function startCell(): string
    {
        return 'A3';
    }

    public function headings():array{
        return[
            'Supplier ID',
            'Supplier Name',
            'Balance B/F',
            'Monthly Earned',
            'Monthly Redeemed',
            'Current',
            'Estimated Unit Value',
            'Estimated Total Value',
            'Cost of Redeemed'
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
                $sheet->setCellValue('A2', "e-POINT List");
                
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
        $currentMonth = "";
        $currentYear = "";
        if (session()->has('monthinnum') && session()->has('year'))
        {
            $currentMonth = session('monthinnum');
            $currentYear = session('year');
        }
        else
        {
            $current = Carbon::now();
            $currentMonth = $current->month;
            $currentYear = $current->year;
        }
        $epoints = DB::table('collection')->join('customer','collection.customer_id','=','customer.id')
                    ->select('customer.id as cus_id','customer.membership_id as user_id','customer.name as user_name')
                    ->where('collection.status','=','1')
                    ->distinct()
                    ->get();
        foreach($epoints as $e)
        {
            $number = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
            $date = $currentYear . '-' . $currentMonth . '-' . $number;
            $currentMonthBalance = DB::table('customer_point_transaction')->where('customer_id',$e->cus_id)->whereMonth('created_at',$currentMonth)
            ->whereYear('created_at',$currentYear)->where('status','!=',2)->sum('balance');
            $totalBalance = DB::table('customer_point_transaction')->where('customer_id',$e->cus_id)
            ->whereDate('created_at','<=',$date)
            ->where('status','!=',2)
            ->sum('balance');
            if(is_null($currentMonthBalance))
            {
                $currentMonthBalance = 0;
            }

            if(is_null($totalBalance))
            {
                $totalBalance = 0;
            }

            $e->previousBalance = $totalBalance - $currentMonthBalance;

            $monthlyearn = DB::table('collection')
            ->where('customer_id',$e->cus_id)
            ->where('collection.status','=','1')
            ->whereMonth('created_at',$currentMonth)
            ->whereYear('created_at',$currentYear)
            ->sum('total_point');

            if(is_null($monthlyearn))
            {
                $monthlyearn = 0;
            }

            $e->monthlyearn = $monthlyearn;

            $currentMonthRedeem = DB::table('customer_reward')
            ->where('customer_id',$e->cus_id)
            ->whereMonth('created_at',$currentMonth)
            ->whereYear('created_at',$currentYear)
            ->sum('point_used');

            if(is_null($currentMonthRedeem))
            {
                $currentMonthRedeem = 0;
            }

            $e->currentMonthRedeem = $currentMonthRedeem;
            $e->current = $e->previousBalance + $e->monthlyearn - $e->currentMonthRedeem;
            $e->unitvalue = "RM0.01";
            $totalcurrent = (float)$e->current/100;
            $totalcurrent = round($totalcurrent,2);
            $totalcurrent = number_format($totalcurrent, 2, '.', '');
            $e->totalvalue = "RM".$totalcurrent;
            $costredeem = (float)$e->currentMonthRedeem/100;
            $costredeem = round($costredeem,2);
            $costredeem = number_format($costredeem, 2, '.', '');
            $e->costredeem = $costredeem; 

            if($e->previousBalance == 0)
            {
                $e->previousBalance = '0';
            }
            if($e->monthlyearn == 0)
            {
                $e->monthlyearn = '0';
            }
            if($e->currentMonthRedeem == 0)
            {
                $e->currentMonthRedeem = '0';
            }
            if($e->current == 0)
            {
                $e->current = '0';
            }
            unset($e->cus_id);
        }
        
        return collect($epoints);
    }
}
