<div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-slate-50">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="flex justify-center">
            <div class="h-12 w-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white font-bold text-2xl shadow-md shadow-indigo-200">
                T
            </div>
        </div>
        <h2 class="mt-4 text-center text-2xl font-bold tracking-tight text-slate-900">
            Masuk ke Taskora
        </h2>
        <p class="mt-1 text-center text-sm text-slate-500">
            Sistem Manajemen Tugas dan Kolaborasi Tim
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <div class="bg-white py-8 px-6 shadow-sm border border-slate-200/80 rounded-2xl sm:px-10">
            <form wire:submit="login" class="space-y-5">
                {{-- Email Field --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">
                        Alamat Email
                    </label>
                    <div class="mt-1.5 relative">
                        <input
                            wire:model="email"
                            id="email"
                            type="email"
                            autocomplete="email"
                            required
                            placeholder="nama@perusahaan.com"
                            class="block w-full rounded-xl border @error('email') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @else border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-offset-1 transition duration-150"
                        />
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Field with Show/Hide toggle --}}
                <div x-data="{ show: false }">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-medium text-slate-700">
                            Password
                        </label>
                    </div>
                    <div class="mt-1.5 relative">
                        <input
                            wire:model="password"
                            id="password"
                            :type="show ? 'text' : 'password'"
                            autocomplete="current-password"
                            required
                            placeholder="••••••••"
                            class="block w-full rounded-xl border @error('password') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @else border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 @enderror px-3.5 py-2.5 pr-10 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-offset-1 transition duration-150"
                        />
                        <button
                            type="button"
                            @click="show = !show"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-none"
                            aria-label="Toggle password visibility"
                        >
                            <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="show" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            wire:model="remember"
                            type="checkbox"
                            class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 transition duration-150"
                        />
                        <span class="text-xs text-slate-600">Ingat Saya</span>
                    </label>
                </div>

                {{-- Submit Button with Loading State --}}
                <div>
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="login"
                        class="w-full flex justify-center items-center gap-2 py-2.5 px-4 border border-transparent rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm transition duration-150 disabled:opacity-60 disabled:cursor-not-allowed"
                    >
                        <svg wire:loading wire:target="login" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="login">Masuk</span>
                        <span wire:loading wire:target="login">Memproses...</span>
                    </button>
                </div>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-slate-500">
            Belum memiliki akun?
            <a href="{{ route('register') }}" wire:navigate class="font-semibold text-indigo-600 hover:text-indigo-500 hover:underline">
                Daftar sekarang
            </a>
        </p>
    </div>
</div>
