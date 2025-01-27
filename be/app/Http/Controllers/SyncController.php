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
            return response()->json(['message' => 'Data tidak valid'], 400);
        }
    
        // Proses setiap entri dalam log
        foreach ($logs as $log) {
            if (!isset($log['operation']) || !isset($log['table'])) {
                return response()->json(['message' => 'Operasi atau tabel tidak ditemukan'], 400);
            }
    
            if (isset($log['data'])) {
                $data = $log['data'];
                if (isset($data['recordId'])) {  // Gunakan recordId yang dikirimkan dari frontend
                    // Lakukan operasi insert atau update
                    if ($log['operation'] === 'insert' || $log['operation'] === 'update') {
                        Books::updateOrCreate(
                            ['id' => $data['recordId']],  // Ganti id dengan recordId
                            $data
                        );
                    } elseif ($log['operation'] === 'delete') {
                        Books::where('id', $data['recordId'])->delete();  // Ganti id dengan recordId
                    }
                } else {
                    return response()->json(['message' => 'Data buku tidak lengkap'], 400);
                }
            } else {
                return response()->json(['message' => 'Data tidak ditemukan'], 400);
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
