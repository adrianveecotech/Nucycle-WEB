<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Models\LoginActivitiy;

class CustomerTableExport implements FromCollection,WithHeadings,WithEvents, WithCustomStartCell
{
    public function startCell(): string
    {
        return 'A3';
    }

    public function headings():array{
        return[
            'Nucycle ID',
            'Email',
            'Phone',
            'City',      
            'State',      
            'Individual/Company',
            'Created At',
            'Last Login Date'       
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
                $sheet->setCellValue('A2', "Customer List");
     
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
        $customers = DB::table('customer')->leftjoin('city','customer.city','=','city.id')
        ->leftjoin('state','customer.state','=','state.id')
        ->select('customer.user_id','customer.email','customer.phone','city.name as city','state.name as state','customer.isIndividual','customer.created_at','customer.updated_at')
        ->get();
        $customer_list = [];
        foreach($customers as $c)
        {
            $last_login = LoginActivitiy::where('user_id',$c->user_id)->orderBy('created_at','desc')->first();
            $cus = (object)[];
            $cus->id = $c->user_id;
            $cus->email = $c->email;
            $cus->phone = $c->phone;
            $cus->city = $c->city;
            $cus->state = $c->state;
            if($c->isIndividual == 1)
            {
                $cus->indi = "Individual";
            }
            else
            {
                $cus->indi = "Company";
            }
            $cus->lastlogin = $last_login? $last_login->created_at->format('Y-m-d H:i:s') : '' ;
            array_push($customer_list,$cus);
        }
        return collect($customer_list);
    }
}
