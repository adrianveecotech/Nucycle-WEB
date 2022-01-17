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
        return 'A3';
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
                    $recycle = DB::table('recycle_type')
                            ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                            ->select('recycle_type.name as product_des','recycle_category.name as product_type')
                            ->where('recycle_type.id',$item->type_id)
                            ->first();
                    array_push($product_type,$recycle->product_type);
                    array_push($product_des,$recycle->product_des);
                    array_push($do_qty,$item->weight);
                }                                
                $n->product_type = json_encode($product_type);
                $n->product_des = json_encode($product_des);
                $n->do_qty = json_encode($do_qty);

                $n->transaction = "Sales";
                $n->newstatus = "Successful";
                $n->newdonum = $n->do_num;
                $n->newdodate = Carbon::parse($n->do_date)->format('Y-m-d');
                $n->newhub = $n->hub_location;
                $n->newbuyer = $n->buyer_name;
                $n->newtype = $n->product_type;
                $n->newdes = $n->product_des;
                $n->newqty = $n->do_qty;
                $n->uom = "KG";
                $n->newinvnum = $n->sales_inv_num;
                $n->newinvdate = Carbon::parse($n->sales_inv_date)->format('Y-m-d');
                $n->invdec = $n->product_des;
                $n->invqty = $n->do_qty;
                $n->invuom = "KG";
                $n->newunitprice = $n->unit_price;
                $n->newtotal_price = "RM".$n->total_price;
                $n->newreceiptdate = Carbon::parse($n->receipt_date)->format('Y-m-d');
                $n->newreceiptnum = $n->receipt_number;
                $n->newreceiptamt = "RM".$n->receipt_amount;
                $n->newreceiptcash = "RM".$n->actual_cash_receive;
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
        }
        else
        {
            $nupsales = DB::table('waste_clearance_schedule')
                        ->join('collection_hub','waste_clearance_schedule.collection_hub_id','=','collection_hub.id')
                        ->leftjoin('waste_clearance_schedule_payment','waste_clearance_schedule.id','=','waste_clearance_schedule_payment.waste_clearance_schedule_id')
                        ->select('waste_clearance_schedule.id as do_num','waste_clearance_schedule.collection_time as do_date','collection_hub.hub_name as hub_location','waste_clearance_schedule.buyer_name','waste_clearance_schedule_payment.id as sales_inv_num','waste_clearance_schedule_payment.invoice_date as sales_inv_date','waste_clearance_schedule_payment.unit_price','waste_clearance_schedule_payment.total_price','waste_clearance_schedule_payment.receipt_date','waste_clearance_schedule_payment.receipt_number','waste_clearance_schedule_payment.total_amount as receipt_amount','waste_clearance_schedule_payment.total_amount as actual_cash_receive')
                        ->get();

            foreach($nupsales as $n)
            {
                $product_type = [];
                $product_des = [];
                $do_qty = [];
                $waste_clearance_schedule_item = DB::table('waste_clearance_schedule_item')
                                                ->select('recycle_type_id as type_id','weight')
                                                ->where('waste_clearance_schedule_id',$n->do_num)
                                                ->get();
                foreach($waste_clearance_schedule_item as $item)
                {
                    $recycle = DB::table('recycle_type')
                            ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
                            ->select('recycle_type.name as product_des','recycle_category.name as product_type')
                            ->where('recycle_type.id',$item->type_id)
                            ->first();
                    array_push($product_type,$recycle->product_type);
                    array_push($product_des,$recycle->product_des);
                    array_push($do_qty,$item->weight);
                }                                
                $n->product_type = json_encode($product_type);
                $n->product_des = json_encode($product_des);
                $n->do_qty = json_encode($do_qty);

                $n->transaction = "Sales";
                $n->newstatus = "Successful";
                $n->newdonum = $n->do_num;
                $n->newdodate = Carbon::parse($n->do_date)->format('Y-m-d');
                $n->newhub = $n->hub_location;
                $n->newbuyer = $n->buyer_name;
                $n->newtype = $n->product_type;
                $n->newdes = $n->product_des;
                $n->newqty = $n->do_qty;
                $n->uom = "KG";
                $n->newinvnum = $n->do_num;
                $n->newinvdate = Carbon::parse($n->sales_inv_date)->format('Y-m-d');
                $n->invdec = $n->product_des;
                $n->invqty = $n->do_qty;
                $n->invuom = "KG";
                $n->newunitprice = $n->unit_price;
                $n->newtotal_price = "RM".$n->total_price;
                $n->newreceiptdate = Carbon::parse($n->receipt_date)->format('Y-m-d');
                $n->newreceiptnum = $n->receipt_number;
                $n->newreceiptamt = "RM".$n->receipt_amount;
                $n->newreceiptcash = "RM".$n->actual_cash_receive;
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
        }
        
            
            return collect($nupsales);
    }
}
