<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;

class ClosingStockTableExport implements FromCollection,WithHeadings,WithEvents, WithCustomStartCell
{
    private $month = "";
    private $year = "";
    public function startCell(): string
    {
        return 'A3';
    }

    public function headings():array{
        return[
            'Stock Category',
            'Description',
            'Bal B/F Qty',
            'Purchase Qty',      
            'Sales Qty',      
            'Adjustment Qty',
            'Current Qty'       
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
                $sheet->setCellValue('A2', "Closing Stocks - ".$this->month."/".$this->year);
     
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
        if (session()->has('monthinnum') && session()->has('year'))
        {
            $currentMonth = session('monthinnum');
            $currentYear = session('year');
            $this->month = $currentMonth;
            $this->year = $currentYear;
            $collection1 = DB::table('collection_detail')->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
            ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
            ->whereYear('collection_detail.created_at','<',$currentYear)
            ->get();

            $collection2 = DB::table('collection_detail')->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
                        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                        ->whereMonth('collection_detail.created_at','<',$currentMonth)
                        ->whereYear('collection_detail.created_at','=',$currentYear)
                        ->get();
            $collection1n2 = $collection1->merge($collection2);
            $collection3 = DB::table('collection_detail')->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
                        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                        ->whereMonth('collection_detail.created_at','=',$currentMonth)
                        ->whereYear('collection_detail.created_at','=',$currentYear)
                        ->get();
          
            $category = DB::table('recycle_category')->select('id','name')->get();

            foreach($category as $cate)
            {
                $cate->balqty = 0;
                $cate->balamt = 0;
                $cate->purchaseqty = 0;
                $cate->purchaseamt = 0;
            }

            foreach($collection1n2 as $c)
            {
                foreach($category as $cate)
                {
                    if($c->recycle_category_id == $cate->id)
                    {
                        $cate->balqty += $c->weight;
                        $cate->balamt += $c->total_point/100;
                    }
                }
            }
            if($collection3->isEmpty())
            {
                foreach($category as $cate)
                {
                    $cate->purchaseqty = 0;
                    $cate->purchaseamt = 0;   
                }
            }
            else
            {
                foreach($collection3 as $c)
                {
                    foreach($category as $cate)
                    {
                        if($c->recycle_category_id == $cate->id)
                        {
                        $cate->purchaseqty += $c->weight;
                        $cate->purchaseamt += $c->total_point/100;
                        }
                        if($c->recycle_category_id == $cate->id)
                        {
                        $cate->purchaseqty += 0;
                        $cate->purchaseamt += 0;
                        }
                    }
                }
            }
            foreach($category as $c)
            {
                if($c->balqty + $c->purchaseqty == 0)
                {
                    $c->avgcost = 0;
                }
                else
                {
                    $c->avgcost = round(($c->balamt+$c->purchaseamt) / ($c->balqty + $c->purchaseqty),2);
                }
                $previous_waste_clearance_item = DB::table('waste_clearance_schedule_item')->join('recycle_type','waste_clearance_schedule_item.recycle_type_id','=','recycle_type.id')
                ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                ->where('recycle_category.id',$c->id)
                ->whereYear('waste_clearance_schedule_item.created_at','<',$currentYear)
                ->select(DB::raw("SUM(waste_clearance_schedule_item.weight) as weight"))->get();
                $current_waste_clearance_item = DB::table('waste_clearance_schedule_item')->join('recycle_type','waste_clearance_schedule_item.recycle_type_id','=','recycle_type.id')
                ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                ->where('recycle_category.id',$c->id)
                ->whereMonth('waste_clearance_schedule_item.created_at','<=',$currentMonth)
                ->whereYear('waste_clearance_schedule_item.created_at','=',$currentYear)
                ->select(DB::raw("SUM(waste_clearance_schedule_item.weight) as weight"))->get();
                $previous_weight = 0;
                $current_weight = 0;
                if(!($previous_waste_clearance_item->isEmpty()))
                {
                    $previous_weight = $previous_waste_clearance_item[0]->weight;
                }
                if(!($current_waste_clearance_item->isEmpty()))
                {
                    $current_weight = $current_waste_clearance_item[0]->weight;
                }
                $salesqty = round($previous_weight + $current_weight,2);
                $c->salesqty = $salesqty;
                $c->salesamt = round($c->salesqty * $c->avgcost,2);
                $c->adjustqty = $c->salesqty - ($c->balqty + $c->purchaseqty);
                $c->adjustamt = round($c->adjustqty * $c->avgcost,2);
                $c->currentqty = round(($c->balqty + $c->purchaseamt) - ($c->salesamt + $c->adjustqty),2);
                $c->currentamt = round($c->currentqty * $c->avgcost,2);

            }
           
            $inv_list = [];
            foreach($category as $c)
            {
                $inv = (object)[];
                $inv->cate = $c->name;
                $inv->des = "Mixed ".$c->name;
                $inv->balqty = number_format($c->balqty, 2, '.', '');
                $inv->purqty = number_format($c->purchaseqty, 2, '.', '');
                $inv->salesqty = number_format($c->salesqty, 2, '.', '');
                $inv->adjustqty = number_format($c->adjustqty, 2, '.', '');
                $inv->currentqty = number_format($c->currentqty, 2, '.', '');
                array_push($inv_list,$inv);
            }
        }
        else
        {
            $inv_list = [];
            $current = Carbon::now();
            $currentMonth = $current->month;
            $currentYear = $current->year;
            $this->month = $currentMonth;
            $this->year = $currentYear;

            $collection1 = DB::table('collection_detail')->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
                        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                        ->whereYear('collection_detail.created_at','<',$currentYear)
                        ->get();
    
            $collection2 = DB::table('collection_detail')->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
                        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                        ->whereMonth('collection_detail.created_at','<',$currentMonth)
                        ->whereYear('collection_detail.created_at','=',$currentYear)
                        ->get();
            $collection1n2 = $collection1->merge($collection2);
            $collection3 = DB::table('collection_detail')->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
                        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                        ->whereMonth('collection_detail.created_at','=',$currentMonth)
                        ->whereYear('collection_detail.created_at','=',$currentYear)
                        ->get();
            
            $category = DB::table('recycle_category')->select('id','name')->get();
            
            foreach($category as $cate)
            {
                $cate->balqty = 0;
                $cate->balamt = 0;
                $cate->purchaseqty = 0;
                $cate->purchaseamt = 0;
            }
    
            foreach($collection1n2 as $c)
            {
                foreach($category as $cate)
                {
                    if($c->recycle_category_id == $cate->id)
                    {
                        $cate->balqty += $c->weight;
                        $cate->balamt += $c->total_point/100;
                    }
                }
            }
            if($collection3->isEmpty())
            {
                foreach($category as $cate)
                {
                    $cate->purchaseqty = 0;
                    $cate->purchaseamt = 0;   
                }
            }
            else
            {
                foreach($collection3 as $c)
                {
                    foreach($category as $cate)
                    {
                        if($c->recycle_category_id == $cate->id)
                        {
                        $cate->purchaseqty += $c->weight;
                        $cate->purchaseamt += $c->total_point/100;
                        }
                        if($c->recycle_category_id == $cate->id)
                        {
                        $cate->purchaseqty += 0;
                        $cate->purchaseamt += 0;
                        }
                    }
                }
            }
            foreach($category as $c)
            {
                if($c->balqty + $c->purchaseqty == 0)
                {
                    $c->avgcost = 0;
                }
                else
                {
                    $c->avgcost = round(($c->balamt+$c->purchaseamt) / ($c->balqty + $c->purchaseqty),2);
                }
                $previous_waste_clearance_item = DB::table('waste_clearance_schedule_item')->join('recycle_type','waste_clearance_schedule_item.recycle_type_id','=','recycle_type.id')
                ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                ->where('recycle_category.id',$c->id)
                ->whereYear('waste_clearance_schedule_item.created_at','<',$currentYear)
                ->select(DB::raw("SUM(waste_clearance_schedule_item.weight) as weight"))->get();
                $current_waste_clearance_item = DB::table('waste_clearance_schedule_item')->join('recycle_type','waste_clearance_schedule_item.recycle_type_id','=','recycle_type.id')
                ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                ->where('recycle_category.id',$c->id)
                ->whereMonth('waste_clearance_schedule_item.created_at','<=',$currentMonth)
                ->whereYear('waste_clearance_schedule_item.created_at','=',$currentYear)
                ->select(DB::raw("SUM(waste_clearance_schedule_item.weight) as weight"))->get();
                $previous_weight = 0;
                $current_weight = 0;
                if(!($previous_waste_clearance_item->isEmpty()))
                {
                    $previous_weight = $previous_waste_clearance_item[0]->weight;
                }
                if(!($current_waste_clearance_item->isEmpty()))
                {
                    $current_weight = $current_waste_clearance_item[0]->weight;
                }
                $salesqty = round($previous_weight + $current_weight,2);
                $c->salesqty = $salesqty;
                $c->salesamt = round($c->salesqty * $c->avgcost,2);
                $c->adjustqty = $c->salesqty - ($c->balqty + $c->purchaseqty);
                $c->adjustamt = round($c->adjustqty * $c->avgcost,2);
                $c->currentqty = round(($c->balqty + $c->purchaseamt) - ($c->salesamt + $c->adjustqty),2);
                $c->currentamt = round($c->currentqty * $c->avgcost,2);
    
            }
            foreach($category as $c)
            {
                $inv = (object)[];
                $inv->cate = $c->name;
                $inv->des = "Mixed ".$c->name;
                $inv->balqty = number_format($c->balqty, 2, '.', '');
                $inv->purqty = number_format($c->purchaseqty, 2, '.', '');
                $inv->salesqty = number_format($c->salesqty, 2, '.', '');
                $inv->adjustqty = number_format($c->adjustqty, 2, '.', '');
                $inv->currentqty = number_format($c->currentqty, 2, '.', '');
                array_push($inv_list,$inv);
            }
        }   
        return collect($inv_list);
    
    }
}
