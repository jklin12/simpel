@php
    // Kelas bersama (HP: input tinggi ±48px). Nama field (name=) tidak boleh diubah: dipakai StorePermohonanRequest.
    $inp = 'w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4';
    $fileCls = 'w-full rounded-lg border border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 transition-colors py-2 px-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100';
    $lbl = 'block text-sm font-medium text-gray-700 mb-2';
    $card = 'bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-8';
@endphp

{{-- Satu x-data untuk OCR agar nilai bisa mengisi field di beberapa langkah. Setiap [data-wizard-step] = satu langkah wizard. --}}
<div x-data="ocrSktmHandler()" class="space-y-6">

    <!-- Langkah 1: Identitas (Scan KTP di sini) -->
    <div data-wizard-step data-title="Identitas" class="{{ $card }}">
        <div class="flex items-start justify-between gap-3 mb-5">
            <h2 class="text-lg font-bold text-gray-900">Identitas yang Bersangkutan</h2>
        </div>

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

        <div class="grid grid-cols-1 gap-5">
            <div>
                <label class="{{ $lbl }}">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" x-model="nama" name="nama_lengkap" value="{{ old('nama_lengkap') }}" autocomplete="name" class="{{ $inp }}" required>
                @error('nama_lengkap') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="{{ $lbl }}">NIK <span class="text-red-500">*</span></label>
                <input type="text" inputmode="numeric" x-model="nik" name="nik_bersangkutan" value="{{ old('nik_bersangkutan') }}" class="{{ $inp }}" maxlength="16" required>
                @error('nik_bersangkutan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="{{ $lbl }}">Jenis Kelamin <span class="text-red-500">*</span></label>
                    <select x-ref="jenisKelaminSelect" name="jenis_kelamin" class="{{ $inp }}" required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('jenis_kelamin') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="{{ $lbl }}">Agama <span class="text-red-500">*</span></label>
                    <select x-ref="agamaSelect" name="agama" class="{{ $inp }}" required>
                        <option value="">Pilih Agama</option>
                        @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                        <option value="{{ $agama }}" {{ old('agama') == $agama ? 'selected' : '' }}>{{ $agama }}</option>
                        @endforeach
                    </select>
                    @error('agama') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="{{ $lbl }}">Tempat Lahir <span class="text-red-500">*</span></label>
                    <input type="text" x-model="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}" class="{{ $inp }}" required>
                    @error('tempat_lahir') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="{{ $lbl }}">Tanggal Lahir <span class="text-red-500">*</span></label>
                    <input type="date" x-model="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="{{ $inp }}" required>
                    @error('tanggal_lahir') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Langkah 2: Alamat & pekerjaan -->
    <div data-wizard-step data-title="Alamat & pekerjaan" class="{{ $card }}">
        <h2 class="text-lg font-bold text-gray-900 mb-5">Alamat &amp; Pekerjaan</h2>

        <div class="grid grid-cols-1 gap-5">
            <div>
                <label class="{{ $lbl }}">Status Perkawinan <span class="text-red-500">*</span></label>
                <select x-ref="statusPerkawinanSelect" name="status_perkawinan" class="{{ $inp }}" required>
                    <option value="">Pilih Status Perkawinan</option>
                    @foreach(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'] as $status)
                    <option value="{{ $status }}" {{ old('status_perkawinan') == $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
                @error('status_perkawinan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="{{ $lbl }}">Pekerjaan <span class="text-red-500">*</span></label>
                <select name="pekerjaan" class="{{ $inp }} select2-pekerjaan" required>
                    <option value="">Pilih Pekerjaan</option>
                    @foreach($pekerjaanList ?? [] as $pekerjaan)
                    <option value="{{ $pekerjaan }}" {{ old('pekerjaan') == $pekerjaan ? 'selected' : '' }}>{{ $pekerjaan }}</option>
                    @endforeach
                </select>
                @error('pekerjaan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="{{ $lbl }}">Alamat Sesuai KTP <span class="text-red-500">*</span></label>
                <textarea x-model="alamat" name="alamat_lengkap" rows="3" class="{{ $inp }}" required>{{ old('alamat_lengkap') }}</textarea>
                @error('alamat_lengkap') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="{{ $lbl }}">No. WhatsApp / HP <span class="text-red-500">*</span></label>
                <input type="tel" inputmode="tel" name="no_wa" value="{{ old('no_wa') }}" placeholder="Contoh: 08123456789" autocomplete="tel" class="{{ $inp }}" required>
                <p class="mt-1 text-xs text-blue-600">Nomor ini digunakan sebagai nomor kontak pemohon.</p>
                @error('no_wa') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <!-- Langkah 3: Pengantar RT/RW & keperluan -->
    <div data-wizard-step data-title="Pengantar & keperluan" class="{{ $card }}">
        <h2 class="text-lg font-bold text-gray-900 mb-5">Pengantar RT/RW &amp; Keperluan</h2>

        <div class="grid grid-cols-1 gap-5">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="{{ $lbl }}">RT <span class="text-red-500">*</span></label>
                    <input type="text" inputmode="numeric" name="rt" value="{{ old('rt') }}" placeholder="Contoh: 001" class="{{ $inp }}" required>
                    @error('rt') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="{{ $lbl }}">RW <span class="text-red-500">*</span></label>
                    <input type="text" inputmode="numeric" name="rw" value="{{ old('rw') }}" placeholder="Contoh: 005" class="{{ $inp }}" required>
                    @error('rw') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="{{ $lbl }}">No. Surat Pengantar <span class="text-red-500">*</span></label>
                <input type="text" name="no_surat_pengantar" value="{{ old('no_surat_pengantar') }}" placeholder="Contoh: 044/RW.009/I/2026" class="{{ $inp }}" required>
                @error('no_surat_pengantar') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="{{ $lbl }}">Tanggal Surat Pengantar <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_surat_pengantar" value="{{ old('tanggal_surat_pengantar') }}" class="{{ $inp }}" required>
                @error('tanggal_surat_pengantar') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="{{ $lbl }}">Keperluan SKTM <span class="text-red-500">*</span></label>
                <select name="keperluan_sktm" class="{{ $inp }}" required>
                    <option value="">Pilih Keperluan</option>
                    @foreach([
                        'Pengajuan Beasiswa Pendidikan',
                        'Persyaratan pendaftaran Sekolah',
                        'Persyaratan pendaftaran Kuliah',
                        'Keringanan atau pembebasan biaya pendidikan',
                        'Bantuan keringanan SPP',
                        'Bantuan biaya kesehatan',
                        'Mendapatkan layanan pengobatan gratis',
                        'Pengurusan BPJS Kesehatan',
                        'Keringanan biaya operasi atau perawatan khusus',
                        'Persyaratan bantuan sosial',
                        'Pengajuan bantuan modal usaha',
                        'Pengajuan bantuan perbaikan rumah',
                        'Persyaratan mendapatkan layanan hukum',
                        'Lainnya',
                    ] as $keperluan)
                    <option value="{{ $keperluan }}" {{ old('keperluan_sktm') == $keperluan ? 'selected' : '' }}>{{ $keperluan }}</option>
                    @endforeach
                </select>
                @error('keperluan_sktm') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="{{ $lbl }}">Keterangan Tambahan <span class="text-red-500">*</span></label>
                <textarea x-model="keterangan_sktm" name="keterangan_sktm" rows="3" class="{{ $inp }}" required>{{ old('keterangan_sktm') }}</textarea>
                @error('keterangan_sktm') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="{{ $lbl }}">Tanggal Surat Pernyataan <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_surat_pernyataan" value="{{ old('tanggal_surat_pernyataan') }}" class="{{ $inp }}" required>
                @error('tanggal_surat_pernyataan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <!-- Langkah 4: Upload berkas pendukung -->
    <div data-wizard-step data-title="Berkas" class="{{ $card }}">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Upload Berkas Pendukung</h2>

        <div class="bg-amber-50 border border-amber-100 rounded-lg p-3 mb-5">
            <p class="text-sm text-amber-700">
                <strong>Catatan:</strong> Format JPG, PNG, atau PDF. Maksimal 5MB per file. File yang digabung harap dijadikan 1 file PDF.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-5">
            <div>
                <label class="{{ $lbl }}">Surat Pengantar RT/RW Setempat <span class="text-red-500">*</span></label>
                <input type="file" name="surat_pengantar_rtrw" accept=".jpg,.jpeg,.png,.pdf" class="{{ $fileCls }}" required>
                @error('surat_pengantar_rtrw') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="{{ $lbl }}">Blangko Pernyataan Bermeterai 10.000 <span class="text-red-500">*</span></label>
                <input type="file" name="blangko_pernyataan" accept=".jpg,.jpeg,.png,.pdf" class="{{ $fileCls }}" required>
                @error('blangko_pernyataan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="{{ $lbl }}">KTP &amp; KK yang Bersangkutan <span class="text-red-500">*</span>
                    <span class="ml-1 text-xs text-gray-500 font-normal">(Dijadikan 1 File)</span></label>
                <input type="file" name="ktp_kk_bersangkutan" accept=".jpg,.jpeg,.png,.pdf" class="{{ $fileCls }}" required>
                @error('ktp_kk_bersangkutan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="{{ $lbl }}">KTP 2 Orang Saksi (RT yang sama) <span class="text-red-500">*</span>
                    <span class="ml-1 text-xs text-gray-500 font-normal">(Dijadikan 1 File)</span></label>
                <input type="file" name="ktp_saksi" accept=".jpg,.jpeg,.png,.pdf" class="{{ $fileCls }}" required>
                @error('ktp_saksi') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="{{ $lbl }}">Surat Pengantar/Rekomendasi Sekolah/Kampus <span class="text-red-500">*</span></label>
                <input type="file" name="surat_rekomendasi_sekolah" accept=".jpg,.jpeg,.png,.pdf" class="{{ $fileCls }}" required>
                @error('surat_rekomendasi_sekolah') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="{{ $lbl }}">Bukti Tanda Lunas PBB-P2 Tahun Berjalan <span class="text-red-500">*</span></label>
                <input type="file" name="bukti_lunas_pbb" accept=".jpg,.jpeg,.png,.pdf" class="{{ $fileCls }}" required>
                @error('bukti_lunas_pbb') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <script>
        function ocrSktmHandler() {
            return {
                nama: '{{ old("nama_lengkap") }}',
                nik: '{{ old("nik_bersangkutan") }}',
                tempat_lahir: '{{ old("tempat_lahir") }}',
                tanggal_lahir: '{{ old("tanggal_lahir") }}',
                alamat: '{{ old("alamat_lengkap") }}',
                keterangan_sktm: '{{ old("keterangan_sktm") }}',
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
            }
        }
    </script>
</div>
