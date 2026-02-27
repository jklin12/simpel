            <!-- Bagian 1: Data Surat Pengantar -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">1</span>
                    Data Surat Pengantar RT/RW
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Surat Pengantar</label>
                        <input type="text" name="nomor_pengantar" value="{{ old('nomor_pengantar') }}" placeholder="Contoh: 044/RW.009/I/2026" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('nomor_pengantar') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Surat Pengantar</label>
                        <input type="date" name="tanggal_pengantar" value="{{ old('tanggal_pengantar') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('tanggal_pengantar') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">RT</label>
                        <input type="text" name="rt" value="{{ old('rt') }}" placeholder="Contoh: 044" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('rt') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">RW</label>
                        <input type="text" name="rw" value="{{ old('rw') }}" placeholder="Contoh: 009" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('rw') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Bagian 2: Data Orang yang Meninggal -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8" x-data="ocrJenazahHandler()">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">2</span>
                        Data Jenazah / Orang yang Meninggal
                    </h2>
                    <div class="flex flex-col items-end">
                        <button type="button" @click="triggerOCR" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <svg class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Scan KTP Jenazah (OCR)
                        </button>
                        <span class="text-xs text-blue-600 mt-1">*Otomatis isi Nama, NIK, Tgl Lahir, Alamat</span>
                    </div>
                    <input type="file" x-ref="ocrInput" class="hidden" accept="image/*" @change="handleFileUpload">
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap (Almarhum/Almarhumah)</label>
                        <input type="text" x-model="nama" name="nama_jenazah" value="{{ old('nama_jenazah') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('nama_jenazah') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">NIK</label>
                            <input type="text" x-model="nik" name="nik_jenazah" value="{{ old('nik_jenazah') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" maxlength="16" required>
                            @error('nik_jenazah') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                            <select x-ref="jenisKelaminSelect" name="jk_jenazah" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L" {{ old('jk_jenazah') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jk_jenazah') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jk_jenazah') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir</label>
                            <input type="text" x-model="tempat_lahir" name="tempat_lahir_jenazah" value="{{ old('tempat_lahir_jenazah') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                            @error('tempat_lahir_jenazah') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                            <input type="date" x-model="tanggal_lahir" name="tanggal_lahir_jenazah" value="{{ old('tanggal_lahir_jenazah') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                            @error('tanggal_lahir_jenazah') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Terakhir</label>
                        <textarea x-model="alamat" name="alamat_jenazah" rows="2" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>{{ old('alamat_jenazah') }}</textarea>
                        @error('alamat_jenazah') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Agama</label>
                            <select x-ref="agamaSelect" name="agama_jenazah" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                                <option value="Islam" {{ old('agama_jenazah') == 'Islam' ? 'selected' : '' }}>Islam</option>
                                <option value="Kristen" {{ old('agama_jenazah') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                <option value="Katolik" {{ old('agama_jenazah') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                <option value="Hindu" {{ old('agama_jenazah') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                <option value="Buddha" {{ old('agama_jenazah') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                <option value="Konghucu" {{ old('agama_jenazah') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                            </select>
                            @error('agama_jenazah') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan Terakhir</label>
                            <select name="pekerjaan_jenazah" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4 select2-pekerjaan" required>
                                <option value="">Pilih Pekerjaan</option>
                                @foreach($pekerjaanList ?? [] as $pekerjaan)
                                <option value="{{ $pekerjaan }}" {{ old('pekerjaan_jenazah') == $pekerjaan ? 'selected' : '' }}>{{ $pekerjaan }}</option>
                                @endforeach
                            </select>
                            @error('pekerjaan_jenazah') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bagian 3: Detail Kejadian -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">3</span>
                    Detail Kematian
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hari Meninggal</label>
                        <select name="hari_meninggal" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                            <option value="Senin" {{ old('hari_meninggal') == 'Senin' ? 'selected' : '' }}>Senin</option>
                            <option value="Selasa" {{ old('hari_meninggal') == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                            <option value="Rabu" {{ old('hari_meninggal') == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                            <option value="Kamis" {{ old('hari_meninggal') == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                            <option value="Jumat" {{ old('hari_meninggal') == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                            <option value="Sabtu" {{ old('hari_meninggal') == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                            <option value="Minggu" {{ old('hari_meninggal') == 'Minggu' ? 'selected' : '' }}>Minggu</option>
                        </select>
                        @error('hari_meninggal') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Meninggal</label>
                        <input type="date" name="tanggal_meninggal" value="{{ old('tanggal_meninggal') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('tanggal_meninggal') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pukul</label>
                        <input type="time" name="pukul_meninggal" value="{{ old('pukul_meninggal') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('pukul_meninggal') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Meninggal</label>
                        <input type="text" name="tempat_meninggal" value="{{ old('tempat_meninggal') }}" placeholder="Rumah Sakit / Rumah / Lainnya" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('tempat_meninggal') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Penyebab Kematian</label>
                        <select name="sebab_kematian" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                            <option value="">Pilih Penyebab Kematian</option>
                            <option value="Sakit" {{ old('sebab_kematian') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="Usia Tua" {{ old('sebab_kematian') == 'Usia Tua' ? 'selected' : '' }}>Usia Tua</option>
                            <option value="Kecelakaan" {{ old('sebab_kematian') == 'Kecelakaan' ? 'selected' : '' }}>Kecelakaan</option>
                            <option value="Bunuh Diri" {{ old('sebab_kematian') == 'Bunuh Diri' ? 'selected' : '' }}>Bunuh Diri</option>
                            <option value="Wabah Penyakit" {{ old('sebab_kematian') == 'Wabah Penyakit' ? 'selected' : '' }}>Wabah Penyakit</option>
                            <option value="Kriminalitas" {{ old('sebab_kematian') == 'Kriminalitas' ? 'selected' : '' }}>Kriminalitas</option>
                            <option value="Lainnya" {{ old('sebab_kematian') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('sebab_kematian') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Pemakaman</label>
                        <input type="text" name="tempat_pemakaman" value="{{ old('tempat_pemakaman') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('tempat_pemakaman') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Bagian 4: Data Pelapor -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">4</span>
                    Data Pelapor
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap Pelapor <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_pelapor" value="{{ old('nama_pelapor') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('nama_pelapor') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">NIK Pelapor <span class="text-red-500">*</span></label>
                        <input type="text" name="nik_pelapor" value="{{ old('nik_pelapor') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" maxlength="16" required>
                        @error('nik_pelapor') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hubungan dengan Jenazah <span class="text-red-500">*</span></label>
                        <select name="hubungan_pelapor" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                            <option value="">Pilih Hubungan</option>
                            <option value="Ayah" {{ old('hubungan_pelapor') == 'Ayah' ? 'selected' : '' }}>Ayah</option>
                            <option value="Ibu" {{ old('hubungan_pelapor') == 'Ibu' ? 'selected' : '' }}>Ibu</option>
                            <option value="Suami" {{ old('hubungan_pelapor') == 'Suami' ? 'selected' : '' }}>Suami</option>
                            <option value="Istri" {{ old('hubungan_pelapor') == 'Istri' ? 'selected' : '' }}>Istri</option>
                            <option value="Anak Kandung" {{ old('hubungan_pelapor') == 'Anak Kandung' ? 'selected' : '' }}>Anak Kandung</option>
                            <option value="Saudara Kandung" {{ old('hubungan_pelapor') == 'Saudara Kandung' ? 'selected' : '' }}>Saudara Kandung</option>
                            <option value="Kerabat" {{ old('hubungan_pelapor') == 'Kerabat' ? 'selected' : '' }}>Kerabat</option>
                        </select>
                        @error('hubungan_pelapor') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">No. WhatsApp / HP Pelapor <span class="text-red-500">*</span></label>
                        <input type="text" name="no_wa" value="{{ old('no_wa') }}" placeholder="Contoh: 08123456789" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        <p class="mt-1 text-xs text-blue-600">*Nomor ini akan digunakan sebagai nomor kontak pemohon.</p>
                        @error('no_wa') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Bagian 5: Upload Berkas Pendukung -->

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 mt-6">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">5</span>
                    Upload Berkas Pendukung
                </h2>

                <div class="bg-amber-50 border border-amber-100 rounded-lg p-4 mb-6">
                    <p class="text-sm text-amber-700">
                        <strong>Catatan:</strong> Upload dokumen dalam format JPG, PNG, atau PDF. Maksimal 5MB per file. File yang digabung harap dijadikan 1 file PDF.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Surat Pengantar RT/RW Setempat <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="skm_surat_pengantar" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required>
                        @error('skm_surat_pengantar') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Blangko Pernyataan bermeterai 10.000 <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="skm_blangko_pernyataan" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required>
                        @error('skm_blangko_pernyataan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            KTP dan KK Pemohon <span class="text-red-500">*</span>
                            <span class="ml-1 text-xs text-gray-500 font-normal">(Dijadikan 1 File)</span>
                        </label>
                        <input type="file" name="skm_ktp_kk_pemohon" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required>
                        @error('skm_ktp_kk_pemohon') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            KTP dan KK yang Meninggal <span class="text-red-500">*</span>
                            <span class="ml-1 text-xs text-gray-500 font-normal">(Dijadikan 1 File)</span>
                        </label>
                        <input type="file" name="skm_ktp_kk_meninggal" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required>
                        @error('skm_ktp_kk_meninggal') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            KTP 2 orang saksi (RT yang sama) <span class="text-red-500">*</span>
                            <span class="ml-1 text-xs text-gray-500 font-normal">(Dijadikan 1 File)</span>
                        </label>
                        <input type="file" name="skm_ktp_saksi" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required>
                        @error('skm_ktp_saksi') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Bukti Tanda Lunas PBB-P2 Tahun Berjalan <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="skm_bukti_pbb" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required>
                        @error('skm_bukti_pbb') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
            <script>
                function ocrJenazahHandler() {
                    return {
                        nama: '{{ old('
                        nama_jenazah ') }}',
                        nik: '{{ old('
                        nik_jenazah ') }}',
                        tempat_lahir: '{{ old('
                        tempat_lahir_jenazah ') }}',
                        tanggal_lahir: '{{ old('
                        tanggal_lahir_jenazah ') }}',
                        alamat: '{{ old('
                        alamat_jenazah ') }}',

                        triggerOCR() {
                            this.$refs.ocrInput.click();
                        },

                        async handleFileUpload(e) {
                            const file = e.target.files[0];
                            if (!file) return;

                            const formData = new FormData();
                            formData.append('ktp_image', file);
                            formData.append('_token', '{{ csrf_token() }}');

                            // Loading
                            alert('Memproses KTP Jenazah...');

                            try {
                                let response = await fetch('<?= route('layanan.surat.ocr') ?>', {
                                    method: 'POST',
                                    body: formData
                                });

                                let result = await response.json();

                                if (result.success) {
                                    alert('Berhasil scan KTP!');
                                    // Populate placeholder data
                                    if (result.data.nama) this.nama = result.data.nama;
                                    if (result.data.nik) this.nik = result.data.nik;
                                    if (result.data.tempat_lahir) this.tempat_lahir = result.data.tempat_lahir;
                                    if (result.data.tanggal_lahir) this.tanggal_lahir = result.data.tanggal_lahir;
                                    if (result.data.alamat) this.alamat = result.data.alamat;
                                    if (result.data.jenis_kelamin) {
                                        this.$nextTick(() => {
                                            const jk = result.data.jenis_kelamin.toLowerCase() === 'laki-laki' ? 'L' : 'P';
                                            this.$refs.jenisKelaminSelect.value = jk;
                                        });
                                    }
                                    if (result.data.agama) {
                                        this.$nextTick(() => {
                                            let agamaTitleCase = result.data.agama.charAt(0).toUpperCase() + result.data.agama.slice(1).toLowerCase();
                                            this.$refs.agamaSelect.value = agamaTitleCase;
                                        });
                                    }
                                    if (result.data.pekerjaan) {
                                        this.$nextTick(() => {
                                            const ts = document.querySelector('.select2-pekerjaan').tomselect;
                                            if (ts) {
                                                let jobTitleCase = result.data.pekerjaan.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase()).join(' ');
                                                if (result.data.pekerjaan.toUpperCase() === 'PNS') jobTitleCase = 'PNS (Pegawai Negeri Sipil)';
                                                else if (result.data.pekerjaan.toUpperCase() === 'TNI') jobTitleCase = 'TNI (Tentara Nasional Indonesia)';
                                                else if (result.data.pekerjaan.toUpperCase() === 'POLRI') jobTitleCase = 'POLRI';

                                                ts.addOption({
                                                    value: jobTitleCase,
                                                    text: jobTitleCase
                                                });
                                                ts.setValue(jobTitleCase);
                                            }
                                        });
                                    }
                                }
                            } catch (error) {
                                console.error('OCR Error:', error);
                                alert('Gagal memproses OCR.');
                            }
                        }
                    }
                }
            </script>
