            <!-- Bagian 1: Data Diri yang Bersangkutan -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8" x-data="ocrSktmHandler()">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">1</span>
                        Data Diri yang Bersangkutan
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

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">NIK <span class="text-red-500">*</span></label>
                            <input type="text" x-model="nik" name="nik_bersangkutan" value="{{ old('nik_bersangkutan') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" maxlength="16" required>
                            @error('nik_bersangkutan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
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
                                @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                <option value="{{ $agama }}" {{ old('agama') == $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                @endforeach
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                        <div x-data="{
                            penghasilan: '{{ old('jumlah_penghasilan') }}',
                            formatRupiah(value) {
                                let number_string = value.replace(/[^,\d]/g, '').toString(),
                                    split = number_string.split(','),
                                    sisa = split[0].length % 3,
                                    rupiah = split[0].substr(0, sisa),
                                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);
                                
                                if(ribuan){
                                    separator = sisa ? '.' : '';
                                    rupiah += separator + ribuan.join('.');
                                }
                                return rupiah;
                            }
                        }">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Penghasilan per Bulan <span class="text-red-500">*</span></label>
                            <div class="relative rounded-lg shadow-sm flex">
                                <span class="inline-flex items-center px-4 rounded-l-lg border border-r-0 border-gray-300 bg-gray-100 text-gray-500 sm:text-sm font-medium">
                                    Rp.
                                </span>
                                <input type="text" x-model="penghasilan" @input="penghasilan = formatRupiah($event.target.value)" name="jumlah_penghasilan" placeholder="3.000.000" class="flex-1 w-full rounded-none rounded-r-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Angka saja, cont: 3000000</p>
                            @error('jumlah_penghasilan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Sesuai KTP <span class="text-red-500">*</span></label>
                        <textarea x-model="alamat" name="alamat_lengkap" rows="2" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>{{ old('alamat_lengkap') }}</textarea>
                        @error('alamat_lengkap') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keperluan <span class="text-red-500">*</span></label>
                        <input type="text" name="keperluan" value="{{ old('keperluan') }}" placeholder="Contoh: Pengajuan beasiswa / Administrasi perbankan" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
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
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">2</span>
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
            </div>

            <!-- Bagian 3: Dokumen Lampiran -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">3</span>
                    Dokumen Lampiran
                </h2>
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <svg class="h-5 w-5 text-blue-400 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <div class="ml-3 text-sm text-blue-800">
                            <p class="font-medium mb-1">Ketentuan Berkas:</p>
                            <ul class="list-disc list-inside space-y-1 text-blue-700 text-xs">
                                <li>Pastikan foto/scan terlihat jelas dan tidak terpotong.</li>
                                <li>Setiap file berukuran maksimal <span class="font-bold">5MB</span>.</li>
                                <li>Format file yang diizinkan: <span class="font-bold">JPG, PNG, PDF</span>.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Pengantar RT/RW -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Surat Pengantar RT/RW Setempat <span class="text-red-500">*</span></label>
                        <input type="file" name="skp_surat_pengantar" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 border border-gray-300 rounded-lg bg-gray-50" required>
                        @error('skp_surat_pengantar') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Blangko Pernyataan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Blangko Pernyataan Bermeterai 10.000 <span class="text-red-500">*</span></label>
                        <input type="file" name="skp_blangko_pernyataan" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 border border-gray-300 rounded-lg bg-gray-50" required>
                        @error('skp_blangko_pernyataan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- KTP & KK -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">KTP & KK yang Bersangkutan <span class="text-gray-500 font-normal">(Jadikan 1 File)</span> <span class="text-red-500">*</span></label>
                        <input type="file" name="skp_ktp_kk" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 border border-gray-300 rounded-lg bg-gray-50" required>
                        @error('skp_ktp_kk') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- KTP Saksi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">KTP 2 Orang Saksi <span class="text-gray-500 font-normal">(RT sama, 1 File)</span> <span class="text-red-500">*</span></label>
                        <input type="file" name="skp_ktp_saksi" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 border border-gray-300 rounded-lg bg-gray-50" required>
                        @error('skp_ktp_saksi') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Bukti PBB -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bukti Tanda Lunas PBB-P2 Tahun Berjalan <span class="text-red-500">*</span></label>
                        <input type="file" name="skp_bukti_pbb" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 border border-gray-300 rounded-lg bg-gray-50" required>
                        @error('skp_bukti_pbb') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- AlpineJS Script for OCR (Same as SKTMR) -->
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('ocrSktmHandler', () => ({
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
                                const response = await fetch('{{ route("permohonan.ocr") }}', {
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
                                                // Convert to Title Case to match job list if possible, or Add New
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
                    }));
                });
            </script>