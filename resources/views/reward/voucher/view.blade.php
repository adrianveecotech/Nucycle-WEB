@extends('layouts.app')
@section('content')

<head>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
        }

        /* The Modal (background) */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1;
            /* Sit on top */
            padding-top: 100px;
            /* Location of the box */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgb(0, 0, 0);
            /* Fallback color */
            background-color: rgba(0, 0, 0, 0.4);
            /* Black w/ opacity */
        }

        /* Modal Content */
        .modal-content {
            position: relative;
            background-color: #fefefe;
            margin: auto;
            padding: 0;
            border: 1px solid #888;
            width: 50%;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -webkit-animation-name: animatetop;
            -webkit-animation-duration: 0.4s;
            animation-name: animatetop;
            animation-duration: 0.4s
        }

        /* Add Animation */
        @-webkit-keyframes animatetop {
            from {
                top: -300px;
                opacity: 0
            }

            to {
                top: 0;
                opacity: 1
            }
        }

        @keyframes animatetop {
            from {
                top: -300px;
                opacity: 0
            }

            to {
                top: 0;
                opacity: 1
            }
        }

        /* The Close Button */
        .close {
            color: white;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-header {
            padding: 2px 16px;
            background-color: #ff7605;
            color: white;
        }

        .modal-body {
            padding: 2px 16px;
        }
    </style>
</head>

<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('View Voucher') }}</div>
                <div class="card-body">
                    <div id="body">
                        <div id="modalImport" class="modal">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5>Import Confirmation</h5>
                                    <span class="close">&times;</span>
                                </div>
                                <div class="modal-body" id="modal-body">
                                    <ul id='listVoucher'></ul>
                                </div>
                                <div class="modal-footer">
                                    <a href="#" id="confirm_import" class="btn btn-xs btn-success pull-right">{{ __('Confirm') }}</a>
                                    <a href="#" id="cancel_import" class="btn btn-xs btn-danger pull-right">{{ __('Cancel') }}</a>
                                    <a href="#" style="display: none" id="close_modal" class="btn btn-xs btn-danger pull-right">{{ __('Close') }}</a>
                                </div>
                            </div>
                        </div>

                        <div id="modalSampleCsv" class="modal">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5>CSV sample</h5>
                                    <span class="close">&times;</span>
                                </div>
                                <div class="modal-body" id="modal-body">
                                    <img id="imagePreview" width="100%" src="<?php echo env('APP_URL') . '/nucycle-admin/images/images/samplecsv.png' ?>">

                                </div>
                                <div class="modal-footer">
                                    <a href="#" id="close_modalSampleCsv" class="btn btn-xs btn-danger pull-right">{{ __('Close') }}</a>
                                </div>
                            </div>
                        </div>

                        @if (\Session::has('successMsg'))
                        <div class="alert alert-success">
                            <ul>
                                <li>{!! \Session::get('successMsg') !!}</li>
                            </ul>
                        </div>
                        @endif
                        <div class="row justify-content-center">
                            <div class="col">
                                <a href="#" id="bulk_add" class="btn btn-xs btn-success">{{ __('Import via CSV') }}</a>
                                <a href="#" id="show_sample" class="btn btn-xs btn-info">{{ __('Show CSV Sample') }}</a>
                                <input type="file" id="inputFile" style="display: none;">
                                <div id="list1" class="dropdown-check-list list-redeem" tabindex="100">
                                    <span class="anchor">Redeem?</span>
                                    <ul class="items">
                                        <li><input type="checkbox" id="cbRedeem" onclick="checkRedeem()" checked/> Redeemed </li>
                                        <li><input type="checkbox" id="cbNotRedeem" onclick="checkRedeem()" checked/> Not Redeemed</li>
                                    </ul>
                                </div>
                                <div id="wrapper" style="margin-top:20px">
                                    <div class="tbl-header">
                                        <table class="table table-striped" id="tableMain">

                                            <thead>
                                                <tr>
                                                    <th><span>Voucher Code</span></th>
                                                    <th><span>Expiry Date</span></th>
                                                    <th><span>Is Used?</span></th>
                                                    <th><span>Redeemed User's Email</span></th>
                                                    <th><span>Redemption Date Time</span></th>
                                                    <th><span>Action</span></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($vouchers as $key => $voucher)
                                                <tr>
                                                    <td class="lalign"> {{$voucher->code}}</td>
                                                    <td> {{$voucher->expiry_date}}</td>
                                                    <td> {{$voucher->is_redeem == 1 ? 'Yes' : 'No'}}</td>
                                                    <td>
                                                        @if($voucher->redeemed_by)
                                                        <a href="{{route('customer.view', ['id' => $voucher->redeemed_by->customer->id])  }}">{{$voucher->redeemed_by->customer->email }}</a>
                                                        @else
                                                        N/A
                                                        @endif
                                                    </td>
                                                    <td>{{$voucher->redeemed_by->redeem_date ?? 'N/A'}}</td>
                                                    <td>
                                                        <a href="{{ route('voucher.edit', ['id' => $voucher->id])  }}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-pencil"></i></a>
                                                        <a href="{{ route('voucher.delete', ['id' => $voucher->id,'reward_id'=>$id])  }}" class="btn btn-xs btn-danger"><i class="nav-icon fa fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function checkRedeem() {
        var chkRedeem = document.getElementById("cbRedeem").checked;
        var chkNotRedeem = document.getElementById("cbNotRedeem").checked;
        var table = $('#tableMain').dataTable();
        $.fn.dataTableExt.afnFiltering.pop();
        $.fn.dataTableExt.afnFiltering.push(
            function(oSettings, aData, iDataIndex) {
                if (chkRedeem == true && chkNotRedeem == true) {
                    return aData[2] == 'Yes' || aData[2] == 'No';
                } else if (chkRedeem == false && chkNotRedeem == true) {
                    return aData[2] == 'No';
                } else if (chkRedeem == true && chkNotRedeem == false) {
                    return aData[2] == 'Yes';
                } else if (chkRedeem == false && chkNotRedeem == false) {
                    return aData[2] != 'Empty';
                }
            }
        );
        table.fnDraw();
    }

    $(document).ready(function() {
        var checkList = document.getElementById('list1');
        checkList.getElementsByClassName('anchor')[0].onclick = function(evt) {
            if (checkList.classList.contains('visible'))
                checkList.classList.remove('visible');
            else
                checkList.classList.add('visible');
        }
        $('#tableMain').dataTable();
        let parsedata = [];
        $("#confirm_import").click(function(e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{!! csrf_token() !!}'
                }
            });
            $.ajax({
                data: {
                    data: JSON.stringify(parsedata),
                    reward_id: <?php echo $id ?>
                },
                type: "POST",
                url: "https://app.nucycle.com.my/reward/insert_voucher",
                success: function(url) {
                    $('#close_modal').css('display', 'block');
                    $('#confirm_import').css('display', 'none');
                    $('#cancel_import').css('display', 'none');
                    $("#listVoucher").empty();

                    if (url == 'success') {
                        $('#modal-body').append('Import completed.');
                    } else {
                        $('#modal-body').append('Import failed. Please check your date format.');
                    }

                }
            });
        });

        $("#bulk_add").click(function(e) {
            e.preventDefault();
            $("#inputFile").trigger('click');

        });

        function uploadDealcsv() {};

        /*------ Method for read uploded csv file ------*/
        uploadDealcsv.prototype.getCsv = function(e) {

            $('#inputFile').change(function() {
                $("#listVoucher").empty();
                if (this.files && this.files[0]) {

                    var myFile = this.files[0];
                    var reader = new FileReader();

                    reader.addEventListener('load', function(e) {

                        let csvdata = e.target.result;
                        parseCsv.getParsecsvdata(csvdata);
                    });

                    reader.readAsBinaryString(myFile);

                    $(this).val('');
                }
            });
        }

        uploadDealcsv.prototype.getParsecsvdata = function(data) {



            let newLinebrk = data.split("\n");
            for (let i = 1; i < newLinebrk.length; i++) {
                if (newLinebrk[i] != '') {
                    parsedata.push(newLinebrk[i].split(","))
                    $("#listVoucher").append('<li>' + newLinebrk[i].split(",").join(' - ') + '</li>');
                }
            }
            modalImport.style.display = "block";
        }

        var parseCsv = new uploadDealcsv();
        parseCsv.getCsv();

        var modalImport = document.getElementById("modalImport");
        var modalSampleCsv = document.getElementById("modalSampleCsv");
        var show_sample = document.getElementById("show_sample");
        var span = document.getElementsByClassName("close")[0];
        var spanSecond = document.getElementsByClassName("close")[1];
        var cancel = document.getElementById("cancel_import");
        var close = document.getElementById("close_modal");
        var closeSampleCsv = document.getElementById("close_modalSampleCsv");

        show_sample.onclick = function() {
            modalSampleCsv.style.display = "block";
        }

        cancel.onclick = function() {
            modalImport.style.display = "none";
        }

        close.onclick = function() {
            modalImport.style.display = "none";
            location.reload();
        }

        closeSampleCsv.onclick = function() {
            modalSampleCsv.style.display = "none";
        }

        span.onclick = function() {
            modalImport.style.display = "none";
        }

        spanSecond.onclick = function() {
            modalSampleCsv.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modalImport) {
                modalImport.style.display = "none";
            } else if (event.target == modalSampleCsv) {
                modalSampleCsv.style.display = "none";
            }
        };
    });
</script>
@endsection