            <!-- Bagian 1: Data Diri yang Bersangkutan -->
            <div x-data="ocrSktmrHandler()" class="space-y-6">

            <!-- Langkah 1: Identitas (Scan KTP) -->
            <div data-wizard-step data-title="Identitas" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-8">
                <h2 class="text-lg font-bold text-gray-900 mb-5">Identitas yang Bersangkutan</h2>

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
                        <span x-text="loading ? 'Memproses...' : 'Scan KTP (OCR)'"></span>
                    </button>
                    <p class="text-xs text-primary-700 mt-2 text-center">Otomatis mengisi nama, NIK, tanggal lahir, alamat, dan data lain di langkah berikutnya.</p>
                    <input type="file" x-ref="ocrInput" class="hidden" accept="image/*" @change="handleFileUpload">
                </div>

                <!-- OCR Status Message -->
                <div x-show="statusMsg" x-text="statusMsg"
                    :class="statusOk ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200'"
                    class="mb-4 text-sm border rounded-lg px-4 py-2" style="display:none"></div>


                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" x-model="nama" name="nama_lengkap" value="{{ old('nama_lengkap') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('nama_lengkap') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">NIK <span class="text-red-500">*</span></label>
                        <input type="text" x-model="nik" inputmode="numeric" name="nik_bersangkutan" value="{{ old('nik_bersangkutan') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" maxlength="16" required>
                        @error('nik_bersangkutan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
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

                </div>
            </div>

            <!-- Langkah 2: Alamat & pekerjaan -->
            <div data-wizard-step data-title="Alamat & pekerjaan" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-8">
                <h2 class="text-lg font-bold text-gray-900 mb-5">Alamat &amp; Pekerjaan</h2>

                <div class="grid grid-cols-1 gap-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status Perkawinan <span class="text-red-500">*</span></label>
                            <select x-ref="statusPerkawinanSelect" name="status_perkawinan" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
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
                            <select name="pekerjaan" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4 select2-pekerjaan" required>
                                <option value="">Pilih Pekerjaan</option>
                                @foreach($pekerjaanList ?? [] as $pekerjaan)
                                <option value="{{ $pekerjaan }}" {{ old('pekerjaan') == $pekerjaan ? 'selected' : '' }}>{{ $pekerjaan }}</option>
                                @endforeach
                            </select>
                            @error('pekerjaan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pendidikan Terakhir <span class="text-red-500">*</span></label>
                        <select name="pendidikan_terakhir" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                            <option value="">Pilih Pendidikan Terakhir</option>
                            @foreach(['Tidak Sekolah','SD','SMP','SMA','DI','DII','DIII','DIV','S1','S2','S3'] as $pend)
                            <option value="{{ $pend }}" {{ old('pendidikan_terakhir') == $pend ? 'selected' : '' }}>{{ $pend }}</option>
                            @endforeach
                        </select>
                        @error('pendidikan_terakhir') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Sesuai KTP <span class="text-red-500">*</span></label>
                        <textarea x-model="alamat" name="alamat_lengkap" rows="2" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>{{ old('alamat_lengkap') }}</textarea>
                        @error('alamat_lengkap') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keperluan <span class="text-red-500">*</span></label>
                        <input type="text" name="keperluan" value="{{ old('keperluan') }}" placeholder="Contoh: Mengurus rumah dinas / Pengajuan bantuan perumahan" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('keperluan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">No. WhatsApp / HP <span class="text-red-500">*</span></label>
                        <input type="text" name="no_wa" value="{{ old('no_wa') }}" placeholder="Contoh: 08123456789" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        <p class="mt-1 text-xs text-blue-600">*Nomor ini akan digunakan sebagai nomor kontak pemohon.</p>
                        @error('no_wa') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Bagian 2: Surat Pengantar RT/RW -->
            <div data-wizard-step data-title="Pengantar RT/RW" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-8">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    Surat Pengantar RT/RW
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">No. Surat Pengantar <span class="text-red-500">*</span></label>
                        <input type="text" name="no_surat_pengantar" value="{{ old('no_surat_pengantar') }}" placeholder="Contoh: 044/RW.009/I/2026" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('no_surat_pengantar') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Surat Pengantar <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_surat_pengantar" value="{{ old('tanggal_surat_pengantar') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('tanggal_surat_pengantar') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <p class="mt-6 mb-3 text-sm font-semibold text-gray-900">Blangko Surat Pernyataan</p>
                <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Surat Pernyataan <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_surat_pernyataan" value="{{ old('tanggal_surat_pernyataan') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('tanggal_surat_pernyataan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Bagian 4: Upload Berkas Pendukung -->
            <div data-wizard-step data-title="Berkas" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-8">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
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
                        <input type="file" name="sktmr_surat_pengantar" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required>
                        @error('sktmr_surat_pengantar') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Blangko Pernyataan Bermeterai 10.000 <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="sktmr_blangko_pernyataan" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required>
                        @error('sktmr_blangko_pernyataan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            KTP & KK yang Bersangkutan <span class="text-red-500">*</span>
                            <span class="ml-1 text-xs text-gray-500 font-normal">(Dijadikan 1 File)</span>
                        </label>
                        <input type="file" name="sktmr_ktp_kk" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required>
                        @error('sktmr_ktp_kk') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            KTP 2 Orang Saksi (RT yang sama) <span class="text-red-500">*</span>
                            <span class="ml-1 text-xs text-gray-500 font-normal">(Dijadikan 1 File)</span>
                        </label>
                        <input type="file" name="sktmr_ktp_saksi" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required>
                        @error('sktmr_ktp_saksi') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Bukti Tanda Lunas PBB-P2 Tahun Berjalan <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="sktmr_bukti_pbb" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required>
                        @error('sktmr_bukti_pbb') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            </div>

            <script>
                function ocrSktmrHandler() {
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
                                    if (d.pekerjaan) {
                                        this.$nextTick(() => {
                                            const ts = document.querySelector('.select2-pekerjaan').tomselect;
                                            if (ts) {
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