<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\User; // Pastikan Anda mengimpor model User
use Illuminate\Support\Facades\Auth; // Untuk menggunakan Auth
use Carbon\Carbon;

class ChatController extends Controller
{
    // Menampilkan dashboard chat
    public function index()
    {
        // Mengambil semua pesan chat dari database
        $chats = Chat::orderBy('tgl_chat', 'desc')->get();

        // Mengambil semua pengguna untuk sebut
        $users = User::all(); 

        return view('dashboard', compact('chats', 'users'));
    }

    // Menyimpan pesan chat baru
    public function store(Request $request)
    {
        // Validasi permintaan
        $validatedData = $request->validate([
            'judul_chat' => 'required|string|max:255',
            'isi_chat' => 'required|string',
            'sebut' => 'nullable|string|max:255',
        ]);

        // Ambil nama pengguna yang terautentikasi
        $user = Auth::user();

        // Menyimpan data ke database
        $chat = new Chat();
        $chat->pengirim = $user ? $user->name : 'Anonim'; // Mengambil nama dari pengguna
        $chat->judul_chat = $validatedData['judul_chat'];
        $chat->isi_chat = $validatedData['isi_chat'];
        $chat->sebut = $validatedData['sebut'];
        $chat->tgl_chat = Carbon::now();
        
        // Menghapus timestamps otomatis jika tidak diperlukan
        $chat->timestamps = false;

        $chat->save();

        return redirect()->route('dashboard')->with('success', 'Chat berhasil dikirim!');
    }
}

