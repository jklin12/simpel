            <!-- Bagian 1: Data Diri Pemohon -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8" x-data="ocrSkmhHandler()">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">1</span>
                        Data Diri Pemohon
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
                            <span x-text="loading ? 'Memproses...' : 'Scan KTP (OCR)'"></span>
                        </button>
                        <span class="text-xs text-blue-600 mt-1">*Otomatis isi Nama, NIK, Tgl Lahir, Alamat</span>
                    </div>
                    <input type="file" x-ref="ocrInput" class="hidden" accept="image/*" @change="handleFileUpload">
                </div>

                <!-- OCR Status -->
                <div x-show="statusMsg" x-text="statusMsg"
                    :class="statusOk ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200'"
                    class="mb-4 text-sm border rounded-lg px-4 py-2" style="display:none"></div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pemohon <span class="text-red-500">*</span></label>
                        <input type="text" x-model="nama" name="nama_lengkap" value="{{ old('nama_lengkap') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('nama_lengkap') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">NIK Pemohon <span class="text-red-500">*</span></label>
                            <input type="text" x-model="nik" name="nik_bersangkutan" value="{{ old('nik_bersangkutan') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" maxlength="16" required>
                            @error('nik_bersangkutan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">No HP (Wajib WA) <span class="text-red-500">*</span></label>
                            <input type="text" name="no_wa" value="{{ old('no_wa') }}" placeholder="Contoh: 08123456789" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                            @error('no_wa') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select x-ref="jenisKelaminSelect" name="jenis_kelamin" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jenis_kelamin') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Agama <span class="text-red-500">*</span></label>
                            <select x-ref="agamaSelect" name="agama" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kewarganegaraan <span class="text-red-500">*</span></label>
                            <select name="kewarganegaraan" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                                <option value="WNI" {{ old('kewarganegaraan','WNI') == 'WNI' ? 'selected' : '' }}>WNI</option>
                                <option value="WNA" {{ old('kewarganegaraan') == 'WNA' ? 'selected' : '' }}>WNA</option>
                            </select>
                            @error('kewarganegaraan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan <span class="text-red-500">*</span></label>
                            <select name="pekerjaan" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4 select2-pekerjaan" required>
                                <option value="">Pilih Pekerjaan</option>
                                @foreach($pekerjaanList ?? [] as $pekerjaan)
                                <option value="{{ $pekerjaan }}" {{ old('pekerjaan') == $pekerjaan ? 'selected' : '' }}>{{ $pekerjaan }}</option>
                                @endforeach
                            </select>
                            @error('pekerjaan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir <span class="text-red-500">*</span></label>
                            <input type="text" x-model="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                            @error('tempat_lahir') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" x-model="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                            @error('tanggal_lahir') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Sesuai KTP <span class="text-red-500">*</span></label>
                        <textarea x-model="alamat" name="alamat_lengkap" rows="2" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>{{ old('alamat_lengkap') }}</textarea>
                        @error('alamat_lengkap') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status Perkawinan <span class="text-red-500">*</span></label>
                        <select name="status_perkawinan" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                            <option value="">Pilih Status Perkawinan</option>
                            <optgroup label="Laki-laki">
                                <option value="Jejaka" {{ old('status_perkawinan') == 'Jejaka' ? 'selected' : '' }}>Jejaka</option>
                                <option value="Duda" {{ old('status_perkawinan') == 'Duda' ? 'selected' : '' }}>Duda</option>
                                <option value="Beristri ke-2" {{ old('status_perkawinan') == 'Beristri ke-2' ? 'selected' : '' }}>Beristri ke-2</option>
                                <option value="Beristri ke-3" {{ old('status_perkawinan') == 'Beristri ke-3' ? 'selected' : '' }}>Beristri ke-3</option>
                                <option value="Beristri ke-4" {{ old('status_perkawinan') == 'Beristri ke-4' ? 'selected' : '' }}>Beristri ke-4</option>
                            </optgroup>
                            <optgroup label="Perempuan">
                                <option value="Perawan" {{ old('status_perkawinan') == 'Perawan' ? 'selected' : '' }}>Perawan</option>
                                <option value="Janda" {{ old('status_perkawinan') == 'Janda' ? 'selected' : '' }}>Janda</option>
                            </optgroup>
                        </select>
                        @error('status_perkawinan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Bagian 2: Data Orang Tua — Ayah -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8" x-data="ocrAyahHandler()">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">2</span>
                        Data Orang Tua — Ayah
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
                            <span x-text="loading ? 'Memproses...' : 'Scan KTP Ayah'"></span>
                        </button>
                    </div>
                    <input type="file" x-ref="ocrInput" class="hidden" accept="image/*" @change="handleFileUpload">
                </div>

                <!-- OCR Status -->
                <div x-show="statusMsg" x-text="statusMsg"
                    :class="statusOk ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200'"
                    class="mb-4 text-sm border rounded-lg px-4 py-2" style="display:none"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Ayah <span class="text-red-500">*</span></label>
                        <input type="text" x-model="nama" name="ayah_nama" value="{{ old('ayah_nama') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('ayah_nama') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bin <span class="text-red-500">*</span></label>
                        <input type="text" name="ayah_bin" value="{{ old('ayah_bin') }}" placeholder="Nama kakek (ayah dari ayah)" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('ayah_bin') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">NIK Ayah <span class="text-red-500">*</span></label>
                        <input type="text" x-model="nik" name="ayah_nik" value="{{ old('ayah_nik') }}" maxlength="16" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('ayah_nik') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Agama Ayah <span class="text-red-500">*</span></label>
                        <select x-ref="agamaSelect" name="ayah_agama" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                            <option value="">Pilih Agama</option>
                            @foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $ag)
                            <option value="{{ $ag }}" {{ old('ayah_agama') == $ag ? 'selected' : '' }}>{{ $ag }}</option>
                            @endforeach
                        </select>
                        @error('ayah_agama') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kewarganegaraan Ayah <span class="text-red-500">*</span></label>
                        <select name="ayah_kewarganegaraan" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                            <option value="WNI" {{ old('ayah_kewarganegaraan','WNI') == 'WNI' ? 'selected' : '' }}>WNI</option>
                            <option value="WNA" {{ old('ayah_kewarganegaraan') == 'WNA' ? 'selected' : '' }}>WNA</option>
                        </select>
                        @error('ayah_kewarganegaraan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan Ayah <span class="text-red-500">*</span></label>
                        <select name="ayah_pekerjaan" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4 select2-pekerjaan" required>
                            <option value="">Pilih Pekerjaan</option>
                            @foreach($pekerjaanList ?? [] as $pekerjaan)
                            <option value="{{ $pekerjaan }}" {{ old('ayah_pekerjaan') == $pekerjaan ? 'selected' : '' }}>{{ $pekerjaan }}</option>
                            @endforeach
                        </select>
                        @error('ayah_pekerjaan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir Ayah <span class="text-red-500">*</span></label>
                        <input type="text" x-model="tempat_lahir" name="ayah_tempat_lahir" value="{{ old('ayah_tempat_lahir') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('ayah_tempat_lahir') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir Ayah <span class="text-red-500">*</span></label>
                        <input type="date" x-model="tanggal_lahir" name="ayah_tanggal_lahir" value="{{ old('ayah_tanggal_lahir') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('ayah_tanggal_lahir') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Sesuai KTP Ayah <span class="text-red-500">*</span></label>
                        <textarea x-model="alamat" name="ayah_alamat" rows="2" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>{{ old('ayah_alamat') }}</textarea>
                        @error('ayah_alamat') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Bagian 3: Data Orang Tua — Ibu -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8" x-data="ocrIbuHandler()">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">3</span>
                        Data Orang Tua — Ibu
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
                            <span x-text="loading ? 'Memproses...' : 'Scan KTP Ibu'"></span>
                        </button>
                    </div>
                    <input type="file" x-ref="ocrInput" class="hidden" accept="image/*" @change="handleFileUpload">
                </div>

                <!-- OCR Status -->
                <div x-show="statusMsg" x-text="statusMsg"
                    :class="statusOk ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200'"
                    class="mb-4 text-sm border rounded-lg px-4 py-2" style="display:none"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Ibu <span class="text-red-500">*</span></label>
                        <input type="text" x-model="nama" name="ibu_nama" value="{{ old('ibu_nama') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('ibu_nama') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Binti <span class="text-red-500">*</span></label>
                        <input type="text" name="ibu_binti" value="{{ old('ibu_binti') }}" placeholder="Nama kakek (ayah dari ibu)" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('ibu_binti') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">NIK Ibu <span class="text-red-500">*</span></label>
                        <input type="text" x-model="nik" name="ibu_nik" value="{{ old('ibu_nik') }}" maxlength="16" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('ibu_nik') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Agama Ibu <span class="text-red-500">*</span></label>
                        <select x-ref="agamaSelect" name="ibu_agama" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                            <option value="">Pilih Agama</option>
                            @foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $ag)
                            <option value="{{ $ag }}" {{ old('ibu_agama') == $ag ? 'selected' : '' }}>{{ $ag }}</option>
                            @endforeach
                        </select>
                        @error('ibu_agama') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kewarganegaraan Ibu <span class="text-red-500">*</span></label>
                        <select name="ibu_kewarganegaraan" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                            <option value="WNI" {{ old('ibu_kewarganegaraan','WNI') == 'WNI' ? 'selected' : '' }}>WNI</option>
                            <option value="WNA" {{ old('ibu_kewarganegaraan') == 'WNA' ? 'selected' : '' }}>WNA</option>
                        </select>
                        @error('ibu_kewarganegaraan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan Ibu <span class="text-red-500">*</span></label>
                        <select name="ibu_pekerjaan" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4 select2-pekerjaan" required>
                            <option value="">Pilih Pekerjaan</option>
                            @foreach($pekerjaanList ?? [] as $pekerjaan)
                            <option value="{{ $pekerjaan }}" {{ old('ibu_pekerjaan') == $pekerjaan ? 'selected' : '' }}>{{ $pekerjaan }}</option>
                            @endforeach
                        </select>
                        @error('ibu_pekerjaan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir Ibu <span class="text-red-500">*</span></label>
                        <input type="text" x-model="tempat_lahir" name="ibu_tempat_lahir" value="{{ old('ibu_tempat_lahir') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('ibu_tempat_lahir') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir Ibu <span class="text-red-500">*</span></label>
                        <input type="date" x-model="tanggal_lahir" name="ibu_tanggal_lahir" value="{{ old('ibu_tanggal_lahir') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('ibu_tanggal_lahir') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Sesuai KTP Ibu <span class="text-red-500">*</span></label>
                        <textarea x-model="alamat" name="ibu_alamat" rows="2" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>{{ old('ibu_alamat') }}</textarea>
                        @error('ibu_alamat') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Bagian 4: Upload Berkas Pendukung -->
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

                @php
                $fileInputClass = 'w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100';
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Wajib --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Surat Pengantar RT/RW Setempat <span class="text-red-500">*</span></label>
                        <input type="file" name="skmh_surat_pengantar" accept=".jpg,.jpeg,.png,.pdf" class="{{ $fileInputClass }}" required>
                        @error('skmh_surat_pengantar') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Akta Kelahiran & Ijazah Terakhir Kedua Calon Pengantin <span class="text-red-500">*</span>
                            <span class="ml-1 text-xs text-gray-500 font-normal">(Dijadikan 1 File)</span>
                        </label>
                        <input type="file" name="skmh_akta_ijazah_catin" accept=".jpg,.jpeg,.png,.pdf" class="{{ $fileInputClass }}" required>
                        @error('skmh_akta_ijazah_catin') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            KTP & KK Kedua Calon Pengantin <span class="text-red-500">*</span>
                            <span class="ml-1 text-xs text-gray-500 font-normal">(Dijadikan 1 File)</span>
                        </label>
                        <input type="file" name="skmh_ktp_kk_catin" accept=".jpg,.jpeg,.png,.pdf" class="{{ $fileInputClass }}" required>
                        @error('skmh_ktp_kk_catin') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            KTP & KK Orang Tua/Wali Kedua Calon Pengantin <span class="text-red-500">*</span>
                            <span class="ml-1 text-xs text-gray-500 font-normal">(Dijadikan 1 File)</span>
                        </label>
                        <input type="file" name="skmh_ktp_kk_ortu" accept=".jpg,.jpeg,.png,.pdf" class="{{ $fileInputClass }}" required>
                        @error('skmh_ktp_kk_ortu') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pas Foto Warna Gandeng Pasangan (Latar Biru) <span class="text-red-500">*</span></label>
                        <input type="file" name="skmh_pas_foto" accept=".jpg,.jpeg,.png,.pdf" class="{{ $fileInputClass }}" required>
                        @error('skmh_pas_foto') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            KTP 2 Orang Saksi (RT yang sama, bukan saksi nikah) <span class="text-red-500">*</span>
                            <span class="ml-1 text-xs text-gray-500 font-normal">(Dijadikan 1 File)</span>
                        </label>
                        <input type="file" name="skmh_ktp_saksi" accept=".jpg,.jpeg,.png,.pdf" class="{{ $fileInputClass }}" required>
                        @error('skmh_ktp_saksi') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Formulir Pengantar Nikah (N2-N5) <span class="text-red-500">*</span></label>
                        <input type="file" name="skmh_form_n2_n5" accept=".jpg,.jpeg,.png,.pdf" class="{{ $fileInputClass }}" required>
                        @error('skmh_form_n2_n5') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bukti Tanda Lunas PBB-P2 Tahun Berjalan <span class="text-red-500">*</span></label>
                        <input type="file" name="skmh_bukti_pbb" accept=".jpg,.jpeg,.png,.pdf" class="{{ $fileInputClass }}" required>
                        @error('skmh_bukti_pbb') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Opsional --}}
                    <div class="md:col-span-2">
                        <p class="text-sm font-semibold text-gray-600 mb-4 border-t pt-4">Lampiran Tambahan (jika diperlukan)</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Akta Cerai Hidup / Akta Kematian Pasangan Sebelumnya
                            <span class="ml-1 text-xs text-gray-500 font-normal">(Jika Duda/Janda)</span>
                        </label>
                        <input type="file" name="skmh_akta_cerai_kematian" accept=".jpg,.jpeg,.png,.pdf" class="{{ $fileInputClass }}">
                        @error('skmh_akta_cerai_kematian') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Surat Dispensasi Kawin dari Pengadilan
                            <span class="ml-1 text-xs text-gray-500 font-normal">(Jika belum usia 19 tahun)</span>
                        </label>
                        <input type="file" name="skmh_dispensasi_pengadilan" accept=".jpg,.jpeg,.png,.pdf" class="{{ $fileInputClass }}">
                        @error('skmh_dispensasi_pengadilan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Surat Izin dari Atasan / Kesatuan
                            <span class="ml-1 text-xs text-gray-500 font-normal">(Jika anggota TNI/POLRI)</span>
                        </label>
                        <input type="file" name="skmh_izin_atasan" accept=".jpg,.jpeg,.png,.pdf" class="{{ $fileInputClass }}">
                        @error('skmh_izin_atasan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Penetapan Izin Poligami dari Pengadilan
                            <span class="ml-1 text-xs text-gray-500 font-normal">(Jika hendak beristri lebih dari seorang)</span>
                        </label>
                        <input type="file" name="skmh_izin_poligami" accept=".jpg,.jpeg,.png,.pdf" class="{{ $fileInputClass }}">
                        @error('skmh_izin_poligami') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Surat Rekomendasi dari DP3APMP2KB
                            <span class="ml-1 text-xs text-gray-500 font-normal">(Jika di bawah usia menikah)</span>
                        </label>
                        <input type="file" name="skmh_rekom_dp3a" accept=".jpg,.jpeg,.png,.pdf" class="{{ $fileInputClass }}">
                        @error('skmh_rekom_dp3a') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Surat Imunisasi Catin
                        </label>
                        <input type="file" name="skmh_surat_imunisasi_catin" accept=".jpg,.jpeg,.png,.pdf" class="{{ $fileInputClass }}">
                        @error('skmh_surat_imunisasi_catin') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <script>
                function ocrSkmhHandler() {
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
                            this.$refs.ocrInput.value = '';
                            this.$refs.ocrInput.click();
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
                                            this.$refs.jenisKelaminSelect.value = d.jenis_kelamin;
                                        });
                                    }
                                    if (d.agama) {
                                        this.$nextTick(() => {
                                            this.$refs.agamaSelect.value = d.agama.charAt(0).toUpperCase() + d.agama.slice(1).toLowerCase();
                                        });
                                    }
                                    if (d.pekerjaan) {
                                        this.$nextTick(() => {
                                            const tsElement = document.querySelector('select[name="pekerjaan"]');
                                            if (tsElement && tsElement.tomselect) {
                                                const ts = tsElement.tomselect;
                                                let jobTitleCase = d.pekerjaan.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase()).join(' ');
                                                if (d.pekerjaan.toUpperCase() === 'PNS') jobTitleCase = 'PNS (Pegawai Negeri Sipil)';
                                                else if (d.pekerjaan.toUpperCase() === 'TNI') jobTitleCase = 'TNI (Tentara Nasional Indonesia)';
                                                else if (d.pekerjaan.toUpperCase() === 'POLRI') jobTitleCase = 'POLRI';

                                                ts.addOption({
                                                    value: jobTitleCase,
                                                    text: jobTitleCase
                                                });
                                                ts.setValue(jobTitleCase);
                                            }
                                        });
                                    }
                                    this.statusOk = true;
                                    this.statusMsg = '✓ Data KTP berhasil diekstrak!';
                                } else {
                                    this.statusOk = false;
                                    this.statusMsg = '✗ ' + (result.message || 'Gagal memproses KTP.');
                                }
                            } catch (err) {
                                this.statusOk = false;
                                this.statusMsg = '✗ Gagal menghubungi server OCR.';
                            } finally {
                                this.loading = false;
                            }
                        }
                    }
                }

                function ocrAyahHandler() {
                    return {
                        nama: '{{ old("ayah_nama") }}',
                        nik: '{{ old("ayah_nik") }}',
                        tempat_lahir: '{{ old("ayah_tempat_lahir") }}',
                        tanggal_lahir: '{{ old("ayah_tanggal_lahir") }}',
                        alamat: '{{ old("ayah_alamat") }}',
                        loading: false,
                        statusMsg: '',
                        statusOk: true,

                        triggerOCR: function() {
                            this.$refs.ocrInput.value = '';
                            this.$refs.ocrInput.click();
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
                                    if (d.agama && this.$refs.agamaSelect) {
                                        this.$nextTick(() => {
                                            this.$refs.agamaSelect.value = d.agama.charAt(0).toUpperCase() + d.agama.slice(1).toLowerCase();
                                        });
                                    }
                                    if (d.pekerjaan) {
                                        this.$nextTick(() => {
                                            const tsElement = document.querySelector('select[name="ayah_pekerjaan"]');
                                            if (tsElement && tsElement.tomselect) {
                                                const ts = tsElement.tomselect;
                                                let jobTitleCase = d.pekerjaan.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase()).join(' ');
                                                if (d.pekerjaan.toUpperCase() === 'PNS') jobTitleCase = 'PNS (Pegawai Negeri Sipil)';
                                                else if (d.pekerjaan.toUpperCase() === 'TNI') jobTitleCase = 'TNI (Tentara Nasional Indonesia)';
                                                else if (d.pekerjaan.toUpperCase() === 'POLRI') jobTitleCase = 'POLRI';

                                                ts.addOption({
                                                    value: jobTitleCase,
                                                    text: jobTitleCase
                                                });
                                                ts.setValue(jobTitleCase);
                                            }
                                        });
                                    }
                                    this.statusOk = true;
                                    this.statusMsg = '✓ Data KTP Ayah berhasil diekstrak!';
                                } else {
                                    this.statusOk = false;
                                    this.statusMsg = '✗ ' + (result.message || 'Gagal memproses KTP.');
                                }
                            } catch (err) {
                                this.statusOk = false;
                                this.statusMsg = '✗ Gagal menghubungi server OCR.';
                            } finally {
                                this.loading = false;
                            }
                        }
                    }
                }

                function ocrIbuHandler() {
                    return {
                        nama: '{{ old("ibu_nama") }}',
                        nik: '{{ old("ibu_nik") }}',
                        tempat_lahir: '{{ old("ibu_tempat_lahir") }}',
                        tanggal_lahir: '{{ old("ibu_tanggal_lahir") }}',
                        alamat: '{{ old("ibu_alamat") }}',
                        loading: false,
                        statusMsg: '',
                        statusOk: true,

                        triggerOCR: function() {
                            this.$refs.ocrInput.value = '';
                            this.$refs.ocrInput.click();
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
                                    if (d.agama && this.$refs.agamaSelect) {
                                        this.$nextTick(() => {
                                            this.$refs.agamaSelect.value = d.agama.charAt(0).toUpperCase() + d.agama.slice(1).toLowerCase();
                                        });
                                    }
                                    if (d.pekerjaan) {
                                        this.$nextTick(() => {
                                            const tsElement = document.querySelector('select[name="ibu_pekerjaan"]');
                                            if (tsElement && tsElement.tomselect) {
                                                const ts = tsElement.tomselect;
                                                let jobTitleCase = d.pekerjaan.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase()).join(' ');
                                                if (d.pekerjaan.toUpperCase() === 'PNS') jobTitleCase = 'PNS (Pegawai Negeri Sipil)';
                                                else if (d.pekerjaan.toUpperCase() === 'TNI') jobTitleCase = 'TNI (Tentara Nasional Indonesia)';
                                                else if (d.pekerjaan.toUpperCase() === 'POLRI') jobTitleCase = 'POLRI';

                                                ts.addOption({
                                                    value: jobTitleCase,
                                                    text: jobTitleCase
                                                });
                                                ts.setValue(jobTitleCase);
                                            }
                                        });
                                    }
                                    this.statusOk = true;
                                    this.statusMsg = '✓ Data KTP Ibu berhasil diekstrak!';
                                } else {
                                    this.statusOk = false;
                                    this.statusMsg = '✗ ' + (result.message || 'Gagal memproses KTP.');
                                }
                            } catch (err) {
                                this.statusOk = false;
                                this.statusMsg = '✗ Gagal menghubungi server OCR.';
                            } finally {
                                this.loading = false;
                            }
                        }
                    }
                }
            </script>