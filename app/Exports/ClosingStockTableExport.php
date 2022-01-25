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
            $previousMonth = 0;
            $previousYear = 0; 

            if($currentMonth == 1)
            {
                $previousMonth = 12;
                $previousYear = $currentYear-1; 
            }
            else
            {
                $previousMonth = $currentMonth-1;
                $previousYear = $currentYear; 
            }
            
           
            $balqty = [];
            $balamt = [];

            $transaction = DB::table('transaction_monthly')->where('month',$previousMonth)->Where('year',$previousYear)->select('balqty','balamt')->get();
            
            if($transaction->isEmpty())
            {
                $balqty = [0,0,0,0,0];
                $balamt = [0,0,0,0,0];
            }

            foreach($transaction as $t)
            {
                $balqty = explode(", ",$t->balqty);
                $balamt = explode(", ",$t->balamt);
            }
            //dd($category2);
            //calculate current
            //dd($previousMonth);
            $collection1 = DB::table('collection_detail')->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
                        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                        ->join('collection','collection_detail.collection_id','=','collection.id')
                        ->where('collection.status','=','1')
                        ->whereYear('collection_detail.created_at','<',$currentYear)
                        ->get();

            $collection2 = DB::table('collection_detail')->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
                        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                        ->join('collection','collection_detail.collection_id','=','collection.id')
                        ->where('collection.status','=','1')
                        ->whereMonth('collection_detail.created_at','<=',$currentMonth)
                        ->whereYear('collection_detail.created_at','=',$currentYear)
                        ->get();

            $collection1n2 = $collection1->merge($collection2);
        
            $collection3 = DB::table('collection_detail')->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
                        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                        ->join('collection','collection_detail.collection_id','=','collection.id')
                        ->where('collection.status','=','1')
                        ->whereMonth('collection_detail.created_at','=',$currentMonth)
                        ->whereYear('collection_detail.created_at','=',$currentYear)
                        ->get();
            //dd($collection2);
            $category = DB::table('recycle_category')->select('id','name')->get();
            
            foreach($category as $key=>$cate)
            {
                $cate->balqty = $balqty[$key];
                $cate->balamt  = $balamt[$key];
                $cate->purchaseqty = 0;
                $cate->purchaseamt = 0;
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
                //dd($c->balqty  + $c->purchaseqty);
                $x = (float)$c->balqty + (float)$c->purchaseqty;

                if( $x == 0)
                {
                    $c->avgcost = 0;
                }
                else
                {
                    $c->avgcost = round(( (float)$c->balamt +$c->purchaseamt) / ((float)$c->balqty + $c->purchaseqty),2);
                }
                $previous_waste_clearance_item = DB::table('waste_clearance_item')->join('recycle_type','waste_clearance_item.recycle_type_id','=','recycle_type.id')
                ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                ->where('recycle_category.id',$c->id)
                ->whereYear('waste_clearance_item.created_at','<',$currentYear)
                ->select(DB::raw("SUM(waste_clearance_item.weight) as weight"))->get();
                $current_waste_clearance_item = DB::table('waste_clearance_item')->join('recycle_type','waste_clearance_item.recycle_type_id','=','recycle_type.id')
                ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                ->where('recycle_category.id',$c->id)
                ->whereMonth('waste_clearance_item.created_at','<=',$currentMonth)
                ->whereYear('waste_clearance_item.created_at','=',$currentYear)
                ->select(DB::raw("SUM(waste_clearance_item.weight) as weight"))->get();
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
                $c->adjustqty = ((float)$c->balqty + $c->purchaseqty) - $c->salesqty;
                $c->adjustamt = round($c->adjustqty * $c->avgcost,2);
                $c->currentqty = round(((float)$c->balqty + $c->purchaseamt) - ($c->salesamt + $c->adjustqty),2);
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
            $previousMonth = 0;
            $previousYear = 0; 

            if($currentMonth == 1)
            {
                $previousMonth = 12;
                $previousYear = $currentYear-1; 
            }
            else
            {
                $previousMonth = $currentMonth-1;
                $previousYear = $currentYear; 
            }
            
           
            $balqty = [];
            $balamt = [];

            $transaction = DB::table('transaction_monthly')->where('month',$previousMonth)->Where('year',$previousYear)->select('balqty','balamt')->get();
            
            if($transaction->isEmpty())
            {
                $balqty = [0,0,0,0,0];
                $balamt = [0,0,0,0,0];
            }

            foreach($transaction as $t)
            {
                $balqty = explode(", ",$t->balqty);
                $balamt = explode(", ",$t->balamt);
            }
            //dd($category2);
            //calculate current
            //dd($previousMonth);
            $collection1 = DB::table('collection_detail')->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
                        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                        ->join('collection','collection_detail.collection_id','=','collection.id')
                        ->where('collection.status','=','1')
                        ->whereYear('collection_detail.created_at','<',$currentYear)
                        ->get();

            $collection2 = DB::table('collection_detail')->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
                        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                        ->join('collection','collection_detail.collection_id','=','collection.id')
                        ->where('collection.status','=','1')
                        ->whereMonth('collection_detail.created_at','<=',$currentMonth)
                        ->whereYear('collection_detail.created_at','=',$currentYear)
                        ->get();

            $collection1n2 = $collection1->merge($collection2);
        
            $collection3 = DB::table('collection_detail')->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
                        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                        ->join('collection','collection_detail.collection_id','=','collection.id')
                        ->where('collection.status','=','1')
                        ->whereMonth('collection_detail.created_at','=',$currentMonth)
                        ->whereYear('collection_detail.created_at','=',$currentYear)
                        ->get();
            //dd($collection2);
            $category = DB::table('recycle_category')->select('id','name')->get();
            
            foreach($category as $key=>$cate)
            {
                $cate->balqty = $balqty[$key];
                $cate->balamt  = $balamt[$key];
                $cate->purchaseqty = 0;
                $cate->purchaseamt = 0;
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
                //dd($c->balqty  + $c->purchaseqty);
                $x = (float)$c->balqty + (float)$c->purchaseqty;

                if( $x == 0)
                {
                    $c->avgcost = 0;
                }
                else
                {
                    $c->avgcost = round(( (float)$c->balamt +$c->purchaseamt) / ((float)$c->balqty + $c->purchaseqty),2);
                }
                $previous_waste_clearance_item = DB::table('waste_clearance_item')->join('recycle_type','waste_clearance_item.recycle_type_id','=','recycle_type.id')
                ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                ->where('recycle_category.id',$c->id)
                ->whereYear('waste_clearance_item.created_at','<',$currentYear)
                ->select(DB::raw("SUM(waste_clearance_item.weight) as weight"))->get();
                $current_waste_clearance_item = DB::table('waste_clearance_item')->join('recycle_type','waste_clearance_item.recycle_type_id','=','recycle_type.id')
                ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                ->where('recycle_category.id',$c->id)
                ->whereMonth('waste_clearance_item.created_at','<=',$currentMonth)
                ->whereYear('waste_clearance_item.created_at','=',$currentYear)
                ->select(DB::raw("SUM(waste_clearance_item.weight) as weight"))->get();
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
                $c->adjustqty = ((float)$c->balqty + $c->purchaseqty) - $c->salesqty;
                $c->adjustamt = round($c->adjustqty * $c->avgcost,2);
                $c->currentqty = round(((float)$c->balqty + $c->purchaseamt) - ($c->salesamt + $c->adjustqty),2);
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
