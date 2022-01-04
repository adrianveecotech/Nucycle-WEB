<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function edit($id)
    {
        $voucher = Voucher::where('id', $id)->first();
        return view('reward.voucher.edit', compact('voucher'));
    }

    public function view($id)
    {
        $vouchers = Voucher::where('reward_id', $id)->get();
        return view('reward.voucher.view', compact('vouchers', 'id'));
    }

    public function insert(Request $request)
    {
        $data = json_decode($request->data);
        $reward_id = $request->reward_id;
        foreach ($data as $array) {
            $date = str_replace('/', '-', $array[1]);
            if (strtotime($date) == '') {
                return 'fail';
            }
        }

        foreach ($data as $array) {
            $date = str_replace('/', '-', $array[1]);

            Voucher::create([
                'reward_id' => $reward_id,
                'code' => $array[0],
                'expiry_date' => date('Y-m-d', strtotime($date)),
                'is_redeem' => 0,
            ]);
        }

        return 'success';
    }

    public function edit_db(Request $request)
    {
        $voucher = Voucher::where('id', $request->id)->first();
        // if ($request->index == null) {
        //     $vouchers->each->delete();
        // } else {
        //     foreach ($vouchers as $key => $value) {
        //         if (!in_array($value->id, $request->index))
        //             $value->delete();
        //     }

        //     foreach ($request->index as $key => $index) {
        //         $voucher = Voucher::find($index);
        //         $voucher->code = $request->code[$key];
        //         if ($request->has('is_redeem')) {
        //             if (in_array($index, $request->is_redeem)) {
        //                 $voucher->is_redeem = 1;
        //             } else
        //                 $voucher->is_redeem = 0;
        //         } else {
        //             $voucher->is_redeem = $request->is_redeem = 0;
        //         }
        //         $voucher->save();
        //     }
        // }
        // if ($request->has('indexNew')) {
        //     foreach ($request->indexNew as $key => $value) {
        //         Voucher::create([
        //             'reward_id' => $request->reward_id,
        //             'code' => $request->codeNew[$key],
        //             'expiry_date' => date('Y-m-d', $request->expiry_date),
        //             'is_redeem' => $request->has('active_statusNew') ? (in_array($value, $request->active_statusNew) ? 1 : 0) : 0
        //         ]);
        //     }
        // }
        $voucher->code = $request->code;
        $voucher->expiry_date = $request->expiry_date;
        $voucher->is_redeem = $request->is_redeem ? 1 : 0;
        $voucher->save();

        return redirect()->route('voucher.view', ['id' => $request->reward_id])->with('successMsg', 'Voucher is edited.');

    }

    public function delete($id, $reward_id)
    {
        $voucher = Voucher::find($id);
        $voucher->delete();
        return redirect()->route('voucher.view', ['id' => $reward_id])->with('successMsg', 'Voucher is deleted.');
    }
}
