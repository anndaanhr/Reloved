@extends('layouts.app')

@section('content')
    @php
        $columns = [
            [
                'key' => 'id',
                'label' => 'ID',
                'type' => 'number',
                'sortable' => true
            ],
            [
                'key' => 'nama',
                'label' => 'Nama Lengkap',
                'type' => 'avatar',
                'subtitle' => 'npm',
                'sortable' => true
            ],
            [
                'key' => 'npm',
                'label' => 'NPM',
                'sortable' => true
            ],
            [
                'key' => 'nama_kelas',
                'label' => 'Kelas',
                'type' => 'badge',
                'badgeColor' => 'primary',
                'icon' => 'building',
                'sortable' => true
            ]
        ];

        $actions = [
            [
                'icon' => 'eye',
                'variant' => 'outline-info',
                'title' => 'Lihat Detail',
                'onclick' => 'viewUser'
            ],
            [
                'icon' => 'pencil',
                'variant' => 'outline-warning',
                'title' => 'Edit User',
                'onclick' => 'editUser'
            ],
            [
                'icon' => 'trash',
                'variant' => 'outline-danger',
                'title' => 'Hapus User',
                'onclick' => 'deleteUser'
            ]
        ];

        $filterOptions = collect($user)->pluck('nama_kelas')->unique()->map(function($kelas) {
            return ['value' => $kelas, 'label' => 'Kelas ' . $kelas];
        })->values()->toArray();

        $addButton = [
            'url' => url('/user/create'),
            'text' => 'Tambah User'
        ];
    @endphp

    <x-dynamic-table 
        :data="$user" 
        :columns="$columns"
        :actions="$actions"
        :filter-options="$filterOptions"
        :add-button="$addButton"
        title="Daftar Pengguna"
        :searchable="true"
        :filterable="true"
    />

    <script>
        function viewUser(id) {
            alert('Melihat detail user dengan ID: ' + id);
            // Implementasi view user
        }

        function editUser(id) {
            alert('Mengedit user dengan ID: ' + id);
            // Implementasi edit user
        }

        function deleteUser(id) {
            if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
                alert('Menghapus user dengan ID: ' + id);
                // Implementasi delete user
            }
        }
    </script>
@endsection
