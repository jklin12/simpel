            <!-- Bagian 1: Data Pemohon -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8" x-data="skdkHandler()">
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
                            <span x-text="loading ? 'Memproses...' : 'Scan KTP (OCR)'"></span>
                        </button>
                        <span class="text-xs text-blue-600 mt-1">*Otomatis isi data dari KTP</span>
                    </div>
                    <input type="file" x-ref="ocrInput" class="hidden" accept="image/*" @change="handleFileUpload">
                </div>

                <!-- OCR Status Message -->
                <div x-show="statusMsg" x-text="statusMsg"
                    :class="statusOk ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200'"
                    class="mb-4 text-sm border rounded-lg px-4 py-2" style="display:none"></div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama <span class="text-red-500">*</span></label>
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
                            <p class="mt-1 text-xs text-blue-600">*Nomor WhatsApp aktif untuk notifikasi.</p>
                            @error('no_wa') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select name="jenis_kelamin" x-model="jenisKelamin" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                                <option value="">-- Pilih --</option>
                                <option value="Laki-laki" {{ old('jenis_kelamin') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jenis_kelamin') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir <span class="text-red-500">*</span></label>
                            <input type="text" x-model="tempatLahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}" placeholder="Contoh: Banjarbaru" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                            @error('tempat_lahir') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" x-model="tanggalLahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                            @error('tanggal_lahir') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat <span class="text-red-500">*</span></label>
                            <textarea x-model="alamat" name="alamat_lengkap" rows="2" placeholder="Alamat lengkap pemohon" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>{{ old('alamat_lengkap') }}</textarea>
                            @error('alamat_lengkap') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <script>
                    function skdkHandler() {
                        return {
                            nama: '{{ old("nama_lengkap") }}',
                            nik: '{{ old("nik_bersangkutan") }}',
                            jenisKelamin: '{{ old("jenis_kelamin") }}',
                            tempatLahir: '{{ old("tempat_lahir") }}',
                            tanggalLahir: '{{ old("tanggal_lahir") }}',
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
                                        if (d.jenis_kelamin) this.jenisKelamin = d.jenis_kelamin;
                                        if (d.tempat_lahir) this.tempatLahir = d.tempat_lahir;
                                        if (d.tanggal_lahir) this.tanggalLahir = d.tanggal_lahir;
                                        if (d.alamat) this.alamat = d.alamat;
                                        this.statusOk = true;
                                        this.statusMsg = '✓ Data KTP berhasil diekstrak!';
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
            </div>

            <!-- Bagian 2: Data Kantor/Partai -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">2</span>
                    Data Kantor / Partai
                </h2>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kantor <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_kantor" value="{{ old('nama_kantor') }}" placeholder="Contoh: DPC Partai ..." class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('nama_kantor') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Kantor <span class="text-red-500">*</span></label>
                        <textarea name="alamat_kantor" rows="2" placeholder="Alamat lengkap kantor/sekretariat partai" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>{{ old('alamat_kantor') }}</textarea>
                        @error('alamat_kantor') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keperluan <span class="text-red-500">*</span></label>
                        <textarea name="keperluan" rows="2" placeholder="Jelaskan keperluan surat ini" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>{{ old('keperluan') }}</textarea>
                        @error('keperluan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Bagian 3: Surat Pengantar Lurah -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">3</span>
                    Surat Pengantar Lurah
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">RT <span class="text-red-500">*</span></label>
                        <input type="text" name="rt" value="{{ old('rt') }}" placeholder="Contoh: 001" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('rt') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">RW <span class="text-red-500">*</span></label>
                        <input type="text" name="rw" value="{{ old('rw') }}" placeholder="Contoh: 005" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('rw') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">No Surat <span class="text-red-500">*</span></label>
                        <input type="text" name="no_surat_pengantar" value="{{ old('no_surat_pengantar') }}" placeholder="Contoh: 02/02.1006.009.044/I/2026" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('no_surat_pengantar') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Surat <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_surat_pengantar" value="{{ old('tanggal_surat_pengantar') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('tanggal_surat_pengantar') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
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
                        <strong>Catatan:</strong> Upload dokumen dalam format JPG, PNG, atau PDF. Maksimal 5MB per file.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Surat Pengantar RT/RW Setempat <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="skdk_surat_pengantar_rtrw" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required>
                        @error('skdk_surat_pengantar_rtrw') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            KTP Pemohon <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="skdk_ktp_pemohon" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required>
                        @error('skdk_ktp_pemohon') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Akta Pendirian / Kepengurusan <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="skdk_akta_kepengurusan" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required>
                        @error('skdk_akta_kepengurusan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Akta Pendirian Partai Politik <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="skdk_akta_parpol" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required>
                        @error('skdk_akta_parpol') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            IMB / Perjanjian Sewa Bangunan <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="skdk_imb_sewa" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required>
                        @error('skdk_imb_sewa') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Surat Pengantar Domisili Kepartaian dari Kelurahan <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="skdk_surat_pengantar_lurah" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required>
                        @error('skdk_surat_pengantar_lurah') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Bukti Tanda Lunas PBB-P2 Tahun Berjalan <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="skdk_bukti_pbb" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required>
                        @error('skdk_bukti_pbb') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
