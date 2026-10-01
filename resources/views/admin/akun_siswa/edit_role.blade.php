@extends('layouts.admin')

@section('title', 'Atur Role Guru')

@section('admin-content')
    <div class="ews-crud ews-crud-form">
        <a href="{{ route('admin.akun-guru.index') }}" class="ews-back-link">← Kembali ke daftar</a>
        <h1 class="text-lg font-semibold mb-1">Atur Role — {{ $guru->nama }}</h1>
        <p class="text-xs text-gray-500 mb-4">NIP: {{ $guru->nip }}</p>

        <form action="{{ route('admin.akun-guru.role.update', $guru) }}" method="POST">
            <div class="ews-fields">
            @csrf
            @if ($errors->any())
                <div class="ews-notice ews-notice-error" role="alert">
                    <strong>Data belum dapat disimpan.</strong>
                    <ul class="list-disc pl-5 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @method('PUT')

            <div class="mb-4">
                <p class="text-sm font-medium mb-2">Centang role yang dimiliki guru ini (boleh lebih dari 1):</p>
                @foreach ($roles as $role)
                    @php
                        $dimiliki = $guru->user->roles->contains('id', $role->id);
                    @endphp
                    <label class="flex items-center mb-2 text-sm">
                        <input type="checkbox" name="role_ids[]" value="{{ $role->id }}" class="mr-2 role-checkbox"
                            data-role-id="{{ $role->id }}"
                            {{ in_array($role->id, old('role_ids', $guru->user->roles->pluck('id')->toArray())) ? 'checked' : '' }}>
                        {{ $role->label }}
                    </label>
                @endforeach
                @error('role_ids')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Role default (dipakai saat pertama login):</label>
                @foreach ($roles as $role)
                    <label class="flex items-center mb-2 text-sm">
                        <input type="radio" name="default_role_id" aria-invalid="{{ $errors->has('default_role_id') ? 'true' : 'false' }}" @error('default_role_id') aria-describedby="error-default_role_id" @enderror value="{{ $role->id }}" class="mr-2 default-radio"
                            data-role-id="{{ $role->id }}"
                            {{ old('default_role_id', $guru->user->default_role_id) == $role->id ? 'checked' : '' }}>
                        {{ $role->label }}
                    </label>
                @endforeach
                @error('default_role_id')
                    <p id="error-default_role_id" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-400 mt-1">Role default harus salah satu dari role yang dicentang di atas.</p>
            </div>

            </div>
            <div class="ews-actions">
                <button type="submit" class="ews-button ews-button-primary">
                    Simpan
                </button>
                <a href="{{ route('admin.akun-guru.index') }}" class="ews-button ews-button-secondary">Batal</a>
            </div>
        </form>
    </div>

    <script>
        document.querySelectorAll('.role-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                if (!this.checked) {
                    var radio = document.querySelector('.default-radio[data-role-id="' + this.dataset
                        .roleId + '"]');
                    if (radio && radio.checked) {
                        radio.checked = false;
                    }
                }
            });
        });
    </script>
@endsection
