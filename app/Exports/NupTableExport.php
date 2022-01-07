<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NupTableExport implements FromCollection,WithHeadings
{

    public function headings():array{
        return[
            'Transaction',
            'Status',
            'Supplier\'s DO Num',
            'DO Date',
            'NUP GRN',
            'NUP GRN Date',
            'Hub Location',
            'Supplier ID',
            'Supplier Name',
            'Product Type',
            'Product Des',
            'DO Qty',
            'DO UOM',
            'Supplier\'s Inv Num',
            'Purchase Inv Date',
            'NUP GRN',
            'Inv Dec',
            'Int Qty',
            'Inv UOM',
            'Unit Cost',
            'Total Cost',
            'e-Coins rewarded',
            'Payment Date',
            'Petty Cash Num',
            'Payment Amt'
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $collections = DB::table('collection_detail')
        ->join('collection_hub_recycle','collection_detail.recycling_type_id','=','collection_hub_recycle.recycle_type_id')
        ->join('recycle_type','collection_detail.recycling_type_id','=','recycle_type.id')
        ->join('recycle_category','recycle_type.recycle_category_id','=','recycle_category.id')
        ->rightJoin('collection','collection.id','=','collection_detail.collection_id')
        ->join('customer','collection.customer_id','=','customer.id')
        ->join('collection_hub','collection.collection_hub_id','=','collection_hub.id')
        ->select('collection_detail.collection_id AS collection_id','collection_detail.weight AS weight','collection_detail.total_point AS total_point','collection_hub_recycle.point AS point','recycle_type.name AS type_name','recycle_category.name AS category_name','collection.status','collection.created_at','customer.membership_id AS customer_id','customer.name','collection_hub.hub_name AS location')
        ->orderBy('collection.created_at')
        ->get();
        $collection = array();
        foreach($collections as $c)
        {
            $c->transaction = "Purchase";
            if($c->status == 0)
            {
                unset($c->status);
                $c->status = "Cancelled";
            }
            else
            {
                unset($c->status);
                $c->status = "Normal";
            }
            $c->donum = "";
            $c->dodate = "";
            $c->nupgrn = "PO".$c->collection_id;
            $c->nupgrndate = Carbon::parse($c->created_at)->format('Y-m-d');
            $location = $c->location;
            unset($c->location);
            $c->location = $location;
            $c->supplierid = $c->customer_id;
            $c->suppliername = $c->name;
            $c->producttype = $c->type_name;
            $c->productdes = $c->category_name;
            $c->doqty = $c->weight;
            $c->douom = "KG";
            $c->supinvnum = "";
            $c->purinvdate = Carbon::parse($c->created_at)->format('Y-m-d');
            $c->nupgrninv = "INV".$c->collection_id;
            $c->invdec = $c->type_name;
            $c->intqty = $c->weight;
            $c->invuom = "KG";
            $point = $c->point/100;
            $point = number_format($point, 2, '.', '');
            $total_point = $c->total_point/100;
            $total_point = round($total_point,2);
            $total_point = number_format($total_point, 2, '.', '');
            $c->unitcost = $point; 
            $c->totalcost = $total_point; 
            $c->totalpoint = $c->total_point;
            $c->paymentdate = Carbon::parse($c->created_at)->format('Y-m-d');
            $c->pettynum = "";
            $c->paymentamt = "";
            unset($c->collection_id);
            unset($c->weight);
            unset($c->total_point);
            unset($c->point);
            unset($c->type_name);
            unset($c->category_name);
            unset($c->created_at);
            unset($c->customer_id);
            unset($c->name);
            array_push($collection,$c);
        }
        
        return collect($collection);
        //
    }
}
