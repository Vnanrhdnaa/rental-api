<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use Illuminate\Http\Request;
//import
use App\Helpers\ApiFormatter;
use Exception;
class RentalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search_supir;
        $limit = $request->limit;
          //cari data berdasarkan data yang di search
          $rentals = Rental::where('supir', 'LIKE', '%'.$search. '%')->limit($limit)->get();
          //ambil semua data melalui model
          //$students = Student::all();
          if ($rentals) {
              //kalau data berhasil diambil
              return ApiFormatter::createAPI(200, 'success', $rentals);
          }else {
              //kalau data gagal diambil 
              return ApiFormatter::createAPI(400, 'failed');
          }
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required',
                'alamat' => 'required',
                'type' => 'required',
                'waktu_jam'=> 'required|numeric',
                'jam_mulai' => 'required',
                'supir' => 'required',
            ]);
            $rentals = Rental::create([
                'nama' => $request->nama,
                'alamat'=> $request->alamat,
                'type' => $request->type,
                'waktu_jam' => $request->waktu_jam,
                'total_harga' => $request->waktu_jam*150000,
                'jam_mulai' => $request->jam_mulai,
                'supir' => $request->supir,
                'jam_selesai'=> NULL,
                'tempat_tujuan' => NULL,
                'riwayat_perjalanan' => NULL,
                'status' => "proses",
            ]);
            $hasilTambahData = Rental::where('id', $rentals->id)->first();
            if ($hasilTambahData) {
                return ApiFormatter::createAPI(200, 'success', $rentals);
            }else {
                return ApiFormatter::createAPI(400, 'failed');
            }
        }catch(Exception $error) {
             //munculin deksripsi error yang bakal tampil di property data json
             return ApiFormatter::createAPI(400, 'error', $error->getMessage());
        };
    }

    public function createToken()
    {
        return csrf_token();
    }

    public function show($id)
    {
        try {
            // ambil data dari table students yang id nya sama kaya $id dari path route nya
            // where & find fungsi mencari, bedanya : where nyari berdasarkan column apa aja boleh, kalau find cuman bisa berdasarkan id nya
            $rental = Rental::find($id);
            if ($rental) {
                //kalau data berhasil diambil, tampilkan data dari $student nya dengan tanda status code 200
                return ApiFormatter::createAPI(200, 'success', $rental);
             }else {
                // kalau data gagal diambil/data gada, yang dikembaliin status code 400
                return ApiFormatter::createAPI(400, 'failed');
            }
    } catch (Exception $error) {
        return ApiFormatter::createAPI(400, 'error', $error->getMessage());
    }
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rental $rental)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
        //cek validasi inputan pada body d postman
            $request->validate([
                'jam_selesai' => 'required',
                'tempat_tujuan' => 'required',
            ]);
    
            $rentals = Rental::find($id);
                $rentals->update([
                    'jam_selesai' => $request->jam_selesai,
                    'tempat_tujuan' => $request->tempat_tujuan,
                    'riwayat_perjalanan' => "Dimulai pada saat jam $rentals->jam_mulai dengan titik awal berada di $rentals->alamat, dan diakhiri pada jam $rentals->jam_selesai dengan tempat tujuan di $rentals->tempat_tujuan",
                    'status' => "selesai",
            
        ]);
        $dataTerbaru = Rental::where('id', $rentals->id)->first();
        if ($dataTerbaru) {
            return ApiFormatter::createAPI(200, 'success', $rentals);
        } else {
            return ApiFormatter::createAPI(400, 'failed');
        }
        }catch (Exception $error) {
            return ApiFormatter::createAPI(400, 'error', $error->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            //ambil data yang mau dihapus
            $rental= Rental::find($id);
            // hapus data yang diambil diatas
            $cekBerhasil = $rental->delete();
            if ($cekBerhasil) {
                //kalau berhasil hapus, data yang dimunculi n teks konfirm dengan status code 200
                return ApiFormatter::createAPI(200, 'success', 'Data Terhapus');
            }else {
                return ApiFormatter::createAPI(400, 'failed');
            }
        } catch (Exception $error) {
            // kalau ada trouble di baris kode dalem try, error desc nya dimunculin
            return ApiFormatter::createAPI(400, 'error', $error->getMessage());
        }
    }
    public function trash ()
    {
        try{
            //ambil data yg sudah dihpus smntra
            $rentals=Rental::onlyTrashed()->get(); //hanya sampah, maggil data dri table student, data yg sudah dihapus
            if($rentals){
                //kalau dta berhasil terambil, 
                return ApiFormatter::createAPI(200, 'success', $rentals);
            }else {
                return ApiFormatter::createAPI(400, 'failed');
            }
        
        }catch(Exception $error){
            return ApiFormatter::createAPI(400, 'error', $error->getMessage());
        }
    }

    public function restore($id)
    {
        try{
            //ambil data yg akan dihapus, diambil berdasarkan id dari route nya
            $rental=Rental::onlyTrashed()->where('id', $id);
            //kembalikan data
            $rental->restore();
            //ambil kembali data yg sudah di restore
            $dataKembali = Rental::where('id', $id)->first();
            if ($dataKembali) {
                return ApiFormatter::createAPI(200, 'success', $dataKembali);
            }else{
                return ApiFormatter::createAPI(400, 'failed');
            }
        }catch(Exception $error){
            return ApiFormatter::createAPI(400, 'error', $error->getMessage());
        }
    }

    public function permanentDelete($id)
    {
        try {
            $rental= Rental::onlyTrashed()->where('id',$id);
            $proses =$rental->forceDelete();
            return ApiFormatter::createAPI(200, 'success', 'Berhasil hapus permanen!');
            

        }catch(Exception $error){
            return ApiFormatter::createAPI(400, 'error', $error->getMessage());
        }
    }
}
