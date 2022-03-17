<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;

class NupSalesTableExport implements FromCollection,WithHeadings,WithEvents, WithCustomStartCell
{
    public function startCell(): string
    {
        return 'A4';
    }

    public function headings():array{
        return[
            'Transaction',
            'Status',
            'DO Num',
            'DO Date',
            'From Hub Location',
            'Buyer Name',
            'Product Type',
            'Product Des', 
            'DO Qty', 
            'DO UOM', 
            'Sales Inv Num', 
            'Sales Inv Date', 
            'Inv Dec', 
            'Inv Qty', 
            'Inv UOM', 
            'Unit Price (RM)',
            'Total Price', 
            'Receipt Date', 
            'Receipt Num', 
            'Receipt Amount', 
            'Actual Cash Receive' 
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
                $sheet->setCellValue('A2', "NUP Sales");
                
                $currentMonth = "";
                $currentYear = "";
                if (session()->has('monthinnum') && session()->has('year'))
                {
                    $currentMonth = session('monthinnum');
                    $currentYear = session('year');
                    $currentMonth = date("F", strtotime(date("Y") ."-". $currentMonth ."-01"));
                }
                else
                {
                    $current = Carbon::now();
                    $currentMonth = $current->month;
                    $currentYear = $current->year;
                    $currentMonth = date("F", strtotime(date("Y") ."-". $currentMonth ."-01"));
                }

                $sheet->setCellValue('A3', $currentYear . '-' . $currentMonth);
     
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
        $currentMonth = "";
        $currentYear = "";
        if (session()->has('monthinnum') && session()->has('year'))
        {
            $currentMonth = session('monthinnum');
            $currentYear = session('year');
            $nupsales = DB::table('waste_clearance_schedule')
                        ->join('collection_hub','waste_clearance_schedule.collection_hub_id','=','collection_hub.id')
                        ->leftjoin('waste_clearance_schedule_payment','waste_clearance_schedule.id','=','waste_clearance_schedule_payment.waste_clearance_schedule_id')
                        ->select('waste_clearance_schedule.id as do_num','waste_clearance_schedule.collection_time as do_date','collection_hub.hub_name as hub_location','waste_clearance_schedule.buyer_name','waste_clearance_schedule_payment.id as sales_inv_num','waste_clearance_schedule_payment.invoice_date as sales_inv_date','waste_clearance_schedule_payment.unit_price','waste_clearance_schedule_payment.total_price','waste_clearance_schedule_payment.receipt_date','waste_clearance_schedule_payment.receipt_number','waste_clearance_schedule_payment.total_amount as receipt_amount','waste_clearance_schedule_payment.total_amount as actual_cash_receive')
                        ->whereMonth('waste_clearance_schedule.collection_time',$currentMonth)
                        ->whereYear('waste_clearance_schedule.collection_time',$currentYear)
                        ->get();
                        $nupsales_list = [];
            foreach($nupsales as $n)
            {
               
                $product_type = [];
                $product_des = [];
                $do_qty = [];
                $waste_clearance_schedule_item = DB::table('waste_clearance_schedule_item')
                                                ->select('recycle_type_id as type_id','weight')
                                                ->where('waste_clearance_schedule_id',$n->do_num)
                                                ->whereMonth('waste_clearance_schedule_item.created_at',$currentMonth)
                                                ->whereYear('waste_clearance_schedule_item.created_at',$currentYear)
                                                ->get();
                foreach($waste_clearance_schedule_item as $item)
                {
                    $newitem = (object)[];
                    $recycle = DB::table('recycle_type')
                            ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                            ->select('recycle_type.name as product_des','recycle_category.name as product_type')
                            ->where('recycle_type.id',$item->type_id)
                            ->first();
                            if($n->do_num < 10)
                            {
                                $newitem->do_num = "DO-000".$n->do_num;
                            }
                            else if($n->do_num<100 && $n->do_num > 9)
                            {
                                $newitem->do_num = "DO-00".$n->do_num;
                            }
                            else if($n->do_num<1000 && $n->do_num > 99)
                            {
                                $newitem->do_num = "DO-0".$n->do_num;
                            }
                            else
                            {
                                $newitem->do_num = "DO-".$n->do_num;
                            }
                            $newitem->do_date = $n->do_date;
                            $newitem->hub_location = $n->hub_location;
                            $newitem->buyer_name = $n->buyer_name;
                            $newitem->sales_inv_num = $n->sales_inv_num;
                            $newitem->sales_inv_date = $n->sales_inv_date;
                            $newitem->unit_price = 0;
                            if($n->unit_price != null)
                            {
                                $price = json_decode($n->unit_price);
                                foreach($price as $p=>$v)
                                {
                                    if($recycle->product_des == $p)
                                    {
                                        $newitem->unit_price = $v;
                                    }
                                }
                            }
                            $newitem->total_price = $n->total_price;
                            $newitem->receipt_date = $n->receipt_date;
                            $newitem->receipt_number = $n->receipt_number;
                            $newitem->receipt_amount = $n->receipt_amount;
                            $newitem->actual_cash_receive = $n->actual_cash_receive;
                            $newitem->product_type = $recycle->product_type;
                            $newitem->product_des = $recycle->product_des;
                            $newitem->do_qty = $item->weight;
                            array_push($nupsales_list,$newitem);
                }                                
             

                $n->transaction = "Sales";
                $n->newstatus = "Successful";
                if($n->do_num < 10)
                {
                    $n->newdonum = "DO-000".$n->do_num;
                }
                else if($n->do_num<100 && $n->do_num > 9)
                {
                    $n->newdonum = "DO-00".$n->do_num;
                }
                else if($n->do_num<1000 && $n->do_num > 99)
                {
                    $n->newdonum= "DO-0".$n->do_num;
                }
                else
                {
                    $n->newdonum= "DO-".$n->do_num;
                }
                $n->newdodate = Carbon::parse($n->do_date)->format('Y-m-d');
                $n->newhub = $n->hub_location;
                $n->newbuyer = $n->buyer_name;
                $n->newtype = $newitem->product_type;
                $n->newdes = $newitem->product_des;
                $n->newqty = $newitem->do_qty;
                $n->uom = "KG";
                $n->newinvnum = $n->do_num;
                $n->newinvdate = Carbon::parse($n->sales_inv_date)->format('Y-m-d');
                $n->invdec = $newitem->product_des;
                $n->invqty = $newitem->do_qty;
                $n->invuom = "KG";
                $n->newunitprice = "RM".number_format((float)$newitem->unit_price, 2, '.', '');
                $n->newtotal_price = "RM".number_format((float)$n->total_price, 2, '.', '');
                $n->newreceiptdate = Carbon::parse($n->receipt_date)->format('Y-m-d');
                $n->newreceiptnum = $n->receipt_number;
                $n->newreceiptamt = "RM".number_format((float)$n->receipt_amount, 2, '.', '');
                $n->newreceiptcash = "RM".number_format((float)$n->actual_cash_receive, 2, '.', '');
                unset($n->do_num); 
                unset($n->do_date); 
                unset($n->hub_location); 
                unset($n->buyer_name); 
                unset($n->sales_inv_num); 
                unset($n->sales_inv_date); 
                unset($n->unit_price);
                unset($n->total_price); 
                unset($n->receipt_date); 
                unset($n->receipt_number);
                unset($n->receipt_amount);
                unset($n->actual_cash_receive);
                unset($n->product_type);
                unset($n->product_des); 
                unset($n->do_qty); 

            }
            foreach($nupsales_list as $newlist)
            {
                $newlist->transaction = "Sales";
                $newlist->newstatus = "Successful";
                $newlist->newdonum = $newlist->do_num;
                $newlist->newdo_date = Carbon::parse($newlist->do_date)->format('Y-m-d');
                $newlist->newhub = $newlist->hub_location;
                $newlist->newbuyer = $newlist->buyer_name;
                $newlist->newtype = $newlist->product_type;
                $newlist->newdes = $newlist->product_des;
                $newlist->newqty = $newlist->do_qty;
                $newlist->uom = "KG";
                $newlist->newinvnum = $newlist->do_num;
                $newlist->newinvdate = $newlist->sales_inv_date;
                $newlist->invdec = $newlist->product_des;
                $newlist->invqty = $newlist->do_qty;
                $newlist->invuom = "KG";
                $newlist->newunitprice = "RM".number_format((float)$newlist->unit_price, 2, '.', '');
                $newlist->newtotal_price = "RM".number_format((float)$newlist->total_price, 2, '.', '');
                $newlist->newreceiptdate = $newlist->receipt_date;
                $newlist->newreceiptnum = $newlist->receipt_number;
                $newlist->newreceiptamt = "RM".number_format((float)$newlist->receipt_amount, 2, '.', '');
                $newlist->newreceiptcash = "RM".number_format((float)$newlist->actual_cash_receive, 2, '.', '');
               
            }
            foreach ($nupsales_list as $newlist)
            {
                unset($newlist->do_num); 
                unset($newlist->do_date); 
                unset($newlist->hub_location); 
                unset($newlist->buyer_name); 
                unset($newlist->sales_inv_num); 
                unset($newlist->sales_inv_date); 
                unset($newlist->unit_price);
                unset($newlist->total_price); 
                unset($newlist->receipt_date); 
                unset($newlist->receipt_number);
                unset($newlist->receipt_amount);
                unset($newlist->actual_cash_receive);
                unset($newlist->product_type);
                unset($newlist->product_des); 
                unset($newlist->do_qty); 
            }
        }
        else
        {
            $nupsales = DB::table('waste_clearance_schedule')
                        ->join('collection_hub','waste_clearance_schedule.collection_hub_id','=','collection_hub.id')
                        ->leftjoin('waste_clearance_schedule_payment','waste_clearance_schedule.id','=','waste_clearance_schedule_payment.waste_clearance_schedule_id')
                        ->select('waste_clearance_schedule.id as do_num','waste_clearance_schedule.collection_time as do_date','collection_hub.hub_name as hub_location','waste_clearance_schedule.buyer_name','waste_clearance_schedule_payment.id as sales_inv_num','waste_clearance_schedule_payment.invoice_date as sales_inv_date','waste_clearance_schedule_payment.unit_price','waste_clearance_schedule_payment.total_price','waste_clearance_schedule_payment.receipt_date','waste_clearance_schedule_payment.receipt_number','waste_clearance_schedule_payment.total_amount as receipt_amount','waste_clearance_schedule_payment.total_amount as actual_cash_receive')
                        ->get();
            $nupsales_list = [];
            
            foreach($nupsales as $n)
            {
                
                $waste_clearance_schedule_item = DB::table('waste_clearance_schedule_item')
                                                ->select('recycle_type_id as type_id','weight')
                                                ->where('waste_clearance_schedule_id',$n->do_num)
                                                ->get();
                
                foreach($waste_clearance_schedule_item as $item)
                {
                    $newitem = (object)[];
                    $recycle = DB::table('recycle_type')
                            ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                            ->select('recycle_type.name as product_des','recycle_category.name as product_type')
                            ->where('recycle_type.id',$item->type_id)
                            ->first();
                            
                            if($n->do_num < 10)
                            {
                                $newitem->do_num = "DO-000".$n->do_num;
                            }
                            else if($n->do_num<100 && $n->do_num > 9)
                            {
                                $newitem->do_num = "DO-00".$n->do_num;
                            }
                            else if($n->do_num<1000 && $n->do_num > 99)
                            {
                                $newitem->do_num = "DO-0".$n->do_num;
                            }
                            else
                            {
                                $newitem->do_num = "DO-".$n->do_num;
                            }
                            $newitem->do_date = $n->do_date;
                            $newitem->hub_location = $n->hub_location;
                            $newitem->buyer_name = $n->buyer_name;
                            $newitem->sales_inv_num = $n->sales_inv_num;
                            $newitem->sales_inv_date = $n->sales_inv_date;
                            $newitem->unit_price = 0;
                            if($n->unit_price != null)
                            {
                                $price = json_decode($n->unit_price);
                                foreach($price as $p=>$v)
                                {
                                    if($recycle->product_des == $p)
                                    {
                                        $newitem->unit_price = $v;
                                    }
                                }
                            }
                            $newitem->total_price = $n->total_price;
                            $newitem->receipt_date = $n->receipt_date;
                            $newitem->receipt_number = $n->receipt_number;
                            $newitem->receipt_amount = $n->receipt_amount;
                            $newitem->actual_cash_receive = $n->actual_cash_receive;
                            $newitem->product_type = $recycle->product_type;
                            $newitem->product_des = $recycle->product_des;
                            $newitem->do_qty = $item->weight;
                            array_push($nupsales_list,$newitem);
                }                                
                

                $n->transaction = "Sales";
                $n->newstatus = "Successful";
                if($n->do_num < 10)
                {
                    $n->newdonum = "DO-000".$n->do_num;
                }
                else if($n->do_num<100 && $n->do_num > 9)
                {
                    $n->newdonum = "DO-00".$n->do_num;
                }
                else if($n->do_num<1000 && $n->do_num > 99)
                {
                    $n->newdonum= "DO-0".$n->do_num;
                }
                else
                {
                    $n->newdonum= "DO-".$n->do_num;
                }
                $n->newdodate = Carbon::parse($n->do_date)->format('Y-m-d');
                $n->newhub = $n->hub_location;
                $n->newbuyer = $n->buyer_name;
                $n->newtype = $newitem->product_type;
                $n->newdes = $newitem->product_des;
                $n->newqty = $newitem->do_qty;
                $n->uom = "KG";
                $n->newinvnum = $n->do_num;
                $n->newinvdate = Carbon::parse($n->sales_inv_date)->format('Y-m-d');
                $n->invdec = $newitem->product_des;
                $n->invqty = $newitem->do_qty;
                $n->invuom = "KG";
                $n->newunitprice = "RM".number_format((float)$newitem->unit_price, 2, '.', '');
                $n->newtotal_price = "RM".number_format((float)$n->total_price, 2, '.', '');
                $n->newreceiptdate = Carbon::parse($n->receipt_date)->format('Y-m-d');
                $n->newreceiptnum = $n->receipt_number;
                $n->newreceiptamt = "RM".number_format((float)$n->receipt_amount, 2, '.', '');
                $n->newreceiptcash = "RM".number_format((float)$n->actual_cash_receive, 2, '.', '');
                unset($n->do_num); 
                unset($n->do_date); 
                unset($n->hub_location); 
                unset($n->buyer_name); 
                unset($n->sales_inv_num); 
                unset($n->sales_inv_date); 
                unset($n->unit_price);
                unset($n->total_price); 
                unset($n->receipt_date); 
                unset($n->receipt_number);
                unset($n->receipt_amount);
                unset($n->actual_cash_receive);
                unset($n->product_type);
                unset($n->product_des); 
                unset($n->do_qty); 

                
            }
            foreach($nupsales_list as $newlist)
            {
                $newlist->transaction = "Sales";
                $newlist->newstatus = "Successful";
                $newlist->newdonum = $newlist->do_num;
                $newlist->newdo_date = Carbon::parse($newlist->do_date)->format('Y-m-d');
                $newlist->newhub = $newlist->hub_location;
                $newlist->newbuyer = $newlist->buyer_name;
                $newlist->newtype = $newlist->product_type;
                $newlist->newdes = $newlist->product_des;
                $newlist->newqty = $newlist->do_qty;
                $newlist->uom = "KG";
                $newlist->newinvnum = $newlist->do_num;
                $newlist->newinvdate = $newlist->sales_inv_date;
                $newlist->invdec = $newlist->product_des;
                $newlist->invqty = $newlist->do_qty;
                $newlist->invuom = "KG";
                $newlist->newunitprice = "RM".number_format((float)$newlist->unit_price, 2, '.', '');
                $newlist->newtotal_price = "RM".number_format((float)$newlist->total_price, 2, '.', '');
                $newlist->newreceiptdate = $newlist->receipt_date;
                $newlist->newreceiptnum = $newlist->receipt_number;
                $newlist->newreceiptamt = "RM".number_format((float)$newlist->receipt_amount, 2, '.', '');
                $newlist->newreceiptcash = "RM".number_format((float)$newlist->actual_cash_receive, 2, '.', '');
               
            }
            foreach ($nupsales_list as $newlist)
            {
                unset($newlist->do_num); 
                unset($newlist->do_date); 
                unset($newlist->hub_location); 
                unset($newlist->buyer_name); 
                unset($newlist->sales_inv_num); 
                unset($newlist->sales_inv_date); 
                unset($newlist->unit_price);
                unset($newlist->total_price); 
                unset($newlist->receipt_date); 
                unset($newlist->receipt_number);
                unset($newlist->receipt_amount);
                unset($newlist->actual_cash_receive);
                unset($newlist->product_type);
                unset($newlist->product_des); 
                unset($newlist->do_qty); 
            }
        }   
        return collect($nupsales_list);
    }
}
