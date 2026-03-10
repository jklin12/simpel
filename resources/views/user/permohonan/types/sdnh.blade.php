<!-- resources/views/user/permohonan/types/sdnh.blade.php -->

<div class="space-y-8 animate-fade-in-up">
    <!-- Header/Title untuk Section -->
    <div class="border-b border-gray-200 pb-4">
        <h3 class="text-xl leading-6 font-bold text-gray-900 border-l-4 border-primary-500 pl-3">
            Formulir Surat Dispensasi Nikah (SDNH)
        </h3>
        <p class="mt-2 text-sm text-gray-500 pl-4">
            Lengkapi formulir di bawah ini dengan data yang sebenar-benarnya sesuai KTP dan dokumen pendukung.
        </p>
    </div>

    <!-- 1. DATA PEMOHON -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8" x-data="ocrSdnhPemohonHandler()">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">1</span>
                Data Pemohon
            </h2>
            <div class="flex flex-col items-end">
                <button type="button" @click="triggerOCR" :disabled="loading"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-60 disabled:cursor-not-allowed">
                    <template x-if="!loading">
                        <svg class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </template>
                    <template x-if="loading">
                        <svg class="animate-spin h-5 w-5 mr-2 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </template>
                    <span x-text="loading ? 'Memproses...' : 'Scan KTP Pemohon (OCR)'"></span>
                </button>
                <span class="text-xs text-blue-600 mt-1">*Otomatis isi Nama, NIK, Tgl Lahir, dsb</span>
            </div>
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
            </div>

            <!-- NIK Pemohon -->
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">NIK Pemohon <span class="text-red-500">*</span></label>
                <input type="text" x-model="nik" name="nik_bersangkutan" maxlength="16" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" placeholder="16 Digit NIK Pemohon">
            </div>

            <!-- Tempat & Tanggal Lahir -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir <span class="text-red-500">*</span></label>
                <input type="text" x-model="tempat_lahir" name="tempat_lahir" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" placeholder="Kota/Kabupaten">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir <span class="text-red-500">*</span></label>
                <input type="date" x-model="tanggal_lahir" name="tanggal_lahir" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
            </div>

            <!-- Jenis Kelamin & Agama -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                <select x-ref="jenisKelaminSelect" name="jenis_kelamin" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="LAKI-LAKI">Laki-laki</option>
                    <option value="PEREMPUAN">Perempuan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Agama <span class="text-red-500">*</span></label>
                <select x-ref="agamaSelect" name="agama" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
                    <option value="">Pilih Agama</option>
                    <option value="Islam">Islam</option>
                    <option value="Kristen">Kristen</option>
                    <option value="Katolik">Katolik</option>
                    <option value="Hindu">Hindu</option>
                    <option value="Buddha">Buddha</option>
                    <option value="Konghucu">Konghucu</option>
                </select>
            </div>

            <!-- Status Perkawinan & Pekerjaan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status Perkawinan <span class="text-red-500">*</span></label>
                <select x-ref="statusPerkawinanSelect" name="status_perkawinan" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
                    <option value="">Pilih Status Perkawinan</option>
                    <option value="Belum Kawin">Belum Kawin</option>
                    <option value="Kawin">Kawin</option>
                    <option value="Cerai Hidup">Cerai Hidup</option>
                    <option value="Cerai Mati">Cerai Mati</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan <span class="text-red-500">*</span></label>
                <select name="pekerjaan" required class="w-full select2-pekerjaan border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4 rounded-lg">
                    <option value="">Pilih/Ketik Pekerjaan</option>
                    @foreach($pekerjaanList ?? [] as $kerja)
                    <option value="{{ $kerja }}">{{ $kerja }}</option>
                    @endforeach
                </select>
            </div>

            <!-- No WA Pemohon -->
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">No. WhatsApp / HP <span class="text-red-500">*</span></label>
                <input type="text" name="no_wa" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" placeholder="Contoh: 08123456789">
                <p class="mt-1 text-xs text-blue-600">*Nomor ini akan dihubungi apabila ada pembaruan status.</p>
            </div>

            <!-- Alamat Sesuai KTP -->
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Sesuai KTP <span class="text-red-500">*</span></label>
                <textarea x-model="alamat" name="alamat_lengkap" rows="3" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" placeholder="Sebutkan alamat lengkap RTRW/Desa/Kec"></textarea>
            </div>
        </div>
    </div>

    <!-- 2. DATA CALON PASANGAN -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8" x-data="ocrSdnhPasanganHandler()">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">2</span>
                Akan Melangsungkan Pernikahan Dengan
            </h2>
            <div class="flex flex-col items-end">
                <button type="button" @click="triggerOCR" :disabled="loading"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-60 disabled:cursor-not-allowed">
                    <template x-if="!loading">
                        <svg class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </template>
                    <template x-if="loading">
                        <svg class="animate-spin h-5 w-5 mr-2 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </template>
                    <span x-text="loading ? 'Memproses...' : 'Scan KTP Pasangan (OCR)'"></span>
                </button>
                <span class="text-xs text-blue-600 mt-1">*Bisa autofill dari berkas KTP Pasangan</span>
            </div>
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
            </div>

            <!-- Tempat Lahir Pasangan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir <span class="text-red-500">*</span></label>
                <input type="text" x-model="tempat_lahir" name="tempat_lahir_pasangan" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
            </div>

            <!-- Tanggal Lahir Pasangan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir <span class="text-red-500">*</span></label>
                <input type="date" x-model="tanggal_lahir" name="tanggal_lahir_pasangan" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
            </div>

            <!-- Jenis Kelamin Pasangan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                <select x-ref="jenisKelaminSelect" name="jenis_kelamin_pasangan" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="LAKI-LAKI">Laki-laki</option>
                    <option value="PEREMPUAN">Perempuan</option>
                </select>
            </div>

            <!-- Agama Pasangan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Agama <span class="text-red-500">*</span></label>
                <select x-ref="agamaSelect" name="agama_pasangan" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
                    <option value="">Pilih Agama</option>
                    <option value="Islam">Islam</option>
                    <option value="Kristen">Kristen</option>
                    <option value="Katolik">Katolik</option>
                    <option value="Hindu">Hindu</option>
                    <option value="Buddha">Buddha</option>
                    <option value="Konghucu">Konghucu</option>
                </select>
            </div>

            <!-- Status Perkawinan Pasangan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status Perkawinan <span class="text-red-500">*</span></label>
                <select x-ref="statusPerkawinanSelect" name="status_perkawinan_pasangan" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
                    <option value="">Pilih Status Perkawinan</option>
                    <option value="Belum Kawin">Belum Kawin</option>
                    <option value="Kawin">Kawin</option>
                    <option value="Cerai Hidup">Cerai Hidup</option>
                    <option value="Cerai Mati">Cerai Mati</option>
                </select>
            </div>

            <!-- Pekerjaan Pasangan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan <span class="text-red-500">*</span></label>
                <select name="pekerjaan_pasangan" required class="w-full select2-pekerjaan border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4 rounded-lg">
                    <option value="">Pilih/Ketik Pekerjaan</option>
                    @foreach($pekerjaanList ?? [] as $kerja)
                    <option value="{{ $kerja }}">{{ $kerja }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Alamat Sesuai KTP Pasangan -->
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Sesuai KTP <span class="text-red-500">*</span></label>
                <textarea x-model="alamat" name="alamat_pasangan" rows="3" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" placeholder="Alamat lengkap calon pasangan"></textarea>
            </div>
        </div>
    </div>

    <!-- 3. PELAKSANAAN PERNIKAHAN -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
        <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
            <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">3</span>
            Pelaksanaan Pernikahan
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Hari Pelaksanaan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Hari <span class="text-red-500">*</span></label>
                <select name="hari_pernikahan" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
                    <option value="">Pilih Hari</option>
                    <option value="Senin">Senin</option>
                    <option value="Selasa">Selasa</option>
                    <option value="Rabu">Rabu</option>
                    <option value="Kamis">Kamis</option>
                    <option value="Jumat">Jumat</option>
                    <option value="Sabtu">Sabtu</option>
                    <option value="Minggu">Minggu</option>
                </select>
            </div>

            <!-- Tanggal Pelaksanaan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_pernikahan" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
            </div>

            <!-- Waktu Pelaksanaan -->
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Waktu <span class="text-red-500">*</span></label>
                <input type="text" name="waktu_pernikahan" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" placeholder="Contoh: 09.00 WITA / 10.00 WIB s.d Selesai">
            </div>

            <!-- Tempat Pelaksanaan -->
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tempat <span class="text-red-500">*</span></label>
                <input type="text" name="tempat_pernikahan" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" placeholder="Contoh: KUA Landasan Ulin / Rumah Mempelai / Gedung X">
            </div>

            <!-- Alamat Pelaksanaan -->
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Tempat Pernikahan <span class="text-red-500">*</span></label>
                <textarea name="alamat_pernikahan" rows="2" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" placeholder="Alamat lengkap tempat akad dilaksanakan"></textarea>
            </div>

            <!-- Alasan Mempelai -->
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Mengajukan Dispensasi Nikah <span class="text-red-500">*</span></label>
                <textarea name="alasan_dispensasi" rows="3" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" placeholder="Jelaskan alasan pengajuan dispensasi..."></textarea>
            </div>
        </div>
    </div>

    <!-- 4. ATTACHMENT (DOKUMEN WAJIB) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
        <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
            <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">4</span>
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
            </div>

            <!-- KTP dan KK -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">KTP dan KK Bersangkutan <span class="text-red-500">*</span>
                    <span class="ml-1 text-xs text-gray-500 font-normal">(Dijadikan dalam 1 File gabungan)</span>
                </label>
                <input type="file" name="sdnh_ktp_kk" required accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
            </div>

            <!-- Formulir Nikah N1-N5 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Formulir Pengantar Nikah (N1-N5) dari Kelurahan <span class="text-red-500">*</span>
                    <span class="ml-1 text-xs text-gray-500 font-normal">(Digabung ke 1 File PDF)</span>
                </label>
                <input type="file" name="sdnh_formulir_n" required accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
            </div>

            <!-- Bukti PBB -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bukti Tanda Lunas PBB-P2 Tahun Berjalan <span class="text-red-500">*</span></label>
                <input type="file" name="sdnh_lunas_pbb" required accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
            </div>

            <!-- Akta Cerai/Kematian -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fotokopi Akta Cerai atau Kematian <span class="font-normal text-gray-500">(Jika janda/duda)</span></label>
                <input type="file" name="sdnh_akta_cerai_mati" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
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