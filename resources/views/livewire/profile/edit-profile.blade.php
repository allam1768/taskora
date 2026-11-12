<div class="min-h-full bg-slate-50">
    <header class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 group">
                    <div class="h-9 w-9 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                        T
                    </div>
                    <span class="text-xl font-bold tracking-tight text-slate-900 group-hover:text-indigo-600 transition">Taskora</span>
                </a>
                <span class="text-slate-300">/</span>
                <span class="text-sm font-medium text-slate-600">Pengaturan Profil</span>
            </div>

            <div class="flex items-center gap-4">
                <a
                    href="{{ route('dashboard') }}"
                    wire:navigate
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-700 hover:text-slate-900 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg transition"
                >
                    &larr; Kembali ke Dashboard
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Flash Notification --}}
        @if (session()->has('status'))
            <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 p-4 flex items-center gap-3 text-emerald-800 text-sm">
                <svg class="h-5 w-5 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <p class="font-medium">{{ session('status') }}</p>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
            <div class="border-b border-slate-100 pb-5 mb-6">
                <h1 class="text-xl font-bold text-slate-900">Informasi Profil</h1>
                <p class="mt-1 text-sm text-slate-500">Perbarui informasi profil dan kata sandi akun Anda.</p>
            </div>

            <form wire:submit="updateProfile" class="space-y-6">
                {{-- Nama --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                    <div class="mt-1.5">
                        <input
                            wire:model="name"
                            id="name"
                            type="text"
                            required
                            class="block w-full rounded-xl border @error('name') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @else border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-offset-1 transition duration-150"
                        />
                    </div>
                    @error('name')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Alamat Email</label>
                    <div class="mt-1.5">
                        <input
                            wire:model="email"
                            id="email"
                            type="email"
                            required
                            class="block w-full rounded-xl border @error('email') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @else border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-offset-1 transition duration-150"
                        />
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Ganti Password Section --}}
                <div class="border-t border-slate-100 pt-6 mt-6">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-1">Keamanan Kata Sandi</h2>
                    <p class="text-xs text-slate-500 mb-4">Kosongkan jika tidak ingin mengubah kata sandi.</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div x-data="{ show: false }">
                            <label for="password" class="block text-sm font-medium text-slate-700">Password Baru</label>
                            <div class="mt-1.5 relative">
                                <input
                                    wire:model="password"
                                    id="password"
                                    :type="show ? 'text' : 'password'"
                                    placeholder="Minimal 8 karakter"
                                    class="block w-full rounded-xl border @error('password') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @else border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror px-3.5 py-2.5 pr-10 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-offset-1 transition duration-150"
                                />
                                <button
                                    type="button"
                                    @click="show = !show"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-none"
                                >
                                    <svg x-show="!show" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="show" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-data="{ show: false }">
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Konfirmasi Password Baru</label>
                            <div class="mt-1.5 relative">
                                <input
                                    wire:model="password_confirmation"
                                    id="password_confirmation"
                                    :type="show ? 'text' : 'password'"
                                    placeholder="Ulangi password baru"
                                    class="block w-full rounded-xl border @error('password_confirmation') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @else border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror px-3.5 py-2.5 pr-10 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-offset-1 transition duration-150"
                                />
                                <button
                                    type="button"
                                    @click="show = !show"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-none"
                                >
                                    <svg x-show="!show" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="show" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Action Button --}}
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="updateProfile"
                        class="inline-flex justify-center items-center gap-2 py-2.5 px-5 border border-transparent rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm transition duration-150 disabled:opacity-60 disabled:cursor-not-allowed"
                    >
                        <svg wire:loading wire:target="updateProfile" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="updateProfile">Simpan Perubahan</span>
                        <span wire:loading wire:target="updateProfile">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
