<?php

namespace App\Services;

class DecisionTreeService
{
    private const int TRAINING_COUNT = 597;

    /**
     * Apply C4.5 decision rules from RapidMiner to normalized attributes.
     *
     * @param  array{age: int, current_promil_code: string, current_action_code: string}  $attributes
     * @return array{
     *     matched: bool,
     *     prediction: string|null,
     *     rule_code: string|null,
     *     decision_path: list<string>,
     *     confidence: float|null,
     *     support_count: int|null,
     *     support_percentage: float|null
     * }
     */
    public function classify(array $attributes): array
    {
        $age = $attributes['age'];
        $promil = $attributes['current_promil_code'];
        $action = $attributes['current_action_code'];

        return match ($promil) {
            'ALAMI' => $this->classifyAlami($age, $action),
            'BELUM_PROMIL' => $this->classifyBelumPromil($age),
            'IUI' => $this->classifyIui($action),
            'IVF' => $this->classifyIvf($age, $action),
            'PEMERIKSAAN_DASAR' => $this->classifyPemeriksaanDasar($age, $action),
            'SENGGAMA_TERJADWAL' => $this->classifySenggamaTerjadwal($age),
            default => $this->unmatched([
                "Promil Saat Ini = {$promil}",
                'Tidak ditemukan decision rule yang sesuai',
            ]),
        };
    }

    /**
     * @return list<array{
     *     code: string,
     *     conditions: list<string>,
     *     prediction: string,
     *     confidence: float,
     *     support_count: int,
     *     support_percentage: float
     * }>
     */
    public function rules(): array
    {
        return [
            [
                'code' => 'R1',
                'conditions' => [
                    'Promil Saat Ini = ALAMI',
                    'Umur > 29,5',
                ],
                'prediction' => 'PRO IVF',
                'confidence' => 54.82,
                'support_count' => 197,
                'support_percentage' => 33.00,
            ],
            [
                'code' => 'R2',
                'conditions' => [
                    'Promil Saat Ini = ALAMI',
                    'Umur <= 29,5',
                    'Tindakan Hari Ini = HDSP',
                ],
                'prediction' => 'PRO Stimulasi IUI',
                'confidence' => 50.00,
                'support_count' => 4,
                'support_percentage' => 0.67,
            ],
            [
                'code' => 'R3',
                'conditions' => [
                    'Promil Saat Ini = ALAMI',
                    'Umur <= 29,5',
                    'Tindakan Hari Ini = HDSP_PLUS_GA',
                ],
                'prediction' => 'PRO IUI',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R4',
                'conditions' => [
                    'Promil Saat Ini = ALAMI',
                    'Umur <= 29,5',
                    'Tindakan Hari Ini = HYPOSY',
                ],
                'prediction' => 'PRO ST',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R5',
                'conditions' => [
                    'Promil Saat Ini = ALAMI',
                    'Umur <= 29,5',
                    'Tindakan Hari Ini = KONSUL_ONLY',
                ],
                'prediction' => 'PRO IVF',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R6',
                'conditions' => [
                    'Promil Saat Ini = ALAMI',
                    'Umur <= 29,5',
                    'Tindakan Hari Ini = KONSUL_USG_FW',
                ],
                'prediction' => 'PRO IUI',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R7',
                'conditions' => [
                    'Promil Saat Ini = ALAMI',
                    'Umur <= 29,5',
                    'Tindakan Hari Ini = KONTROL_D12_D13_PERTENGAHAN_SIKLUS',
                ],
                'prediction' => 'PRO IVF',
                'confidence' => 40.00,
                'support_count' => 5,
                'support_percentage' => 0.84,
            ],
            [
                'code' => 'R8',
                'conditions' => [
                    'Promil Saat Ini = ALAMI',
                    'Umur <= 29,5',
                    'Tindakan Hari Ini = KONTROL_D2_HAID',
                ],
                'prediction' => 'PRO Stimulasi IVF',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R9',
                'conditions' => [
                    'Promil Saat Ini = ALAMI',
                    'Umur <= 29,5',
                    'Tindakan Hari Ini = KONTROL_D2_D5',
                ],
                'prediction' => 'PRO ST',
                'confidence' => 42.86,
                'support_count' => 14,
                'support_percentage' => 2.35,
            ],
            [
                'code' => 'R10',
                'conditions' => [
                    'Promil Saat Ini = ALAMI',
                    'Umur <= 29,5',
                    'Tindakan Hari Ini = KONTROL_ONLY',
                ],
                'prediction' => 'PRO IVF',
                'confidence' => 37.50,
                'support_count' => 8,
                'support_percentage' => 1.34,
            ],
            [
                'code' => 'R11',
                'conditions' => [
                    'Promil Saat Ini = ALAMI',
                    'Umur <= 29,5',
                    'Tindakan Hari Ini = KONTROL_POST_HDSP',
                ],
                'prediction' => 'PRO IUI',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R12',
                'conditions' => [
                    'Promil Saat Ini = ALAMI',
                    'Umur <= 29,5',
                    'Tindakan Hari Ini = KONTROL_USG',
                ],
                'prediction' => 'PRO ST',
                'confidence' => 71.43,
                'support_count' => 7,
                'support_percentage' => 1.17,
            ],
            [
                'code' => 'R13',
                'conditions' => [
                    'Promil Saat Ini = ALAMI',
                    'Umur <= 29,5',
                    'Tindakan Hari Ini = PAKET_AKUPUNTUR_6X_KE_3',
                ],
                'prediction' => 'PRO IVF',
                'confidence' => 100.00,
                'support_count' => 2,
                'support_percentage' => 0.34,
            ],
            [
                'code' => 'R14',
                'conditions' => [
                    'Promil Saat Ini = ALAMI',
                    'Umur <= 29,5',
                    'Tindakan Hari Ini = PAKET_AKUPUNTUR_6X_KE_4',
                ],
                'prediction' => 'PRO IVF',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R15',
                'conditions' => [
                    'Promil Saat Ini = ALAMI',
                    'Umur <= 29,5',
                    'Tindakan Hari Ini = PAKET_AKUPUNTUR_6X_KE_5',
                ],
                'prediction' => 'PRO IVF',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R16',
                'conditions' => [
                    'Promil Saat Ini = ALAMI',
                    'Umur <= 29,5',
                    'Tindakan Hari Ini = SA_PLUS_DFI_PLUS_KONTROL',
                ],
                'prediction' => 'PRO Stimulasi IUI',
                'confidence' => 100.00,
                'support_count' => 2,
                'support_percentage' => 0.34,
            ],
            [
                'code' => 'R17',
                'conditions' => [
                    'Promil Saat Ini = ALAMI',
                    'Umur <= 29,5',
                    'Tindakan Hari Ini = SA_PLUS_KONTROL',
                ],
                'prediction' => 'PRO IUI/PRO IVF',
                'confidence' => 33.33,
                'support_count' => 3,
                'support_percentage' => 0.50,
            ],
            [
                'code' => 'R18',
                'conditions' => [
                    'Promil Saat Ini = BELUM_PROMIL',
                    'Umur > 42',
                ],
                'prediction' => 'PRO Stimulasi IVF',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R19',
                'conditions' => [
                    'Promil Saat Ini = BELUM_PROMIL',
                    'Umur <= 42',
                    'Umur > 25',
                ],
                'prediction' => 'PRO IUI',
                'confidence' => 36.17,
                'support_count' => 47,
                'support_percentage' => 7.87,
            ],
            [
                'code' => 'R20',
                'conditions' => [
                    'Promil Saat Ini = BELUM_PROMIL',
                    'Umur <= 42',
                    'Umur <= 25',
                ],
                'prediction' => 'PRO Alami',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R21',
                'conditions' => [
                    'Promil Saat Ini = IUI',
                    'Tindakan Hari Ini = KONTROL_D2_D5',
                ],
                'prediction' => 'PRO Stimulasi IUI',
                'confidence' => 66.67,
                'support_count' => 3,
                'support_percentage' => 0.50,
            ],
            [
                'code' => 'R22',
                'conditions' => [
                    'Promil Saat Ini = IUI',
                    'Tindakan Hari Ini = KONTROL_STIMULASI_IUI',
                ],
                'prediction' => 'PRO IVF',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R23',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur > 43',
                ],
                'prediction' => 'PRO Stimulasi IVF',
                'confidence' => 50.00,
                'support_count' => 2,
                'support_percentage' => 0.34,
            ],
            [
                'code' => 'R24',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = ELEKTROAKUPUNTUR',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 100.00,
                'support_count' => 3,
                'support_percentage' => 0.50,
            ],
            [
                'code' => 'R25',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = HD',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 85.71,
                'support_count' => 7,
                'support_percentage' => 1.17,
            ],
            [
                'code' => 'R26',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = HDSP',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R27',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = KONSUL_PLUS_ELEKTROAKUPUNTUR',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 100.00,
                'support_count' => 5,
                'support_percentage' => 0.84,
            ],
            [
                'code' => 'R28',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = KONSUL_DAN_USG',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R29',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = KONTROL_PLUS_ELEKTROAKUPUNTUR',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 100.00,
                'support_count' => 16,
                'support_percentage' => 2.68,
            ],
            [
                'code' => 'R30',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = KONTROL_D12_D13_PERTENGAHAN_SIKLUS',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 100.00,
                'support_count' => 6,
                'support_percentage' => 1.01,
            ],
            [
                'code' => 'R31',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = KONTROL_D2_D5',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 78.57,
                'support_count' => 14,
                'support_percentage' => 2.35,
            ],
            [
                'code' => 'R32',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = KONTROL_MINI_STIMULASI_IVF',
                ],
                'prediction' => 'PRO Stimulasi IVF',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R33',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = KONTROL_ONLY',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R34',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = KONTROL_POST_OPU',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 94.12,
                'support_count' => 17,
                'support_percentage' => 2.85,
            ],
            [
                'code' => 'R35',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = KONTROL_STIMULASI_FET',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 100.00,
                'support_count' => 10,
                'support_percentage' => 1.68,
            ],
            [
                'code' => 'R36',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = KONTROL_STIMULASI_IVF',
                ],
                'prediction' => 'PRO Stimulasi IVF',
                'confidence' => 100.00,
                'support_count' => 5,
                'support_percentage' => 0.84,
            ],
            [
                'code' => 'R37',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = KONTROL_USG',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R38',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = KONTROL_USG',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 90.00,
                'support_count' => 20,
                'support_percentage' => 3.35,
            ],
            [
                'code' => 'R39',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = KONTROL_DAN_USG',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 50.00,
                'support_count' => 2,
                'support_percentage' => 0.34,
            ],
            [
                'code' => 'R40',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = OPU',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 66.67,
                'support_count' => 6,
                'support_percentage' => 1.01,
            ],
            [
                'code' => 'R41',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = OPU_PLUS_HDSP',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R42',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = PAKET_AKUPUNTUR_12X_KE_1',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R43',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = PAKET_AKUPUNTUR_12X_KE_11',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R44',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = PAKET_AKUPUNTUR_12X_KE_12',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R45',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = PAKET_AKUPUNTUR_12X_KE_2',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 100.00,
                'support_count' => 3,
                'support_percentage' => 0.50,
            ],
            [
                'code' => 'R46',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = PAKET_AKUPUNTUR_12X_KE_3',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 100.00,
                'support_count' => 3,
                'support_percentage' => 0.50,
            ],
            [
                'code' => 'R47',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = PAKET_AKUPUNTUR_12X_KE_4',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 100.00,
                'support_count' => 2,
                'support_percentage' => 0.34,
            ],
            [
                'code' => 'R48',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = PAKET_AKUPUNTUR_12X_KE_5',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 50.00,
                'support_count' => 2,
                'support_percentage' => 0.34,
            ],
            [
                'code' => 'R49',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = PAKET_AKUPUNTUR_12X_KE_6',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 100.00,
                'support_count' => 2,
                'support_percentage' => 0.34,
            ],
            [
                'code' => 'R50',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = PAKET_AKUPUNTUR_12X_KE_7',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 100.00,
                'support_count' => 2,
                'support_percentage' => 0.34,
            ],
            [
                'code' => 'R51',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = PAKET_AKUPUNTUR_12X_KE_8',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R52',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = PAKET_AKUPUNTUR_6X_KE_1',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R53',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = PAKET_AKUPUNTUR_6X_KE_2',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 80.00,
                'support_count' => 5,
                'support_percentage' => 0.84,
            ],
            [
                'code' => 'R54',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = PAKET_AKUPUNTUR_6X_KE_3',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 100.00,
                'support_count' => 7,
                'support_percentage' => 1.17,
            ],
            [
                'code' => 'R55',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = PAKET_AKUPUNTUR_6X_KE_4',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 100.00,
                'support_count' => 4,
                'support_percentage' => 0.67,
            ],
            [
                'code' => 'R56',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = PAKET_AKUPUNTUR_6X_KE_5',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 100.00,
                'support_count' => 4,
                'support_percentage' => 0.67,
            ],
            [
                'code' => 'R57',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur <= 43',
                    'Tindakan Hari Ini = PAKET_AKUPUNTUR_6X_KE_6',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 100.00,
                'support_count' => 2,
                'support_percentage' => 0.34,
            ],
            [
                'code' => 'R58',
                'conditions' => [
                    'Promil Saat Ini = PEMERIKSAAN_DASAR',
                    'Tindakan Hari Ini = HYPOSY',
                ],
                'prediction' => 'PRO IUI',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R59',
                'conditions' => [
                    'Promil Saat Ini = PEMERIKSAAN_DASAR',
                    'Tindakan Hari Ini = KONSUL_PLUS_SA_PLUS_DFI_NEW_RUJUKAN_DPJP_BI',
                ],
                'prediction' => 'PRO Stimulasi IVF',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R60',
                'conditions' => [
                    'Promil Saat Ini = PEMERIKSAAN_DASAR',
                    'Tindakan Hari Ini = KONSUL_FW',
                ],
                'prediction' => 'PRO IVF',
                'confidence' => 100.00,
                'support_count' => 2,
                'support_percentage' => 0.34,
            ],
            [
                'code' => 'R61',
                'conditions' => [
                    'Promil Saat Ini = PEMERIKSAAN_DASAR',
                    'Tindakan Hari Ini = KONSUL_FW_PLUS_SA',
                    'Umur > 37,5',
                ],
                'prediction' => 'PRO IVF',
                'confidence' => 100.00,
                'support_count' => 2,
                'support_percentage' => 0.34,
            ],
            [
                'code' => 'R62',
                'conditions' => [
                    'Promil Saat Ini = PEMERIKSAAN_DASAR',
                    'Tindakan Hari Ini = KONSUL_FW_PLUS_SA',
                    'Umur <= 37,5',
                ],
                'prediction' => 'PRO IUI',
                'confidence' => 36.36,
                'support_count' => 11,
                'support_percentage' => 1.84,
            ],
            [
                'code' => 'R63',
                'conditions' => [
                    'Promil Saat Ini = PEMERIKSAAN_DASAR',
                    'Tindakan Hari Ini = KONSUL_FW_PLUS_SA_PLUS_DFI',
                ],
                'prediction' => 'PRO IUI',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R64',
                'conditions' => [
                    'Promil Saat Ini = PEMERIKSAAN_DASAR',
                    'Tindakan Hari Ini = KONSUL_FW_ONLY',
                    'Umur > 44,5',
                ],
                'prediction' => 'PRO Stimulasi IUI',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R65',
                'conditions' => [
                    'Promil Saat Ini = PEMERIKSAAN_DASAR',
                    'Tindakan Hari Ini = KONSUL_FW_ONLY',
                    'Umur <= 44,5',
                ],
                'prediction' => 'PRO IUI',
                'confidence' => 33.33,
                'support_count' => 9,
                'support_percentage' => 1.51,
            ],
            [
                'code' => 'R66',
                'conditions' => [
                    'Promil Saat Ini = PEMERIKSAAN_DASAR',
                    'Tindakan Hari Ini = KONSUL_ONLY_NEW_RUJUKAN_DPJP_BI',
                ],
                'prediction' => 'PRO Stimulasi IUI',
                'confidence' => 100.00,
                'support_count' => 2,
                'support_percentage' => 0.34,
            ],
            [
                'code' => 'R67',
                'conditions' => [
                    'Promil Saat Ini = PEMERIKSAAN_DASAR',
                    'Tindakan Hari Ini = KONSUL_USG_NEW_RUJUKAN_DPJP_BI',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 50.00,
                'support_count' => 2,
                'support_percentage' => 0.34,
            ],
            [
                'code' => 'R68',
                'conditions' => [
                    'Promil Saat Ini = PEMERIKSAAN_DASAR',
                    'Tindakan Hari Ini = KONSUL_USG_FW',
                    'Umur > 25,5',
                ],
                'prediction' => 'PRO IVF',
                'confidence' => 29.69,
                'support_count' => 64,
                'support_percentage' => 10.72,
            ],
            [
                'code' => 'R69',
                'conditions' => [
                    'Promil Saat Ini = PEMERIKSAAN_DASAR',
                    'Tindakan Hari Ini = KONSUL_USG_FW',
                    'Umur <= 25,5',
                ],
                'prediction' => 'PRO IUI',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R70',
                'conditions' => [
                    'Promil Saat Ini = PEMERIKSAAN_DASAR',
                    'Tindakan Hari Ini = KONTROL_D12_D13_PERTENGAHAN_SIKLUS',
                ],
                'prediction' => 'PRO IUI',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R71',
                'conditions' => [
                    'Promil Saat Ini = PEMERIKSAAN_DASAR',
                    'Tindakan Hari Ini = KONTROL_USG',
                    'Umur > 32,5',
                ],
                'prediction' => 'PRO IVF',
                'confidence' => 100.00,
                'support_count' => 2,
                'support_percentage' => 0.34,
            ],
            [
                'code' => 'R72',
                'conditions' => [
                    'Promil Saat Ini = PEMERIKSAAN_DASAR',
                    'Tindakan Hari Ini = KONTROL_USG',
                    'Umur <= 32,5',
                ],
                'prediction' => 'PRO IUI/PRO IVF',
                'confidence' => 50.00,
                'support_count' => 2,
                'support_percentage' => 0.34,
            ],
            [
                'code' => 'R73',
                'conditions' => [
                    'Promil Saat Ini = PEMERIKSAAN_DASAR',
                    'Tindakan Hari Ini = SA_PLUS_DFI_PLUS_KONSUL_FW',
                ],
                'prediction' => 'PRO IVF',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R74',
                'conditions' => [
                    'Promil Saat Ini = PEMERIKSAAN_DASAR',
                    'Tindakan Hari Ini = SA_PLUS_DFI_PLUS_KONTROL',
                ],
                'prediction' => 'PRO IUI/PRO IVF',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.17,
            ],
            [
                'code' => 'R75',
                'conditions' => [
                    'Promil Saat Ini = PEMERIKSAAN_DASAR',
                    'Tindakan Hari Ini = SA_PLUS_KONTROL',
                ],
                'prediction' => 'PRO Stimulasi IUI',
                'confidence' => 33.33,
                'support_count' => 3,
                'support_percentage' => 0.50,
            ],
            [
                'code' => 'R76',
                'conditions' => [
                    'Promil Saat Ini = SENGGAMA_TERJADWAL',
                    'Umur > 26,5',
                    'Umur > 27,5',
                ],
                'prediction' => 'PRO IUI',
                'confidence' => 69.57,
                'support_count' => 23,
                'support_percentage' => 3.85,
            ],
            [
                'code' => 'R77',
                'conditions' => [
                    'Promil Saat Ini = SENGGAMA_TERJADWAL',
                    'Umur > 26,5',
                    'Umur <= 27,5',
                ],
                'prediction' => 'PRO IUI/PRO IVF',
                'confidence' => 50.00,
                'support_count' => 2,
                'support_percentage' => 0.34,
            ],
            [
                'code' => 'R78',
                'conditions' => [
                    'Promil Saat Ini = SENGGAMA_TERJADWAL',
                    'Umur <= 26,5',
                ],
                'prediction' => 'PRO IUI/PRO IVF',
                'confidence' => 50.00,
                'support_count' => 2,
                'support_percentage' => 0.34,
            ],
        ];
    }

    /**
     * @return array{
     *     matched: bool,
     *     prediction: string|null,
     *     rule_code: string|null,
     *     decision_path: list<string>,
     *     confidence: float|null,
     *     support_count: int|null,
     *     support_percentage: float|null
     * }
     */
    private function classifyAlami(int $age, string $action): array
    {
        if ($age >= 30) {
            return $this->leaf('PRO IVF', 'R1', ['Promil Saat Ini = ALAMI', 'Umur > 29,5'], 108, 197);
        }

        return match ($action) {
            'HDSP' => $this->leaf('PRO Stimulasi IUI', 'R2', ['Promil Saat Ini = ALAMI', 'Umur <= 29,5', 'Tindakan Hari Ini = HDSP'], 2, 4),
            'HDSP_PLUS_GA' => $this->leaf('PRO IUI', 'R3', ['Promil Saat Ini = ALAMI', 'Umur <= 29,5', 'Tindakan Hari Ini = HDSP_PLUS_GA'], 1, 1),
            'HYPOSY' => $this->leaf('PRO ST', 'R4', ['Promil Saat Ini = ALAMI', 'Umur <= 29,5', 'Tindakan Hari Ini = HYPOSY'], 1, 1),
            'KONSUL_ONLY' => $this->leaf('PRO IVF', 'R5', ['Promil Saat Ini = ALAMI', 'Umur <= 29,5', 'Tindakan Hari Ini = KONSUL_ONLY'], 1, 1),
            'KONSUL_USG_FW' => $this->leaf('PRO IUI', 'R6', ['Promil Saat Ini = ALAMI', 'Umur <= 29,5', 'Tindakan Hari Ini = KONSUL_USG_FW'], 1, 1),
            'KONTROL_D12_D13_PERTENGAHAN_SIKLUS' => $this->leaf('PRO IVF', 'R7', ['Promil Saat Ini = ALAMI', 'Umur <= 29,5', 'Tindakan Hari Ini = KONTROL_D12_D13_PERTENGAHAN_SIKLUS'], 2, 5),
            'KONTROL_D2_HAID' => $this->leaf('PRO Stimulasi IVF', 'R8', ['Promil Saat Ini = ALAMI', 'Umur <= 29,5', 'Tindakan Hari Ini = KONTROL_D2_HAID'], 1, 1),
            'KONTROL_D2_D5' => $this->leaf('PRO ST', 'R9', ['Promil Saat Ini = ALAMI', 'Umur <= 29,5', 'Tindakan Hari Ini = KONTROL_D2_D5'], 6, 14),
            'KONTROL_ONLY' => $this->leaf('PRO IVF', 'R10', ['Promil Saat Ini = ALAMI', 'Umur <= 29,5', 'Tindakan Hari Ini = KONTROL_ONLY'], 3, 8),
            'KONTROL_POST_HDSP' => $this->leaf('PRO IUI', 'R11', ['Promil Saat Ini = ALAMI', 'Umur <= 29,5', 'Tindakan Hari Ini = KONTROL_POST_HDSP'], 1, 1),
            'KONTROL_USG' => $this->leaf('PRO ST', 'R12', ['Promil Saat Ini = ALAMI', 'Umur <= 29,5', 'Tindakan Hari Ini = KONTROL_USG'], 5, 7),
            'PAKET_AKUPUNTUR_6X_KE_3' => $this->leaf('PRO IVF', 'R13', ['Promil Saat Ini = ALAMI', 'Umur <= 29,5', 'Tindakan Hari Ini = PAKET_AKUPUNTUR_6X_KE_3'], 2, 2),
            'PAKET_AKUPUNTUR_6X_KE_4' => $this->leaf('PRO IVF', 'R14', ['Promil Saat Ini = ALAMI', 'Umur <= 29,5', 'Tindakan Hari Ini = PAKET_AKUPUNTUR_6X_KE_4'], 1, 1),
            'PAKET_AKUPUNTUR_6X_KE_5' => $this->leaf('PRO IVF', 'R15', ['Promil Saat Ini = ALAMI', 'Umur <= 29,5', 'Tindakan Hari Ini = PAKET_AKUPUNTUR_6X_KE_5'], 1, 1),
            'SA_PLUS_DFI_PLUS_KONTROL' => $this->leaf('PRO Stimulasi IUI', 'R16', ['Promil Saat Ini = ALAMI', 'Umur <= 29,5', 'Tindakan Hari Ini = SA_PLUS_DFI_PLUS_KONTROL'], 2, 2),
            'SA_PLUS_KONTROL' => $this->leaf('PRO IUI/PRO IVF', 'R17', ['Promil Saat Ini = ALAMI', 'Umur <= 29,5', 'Tindakan Hari Ini = SA_PLUS_KONTROL'], 1, 3),
            default => $this->unmatched(['Promil Saat Ini = ALAMI', 'Umur <= 29,5', 'Tindakan Hari Ini tidak memiliki cabang pada model aktif', 'Tidak ditemukan decision rule yang sesuai']),
        };
    }

    /**
     * @return array{
     *     matched: bool,
     *     prediction: string|null,
     *     rule_code: string|null,
     *     decision_path: list<string>,
     *     confidence: float|null,
     *     support_count: int|null,
     *     support_percentage: float|null
     * }
     */
    private function classifyBelumPromil(int $age): array
    {
        if ($age > 42) {
            return $this->leaf('PRO Stimulasi IVF', 'R18', ['Promil Saat Ini = BELUM_PROMIL', 'Umur > 42'], 1, 1);
        }

        if ($age > 25) {
            return $this->leaf('PRO IUI', 'R19', ['Promil Saat Ini = BELUM_PROMIL', 'Umur <= 42', 'Umur > 25'], 17, 47);
        }

        return $this->leaf('PRO Alami', 'R20', ['Promil Saat Ini = BELUM_PROMIL', 'Umur <= 42', 'Umur <= 25'], 1, 1);
    }

    /**
     * @return array{
     *     matched: bool,
     *     prediction: string|null,
     *     rule_code: string|null,
     *     decision_path: list<string>,
     *     confidence: float|null,
     *     support_count: int|null,
     *     support_percentage: float|null
     * }
     */
    private function classifyIui(string $action): array
    {
        return match ($action) {
            'KONTROL_D2_D5' => $this->leaf('PRO Stimulasi IUI', 'R21', ['Promil Saat Ini = IUI', 'Tindakan Hari Ini = KONTROL_D2_D5'], 2, 3),
            'KONTROL_STIMULASI_IUI' => $this->leaf('PRO IVF', 'R22', ['Promil Saat Ini = IUI', 'Tindakan Hari Ini = KONTROL_STIMULASI_IUI'], 1, 1),
            default => $this->unmatched(['Promil Saat Ini = IUI', 'Tindakan Hari Ini tidak memiliki cabang pada model aktif', 'Tidak ditemukan decision rule yang sesuai']),
        };
    }

    /**
     * @return array{
     *     matched: bool,
     *     prediction: string|null,
     *     rule_code: string|null,
     *     decision_path: list<string>,
     *     confidence: float|null,
     *     support_count: int|null,
     *     support_percentage: float|null
     * }
     */
    private function classifyIvf(int $age, string $action): array
    {
        if ($age > 43) {
            return $this->leaf('PRO Stimulasi IVF', 'R23', ['Promil Saat Ini = IVF', 'Umur > 43'], 1, 2);
        }

        return match ($action) {
            'ELEKTROAKUPUNTUR' => $this->leaf('PRO Stimulasi FET', 'R24', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = ELEKTROAKUPUNTUR'], 3, 3),
            'HD' => $this->leaf('PRO Stimulasi FET', 'R25', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = HD'], 6, 7),
            'HDSP' => $this->leaf('PRO Stimulasi FET', 'R26', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = HDSP'], 1, 1),
            'KONSUL_PLUS_ELEKTROAKUPUNTUR' => $this->leaf('PRO Stimulasi FET', 'R27', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = KONSUL_PLUS_ELEKTROAKUPUNTUR'], 5, 5),
            'KONSUL_DAN_USG' => $this->leaf('PRO Stimulasi FET', 'R28', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = KONSUL_DAN_USG'], 1, 1),
            'KONTROL_PLUS_ELEKTROAKUPUNTUR' => $this->leaf('PRO Stimulasi FET', 'R29', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = KONTROL_PLUS_ELEKTROAKUPUNTUR'], 16, 16),
            'KONTROL_D12_D13_PERTENGAHAN_SIKLUS' => $this->leaf('PRO Stimulasi FET', 'R30', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = KONTROL_D12_D13_PERTENGAHAN_SIKLUS'], 6, 6),
            'KONTROL_D2_D5' => $this->leaf('PRO Stimulasi FET', 'R31', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = KONTROL_D2_D5'], 11, 14),
            'KONTROL_MINI_STIMULASI_IVF' => $this->leaf('PRO Stimulasi IVF', 'R32', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = KONTROL_MINI_STIMULASI_IVF'], 1, 1),
            'KONTROL_ONLY' => $this->leaf('PRO Stimulasi FET', 'R33', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = KONTROL_ONLY'], 1, 1),
            'KONTROL_POST_OPU' => $this->leaf('PRO Stimulasi FET', 'R34', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = KONTROL_POST_OPU'], 16, 17),
            'KONTROL_STIMULASI_FET' => $this->leaf('PRO Stimulasi FET', 'R35', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = KONTROL_STIMULASI_FET'], 10, 10),
            'KONTROL_STIMULASI_IVF' => $this->leaf('PRO Stimulasi IVF', 'R36', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = KONTROL_STIMULASI_IVF'], 5, 5),
            'KONTROL_USG' => $this->leaf('PRO Stimulasi FET', 'R37', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = KONTROL_USG'], 19, 21),
            'KONTROL_DAN_USG' => $this->leaf('PRO Stimulasi FET', 'R39', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = KONTROL_DAN_USG'], 1, 2),
            'OPU' => $this->leaf('PRO Stimulasi FET', 'R40', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = OPU'], 4, 6),
            'OPU_PLUS_HDSP' => $this->leaf('PRO Stimulasi FET', 'R41', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = OPU_PLUS_HDSP'], 1, 1),
            'PAKET_AKUPUNTUR_12X_KE_1' => $this->leaf('PRO Stimulasi FET', 'R42', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = PAKET_AKUPUNTUR_12X_KE_1'], 1, 1),
            'PAKET_AKUPUNTUR_12X_KE_11' => $this->leaf('PRO Stimulasi FET', 'R43', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = PAKET_AKUPUNTUR_12X_KE_11'], 1, 1),
            'PAKET_AKUPUNTUR_12X_KE_12' => $this->leaf('PRO Stimulasi FET', 'R44', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = PAKET_AKUPUNTUR_12X_KE_12'], 1, 1),
            'PAKET_AKUPUNTUR_12X_KE_2' => $this->leaf('PRO Stimulasi FET', 'R45', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = PAKET_AKUPUNTUR_12X_KE_2'], 3, 3),
            'PAKET_AKUPUNTUR_12X_KE_3' => $this->leaf('PRO Stimulasi FET', 'R46', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = PAKET_AKUPUNTUR_12X_KE_3'], 3, 3),
            'PAKET_AKUPUNTUR_12X_KE_4' => $this->leaf('PRO Stimulasi FET', 'R47', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = PAKET_AKUPUNTUR_12X_KE_4'], 2, 2),
            'PAKET_AKUPUNTUR_12X_KE_5' => $this->leaf('PRO Stimulasi FET', 'R48', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = PAKET_AKUPUNTUR_12X_KE_5'], 1, 2),
            'PAKET_AKUPUNTUR_12X_KE_6' => $this->leaf('PRO Stimulasi FET', 'R49', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = PAKET_AKUPUNTUR_12X_KE_6'], 2, 2),
            'PAKET_AKUPUNTUR_12X_KE_7' => $this->leaf('PRO Stimulasi FET', 'R50', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = PAKET_AKUPUNTUR_12X_KE_7'], 2, 2),
            'PAKET_AKUPUNTUR_12X_KE_8' => $this->leaf('PRO Stimulasi FET', 'R51', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = PAKET_AKUPUNTUR_12X_KE_8'], 1, 1),
            'PAKET_AKUPUNTUR_6X_KE_1' => $this->leaf('PRO Stimulasi FET', 'R52', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = PAKET_AKUPUNTUR_6X_KE_1'], 1, 1),
            'PAKET_AKUPUNTUR_6X_KE_2' => $this->leaf('PRO Stimulasi FET', 'R53', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = PAKET_AKUPUNTUR_6X_KE_2'], 4, 5),
            'PAKET_AKUPUNTUR_6X_KE_3' => $this->leaf('PRO Stimulasi FET', 'R54', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = PAKET_AKUPUNTUR_6X_KE_3'], 7, 7),
            'PAKET_AKUPUNTUR_6X_KE_4' => $this->leaf('PRO Stimulasi FET', 'R55', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = PAKET_AKUPUNTUR_6X_KE_4'], 4, 4),
            'PAKET_AKUPUNTUR_6X_KE_5' => $this->leaf('PRO Stimulasi FET', 'R56', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = PAKET_AKUPUNTUR_6X_KE_5'], 4, 4),
            'PAKET_AKUPUNTUR_6X_KE_6' => $this->leaf('PRO Stimulasi FET', 'R57', ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = PAKET_AKUPUNTUR_6X_KE_6'], 2, 2),
            default => $this->unmatched(['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini tidak memiliki cabang pada model aktif', 'Tidak ditemukan decision rule yang sesuai']),
        };
    }

    /**
     * @return array{
     *     matched: bool,
     *     prediction: string|null,
     *     rule_code: string|null,
     *     decision_path: list<string>,
     *     confidence: float|null,
     *     support_count: int|null,
     *     support_percentage: float|null
     * }
     */
    private function classifyPemeriksaanDasar(int $age, string $action): array
    {
        if ($action === 'HYPOSY') {
            return $this->leaf('PRO IUI', 'R58', ['Promil Saat Ini = PEMERIKSAAN_DASAR', 'Tindakan Hari Ini = HYPOSY'], 1, 1);
        }

        if ($action === 'KONSUL_PLUS_SA_PLUS_DFI_NEW_RUJUKAN_DPJP_BI') {
            return $this->leaf('PRO Stimulasi IVF', 'R59', ['Promil Saat Ini = PEMERIKSAAN_DASAR', 'Tindakan Hari Ini = KONSUL_PLUS_SA_PLUS_DFI_NEW_RUJUKAN_DPJP_BI'], 1, 1);
        }

        if ($action === 'KONSUL_FW') {
            return $this->leaf('PRO IVF', 'R60', ['Promil Saat Ini = PEMERIKSAAN_DASAR', 'Tindakan Hari Ini = KONSUL_FW'], 2, 2);
        }

        if ($action === 'KONSUL_FW_PLUS_SA_PLUS_DFI') {
            return $this->leaf('PRO IUI', 'R63', ['Promil Saat Ini = PEMERIKSAAN_DASAR', 'Tindakan Hari Ini = KONSUL_FW_PLUS_SA_PLUS_DFI'], 1, 1);
        }

        if ($action === 'KONSUL_ONLY_NEW_RUJUKAN_DPJP_BI') {
            return $this->leaf('PRO Stimulasi IUI', 'R66', ['Promil Saat Ini = PEMERIKSAAN_DASAR', 'Tindakan Hari Ini = KONSUL_ONLY_NEW_RUJUKAN_DPJP_BI'], 2, 2);
        }

        if ($action === 'KONSUL_USG_NEW_RUJUKAN_DPJP_BI') {
            return $this->leaf('PRO Stimulasi FET', 'R67', ['Promil Saat Ini = PEMERIKSAAN_DASAR', 'Tindakan Hari Ini = KONSUL_USG_NEW_RUJUKAN_DPJP_BI'], 1, 2);
        }

        if ($action === 'KONTROL_D12_D13_PERTENGAHAN_SIKLUS') {
            return $this->leaf('PRO IUI', 'R70', ['Promil Saat Ini = PEMERIKSAAN_DASAR', 'Tindakan Hari Ini = KONTROL_D12_D13_PERTENGAHAN_SIKLUS'], 1, 1);
        }

        if ($action === 'SA_PLUS_DFI_PLUS_KONSUL_FW') {
            return $this->leaf('PRO IVF', 'R73', ['Promil Saat Ini = PEMERIKSAAN_DASAR', 'Tindakan Hari Ini = SA_PLUS_DFI_PLUS_KONSUL_FW'], 1, 1);
        }

        if ($action === 'SA_PLUS_DFI_PLUS_KONTROL') {
            return $this->leaf('PRO IUI/PRO IVF', 'R74', ['Promil Saat Ini = PEMERIKSAAN_DASAR', 'Tindakan Hari Ini = SA_PLUS_DFI_PLUS_KONTROL'], 1, 1);
        }

        if ($action === 'SA_PLUS_KONTROL') {
            return $this->leaf('PRO Stimulasi IUI', 'R75', ['Promil Saat Ini = PEMERIKSAAN_DASAR', 'Tindakan Hari Ini = SA_PLUS_KONTROL'], 1, 3);
        }

        if ($action === 'KONSUL_FW_PLUS_SA') {
            if ($age >= 38) {
                return $this->leaf('PRO IVF', 'R61', ['Promil Saat Ini = PEMERIKSAAN_DASAR', 'Tindakan Hari Ini = KONSUL_FW_PLUS_SA', 'Umur > 37,5'], 2, 2);
            }

            return $this->leaf('PRO IUI', 'R62', ['Promil Saat Ini = PEMERIKSAAN_DASAR', 'Tindakan Hari Ini = KONSUL_FW_PLUS_SA', 'Umur <= 37,5'], 4, 11);
        }

        if ($action === 'KONSUL_FW_ONLY') {
            if ($age >= 45) {
                return $this->leaf('PRO Stimulasi IUI', 'R64', ['Promil Saat Ini = PEMERIKSAAN_DASAR', 'Tindakan Hari Ini = KONSUL_FW_ONLY', 'Umur > 44,5'], 1, 1);
            }

            return $this->leaf('PRO IUI', 'R65', ['Promil Saat Ini = PEMERIKSAAN_DASAR', 'Tindakan Hari Ini = KONSUL_FW_ONLY', 'Umur <= 44,5'], 3, 9);
        }

        if ($action === 'KONSUL_USG_FW') {
            if ($age >= 26) {
                return $this->leaf('PRO IVF', 'R68', ['Promil Saat Ini = PEMERIKSAAN_DASAR', 'Tindakan Hari Ini = KONSUL_USG_FW', 'Umur > 25,5'], 19, 64);
            }

            return $this->leaf('PRO IUI', 'R69', ['Promil Saat Ini = PEMERIKSAAN_DASAR', 'Tindakan Hari Ini = KONSUL_USG_FW', 'Umur <= 25,5'], 1, 1);
        }

        if ($action === 'KONTROL_USG') {
            if ($age >= 33) {
                return $this->leaf('PRO IVF', 'R71', ['Promil Saat Ini = PEMERIKSAAN_DASAR', 'Tindakan Hari Ini = KONTROL_USG', 'Umur > 32,5'], 2, 2);
            }

            return $this->leaf('PRO IUI/PRO IVF', 'R72', ['Promil Saat Ini = PEMERIKSAAN_DASAR', 'Tindakan Hari Ini = KONTROL_USG', 'Umur <= 32,5'], 1, 2);
        }

        return $this->unmatched(['Promil Saat Ini = PEMERIKSAAN_DASAR', 'Tindakan Hari Ini tidak memiliki cabang pada model aktif', 'Tidak ditemukan decision rule yang sesuai']);
    }

    /**
     * @return array{
     *     matched: bool,
     *     prediction: string|null,
     *     rule_code: string|null,
     *     decision_path: list<string>,
     *     confidence: float|null,
     *     support_count: int|null,
     *     support_percentage: float|null
     * }
     */
    private function classifySenggamaTerjadwal(int $age): array
    {
        if ($age >= 27) {
            if ($age >= 28) {
                return $this->leaf('PRO IUI', 'R76', ['Promil Saat Ini = SENGGAMA_TERJADWAL', 'Umur > 26,5', 'Umur > 27,5'], 16, 23);
            }

            return $this->leaf('PRO IUI/PRO IVF', 'R77', ['Promil Saat Ini = SENGGAMA_TERJADWAL', 'Umur > 26,5', 'Umur <= 27,5'], 1, 2);
        }

        return $this->leaf('PRO IUI/PRO IVF', 'R78', ['Promil Saat Ini = SENGGAMA_TERJADWAL', 'Umur <= 26,5'], 1, 2);
    }

    /**
     * @param  list<string>  $decisionPath
     * @return array{
     *     matched: bool,
     *     prediction: string|null,
     *     rule_code: string|null,
     *     decision_path: list<string>,
     *     confidence: float|null,
     *     support_count: int|null,
     *     support_percentage: float|null
     * }
     */
    private function leaf(
        string $prediction,
        string $ruleCode,
        array $decisionPath,
        int $majorityCount,
        int $supportCount,
    ): array {
        return $this->matched(
            prediction: $prediction,
            ruleCode: $ruleCode,
            decisionPath: [...$decisionPath, "Promil Selanjutnya = {$prediction}"],
            confidence: round(($majorityCount / $supportCount) * 100, 2),
            supportCount: $supportCount,
            supportPercentage: round(($supportCount / self::TRAINING_COUNT) * 100, 2),
        );
    }

    /**
     * @param  list<string>  $decisionPath
     * @return array{
     *     matched: bool,
     *     prediction: string|null,
     *     rule_code: string|null,
     *     decision_path: list<string>,
     *     confidence: float|null,
     *     support_count: int|null,
     *     support_percentage: float|null
     * }
     */
    private function matched(
        string $prediction,
        string $ruleCode,
        array $decisionPath,
        float $confidence,
        int $supportCount,
        float $supportPercentage,
    ): array {
        return [
            'matched' => true,
            'prediction' => $prediction,
            'rule_code' => $ruleCode,
            'decision_path' => $decisionPath,
            'confidence' => $confidence,
            'support_count' => $supportCount,
            'support_percentage' => $supportPercentage,
        ];
    }

    /**
     * @param  list<string>  $decisionPath
     * @return array{
     *     matched: bool,
     *     prediction: string|null,
     *     rule_code: string|null,
     *     decision_path: list<string>,
     *     confidence: float|null,
     *     support_count: int|null,
     *     support_percentage: float|null
     * }
     */
    private function unmatched(array $decisionPath): array
    {
        return [
            'matched' => false,
            'prediction' => null,
            'rule_code' => null,
            'decision_path' => $decisionPath,
            'confidence' => null,
            'support_count' => null,
            'support_percentage' => null,
        ];
    }
}
