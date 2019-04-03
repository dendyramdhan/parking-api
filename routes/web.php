<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['prefix', 'api'], function() use ($router) {
    $router->get('create', function() {
        $abjad = ['A', 'B', 'C'];

        foreach ($abjad as $key => $value) {
            for ($i = 1; $i < 6; $i++) { 
                $parking_lot[] = [
                    'parking_lot' => $value.$i                    
                ];
            }
        }

        file_put_contents('/opt/lampp/htdocs/parking-api/parking-lot.json', json_encode($parking_lot, JSON_PRETTY_PRINT));

        $parking_lot = json_decode(file_get_contents('/opt/lampp/htdocs/parking-api/parking-lot.json'), true);

        return response()->json(['message' => 'create success', 'parking_lot' => $parking_lot], 200);
    });

    $router->post('regist', function(Request $request) {
        $parking_lot = json_decode(file_get_contents('/opt/lampp/htdocs/parking-api/parking-lot.json'), true);

        $length = count($parking_lot);

        foreach ($parking_lot as $key => $value) {
            if (!isset($value['plat_nomor'])) {
                $parking_lot[$key]['plat_nomor'] = $request->input('plat_nomor');
                $parking_lot[$key]['warna'] = $request->input('warna');
                $parking_lot[$key]['tipe'] = $request->input('tipe');
                $parking_lot[$key]['tanggal_masuk'] = date('Y-m-d H:i');
                break;
            }

            if ($key == $length - 1) {
                return response()->json(['message' => 'parking lot is full'], 200);        
            }
        }

        file_put_contents('/opt/lampp/htdocs/parking-api/parking-lot.json', json_encode($parking_lot, JSON_PRETTY_PRINT));

        $parking_lot = json_decode(file_get_contents('/opt/lampp/htdocs/parking-api/parking-lot.json'), true);          
        
        $new_parking_lot = [];

        foreach ($parking_lot as $key => $value) {
            if ($value['plat_nomor'] === $request->input('plat_nomor')) {
                $new_parking_lot['parking_lot'] = $parking_lot[$key]['parking_lot'];
                $new_parking_lot['plat_nomor'] = $parking_lot[$key]['plat_nomor']; 
                $new_parking_lot['tanggal_masuk'] = $parking_lot[$key]['tanggal_masuk'];
                break;
            }   
        }

        return response()->json($new_parking_lot, 200);
    });

    $router->post('out', function(Request $request) {
        $parking_lot = json_decode(file_get_contents('/opt/lampp/htdocs/parking-api/parking-lot.json'), true);
        
        $out_parking_lot = [];
        $tanggal_keluar = date('Y-m-d H:i');
        $biaya_parkir_jam_pertama = 0;
        $jumlah_bayar = 0;

        foreach ($parking_lot as $key => $value) {
            if (isset($value['plat_nomor']) && $value['plat_nomor'] === $request->input('plat_nomor')) {
                $out_parking_lot['plat_nomor'] = $parking_lot[$key]['plat_nomor'];
                $out_parking_lot['tanggal_masuk'] = $parking_lot[$key]['tanggal_masuk']; 
                $out_parking_lot['tanggal_keluar'] = $tanggal_keluar;

                if ($parking_lot[$key]['tipe'] === 'SUV') {
                    $biaya_parkir_jam_pertama = 25000;
                } else {
                    $biaya_parkir_jam_pertama = 35000;
                }

                $hourdiff = ceil((strtotime($tanggal_keluar) - strtotime($parking_lot[$key]['tanggal_masuk']))/3600);
                
                if ($hourdiff === 0 OR $hourdiff === 1) {
                    $out_parking_lot['jumlah_bayar'] = $biaya_parkir_jam_pertama;
                } else {
                    $jumlah_bayar = $biaya_parkir_jam_pertama + ($hourdiff * ($biaya_parkir_jam_pertama * 20/100));                    
                    $out_parking_lot['jumlah_bayar'] = $jumlah_bayar;                    
                }                             

                unset($parking_lot[$key]['plat_nomor']);
                unset($parking_lot[$key]['warna']);
                unset($parking_lot[$key]['tipe']);
                unset($parking_lot[$key]['tanggal_masuk']);
                break;
            } else {
                return response()->json(['message' => 'plat nomor not found'], 200);        
            }   
        }

        file_put_contents('/opt/lampp/htdocs/parking-api/parking-lot.json', json_encode($parking_lot, JSON_PRETTY_PRINT));
        
        return response()->json($out_parking_lot, 200);
    });

    $router->get('report_by_warna/{warna}', function($warna) {
        $parking_lot = json_decode(file_get_contents('/opt/lampp/htdocs/parking-api/parking-lot.json'), true);

        $report_parking_lot = [];

        foreach ($parking_lot as $key => $value) {            
            if ($value['warna'] === $warna) {
                $report_parking_lot['plat_nomor'][] = $parking_lot[$key]['plat_nomor'];              
            }   
        }

        return response()->json($report_parking_lot, 200);
    });

    $router->get('report_by_tipe/{tipe}', function($tipe) {
        $parking_lot = json_decode(file_get_contents('/opt/lampp/htdocs/parking-api/parking-lot.json'), true);

        $report_parking_lot = [];

        foreach ($parking_lot as $key => $value) {            
            if ($value['tipe'] === $tipe) {
                $report_parking_lot['plat_nomor'][] = $parking_lot[$key]['plat_nomor'];              
            }   
        }

        $length = count($report_parking_lot['plat_nomor']);

        return response()->json(['jumlah_kendaraan' => $length], 200);
    });
});
