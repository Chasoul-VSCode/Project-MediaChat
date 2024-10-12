<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('ChasoulUIX') }}
        </h2>
        <div class="mt-6">
            <button id="showFormBtn" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Buat Postingan Baru') }}
            </button>
            <form id="postForm" action="{{ route('chat.store') }}" method="POST" class="space-y-4 hidden mt-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="judul_chat" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Judul Chat') }}</label>
                        <input type="text" name="judul_chat" id="judul_chat" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                </div>
                <div>
                    <label for="isi_chat" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Isi Chat') }}</label>
                    <textarea name="isi_chat" id="isi_chat" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                </div>
                <div>
                    <label for="sebut" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Sebut (@username)') }}</label>
                    <input type="text" name="sebut" id="sebut" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="{{ __('Ketik username') }}">
                    <div id="suggestions" class="mt-1 hidden bg-white shadow-lg rounded-md z-10">
                        <!-- Rekomendasi nama akan muncul di sini -->
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Kirim') }}
                    </button>
                </div>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Sukses!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if(isset($chats) && count($chats) > 0)
                @foreach ($chats as $chat)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">{{ $chat->judul_chat }}</h3>
                                <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($chat->tgl_chat)->format('d/m/Y H:i') }}</span>
                            </div>
                            <p class="mt-2">{{ $chat->isi_chat }}</p>
                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Pengirim: ') . $chat->pengirim }}</span>
                                @if($chat->sebut)
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Sebut: ') . $chat->sebut }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        {{ __('Tidak ada chat untuk ditampilkan.') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const showFormBtn = document.getElementById('showFormBtn');
            const postForm = document.getElementById('postForm');
            const sebutInput = document.getElementById('sebut');
            const suggestions = document.getElementById('suggestions');
            const users = @json($users->pluck('name')); // Ambil semua nama pengguna dalam array

            showFormBtn.addEventListener('click', function() {
                postForm.classList.toggle('hidden');
                showFormBtn.textContent = postForm.classList.contains('hidden') ? '{{ __('Buat Postingan Baru') }}' : '{{ __('Tutup Form') }}';
            });

            sebutInput.addEventListener('input', function() {
                const query = this.value.toLowerCase();
                suggestions.innerHTML = ''; // Bersihkan saran sebelumnya
                if (query.length > 0) {
                    const filteredUsers = users.filter(user => user.toLowerCase().includes(query));
                    if (filteredUsers.length > 0) {
                        filteredUsers.forEach(user => {
                            const suggestionItem = document.createElement('div');
                            suggestionItem.textContent = user;
                            suggestionItem.classList.add('cursor-pointer', 'p-2', 'hover:bg-indigo-100');
                            suggestionItem.addEventListener('click', function() {
                                sebutInput.value = user; // Set value ke input
                                suggestions.innerHTML = ''; // Hapus saran setelah memilih
                                suggestions.classList.add('hidden'); // Sembunyikan saran
                            });
                            suggestions.appendChild(suggestionItem);
                        });
                        suggestions.classList.remove('hidden'); // Tampilkan saran
                    } else {
                        suggestions.classList.add('hidden'); // Sembunyikan jika tidak ada saran
                    }
                } else {
                    suggestions.classList.add('hidden'); // Sembunyikan jika input kosong
                }
            });

            // Menyembunyikan saran saat mengklik di luar
            document.addEventListener('click', function(event) {
                if (!suggestions.contains(event.target) && event.target !== sebutInput) {
                    suggestions.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>
