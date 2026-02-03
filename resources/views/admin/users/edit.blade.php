@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit User: {{ $user->name }}</h1>
        <p class="text-gray-500">Update user details and access level.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8"
        x-data="userForm()"
        x-init="init()">

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Role Selection -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role" id="role" x-model="role" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors border px-4 py-2 text-sm @error('role') border-red-500 @enderror">
                    @foreach($roles as $role)
                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $user->name) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors border px-4 py-2 text-sm @error('name') border-red-500 @enderror">
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" id="email" required value="{{ old('email', $user->email) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors border px-4 py-2 text-sm @error('email') border-red-500 @enderror">
                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password (Optional)</label>
                    <input type="password" name="password" id="password" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors border px-4 py-2 text-sm @error('password') border-red-500 @enderror">
                    @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors border px-4 py-2 text-sm">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors border px-4 py-2 text-sm @error('phone') border-red-500 @enderror">
                    @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Location Logic -->
            <div class="border-t border-gray-100 pt-6 mt-6" x-show="needsLocation">
                <h3 class="text-sm font-semibold text-gray-900 mb-4 uppercase tracking-wider">Location Assignment</h3>

                <!-- Hardcoded Kabupaten ID 176 -->
                <input type="hidden" name="kabupaten_id" value="176">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kecamatan -->
                    <div x-show="showKecamatan">
                        <label for="kecamatan_id" class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                        <select name="kecamatan_id" id="kecamatan_id" x-model="kecamatan_id" @change="fetchKelurahan()" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors border px-4 py-2 text-sm @error('kecamatan_id') border-red-500 @enderror">
                            <option value="">Select Kecamatan</option>
                            <template x-for="kec in kecamatans" :key="kec.id">
                                <option :value="kec.id" x-text="kec.nama" :selected="kec.id == kecamatan_id"></option>
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
                                <option :value="kel.id" x-text="kel.nama" :selected="kel.id == kelurahan_id"></option>
                            </template>
                        </select>
                        @error('kelurahan_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg border border-gray-300">Cancel</a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm">Update User</button>
            </div>
        </form>
    </div>
</div>

<script>
    function userForm() {
        return {
            role: '{{ old('
            role ', $user->roles->first()?->name) }}',
            kabupaten_id: '176', // Hardcoded Banjarbaru (was: '{{ $user->kabupaten_id }}')
            kecamatan_id: '{{ old('
            kecamatan_id ', $user->kecamatan_id) }}',
            kelurahan_id: '{{ old('
            kelurahan_id ', $user->kelurahan_id) }}',
            kecamatans: [],
            kelurahans: [],

            get needsLocation() {
                return ['admin_kabupaten', 'admin_kecamatan', 'admin_kelurahan'].includes(this.role);
            },
            get showKecamatan() {
                return ['admin_kecamatan', 'admin_kelurahan'].includes(this.role);
            },
            get showKelurahan() {
                return ['admin_kelurahan'].includes(this.role);
            },

            async init() {
                // If editing, we likely have values.
                // If role requires location, fetch kecamatans for ID 176
                if (this.needsLocation) {
                    await this.fetchKecamatan(false); // false = don't reset values
                }

                // If we also have a kecamatan selected (from old data), fetch kelurahans
                if (this.kecamatan_id) {
                    await this.fetchKelurahan();
                }

                this.$watch('role', value => {
                    if (this.needsLocation) {
                        // If switching to a role that needs location, 
                        // and we don't have kecamatans yet (or just to be safe), load them.
                        // But if we already have them, maybe don't reset?
                        // "create" logic resets on role change. Edit logic might want to preserve 
                        // if user is just switching around? 
                        // Let's stick to "fetch if needed". If switching from super_admin to admin_kecamatan, load.
                        this.fetchKecamatan(this.kecamatan_id == '');
                    } else {
                        this.kecamatan_id = '';
                        this.kelurahan_id = '';
                    }
                });
            },

            async fetchKecamatan(reset = true) {
                if (reset) {
                    this.kecamatan_id = '';
                    this.kelurahan_id = '';
                }
                // Always fetch for ID 176
                const res = await fetch(`/api/location/kecamatan/176`);
                this.kecamatans = await res.json();
            },

            async fetchKelurahan() {
                // Determine if we should reset kelurahan. 
                // If this is triggered by @change on kecamatan, yes.
                // If triggered by init, no.
                // But fetchKelurahan() implementation in init calls it directly.
                // Simple approach: When changing kecamatan, we manually clear kelurahan in HTML @change if needed,
                // or we accept that fetchKelurahan re-populates the list, and if the old ID isn't in there, it becomes invalid.
                // But wait, @change="fetchKelurahan()" implies we just fetch the list. 
                // We should clear the selected value if it's a new fetch.

                // Note: Alpine @change happens after the model update.

                this.kelurahans = [];
                if (this.kecamatan_id) {
                    const res = await fetch(`/api/location/kelurahan/${this.kecamatan_id}`);
                    this.kelurahans = await res.json();
                }
            }
        }
    }
</script>
@endsection