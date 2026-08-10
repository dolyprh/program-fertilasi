from __future__ import annotations

from datetime import date
from pathlib import Path

from reportlab.lib import colors
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import cm
from reportlab.platypus import (
    PageBreak,
    Paragraph,
    SimpleDocTemplate,
    Spacer,
    Table,
    TableStyle,
)


OUTPUT_PATH = Path("output/pdf/presentasi-demo-sistem-klasifikasi-fertilitas.pdf")


def id_percent(value: float) -> str:
    return f"{value:.2f}".replace(".", ",") + "%"


def build_styles():
    styles = getSampleStyleSheet()
    styles.add(
        ParagraphStyle(
            name="TitleMain",
            parent=styles["Title"],
            fontName="Helvetica-Bold",
            fontSize=22,
            leading=27,
            textColor=colors.HexColor("#1f2937"),
            alignment=1,
        )
    )
    styles.add(
        ParagraphStyle(
            name="HeadingBlue",
            parent=styles["Heading2"],
            fontName="Helvetica-Bold",
            fontSize=14,
            leading=18,
            textColor=colors.HexColor("#0f4c81"),
            spaceAfter=8,
        )
    )
    styles.add(
        ParagraphStyle(
            name="Body",
            parent=styles["BodyText"],
            fontName="Helvetica",
            fontSize=10.5,
            leading=15,
            textColor=colors.HexColor("#111827"),
        )
    )
    styles.add(
        ParagraphStyle(
            name="BodySmall",
            parent=styles["BodyText"],
            fontName="Helvetica",
            fontSize=9.3,
            leading=13.2,
            textColor=colors.HexColor("#111827"),
        )
    )
    styles.add(
        ParagraphStyle(
            name="BulletBody",
            parent=styles["BodyText"],
            fontName="Helvetica",
            fontSize=10.5,
            leading=15,
            leftIndent=14,
            bulletIndent=2,
            spaceAfter=2,
        )
    )
    return styles


def page_footer(canvas, doc):
    canvas.saveState()
    canvas.setStrokeColor(colors.HexColor("#d1d5db"))
    canvas.setLineWidth(0.6)
    canvas.line(2 * cm, 1.5 * cm, A4[0] - 2 * cm, 1.5 * cm)
    canvas.setFont("Helvetica", 8.5)
    canvas.setFillColor(colors.HexColor("#4b5563"))
    canvas.drawString(2 * cm, 1 * cm, "Sistem Klasifikasi Program Fertilitas Selanjutnya")
    canvas.drawRightString(A4[0] - 2 * cm, 1 * cm, f"Halaman {doc.page}")
    canvas.restoreState()


def bullet(text: str, styles) -> Paragraph:
    return Paragraph(f"• {text}", styles["BulletBody"])


def build_story():
    styles = build_styles()

    model_accuracy = 36.00
    macro_precision = 17.11
    macro_recall = 22.92
    macro_f1 = 18.97
    training_count = 101
    testing_count = 25
    training_age_min = 26
    training_age_max = 59

    rules = [
        ("R1", "ALAMI, umur >= 29", "PRO IVF", 40.48, 42, 41.58),
        ("R2", "ALAMI, umur = 28", "PRO Stimulasi IUI", 100.00, 1, 0.99),
        ("R3", "ALAMI, umur <= 27", "PRO ST", 100.00, 3, 2.97),
        ("R4", "BELUM_PROMIL", "PRO IUI/PRO IVF", 100.00, 1, 0.99),
        ("R5", "IUI", "PRO IUI", 100.00, 1, 0.99),
        ("R6", "IVF, umur <= 40", "PRO Stimulasi FET", 95.83, 24, 23.76),
        ("R7", "IVF, umur >= 41, tindakan HO", "PRO Stimulasi FET", 100.00, 1, 0.99),
        ("R8", "IVF, umur >= 41, tindakan KONTROL_D2_D5", "PRO Stimulasi FET", 66.67, 3, 2.97),
        ("R9", "IVF, umur >= 41, tindakan PRP_OVARIUM_TRANSVAGINAL", "PRO Stimulasi IVF", 100.00, 1, 0.99),
        ("R10", "IVF, umur >= 41, tindakan PAKET_AKUPUNTUR_12X_KE_6", "PRO Stimulasi FET", 100.00, 1, 0.99),
        ("R11", "PEMERIKSAAN_DASAR, umur >= 47", "PRO IUI", 50.00, 2, 1.98),
        ("R12", "PEMERIKSAAN_DASAR, umur 29-46", "PRO IVF", 40.00, 15, 14.85),
        ("R13", "PEMERIKSAAN_DASAR, umur <= 28", "PRO IUI", 100.00, 3, 2.97),
        ("R14", "SENGGAMA_TERJADWAL", "PRO IUI", 66.67, 3, 2.97),
    ]

    support_total = sum(item[4] for item in rules)
    most_support_rule = max(rules, key=lambda row: row[4])

    story = []
    story.append(Spacer(1, 1.5 * cm))
    story.append(
        Paragraph(
            "Presentasi Demo Aplikasi",
            styles["TitleMain"],
        )
    )
    story.append(
        Paragraph(
            "Sistem Klasifikasi Program Fertilitas Selanjutnya",
            styles["HeadingBlue"],
        )
    )
    story.append(Spacer(1, 0.3 * cm))
    story.append(
        Paragraph(
            "Dokumen ini disusun dari implementasi aktual aplikasi Laravel + Inertia Svelte pada repository "
            "dan siap digunakan sebagai alur presentasi ke dosen.",
            styles["Body"],
        )
    )
    story.append(Spacer(1, 0.5 * cm))
    story.append(Paragraph(f"Tanggal penyusunan: {date.today().strftime('%d-%m-%Y')}", styles["BodySmall"]))
    story.append(PageBreak())

    story.append(Paragraph("1. Gambaran Umum Sistem", styles["HeadingBlue"]))
    story.extend(
        [
            bullet("Aplikasi membantu prediksi Program Fertilitas Selanjutnya berdasarkan pola historis penelitian.", styles),
            bullet("Pengguna utama: staf internal yang login dan email terverifikasi.", styles),
            bullet("Teknologi: Laravel 13, Fortify auth, Inertia v3, Svelte 5, Tailwind 4.", styles),
            bullet("Model penelitian dibangun di RapidMiner, lalu diimplementasikan sebagai rule engine statis di Laravel.", styles),
            bullet("Aplikasi tidak melakukan training ulang saat runtime.", styles),
        ]
    )
    story.append(Spacer(1, 0.4 * cm))
    story.append(Paragraph("Alur proses data", styles["Body"]))
    flow_data = [["Input Form", "Validasi", "Hitung Umur", "DecisionTreeService", "Simpan Hasil", "Tampilkan Detail"]]
    flow_table = Table(flow_data, colWidths=[2.7 * cm] * 6)
    flow_table.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, -1), colors.HexColor("#e8f1fb")),
                ("TEXTCOLOR", (0, 0), (-1, -1), colors.HexColor("#0f4c81")),
                ("FONTNAME", (0, 0), (-1, -1), "Helvetica-Bold"),
                ("FONTSIZE", (0, 0), (-1, -1), 9),
                ("ALIGN", (0, 0), (-1, -1), "CENTER"),
                ("VALIGN", (0, 0), (-1, -1), "MIDDLE"),
                ("GRID", (0, 0), (-1, -1), 0.5, colors.HexColor("#93c5fd")),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 8),
                ("TOPPADDING", (0, 0), (-1, -1), 8),
            ]
        )
    )
    story.append(flow_table)
    story.append(PageBreak())

    story.append(Paragraph("2. Fitur yang Dapat Didemokan", styles["HeadingBlue"]))
    story.extend(
        [
            bullet("Dashboard: total klasifikasi, klasifikasi hari ini, status classified/no-rule, distribusi hasil, dan 5 data terbaru.", styles),
            bullet("Klasifikasi Baru: form informasi kunjungan + data klasifikasi (promil, tindakan, PoA1).", styles),
            bullet("Hasil Klasifikasi: prediksi, kode rule, confidence, support count, support percentage, dan decision path.", styles),
            bullet("Riwayat Klasifikasi: filter by case code/promil/hasil/status, pagination, detail per kasus.", styles),
            bullet("Informasi Model: metadata model C4.5 dan 14 decision rule.", styles),
        ]
    )
    story.append(Spacer(1, 0.35 * cm))
    story.append(Paragraph("Catatan keamanan dan privasi", styles["Body"]))
    story.extend(
        [
            bullet("Semua halaman klasifikasi dilindungi auth + verified.", styles),
            bullet("User hanya bisa melihat data miliknya sendiri.", styles),
            bullet("Nama pasien tidak dijadikan parameter URL.", styles),
        ]
    )
    story.append(PageBreak())

    story.append(Paragraph("3. Perhitungan Inti pada Aplikasi", styles["HeadingBlue"]))
    story.append(Paragraph("<b>3.1 Perhitungan umur (backend sebagai sumber kebenaran)</b>", styles["Body"]))
    story.append(Paragraph("Rumus implementasi:", styles["Body"]))
    story.append(Paragraph("umur = tanggal_lahir.diff(tanggal_kunjungan).years", styles["BodySmall"]))
    story.append(Spacer(1, 0.2 * cm))
    story.append(Paragraph("Contoh kasus:", styles["Body"]))
    story.extend(
        [
            bullet("Tanggal Lahir: 10-01-1990", styles),
            bullet("Tanggal Kunjungan: 05-01-2026", styles),
            bullet("Hasil umur: 35 tahun (karena ulang tahun ke-36 belum terjadi).", styles),
            bullet("Validasi umur: wajib 1-120 tahun.", styles),
            bullet(f"Peringatan non-blocking bila umur di luar rentang data latih {training_age_min}-{training_age_max} tahun.", styles),
        ]
    )
    story.append(Spacer(1, 0.35 * cm))
    story.append(Paragraph("<b>3.2 Perhitungan support percentage</b>", styles["Body"]))
    story.append(Paragraph("Rumus:", styles["Body"]))
    story.append(Paragraph("support_percentage = (support_count / total_data_latih) x 100%", styles["BodySmall"]))
    story.append(Paragraph("Contoh rule R6:", styles["Body"]))
    story.extend(
        [
            bullet("support_count = 24", styles),
            bullet(f"total_data_latih = {training_count}", styles),
            bullet("support_percentage = (24/101) x 100% = 23,76%", styles),
        ]
    )
    story.append(Spacer(1, 0.35 * cm))
    story.append(Paragraph("<b>3.3 Confidence</b>", styles["Body"]))
    story.append(Paragraph("Confidence berasal dari hasil model penelitian (RapidMiner) dan disimpan sebagai metadata rule.", styles["Body"]))
    story.append(PageBreak())

    story.append(Paragraph("4. Decision Rule C4.5 yang Dipakai Aplikasi", styles["HeadingBlue"]))
    header = ["Rule", "Kondisi Utama", "Prediksi", "Confidence", "Support", "Support %"]
    rows = [header]
    for code, condition, prediction, confidence, support_count, support_percentage in rules:
        rows.append([code, condition, prediction, id_percent(confidence), str(support_count), id_percent(support_percentage)])
    rows.append(["TOTAL", "-", "-", "-", str(support_total), "100,00%"])
    rule_table = Table(rows, colWidths=[1.3 * cm, 6.0 * cm, 3.5 * cm, 2.1 * cm, 1.5 * cm, 2.1 * cm])
    rule_table.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#0f4c81")),
                ("TEXTCOLOR", (0, 0), (-1, 0), colors.white),
                ("FONTNAME", (0, 0), (-1, 0), "Helvetica-Bold"),
                ("FONTSIZE", (0, 0), (-1, 0), 8.7),
                ("ALIGN", (0, 0), (-1, 0), "CENTER"),
                ("FONTNAME", (0, 1), (-1, -2), "Helvetica"),
                ("FONTSIZE", (0, 1), (-1, -2), 8.1),
                ("VALIGN", (0, 0), (-1, -1), "TOP"),
                ("GRID", (0, 0), (-1, -1), 0.35, colors.HexColor("#9ca3af")),
                ("ROWBACKGROUNDS", (0, 1), (-1, -2), [colors.white, colors.HexColor("#f9fafb")]),
                ("BACKGROUND", (0, -1), (-1, -1), colors.HexColor("#e5e7eb")),
                ("FONTNAME", (0, -1), (-1, -1), "Helvetica-Bold"),
                ("ALIGN", (0, 1), (0, -1), "CENTER"),
                ("ALIGN", (3, 1), (5, -1), "CENTER"),
            ]
        )
    )
    story.append(rule_table)
    story.append(Spacer(1, 0.25 * cm))
    story.append(
        Paragraph(
            f"Insight: rule dengan dukungan terbesar adalah {most_support_rule[0]} ({most_support_rule[4]} dari {training_count} data latih).",
            styles["BodySmall"],
        )
    )
    story.append(PageBreak())

    story.append(Paragraph("5. Simulasi Demo Perhitungan", styles["HeadingBlue"]))
    story.append(Paragraph("<b>Skenario A - Kasus classified (R6)</b>", styles["Body"]))
    story.extend(
        [
            bullet("Input: Promil Saat Ini = IVF, Tindakan = KONTROL_D2_D5, Umur = 35.", styles),
            bullet("Cek cabang: IVF -> Umur <= 40,5 -> masuk R6.", styles),
            bullet("Output: Prediksi = PRO Stimulasi FET.", styles),
            bullet("Confidence = 95,83%; Support = 24/101 (23,76%).", styles),
            bullet("Decision path tersimpan dan tampil di halaman hasil.", styles),
        ]
    )
    story.append(Spacer(1, 0.35 * cm))
    story.append(Paragraph("<b>Skenario B - Kasus no-rule</b>", styles["Body"]))
    story.extend(
        [
            bullet("Input: Promil Saat Ini = IVF, Umur = 41, Tindakan = LAINNYA.", styles),
            bullet("Cek cabang: IVF -> Umur > 40,5 -> tindakan tidak punya cabang aktif.", styles),
            bullet("Output: status = no_rule, prediction = null, confidence/support = null.", styles),
            bullet("Aplikasi tetap menyimpan kasus untuk evaluasi klinis lanjutan.", styles),
        ]
    )
    story.append(Spacer(1, 0.35 * cm))
    story.append(Paragraph("<b>Skenario C - Batas umur ALAMI</b>", styles["Body"]))
    story.extend(
        [
            bullet("Umur 27 -> R3 -> PRO ST", styles),
            bullet("Umur 28 -> R2 -> PRO Stimulasi IUI", styles),
            bullet("Umur 29 -> R1 -> PRO IVF", styles),
        ]
    )
    story.append(PageBreak())

    story.append(Paragraph("6. Interpretasi Hasil dan Batasan Penelitian", styles["HeadingBlue"]))
    story.extend(
        [
            bullet(f"Model dibentuk dari {training_count} data latih dan {testing_count} data uji.", styles),
            bullet(f"Akurasi model: {id_percent(model_accuracy)}.", styles),
            bullet(f"Macro Precision: {id_percent(macro_precision)} | Macro Recall: {id_percent(macro_recall)} | Macro F1: {id_percent(macro_f1)}.", styles),
            bullet("Nilai metrik perlu dijelaskan sebagai baseline penelitian, bukan keputusan klinis final.", styles),
            bullet("Aplikasi berfungsi sebagai decision support; keputusan akhir tetap pada dokter.", styles),
        ]
    )
    story.append(Spacer(1, 0.4 * cm))
    story.append(Paragraph("Disclaimer yang ditampilkan di aplikasi:", styles["Body"]))
    story.append(
        Paragraph(
            '"Hasil klasifikasi merupakan pola berdasarkan data historis penelitian dan tidak menggantikan keputusan dokter."',
            styles["BodySmall"],
        )
    )
    story.append(Spacer(1, 0.55 * cm))
    story.append(Paragraph("Checklist alur demo 7-10 menit", styles["HeadingBlue"]))
    story.extend(
        [
            bullet("Buka Dashboard dan jelaskan metrik ringkas.", styles),
            bullet("Masuk ke Klasifikasi Baru, isi data contoh, tunjukkan preview umur.", styles),
            bullet("Submit lalu jelaskan hasil classified (rule, confidence, support, decision path).", styles),
            bullet("Ulangi dengan skenario no-rule.", styles),
            bullet("Tutup dengan halaman Informasi Model dan tabel 14 rule.", styles),
        ]
    )

    return story


def main() -> None:
    OUTPUT_PATH.parent.mkdir(parents=True, exist_ok=True)

    doc = SimpleDocTemplate(
        str(OUTPUT_PATH),
        pagesize=A4,
        rightMargin=2 * cm,
        leftMargin=2 * cm,
        topMargin=1.7 * cm,
        bottomMargin=2.0 * cm,
        title="Presentasi Demo Sistem Klasifikasi Fertilitas",
        author="Cursor AI Assistant",
        subject="Dokumen presentasi demo aplikasi",
    )

    story = build_story()
    doc.build(story, onFirstPage=page_footer, onLaterPages=page_footer)
    print(f"PDF generated: {OUTPUT_PATH}")


if __name__ == "__main__":
    main()
