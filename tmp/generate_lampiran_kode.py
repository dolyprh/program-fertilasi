#!/usr/bin/env python3
"""Generate DOCX appendix with important code snippets for the thesis/report."""

import sys
from pathlib import Path

from docx import Document
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.oxml.ns import qn
from docx.shared import Cm, Pt, RGBColor

sys.stdout.reconfigure(encoding="utf-8")

OUT = Path(r"d:\ME\JOKI\TA Bambang\program-fertilasi\output\docx\Lampiran_Potongan_Kode_Program.docx")
COPY = Path(r"c:\Users\Doly\Downloads\Lampiran_Potongan_Kode_Program.docx")

SNIPPETS: list[tuple[str, str, str, str]] = [
    (
        "A",
        "Konfigurasi Parameter Model",
        "config/fertility.php",
        """<?php

return [
    'training_age_range' => [
        'min' => 24,
        'max' => 47,
    ],

    'model' => [
        'algorithm' => 'Decision Tree C4.5',
        'criterion' => 'Gain Ratio',
        'training_count' => 478,
        'testing_count' => 119,
        'root_node' => 'Promil Saat Ini',
        'maximal_depth' => 4,
        'minimal_gain' => 0.001,
        'minimal_leaf_size' => 1,
        'pruning' => true,
        'prepruning' => true,
        'accuracy' => 50.42,
        'macro_precision' => 36.40,
        'macro_recall' => 28.91,
        'macro_f1' => 27.92,
    ],
];""",
    ),
    (
        "B",
        "Metode Klasifikasi Decision Tree",
        "app/Services/DecisionTreeService.php",
        """public function classify(array $attributes): array
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
}""",
    ),
    (
        "C",
        "Contoh Cabang Aturan (Promil IVF)",
        "app/Services/DecisionTreeService.php",
        """private function classifyIvf(int $age, string $action): array
{
    if ($age > 43) {
        return $this->leaf('PRO Stimulasi IVF', 'R23', [
            'Promil Saat Ini = IVF', 'Umur > 43'
        ], 1, 2);
    }

    return match ($action) {
        'KONTROL_D2_D5' => $this->leaf(
            'PRO Stimulasi FET', 'R31',
            ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = KONTROL_D2_D5'],
            11, 14
        ),
        'KONTROL_STIMULASI_IVF' => $this->leaf(
            'PRO Stimulasi IVF', 'R36',
            ['Promil Saat Ini = IVF', 'Umur <= 43', 'Tindakan Hari Ini = KONTROL_STIMULASI_IVF'],
            5, 5
        ),
        default => $this->unmatched([
            'Promil Saat Ini = IVF',
            "Tindakan Hari Ini = {$action}",
            'Tidak ditemukan decision rule yang sesuai',
        ]),
    };
}""",
    ),
    (
        "D",
        "Perhitungan Confidence dan Support",
        "app/Services/DecisionTreeService.php",
        """private function leaf(
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
}""",
    ),
    (
        "E",
        "Proses Klasifikasi di Controller",
        "app/Http/Controllers/ClassificationController.php",
        """public function store(
    StoreClassificationRequest $request,
    DecisionTreeService $decisionTreeService,
): RedirectResponse {
    $validated = $request->validated();

    $currentPromil = CurrentPromil::query()->active()
        ->findOrFail($validated['current_promil_id']);
    $currentAction = CurrentAction::query()->active()
        ->findOrFail($validated['current_action_id']);

    $dateOfBirth = CarbonImmutable::parse($validated['date_of_birth']);
    $visitDate = CarbonImmutable::parse($validated['visit_date']);
    $age = $dateOfBirth->diff($visitDate)->y;

    $result = $decisionTreeService->classify([
        'age' => $age,
        'current_promil_code' => $currentPromil->code,
        'current_action_code' => $currentAction->code,
    ]);

    $classification = Classification::query()->create([
        'user_id' => $request->user()->id,
        'age' => $age,
        'prediction_result' => $result['prediction'],
        'rule_code' => $result['rule_code'],
        'decision_path' => $result['decision_path'],
        'confidence' => $result['confidence'],
        'support_count' => $result['support_count'],
        'support_percentage' => $result['support_percentage'],
        'status' => $result['matched']
            ? ClassificationStatus::Classified
            : ClassificationStatus::NoRule,
    ]);

    return to_route('classifications.show', $classification);
}""",
    ),
    (
        "F",
        "Validasi Input Klasifikasi",
        "app/Http/Requests/StoreClassificationRequest.php",
        """public function rules(): array
{
    return [
        'patient_name' => ['required', 'string', 'max:255'],
        'visit_date' => ['required', 'date'],
        'date_of_birth' => [
            'required', 'date', 'before_or_equal:visit_date',
        ],
        'doctor_id' => [
            'required',
            Rule::exists('doctors', 'id')
                ->where(fn ($q) => $q->where('is_active', true)),
        ],
        'current_promil_id' => [
            'required',
            Rule::exists('current_promils', 'id')
                ->where(fn ($q) => $q->where('is_active', true)),
        ],
        'current_action_id' => [
            'required',
            Rule::exists('current_actions', 'id')
                ->where(fn ($q) => $q->where('is_active', true)),
        ],
        'poa1_option_id' => [
            'required',
            Rule::exists('poa1_options', 'id')
                ->where(fn ($q) => $q->where('is_active', true)),
        ],
    ];
}""",
    ),
    (
        "G",
        "Struktur Tabel Klasifikasi",
        "database/migrations/2026_07_16_074805_create_classifications_table.php",
        """Schema::create('classifications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('doctor_id')->constrained('doctors')->restrictOnDelete();
    $table->foreignId('current_promil_id')->constrained('current_promils')->restrictOnDelete();
    $table->foreignId('current_action_id')->constrained('current_actions')->restrictOnDelete();
    $table->foreignId('poa1_option_id')->constrained('poa1_options')->restrictOnDelete();
    $table->string('case_code')->unique();
    $table->string('patient_name');
    $table->date('visit_date');
    $table->date('date_of_birth');
    $table->unsignedSmallInteger('age');
    $table->string('prediction_result')->nullable();
    $table->string('rule_code')->nullable();
    $table->json('decision_path')->nullable();
    $table->decimal('confidence', 5, 2)->nullable();
    $table->unsignedInteger('support_count')->nullable();
    $table->decimal('support_percentage', 5, 2)->nullable();
    $table->string('status')->default('classified');
    $table->timestamps();
});""",
    ),
    (
        "H",
        "Definisi Rute Aplikasi",
        "routes/web.php",
        """Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('classifications', ClassificationController::class)
        ->only(['index', 'create', 'store', 'show']);

    Route::get('/model-information', ModelInformationController::class)
        ->name('model-information');

    Route::prefix('master-data')->name('master-data.')->group(function () {
        Route::get('/{type}', [MasterDataController::class, 'manage']);
        Route::post('/{type}', [MasterDataController::class, 'store']);
        Route::put('/{type}/{id}', [MasterDataController::class, 'update']);
        Route::delete('/{type}/{id}', [MasterDataController::class, 'destroy']);
    });
});""",
    ),
    (
        "I",
        "Controller Informasi Model",
        "app/Http/Controllers/ModelInformationController.php",
        """public function __invoke(DecisionTreeService $decisionTreeService): Response
{
    return Inertia::render('model/Index', [
        'model' => config('fertility.model'),
        'rules' => $decisionTreeService->rules(),
    ]);
}""",
    ),
    (
        "J",
        "Perhitungan Umur di Frontend",
        "resources/js/pages/classifications/Create.svelte",
        """const previewAge = $derived.by(() => {
    if (!visitDate || !dateOfBirth) {
        return null;
    }

    const birth = new Date(`${dateOfBirth}T00:00:00`);
    const visit = new Date(`${visitDate}T00:00:00`);

    if (Number.isNaN(birth.getTime()) || Number.isNaN(visit.getTime()) || birth > visit) {
        return null;
    }

    let age = visit.getFullYear() - birth.getFullYear();
    const monthDiff = visit.getMonth() - birth.getMonth();

    if (monthDiff < 0 || (monthDiff === 0 && visit.getDate() < birth.getDate())) {
        age -= 1;
    }

    return age;
});""",
    ),
]


def set_run_font(run, name: str = "Consolas", size: int = 9, bold: bool = False) -> None:
    run.font.name = name
    run.font.size = Pt(size)
    run.font.bold = bold
    run.font.color.rgb = RGBColor(0x1E, 0x29, 0x3B)
    r = run._element
    r.rPr.rFonts.set(qn("w:eastAsia"), name)


def add_code_block(doc: Document, code: str) -> None:
    for line in code.split("\n"):
        p = doc.add_paragraph()
        p.paragraph_format.space_after = Pt(0)
        p.paragraph_format.space_before = Pt(0)
        p.paragraph_format.line_spacing = 1.0
        p.paragraph_format.left_indent = Cm(0.4)
        run = p.add_run(line if line else " ")
        set_run_font(run)


def main() -> None:
    OUT.parent.mkdir(parents=True, exist_ok=True)

    doc = Document()

    section = doc.sections[0]
    section.top_margin = Cm(2.5)
    section.bottom_margin = Cm(2.5)
    section.left_margin = Cm(2.5)
    section.right_margin = Cm(2.5)

    style = doc.styles["Normal"]
    style.font.name = "Times New Roman"
    style.font.size = Pt(12)

    title = doc.add_paragraph()
    title.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = title.add_run("LAMPIRAN\nPOTONGAN KODE PROGRAM")
    run.bold = True
    run.font.size = Pt(14)
    run.font.name = "Times New Roman"

    subtitle = doc.add_paragraph()
    subtitle.alignment = WD_ALIGN_PARAGRAPH.CENTER
    sub_run = subtitle.add_run(
        "Sistem Klasifikasi Program Fertilitas Selanjutnya\n"
        "Decision Tree C4.5 — Laravel & Inertia Svelte"
    )
    sub_run.font.size = Pt(12)
    sub_run.font.name = "Times New Roman"

    doc.add_paragraph()

    intro = doc.add_paragraph(
        "Berikut ini disajikan potongan kode sumber yang merepresentasikan komponen "
        "intisistem, meliputi konfigurasi model, implementasi decision tree, proses "
        "klasifikasi, validasi input, struktur basis data, rute aplikasi, dan "
        "logika frontend. Potongan kode disederhanakan agar fokus pada bagian yang "
        "paling relevan dengan implementasi sistem."
    )
    intro.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.JUSTIFY

    doc.add_page_break()

    for letter, title_text, filepath, code in SNIPPETS:
        heading = doc.add_paragraph()
        heading_run = heading.add_run(f"Lampiran {letter} — {title_text}")
        heading_run.bold = True
        heading_run.font.size = Pt(12)
        heading_run.font.name = "Times New Roman"

        source = doc.add_paragraph()
        source_run = source.add_run(f"Sumber: {filepath}")
        source_run.italic = True
        source_run.font.size = Pt(11)
        source_run.font.name = "Times New Roman"

        doc.add_paragraph()
        add_code_block(doc, code)
        doc.add_paragraph()

    doc.save(OUT)
    doc.save(COPY)

    print(f"Saved: {OUT}")
    print(f"Copy : {COPY}")


if __name__ == "__main__":
    main()
