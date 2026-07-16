<?php

use App\Models\CurrentAction;
use App\Models\CurrentPromil;
use App\Models\Doctor;
use App\Models\Poa1Option;

return [
    'doctors' => [
        'label' => 'Dokter Pemeriksa',
        'description' => 'Kelola daftar dokter yang tersedia pada form klasifikasi.',
        'model' => Doctor::class,
    ],
    'current-promils' => [
        'label' => 'Promil Saat Ini',
        'description' => 'Kelola opsi promil saat ini sesuai kode decision rule.',
        'model' => CurrentPromil::class,
    ],
    'current-actions' => [
        'label' => 'Tindakan Hari Ini',
        'description' => 'Kelola tindakan yang digunakan pada klasifikasi.',
        'model' => CurrentAction::class,
    ],
    'poa1-options' => [
        'label' => 'PoA1',
        'description' => 'Kelola opsi PoA1 sebagai atribut penelitian.',
        'model' => Poa1Option::class,
    ],
];
