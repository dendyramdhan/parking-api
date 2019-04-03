<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ParksController extends Controller
{
   public function create() 
   {
        $abjad = ['A', 'B', 'C'];

        foreach ($abjad as $key => $value) {
            for ($i = 1; $i < 6; $i++) { 
                $parking_lot[] = [
                    'parking_lot' => $value.$i,
                    'plat_nomor' => '',                                      
                ];
            }
        }

        file_put_contents(resource_path().'/parking-lot.json', json_encode($parking_lot, JSON_PRETTY_PRINT));

        $parking_lot = json_decode(file_get_contents(resource_path().'/parking-lot.json'), true);

        return response()->json(['message' => 'create success', 'parking_lot' => $parking_lot], 200);
   }    

   public function regist(Request $request)
   {    
        $this->validate($request, [
           'plat_nomor' => 'required',
           'warna' => 'required',
           'tipe' => 'required',            
        ]);

        if (!file_exists(resource_path().'/parking-lot.json')) {
            return response()->json(['message' => 'parking-lot.json is not created'], 403);        
        }

        $parking_lot = json_decode(file_get_contents(resource_path().'/parking-lot.json'), true);

        $length = count($parking_lot);

        foreach ($parking_lot as $key => $value) {
            if ($value['plat_nomor'] === "") {
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

        file_put_contents(resource_path().'/parking-lot.json', json_encode($parking_lot, JSON_PRETTY_PRINT));

        $parking_lot = json_decode(file_get_contents(resource_path().'/parking-lot.json'), true);          
        
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
   }

   public function out(Request $request)
   {
        $this->validate($request, [
            'plat_nomor' => 'required'
        ]);

        if (!file_exists(resource_path().'/parking-lot.json')) {
            return response()->json(['message' => 'parking-lot.json is not created'], 403);        
        }

        $parking_lot = json_decode(file_get_contents(resource_path().'/parking-lot.json'), true);
            
        $out_parking_lot = [];
        $tanggal_keluar = date('Y-m-d H:i');
        $biaya_parkir_jam_pertama = 0;
        $jumlah_bayar = 0;        

        foreach ($parking_lot as $key => $value) {
            if ($value['plat_nomor'] === $request->input('plat_nomor')) {
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

                $parking_lot[$key]['plat_nomor'] == "";
                unset($parking_lot[$key]['warna']);
                unset($parking_lot[$key]['tipe']);
                unset($parking_lot[$key]['tanggal_masuk']);
                break;
            }            
        }

        if (count($out_parking_lot) === 0) {
            return response()->json(['message' => 'plat nomor not found'], 200);        
        }   

        file_put_contents(resource_path().'/parking-lot.json', json_encode($parking_lot, JSON_PRETTY_PRINT));
        
        return response()->json($out_parking_lot, 200);
   }

   public function reportByWarna($warna)
   {
        if (!file_exists(resource_path().'/parking-lot.json')) {
            return response()->json(['message' => 'parking-lot.json is not created'], 403);        
        }

        $parking_lot = json_decode(file_get_contents(resource_path().'/parking-lot.json'), true);
        
        $report_parking_lot = [];

        foreach ($parking_lot as $key => $value) {            
            if (!isset($value['warna'])) {
                continue;
            }

            if ($value['warna'] === $warna) {
                $report_parking_lot['plat_nomor'][] = $parking_lot[$key]['plat_nomor'];              
            }   
        }

        if (count($report_parking_lot) === 0) {
            return response()->json(['message' => 'no one '.$warna.' car'], 200);                  
        }

        return response()->json($report_parking_lot, 200);
   }

   public function reportByTipe($tipe)
   {
        if (!file_exists(resource_path().'/parking-lot.json')) {
            return response()->json(['message' => 'parking-lot.json is not created'], 403);        
        }

        $parking_lot = json_decode(file_get_contents(resource_path().'/parking-lot.json'), true);      
        $report_parking_lot = [];

        foreach ($parking_lot as $key => $value) {   
            if (!isset($value['tipe'])) {
                continue;
            }

            if ($value['tipe'] === $tipe) {
                $report_parking_lot['plat_nomor'][] = $parking_lot[$key]['plat_nomor'];              
            }   
        }

        if (count($report_parking_lot) === 0) {
            return response()->json(['message' => 'no one '.$tipe.' car'], 200);                  
        }

        $length = count($report_parking_lot['plat_nomor']);

        return response()->json(['jumlah_kendaraan' => $length], 200);
   }
}
