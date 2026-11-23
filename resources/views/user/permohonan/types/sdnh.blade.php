<!-- resources/views/user/permohonan/types/sdnh.blade.php -->

<div class="space-y-8 animate-fade-in-up">
    <!-- Header/Title untuk Section -->
    <div class="border-b border-gray-200 pb-4" x-show="current === 0">
        <h3 class="text-xl leading-6 font-bold text-gray-900 border-l-4 border-primary-500 pl-3">
            Formulir Surat Dispensasi Nikah (SDNH)
        </h3>
        <p class="mt-2 text-sm text-gray-500 pl-4">
            Lengkapi formulir di bawah ini dengan data yang sebenar-benarnya sesuai KTP dan dokumen pendukung.
        </p>
    </div>

    <!-- 1. DATA PEMOHON -->
    <div x-data="ocrSdnhPemohonHandler()" class="space-y-6">

    <div data-wizard-step data-title="Pemohon" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-8">
        <h2 class="text-lg font-bold text-gray-900 mb-5">Data Pemohon</h2>

        <div class="mb-5 rounded-xl border border-primary-100 bg-primary-50 p-3">
            <button type="button" @click="triggerOCR" :disabled="loading"
                class="w-full inline-flex items-center justify-center gap-2 h-12 rounded-lg bg-white border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 disabled:opacity-60 disabled:cursor-not-allowed">
                <template x-if="!loading">
                    <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </template>
                <template x-if="loading">
                    <svg class="animate-spin h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </template>
                <span x-text="loading ? 'Memproses...' : 'Scan KTP Pemohon (OCR)'"></span>
            </button>
            <p class="text-xs text-primary-700 mt-2 text-center">Otomatis mengisi data dari foto KTP.</p>
            <input type="file" x-ref="ocrInputPemohon" class="hidden" accept="image/*" @change="handleFileUpload">
        </div>

        <!-- OCR Status Message -->
        <div x-show="statusMsg" x-text="statusMsg"
            :class="statusOk ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200'"
            class="mb-4 text-sm border rounded-lg px-4 py-2" style="display:none"></div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nama Pemohon -->
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap Pemohon <span class="text-red-500">*</span></label>
                <input type="text" x-model="nama" name="nama_lengkap" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" placeholder="Sesuai KTP Pemohon">
                @error('nama_lengkap') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- NIK Pemohon -->
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">NIK Pemohon <span class="text-red-500">*</span></label>
                <input type="text" x-model="nik" inputmode="numeric" name="nik_bersangkutan" maxlength="16" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" placeholder="16 Digit NIK Pemohon">
                @error('nik_bersangkutan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Tempat & Tanggal Lahir -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir <span class="text-red-500">*</span></label>
                <input type="text" x-model="tempat_lahir" name="tempat_lahir" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" placeholder="Kota/Kabupaten">
                @error('tempat_lahir') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir <span class="text-red-500">*</span></label>
                <input type="date" x-model="tanggal_lahir" name="tanggal_lahir" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
                @error('tanggal_lahir') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Jenis Kelamin & Agama -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                <select x-ref="jenisKelaminSelect" name="jenis_kelamin" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="LAKI-LAKI" {{ old('jenis_kelamin') == 'LAKI-LAKI' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="PEREMPUAN" {{ old('jenis_kelamin') == 'PEREMPUAN' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('jenis_kelamin') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Agama <span class="text-red-500">*</span></label>
                <select x-ref="agamaSelect" name="agama" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
                    <option value="">Pilih Agama</option>
                    <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                    <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                    <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                    <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                    <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                    <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                </select>
                @error('agama') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

        </div>
    </div>

    <div data-wizard-step data-title="Alamat & pekerjaan" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-8">
        <h2 class="text-lg font-bold text-gray-900 mb-5">Alamat &amp; Pekerjaan Pemohon</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Status Perkawinan & Pekerjaan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status Perkawinan <span class="text-red-500">*</span></label>
                <select x-ref="statusPerkawinanSelect" name="status_perkawinan" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
                    <option value="">Pilih Status Perkawinan</option>
                    <option value="Belum Kawin" {{ old('status_perkawinan') == 'Belum Kawin' ? 'selected' : '' }}>Belum Kawin</option>
                    <option value="Kawin" {{ old('status_perkawinan') == 'Kawin' ? 'selected' : '' }}>Kawin</option>
                    <option value="Cerai Hidup" {{ old('status_perkawinan') == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                    <option value="Cerai Mati" {{ old('status_perkawinan') == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                </select>
                @error('status_perkawinan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan <span class="text-red-500">*</span></label>
                <select name="pekerjaan" required class="w-full select2-pekerjaan border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4 rounded-lg">
                    <option value="">Pilih/Ketik Pekerjaan</option>
                    @foreach($pekerjaanList ?? [] as $kerja)
                    <option value="{{ $kerja }}" {{ old('pekerjaan') == $kerja ? 'selected' : '' }}>{{ $kerja }}</option>
                    @endforeach
                </select>
                @error('pekerjaan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- No WA Pemohon -->
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">No. WhatsApp / HP <span class="text-red-500">*</span></label>
                <input type="text" name="no_wa" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" placeholder="Contoh: 08123456789" value="{{ old('no_wa') }}">
                @error('no_wa') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                <p class="mt-1 text-xs text-blue-600">*Nomor ini akan dihubungi apabila ada pembaruan status.</p>
            </div>

            <!-- Alamat Sesuai KTP -->
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Sesuai KTP <span class="text-red-500">*</span></label>
                <textarea x-model="alamat" name="alamat_lengkap" rows="3" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" placeholder="Sebutkan alamat lengkap RTRW/Desa/Kec"></textarea>
                @error('alamat_lengkap') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>
    </div>

    <!-- 2. DATA CALON PASANGAN -->
    <div x-data="ocrSdnhPasanganHandler()" class="space-y-6">

    <div data-wizard-step data-title="Calon pasangan" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-8">
        <h2 class="text-lg font-bold text-gray-900 mb-5">Akan Melangsungkan Pernikahan Dengan</h2>

        <div class="mb-5 rounded-xl border border-primary-100 bg-primary-50 p-3">
            <button type="button" @click="triggerOCR" :disabled="loading"
                class="w-full inline-flex items-center justify-center gap-2 h-12 rounded-lg bg-white border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 disabled:opacity-60 disabled:cursor-not-allowed">
                <template x-if="!loading">
                    <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </template>
                <template x-if="loading">
                    <svg class="animate-spin h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </template>
                <span x-text="loading ? 'Memproses...' : 'Scan KTP Pasangan (OCR)'"></span>
            </button>
            <p class="text-xs text-primary-700 mt-2 text-center">Otomatis mengisi data dari foto KTP.</p>
            <input type="file" x-ref="ocrInputPasangan" class="hidden" accept="image/*" @change="handleFileUpload">
        </div>

        <!-- OCR Status Message -->
        <div x-show="statusMsg" x-text="statusMsg"
            :class="statusOk ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200'"
            class="mb-4 text-sm border rounded-lg px-4 py-2" style="display:none"></div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nama Pasangan -->
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Calon Pasangan <span class="text-red-500">*</span></label>
                <input type="text" x-model="nama" name="nama_pasangan" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" placeholder="Nama Lengkap Calon Pasangan">
                @error('nama_pasangan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Tempat Lahir Pasangan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir <span class="text-red-500">*</span></label>
                <input type="text" x-model="tempat_lahir" name="tempat_lahir_pasangan" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
                @error('tempat_lahir_pasangan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Tanggal Lahir Pasangan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir <span class="text-red-500">*</span></label>
                <input type="date" x-model="tanggal_lahir" name="tanggal_lahir_pasangan" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
                @error('tanggal_lahir_pasangan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Jenis Kelamin Pasangan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                <select x-ref="jenisKelaminSelect" name="jenis_kelamin_pasangan" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="LAKI-LAKI" {{ old('jenis_kelamin_pasangan') == 'LAKI-LAKI' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="PEREMPUAN" {{ old('jenis_kelamin_pasangan') == 'PEREMPUAN' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('jenis_kelamin_pasangan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Agama Pasangan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Agama <span class="text-red-500">*</span></label>
                <select x-ref="agamaSelect" name="agama_pasangan" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
                    <option value="">Pilih Agama</option>
                    <option value="Islam" {{ old('agama_pasangan') == 'Islam' ? 'selected' : '' }}>Islam</option>
                    <option value="Kristen" {{ old('agama_pasangan') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                    <option value="Katolik" {{ old('agama_pasangan') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                    <option value="Hindu" {{ old('agama_pasangan') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                    <option value="Buddha" {{ old('agama_pasangan') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                    <option value="Konghucu" {{ old('agama_pasangan') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                </select>
                @error('agama_pasangan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

        </div>
    </div>

    <div data-wizard-step data-title="Alamat pasangan" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-8">
        <h2 class="text-lg font-bold text-gray-900 mb-5">Alamat &amp; Pekerjaan Calon Pasangan</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Status Perkawinan Pasangan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status Perkawinan <span class="text-red-500">*</span></label>
                <select x-ref="statusPerkawinanSelect" name="status_perkawinan_pasangan" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
                    <option value="">Pilih Status Perkawinan</option>
                    <option value="Belum Kawin" {{ old('status_perkawinan_pasangan') == 'Belum Kawin' ? 'selected' : '' }}>Belum Kawin</option>
                    <option value="Kawin" {{ old('status_perkawinan_pasangan') == 'Kawin' ? 'selected' : '' }}>Kawin</option>
                    <option value="Cerai Hidup" {{ old('status_perkawinan_pasangan') == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                    <option value="Cerai Mati" {{ old('status_perkawinan_pasangan') == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                </select>
                @error('status_perkawinan_pasangan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Pekerjaan Pasangan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan <span class="text-red-500">*</span></label>
                <select name="pekerjaan_pasangan" required class="w-full select2-pekerjaan border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4 rounded-lg">
                    <option value="">Pilih/Ketik Pekerjaan</option>
                    @foreach($pekerjaanList ?? [] as $kerja)
                    <option value="{{ $kerja }}" {{ old('pekerjaan_pasangan') == $kerja ? 'selected' : '' }}>{{ $kerja }}</option>
                    @endforeach
                </select>
                @error('pekerjaan_pasangan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Alamat Sesuai KTP Pasangan -->
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Sesuai KTP <span class="text-red-500">*</span></label>
                <textarea x-model="alamat" name="alamat_pasangan" rows="3" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" placeholder="Alamat lengkap calon pasangan"></textarea>
                @error('alamat_pasangan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>
    </div>

    <!-- 3. PELAKSANAAN PERNIKAHAN -->
    <div data-wizard-step data-title="Pelaksanaan" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-8">
        <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
            Pelaksanaan Pernikahan
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Hari Pelaksanaan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Hari <span class="text-red-500">*</span></label>
                <select name="hari_pernikahan" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
                    <option value="">Pilih Hari</option>
                    <option value="Senin" {{ old('hari_pernikahan') == 'Senin' ? 'selected' : '' }}>Senin</option>
                    <option value="Selasa" {{ old('hari_pernikahan') == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                    <option value="Rabu" {{ old('hari_pernikahan') == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                    <option value="Kamis" {{ old('hari_pernikahan') == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                    <option value="Jumat" {{ old('hari_pernikahan') == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                    <option value="Sabtu" {{ old('hari_pernikahan') == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                    <option value="Minggu" {{ old('hari_pernikahan') == 'Minggu' ? 'selected' : '' }}>Minggu</option>
                </select>
                @error('hari_pernikahan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Tanggal Pelaksanaan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_pernikahan" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" value="{{ old('tanggal_pernikahan') }}">
                @error('tanggal_pernikahan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Waktu Pelaksanaan -->
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Waktu <span class="text-red-500">*</span></label>
                <input type="text" name="waktu_pernikahan" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" placeholder="Contoh: 09.00 WITA / 10.00 WIB s.d Selesai" value="{{ old('waktu_pernikahan') }}">
                @error('waktu_pernikahan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Tempat Pelaksanaan -->
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tempat <span class="text-red-500">*</span></label>
                <input type="text" name="tempat_pernikahan" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" placeholder="Contoh: KUA Landasan Ulin / Rumah Mempelai / Gedung X" value="{{ old('tempat_pernikahan') }}">
                @error('tempat_pernikahan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Alamat Pelaksanaan -->
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Tempat Pernikahan <span class="text-red-500">*</span></label>
                <textarea name="alamat_pernikahan" rows="2" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" placeholder="Alamat lengkap tempat akad dilaksanakan">{{ old('alamat_pernikahan') }}</textarea>
                @error('alamat_pernikahan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Alasan Mempelai -->
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Mengajukan Dispensasi Nikah <span class="text-red-500">*</span></label>
                <textarea name="alasan_dispensasi" rows="3" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" placeholder="Jelaskan alasan pengajuan dispensasi...">{{ old('alasan_dispensasi') }}</textarea>
                @error('alasan_dispensasi') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <!-- 4. ATTACHMENT (DOKUMEN WAJIB) -->
    <div data-wizard-step data-title="Berkas" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-8">
        <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
            Upload Berkas Pendukung
        </h2>

        <div class="bg-amber-50 border border-amber-100 rounded-lg p-4 mb-6">
            <p class="text-sm text-amber-700">
                <strong>Catatan:</strong> Upload dokumen dalam format JPG, PNG, atau PDF. Maksimal 5MB per file. File yang digabung harap dijadikan 1 file PDF.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <!-- Pengantar RT/RW -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Surat Pengantar RT/RW Setempat <span class="text-red-500">*</span></label>
                <input type="file" name="sdnh_surat_pengantar" required accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                @error('sdnh_surat_pengantar') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- KTP dan KK -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">KTP dan KK Bersangkutan <span class="text-red-500">*</span>
                    <span class="ml-1 text-xs text-gray-500 font-normal">(Dijadikan dalam 1 File gabungan)</span>
                </label>
                <input type="file" name="sdnh_ktp_kk" required accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                @error('sdnh_ktp_kk') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Formulir Nikah N1-N5 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Formulir Pengantar Nikah (N1-N5) dari Kelurahan <span class="text-red-500">*</span>
                    <span class="ml-1 text-xs text-gray-500 font-normal">(Digabung ke 1 File PDF)</span>
                </label>
                <input type="file" name="sdnh_formulir_n" required accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                @error('sdnh_formulir_n') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Bukti PBB -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bukti Tanda Lunas PBB-P2 Tahun Berjalan <span class="text-red-500">*</span></label>
                <input type="file" name="sdnh_lunas_pbb" required accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                @error('sdnh_lunas_pbb') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Surat Rekomendasi KUA -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Surat Rekomendasi KUA <span class="text-red-500">*</span></label>
                <input type="file" name="sdnh_rekom_kua" required accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                @error('sdnh_rekom_kua') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Akta Cerai/Kematian -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fotokopi Akta Cerai atau Kematian <span class="font-normal text-gray-500">(Jika janda/duda)</span></label>
                <input type="file" name="sdnh_akta_cerai_mati" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                @error('sdnh_akta_cerai_mati') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                <p class="mt-1 text-xs text-blue-600">*Hanya dilampirkan jika pemohon atau pasangan berstatus cerai hidup/cerai mati.</p>
            </div>

        </div>
    </div>
</div>

<script>
    function ocrSdnhPemohonHandler() {
        return {
            nama: '{{ old("nama_lengkap") }}',
            nik: '{{ old("nik_bersangkutan") }}',
            tempat_lahir: '{{ old("tempat_lahir") }}',
            tanggal_lahir: '{{ old("tanggal_lahir") }}',
            alamat: '{{ old("alamat_lengkap") }}',
            loading: false,
            statusMsg: '',
            statusOk: true,

            triggerOCR() {
                this.$refs.ocrInputPemohon.value = '';
                this.$refs.ocrInputPemohon.click();
            },

            async handleFileUpload(e) {
                const file = e.target.files[0];
                if (!file) return;

                this.loading = true;
                this.statusMsg = '';

                const formData = new FormData();
                formData.append('ktp_image', file);
                formData.append('_token', '{{ csrf_token() }}');

                try {
                    const response = await fetch('{{ route("layanan.surat.ocr") }}', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        const d = result.data;
                        if (d.nama) this.nama = d.nama;
                        if (d.nik) this.nik = d.nik;
                        if (d.tempat_lahir) this.tempat_lahir = d.tempat_lahir;
                        if (d.tanggal_lahir) this.tanggal_lahir = d.tanggal_lahir;
                        if (d.alamat) this.alamat = d.alamat;
                        if (d.jenis_kelamin) {
                            this.$nextTick(() => {
                                let jk = d.jenis_kelamin.toUpperCase();
                                if (!jk.includes('LAKI')) jk = 'PEREMPUAN';
                                else jk = 'LAKI-LAKI';
                                this.$refs.jenisKelaminSelect.value = jk;
                            });
                        }
                        if (d.agama) {
                            this.$nextTick(() => {
                                let agamaTitleCase = d.agama.charAt(0).toUpperCase() + d.agama.slice(1).toLowerCase();
                                this.$refs.agamaSelect.value = agamaTitleCase;
                            });
                        }
                        if (d.status_perkawinan) {
                            this.$nextTick(() => {
                                const normalizedStatus = d.status_perkawinan.toLowerCase();
                                let selectedStatus = '';
                                if (normalizedStatus.includes('belum kawin')) selectedStatus = 'Belum Kawin';
                                else if (normalizedStatus.includes('cerai mati')) selectedStatus = 'Cerai Mati';
                                else if (normalizedStatus.includes('cerai hidup')) selectedStatus = 'Cerai Hidup';
                                else if (normalizedStatus.includes('kawin')) selectedStatus = 'Kawin';

                                if (selectedStatus) {
                                    this.$refs.statusPerkawinanSelect.value = selectedStatus;
                                }
                            });
                        }
                        this.statusOk = true;
                        this.statusMsg = '✓ Data KTP Pemohon berhasil diekstrak!';
                    } else {
                        this.statusOk = false;
                        this.statusMsg = '✗ ' + (result.message || 'Gagal memproses KTP.');
                    }
                } catch (err) {
                    console.error('OCR Error:', err);
                    this.statusOk = false;
                    this.statusMsg = '✗ Gagal menghubungi server OCR.';
                } finally {
                    this.loading = false;
                }
            }
        }
    }

    function ocrSdnhPasanganHandler() {
        return {
            nama: '{{ old("nama_pasangan") }}',
            tempat_lahir: '{{ old("tempat_lahir_pasangan") }}',
            tanggal_lahir: '{{ old("tanggal_lahir_pasangan") }}',
            alamat: '{{ old("alamat_pasangan") }}',
            loading: false,
            statusMsg: '',
            statusOk: true,

            triggerOCR() {
                this.$refs.ocrInputPasangan.value = '';
                this.$refs.ocrInputPasangan.click();
            },

            async handleFileUpload(e) {
                const file = e.target.files[0];
                if (!file) return;

                this.loading = true;
                this.statusMsg = '';

                const formData = new FormData();
                formData.append('ktp_image', file);
                formData.append('_token', '{{ csrf_token() }}');

                try {
                    const response = await fetch('{{ route("layanan.surat.ocr") }}', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        const d = result.data;
                        if (d.nama) this.nama = d.nama;
                        if (d.tempat_lahir) this.tempat_lahir = d.tempat_lahir;
                        if (d.tanggal_lahir) this.tanggal_lahir = d.tanggal_lahir;
                        if (d.alamat) this.alamat = d.alamat;
                        if (d.jenis_kelamin) {
                            this.$nextTick(() => {
                                let jk = d.jenis_kelamin.toUpperCase();
                                if (!jk.includes('LAKI')) jk = 'PEREMPUAN';
                                else jk = 'LAKI-LAKI';
                                this.$refs.jenisKelaminSelect.value = jk;
                            });
                        }
                        if (d.agama) {
                            this.$nextTick(() => {
                                let agamaTitleCase = d.agama.charAt(0).toUpperCase() + d.agama.slice(1).toLowerCase();
                                this.$refs.agamaSelect.value = agamaTitleCase;
                            });
                        }
                        if (d.status_perkawinan) {
                            this.$nextTick(() => {
                                const normalizedStatus = d.status_perkawinan.toLowerCase();
                                let selectedStatus = '';
                                if (normalizedStatus.includes('belum kawin')) selectedStatus = 'Belum Kawin';
                                else if (normalizedStatus.includes('cerai mati')) selectedStatus = 'Cerai Mati';
                                else if (normalizedStatus.includes('cerai hidup')) selectedStatus = 'Cerai Hidup';
                                else if (normalizedStatus.includes('kawin')) selectedStatus = 'Kawin';

                                if (selectedStatus) {
                                    this.$refs.statusPerkawinanSelect.value = selectedStatus;
                                }
                            });
                        }
                        this.statusOk = true;
                        this.statusMsg = '✓ Data KTP Pasangan berhasil diekstrak!';
                    } else {
                        this.statusOk = false;
                        this.statusMsg = '✗ ' + (result.message || 'Gagal memproses KTP.');
                    }
                } catch (err) {
                    console.error('OCR Error:', err);
                    this.statusOk = false;
                    this.statusMsg = '✗ Gagal menghubungi server OCR.';
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>