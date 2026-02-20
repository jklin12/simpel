            <!-- Bagian 1: Data Surat Pengantar RT/RW -->
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
                        <input type="text" name="rt" value="{{ old('rt') }}" placeholder="Contoh: 001" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('rt') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">RW</label>
                        <input type="text" name="rw" value="{{ old('rw') }}" placeholder="Contoh: 005" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('rw') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Bagian 2: Data Diri yang Bersangkutan -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">2</span>
                    Data Diri yang Bersangkutan
                </h2>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('nama_lengkap') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">NIK</label>
                            <input type="text" name="nik_bersangkutan" value="{{ old('nik_bersangkutan') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" maxlength="16" required>
                            @error('nik_bersangkutan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">No. KK</label>
                            <input type="text" name="no_kk" value="{{ old('no_kk') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" maxlength="16" required>
                            @error('no_kk') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                            @error('tempat_lahir') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                            @error('tanggal_lahir') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jenis_kelamin') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Agama</label>
                            <select name="agama" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
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
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status Perkawinan</label>
                        <select name="status_perkawinan" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                            <option value="">Pilih Status Perkawinan</option>
                            <option value="Belum Kawin" {{ old('status_perkawinan') == 'Belum Kawin' ? 'selected' : '' }}>Belum Kawin</option>
                            <option value="Kawin" {{ old('status_perkawinan') == 'Kawin' ? 'selected' : '' }}>Kawin</option>
                            <option value="Cerai Hidup" {{ old('status_perkawinan') == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                            <option value="Cerai Mati" {{ old('status_perkawinan') == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                        </select>
                        @error('status_perkawinan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan</label>
                        <input type="text" name="pekerjaan" value="{{ old('pekerjaan') }}" placeholder="Contoh: Petani / Buruh / Tidak Bekerja" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('pekerjaan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
                        <textarea name="alamat_lengkap" rows="2" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>{{ old('alamat_lengkap') }}</textarea>
                        @error('alamat_lengkap') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Bagian 3: Keterangan Ekonomi -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">3</span>
                    Keterangan Ekonomi
                </h2>

                <div class="grid grid-cols-1 gap-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Penghasilan per Bulan</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm">Rp</span>
                                <input type="number" name="penghasilan_perbulan" value="{{ old('penghasilan_perbulan') }}" placeholder="0" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 pl-12 pr-4" required>
                            </div>
                            @error('penghasilan_perbulan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Tanggungan</label>
                            <input type="number" name="jumlah_tanggungan" value="{{ old('jumlah_tanggungan') }}" placeholder="0" min="0" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                            @error('jumlah_tanggungan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kondisi Tempat Tinggal</label>
                        <select name="kondisi_rumah" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                            <option value="">Pilih Kondisi</option>
                            <option value="Milik Sendiri" {{ old('kondisi_rumah') == 'Milik Sendiri' ? 'selected' : '' }}>Milik Sendiri</option>
                            <option value="Kontrak/Sewa" {{ old('kondisi_rumah') == 'Kontrak/Sewa' ? 'selected' : '' }}>Kontrak/Sewa</option>
                            <option value="Menumpang" {{ old('kondisi_rumah') == 'Menumpang' ? 'selected' : '' }}>Menumpang</option>
                            <option value="Lainnya" {{ old('kondisi_rumah') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('kondisi_rumah') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Bagian 4: Keperluan SKTM -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">4</span>
                    Keperluan Surat
                </h2>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keperluan SKTM</label>
                        <select name="keperluan_sktm" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                            <option value="">Pilih Keperluan</option>
                            <option value="Beasiswa Pendidikan" {{ old('keperluan_sktm') == 'Beasiswa Pendidikan' ? 'selected' : '' }}>Beasiswa Pendidikan</option>
                            <option value="Keringanan Biaya Pendidikan" {{ old('keperluan_sktm') == 'Keringanan Biaya Pendidikan' ? 'selected' : '' }}>Keringanan Biaya Pendidikan</option>
                            <option value="Bantuan Biaya Kesehatan" {{ old('keperluan_sktm') == 'Bantuan Biaya Kesehatan' ? 'selected' : '' }}>Bantuan Biaya Kesehatan</option>
                            <option value="Bantuan Sosial" {{ old('keperluan_sktm') == 'Bantuan Sosial' ? 'selected' : '' }}>Bantuan Sosial</option>
                            <option value="Keringanan Biaya Pengobatan" {{ old('keperluan_sktm') == 'Keringanan Biaya Pengobatan' ? 'selected' : '' }}>Keringanan Biaya Pengobatan</option>
                            <option value="Lainnya" {{ old('keperluan_sktm') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('keperluan_sktm') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan Tambahan</label>
                        <textarea name="keterangan_tambahan" rows="3" placeholder="Jelaskan secara singkat keperluan SKTM ini (opsional)" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">{{ old('keterangan_tambahan') }}</textarea>
                        @error('keterangan_tambahan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Bagian 5: Upload Berkas Pendukung -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">5</span>
                    Upload Berkas Pendukung
                </h2>

                <div class="bg-amber-50 border border-amber-100 rounded-lg p-4 mb-6">
                    <p class="text-sm text-amber-700">
                        <strong>Catatan:</strong> Upload dokumen dalam format JPG, PNG, atau PDF. Maksimal 2MB per file.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto KTP <span class="text-red-500">*</span></label>
                        <input type="file" name="foto_ktp" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required>
                        @error('foto_ktp') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto KK <span class="text-red-500">*</span></label>
                        <input type="file" name="foto_kk" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required>
                        @error('foto_kk') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Surat Pengantar RT <span class="text-red-500">*</span></label>
                        <input type="file" name="surat_pengantar_rt" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required>
                        @error('surat_pengantar_rt') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Rumah <span class="text-red-500">*</span></label>
                        <input type="file" name="foto_rumah" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required>
                        @error('foto_rumah') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>