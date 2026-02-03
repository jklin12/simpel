@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Create New User</h1>
        <p class="text-gray-500">Add a new user and assign their role and location.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8"
        x-data="userForm()"
        x-init="init()">

        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Role Selection -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role" id="role" x-model="role" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors border px-4 py-2 text-sm @error('role') border-red-500 @enderror">
                    <option value="">Select Role</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors border px-4 py-2 text-sm @error('name') border-red-500 @enderror">
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" id="email" required value="{{ old('email') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors border px-4 py-2 text-sm @error('email') border-red-500 @enderror">
                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" id="password" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors border px-4 py-2 text-sm @error('password') border-red-500 @enderror">
                    @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors border px-4 py-2 text-sm">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors border px-4 py-2 text-sm @error('phone') border-red-500 @enderror">
                    @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Location Logic -->
            <div class="border-t border-gray-100 pt-6 mt-6" x-show="needsLocation">
                <h3 class="text-sm font-semibold text-gray-900 mb-4 uppercase tracking-wider">Location Assignment</h3>

                <!-- Hardcoded Kabupaten ID 176 (Banjarbaru) -->
                <input type="hidden" name="kabupaten_id" value="176">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kecamatan -->
                    <div x-show="showKecamatan">
                        <label for="kecamatan_id" class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                        <select name="kecamatan_id" id="kecamatan_id" x-model="kecamatan_id" @change="fetchKelurahan()" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors border px-4 py-2 text-sm @error('kecamatan_id') border-red-500 @enderror">
                            <option value="">Select Kecamatan</option>
                            <template x-for="kec in kecamatans" :key="kec.id">
                                <option :value="kec.id" x-text="kec.nama"></option>
                            </template>
                        </select>
                        @error('kecamatan_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Kelurahan -->
                    <div x-show="showKelurahan">
                        <label for="kelurahan_id" class="block text-sm font-medium text-gray-700 mb-1">Kelurahan</label>
                        <select name="kelurahan_id" id="kelurahan_id" x-model="kelurahan_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors border px-4 py-2 text-sm @error('kelurahan_id') border-red-500 @enderror" :disabled="!kecamatan_id">
                            <option value="">Select Kelurahan</option>
                            <template x-for="kel in kelurahans" :key="kel.id">
                                <option :value="kel.id" x-text="kel.nama"></option>
                            </template>
                        </select>
                        @error('kelurahan_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg border border-gray-300">Cancel</a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm">Save User</button>
            </div>
        </form>
    </div>
</div>

<script>
    function userForm() {
        return {
            role: '{{ old("role") }}',
            kabupaten_id: '176', // Hardcoded Banjarbaru/Simpel Location as requested
            kecamatan_id: '{{ old("kecamatan_id") }}',
            kelurahan_id: '{{ old("kelurahan_id") }}',
            kecamatans: [],
            kelurahans: [],

            get needsLocation() {
                // Returns true if role requires any location UI section to be shown
                return ['admin_kabupaten', 'admin_kecamatan', 'admin_kelurahan'].includes(this.role);
            },
            get showKabupaten() {
                // Kabupaten logic: The user asked to make it static and hidden, 
                // so we don't need a UI toggle for it in the script logic if it's visually removed from HTML.
                // But for consistency with previous logic if it's needed:
                return this.needsLocation;
            },
            get showKecamatan() {
                return ['admin_kecamatan', 'admin_kelurahan'].includes(this.role);
            },
            get showKelurahan() {
                return ['admin_kelurahan'].includes(this.role);
            },

            init() {
                // Watch for role changes
                this.$watch('role', value => {
                    if (this.needsLocation) {
                        this.fetchKecamatan(!this.kecamatan_id); // Fetch if needed
                    } else {
                        // Reset if switching to super_admin or other
                        this.kecamatan_id = '';
                        this.kelurahan_id = '';
                    }
                });

                // Initial fetch if old values exist or role is selected (e.g. from redirect with error)
                if (this.needsLocation) {
                    this.fetchKecamatan(false);
                }
                if (this.kecamatan_id) {
                    this.fetchKelurahan();
                }
            },

            fetchKecamatan(reset = true) {
                if (reset) {
                    this.kecamatan_id = '';
                    this.kelurahan_id = '';
                }
                // Always fetch for ID 176
                fetch(`/api/location/kecamatan/176`)
                    .then(res => res.json())
                    .then(data => this.kecamatans = data);
            },

            fetchKelurahan() {
                this.kelurahan_id = '';
                this.kelurahans = [];

                if (this.kecamatan_id) {
                    fetch(`/api/location/kelurahan/${this.kecamatan_id}`)
                        .then(res => res.json())
                        .then(data => this.kelurahans = data);
                }
            }
        }
    }
</script>
@endsection