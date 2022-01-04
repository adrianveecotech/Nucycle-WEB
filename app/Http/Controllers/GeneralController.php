<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\City;
use App\Models\CollectionHub;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function summernoteUploadImage()
    {

        if ($_FILES['file']['name']) {
            if (!$_FILES['file']['error']) {
                $locations = $_POST['locations'];
                $name1 = md5(rand(100, 200));
                $name2 = date("Ymdhis");
                $name = $name2 . $name1;
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                $filename = $name . '.' . $ext;
                $destination = public_path() . '/nucycle-admin/images/' . $locations . '/' . $filename;
                $location = $_FILES["file"]["tmp_name"];
                move_uploaded_file($location, $destination);
                echo asset('nucycle-admin/images/' . $locations . '/' . $filename);
            } else {
                echo $message = 'Ooops!  Your upload triggered the following error:  ' . $_FILES['file']['error'];
            }
        }
    }

    public function get_city_by_state(Request $request)
    {
        $cities = City::where('state_id', $request->state_id)->get();
        $html = '';

        foreach ($cities as $city) {
            if ($city->id == $request->selected_city_id)
                $html .= '<option value = ' . $city->id . ' selected >' . $city->name . '</option>';
            else
                $html .= '<option value = ' . $city->id . '>' . $city->name . '</option>';
        }
        return response()->json(['html' => $html]);
    }
}
