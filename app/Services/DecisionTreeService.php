<?php

namespace App\Services;

class DecisionTreeService
{
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
            'ALAMI' => $this->classifyAlami($age),
            'BELUM_PROMIL' => $this->matched(
                prediction: 'PRO IUI/PRO IVF',
                ruleCode: 'R4',
                decisionPath: [
                    'Promil Saat Ini = BELUM_PROMIL',
                    'Promil Selanjutnya = PRO IUI/PRO IVF',
                ],
                confidence: 100.00,
                supportCount: 1,
                supportPercentage: 0.99,
            ),
            'IUI' => $this->matched(
                prediction: 'PRO IUI',
                ruleCode: 'R5',
                decisionPath: [
                    'Promil Saat Ini = IUI',
                    'Promil Selanjutnya = PRO IUI',
                ],
                confidence: 100.00,
                supportCount: 1,
                supportPercentage: 0.99,
            ),
            'IVF' => $this->classifyIvf($age, $action),
            'PEMERIKSAAN_DASAR' => $this->classifyPemeriksaanDasar($age),
            'SENGGAMA_TERJADWAL' => $this->matched(
                prediction: 'PRO IUI',
                ruleCode: 'R14',
                decisionPath: [
                    'Promil Saat Ini = SENGGAMA_TERJADWAL',
                    'Promil Selanjutnya = PRO IUI',
                ],
                confidence: 66.67,
                supportCount: 3,
                supportPercentage: 2.97,
            ),
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
                    'Umur minimal 29 (Umur > 28,5)',
                ],
                'prediction' => 'PRO IVF',
                'confidence' => 40.48,
                'support_count' => 42,
                'support_percentage' => 41.58,
            ],
            [
                'code' => 'R2',
                'conditions' => [
                    'Promil Saat Ini = ALAMI',
                    'Umur = 28 (Umur > 27,5 dan ≤ 28,5)',
                ],
                'prediction' => 'PRO Stimulasi IUI',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.99,
            ],
            [
                'code' => 'R3',
                'conditions' => [
                    'Promil Saat Ini = ALAMI',
                    'Umur maksimal 27 (Umur ≤ 27,5)',
                ],
                'prediction' => 'PRO ST',
                'confidence' => 100.00,
                'support_count' => 3,
                'support_percentage' => 2.97,
            ],
            [
                'code' => 'R4',
                'conditions' => [
                    'Promil Saat Ini = BELUM_PROMIL',
                ],
                'prediction' => 'PRO IUI/PRO IVF',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.99,
            ],
            [
                'code' => 'R5',
                'conditions' => [
                    'Promil Saat Ini = IUI',
                ],
                'prediction' => 'PRO IUI',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.99,
            ],
            [
                'code' => 'R6',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur maksimal 40 (Umur ≤ 40,5)',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 95.83,
                'support_count' => 24,
                'support_percentage' => 23.76,
            ],
            [
                'code' => 'R7',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur minimal 41 (Umur > 40,5)',
                    'Tindakan Hari Ini = HO',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.99,
            ],
            [
                'code' => 'R8',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur minimal 41 (Umur > 40,5)',
                    'Tindakan Hari Ini = KONTROL_D2_D5',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 66.67,
                'support_count' => 3,
                'support_percentage' => 2.97,
            ],
            [
                'code' => 'R9',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur minimal 41 (Umur > 40,5)',
                    'Tindakan Hari Ini = PRP_OVARIUM_TRANSVAGINAL',
                ],
                'prediction' => 'PRO Stimulasi IVF',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.99,
            ],
            [
                'code' => 'R10',
                'conditions' => [
                    'Promil Saat Ini = IVF',
                    'Umur minimal 41 (Umur > 40,5)',
                    'Tindakan Hari Ini = PAKET_AKUPUNTUR_12X_KE_6',
                ],
                'prediction' => 'PRO Stimulasi FET',
                'confidence' => 100.00,
                'support_count' => 1,
                'support_percentage' => 0.99,
            ],
            [
                'code' => 'R11',
                'conditions' => [
                    'Promil Saat Ini = PEMERIKSAAN_DASAR',
                    'Umur minimal 47 (Umur > 46,5)',
                ],
                'prediction' => 'PRO IUI',
                'confidence' => 50.00,
                'support_count' => 2,
                'support_percentage' => 1.98,
            ],
            [
                'code' => 'R12',
                'conditions' => [
                    'Promil Saat Ini = PEMERIKSAAN_DASAR',
                    'Umur 29 sampai 46 (Umur > 28,5 dan ≤ 46,5)',
                ],
                'prediction' => 'PRO IVF',
                'confidence' => 40.00,
                'support_count' => 15,
                'support_percentage' => 14.85,
            ],
            [
                'code' => 'R13',
                'conditions' => [
                    'Promil Saat Ini = PEMERIKSAAN_DASAR',
                    'Umur maksimal 28 (Umur ≤ 28,5)',
                ],
                'prediction' => 'PRO IUI',
                'confidence' => 100.00,
                'support_count' => 3,
                'support_percentage' => 2.97,
            ],
            [
                'code' => 'R14',
                'conditions' => [
                    'Promil Saat Ini = SENGGAMA_TERJADWAL',
                ],
                'prediction' => 'PRO IUI',
                'confidence' => 66.67,
                'support_count' => 3,
                'support_percentage' => 2.97,
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
    private function classifyAlami(int $age): array
    {
        if ($age >= 29) {
            return $this->matched(
                prediction: 'PRO IVF',
                ruleCode: 'R1',
                decisionPath: [
                    'Promil Saat Ini = ALAMI',
                    'Umur > 28,5',
                    'Promil Selanjutnya = PRO IVF',
                ],
                confidence: 40.48,
                supportCount: 42,
                supportPercentage: 41.58,
            );
        }

        if ($age === 28) {
            return $this->matched(
                prediction: 'PRO Stimulasi IUI',
                ruleCode: 'R2',
                decisionPath: [
                    'Promil Saat Ini = ALAMI',
                    'Umur > 27,5 dan ≤ 28,5',
                    'Promil Selanjutnya = PRO Stimulasi IUI',
                ],
                confidence: 100.00,
                supportCount: 1,
                supportPercentage: 0.99,
            );
        }

        return $this->matched(
            prediction: 'PRO ST',
            ruleCode: 'R3',
            decisionPath: [
                'Promil Saat Ini = ALAMI',
                'Umur ≤ 27,5',
                'Promil Selanjutnya = PRO ST',
            ],
            confidence: 100.00,
            supportCount: 3,
            supportPercentage: 2.97,
        );
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
        if ($age <= 40) {
            return $this->matched(
                prediction: 'PRO Stimulasi FET',
                ruleCode: 'R6',
                decisionPath: [
                    'Promil Saat Ini = IVF',
                    'Umur ≤ 40,5',
                    'Promil Selanjutnya = PRO Stimulasi FET',
                ],
                confidence: 95.83,
                supportCount: 24,
                supportPercentage: 23.76,
            );
        }

        return match ($action) {
            'HO' => $this->matched(
                prediction: 'PRO Stimulasi FET',
                ruleCode: 'R7',
                decisionPath: [
                    'Promil Saat Ini = IVF',
                    'Umur > 40,5',
                    'Tindakan Hari Ini = HO',
                    'Promil Selanjutnya = PRO Stimulasi FET',
                ],
                confidence: 100.00,
                supportCount: 1,
                supportPercentage: 0.99,
            ),
            'KONTROL_D2_D5' => $this->matched(
                prediction: 'PRO Stimulasi FET',
                ruleCode: 'R8',
                decisionPath: [
                    'Promil Saat Ini = IVF',
                    'Umur > 40,5',
                    'Tindakan Hari Ini = KONTROL_D2_D5',
                    'Promil Selanjutnya = PRO Stimulasi FET',
                ],
                confidence: 66.67,
                supportCount: 3,
                supportPercentage: 2.97,
            ),
            'PRP_OVARIUM_TRANSVAGINAL' => $this->matched(
                prediction: 'PRO Stimulasi IVF',
                ruleCode: 'R9',
                decisionPath: [
                    'Promil Saat Ini = IVF',
                    'Umur > 40,5',
                    'Tindakan Hari Ini = PRP_OVARIUM_TRANSVAGINAL',
                    'Promil Selanjutnya = PRO Stimulasi IVF',
                ],
                confidence: 100.00,
                supportCount: 1,
                supportPercentage: 0.99,
            ),
            'PAKET_AKUPUNTUR_12X_KE_6' => $this->matched(
                prediction: 'PRO Stimulasi FET',
                ruleCode: 'R10',
                decisionPath: [
                    'Promil Saat Ini = IVF',
                    'Umur > 40,5',
                    'Tindakan Hari Ini = PAKET_AKUPUNTUR_12X_KE_6',
                    'Promil Selanjutnya = PRO Stimulasi FET',
                ],
                confidence: 100.00,
                supportCount: 1,
                supportPercentage: 0.99,
            ),
            default => $this->unmatched([
                'Promil Saat Ini = IVF',
                'Umur > 40,5',
                'Tindakan Hari Ini tidak memiliki cabang pada model aktif',
                'Tidak ditemukan decision rule yang sesuai',
            ]),
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
    private function classifyPemeriksaanDasar(int $age): array
    {
        if ($age >= 47) {
            return $this->matched(
                prediction: 'PRO IUI',
                ruleCode: 'R11',
                decisionPath: [
                    'Promil Saat Ini = PEMERIKSAAN_DASAR',
                    'Umur > 46,5',
                    'Promil Selanjutnya = PRO IUI',
                ],
                confidence: 50.00,
                supportCount: 2,
                supportPercentage: 1.98,
            );
        }

        if ($age >= 29) {
            return $this->matched(
                prediction: 'PRO IVF',
                ruleCode: 'R12',
                decisionPath: [
                    'Promil Saat Ini = PEMERIKSAAN_DASAR',
                    'Umur > 28,5 dan ≤ 46,5',
                    'Promil Selanjutnya = PRO IVF',
                ],
                confidence: 40.00,
                supportCount: 15,
                supportPercentage: 14.85,
            );
        }

        return $this->matched(
            prediction: 'PRO IUI',
            ruleCode: 'R13',
            decisionPath: [
                'Promil Saat Ini = PEMERIKSAAN_DASAR',
                'Umur ≤ 28,5',
                'Promil Selanjutnya = PRO IUI',
            ],
            confidence: 100.00,
            supportCount: 3,
            supportPercentage: 2.97,
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
