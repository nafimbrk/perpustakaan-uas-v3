<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Books;
use App\Models\Peminjaman;

class SyncController extends Controller
{
    public function syncData(Request $request)
{
    $logs = $request->all();  // Mengambil semua log yang dikirimkan

    if (empty($logs)) {
        return response()->json(['message' => 'Data tidak valid', 'data' => $logs], 400);
    }

    foreach ($logs as $log) {
        if (!isset($log['operation']) || !isset($log['table'])) {
            return response()->json(['message' => 'Operasi atau tabel tidak ditemukan', 'log' => $log], 400);
        }

        $operation = strtolower($log['operation']);
        if (!in_array($operation, ['insert', 'update', 'delete'])) {
            return response()->json(['message' => 'Operasi tidak dikenal', 'log' => $log], 400);
        }

        // Menangani operasi update
        if ($operation === 'update' && isset($log['changes'])) {
            $data = $log['changes']['after'];
            if (isset($data['recordId'])) {
                Books::updateOrCreate(
                    ['id' => $data['recordId']],
                    $data
                );
            } else {
                return response()->json(['message' => 'Data buku tidak lengkap', 'log' => $log], 400);
            }
        } 
        // Menangani operasi insert
        elseif ($operation === 'insert') {
            $data = $log['data'];
            if (isset($data['recordId']) && !empty($data['recordId'])) {
                // Periksa apakah data buku sudah lengkap
                if (!isset($data['judul']) || !isset($data['pengarang']) || !isset($data['genre']) || !isset($data['tahunTerbit']) || !isset($data['penerbit']) || !isset($data['jumlahHalaman'])) {
                    return response()->json(['message' => 'Data buku tidak lengkap', 'log' => $log], 400);
                }
                
                // Menyimpan buku baru
                Books::create($data);
            } else {
                return response()->json(['message' => 'Data buku tidak lengkap', 'log' => $log], 400);
            }
        }

        // Menangani operasi delete
        elseif ($operation === 'delete') {
            $data = $log['data'];
            if (isset($data['recordId']) && !empty($data['recordId'])) {
                // Hapus data berdasarkan ID yang ada
                Books::where('id', $data['recordId'])->delete();
                return response()->json(['message' => 'Buku berhasil dihapus', 'log' => $log]);
            } else {
                return response()->json(['message' => 'Data buku tidak lengkap', 'log' => $log], 400);
            }
        }
    }

    return response()->json(['message' => 'Data berhasil disinkronkan']);
}







    



    // Mengambil data terbaru dari server
    public function getData()
    {
        $books = Books::all(); // Ambil data buku
        $peminjaman = Peminjaman::all(); // Ambil data peminjaman
    
        return response()->json([
            'data' => [
                'books' => $books,
                'peminjaman' => $peminjaman
            ]
        ]);
    }
}
