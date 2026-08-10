#!/usr/bin/env python3
import os
import sys
from docx import Document

sys.stdout.reconfigure(encoding="utf-8")

src = r"c:\Users\Doly\Downloads\Hasil Pengujian Model.docx"
out = r"d:\ME\JOKI\TA Bambang\program-fertilasi\output\docx\Hasil_Pengujian_Model_Revisi_597.docx"

doc = Document(src)


def set_para_text(paragraph, text: str) -> None:
    if not paragraph.runs:
        paragraph.text = text
        return
    first = paragraph.runs[0]
    first.text = text
    for run in paragraph.runs[1:]:
        run.text = ""


def set_cell(cell, text: str) -> None:
    if cell.paragraphs and cell.paragraphs[0].runs:
        p = cell.paragraphs[0]
        p.runs[0].text = text
        for r in p.runs[1:]:
            r.text = ""
        for extra in cell.paragraphs[1:]:
            for r in extra.runs:
                r.text = ""
    else:
        cell.text = text


replacements = {
    10: (
        "Tabel 4.12 menunjukkan bahwa pengujian dilakukan terhadap 119 data uji. Berdasarkan nilai pada diagonal utama "
        "confusion matrix, terdapat 60 data yang berhasil diklasifikasikan dengan benar, sedangkan 59 data lainnya "
        "mengalami kesalahan klasifikasi. Prediksi benar terdiri atas 30 data kelas PRO Stimulasi FET, 10 data kelas "
        "PRO IUI, 1 data kelas PRO Stimulasi IVF, 17 data kelas PRO IVF, dan 2 data kelas PRO ST. Sementara itu, pada "
        "kelas PRO IUI/PRO IVF, PRO Stimulasi IUI, dan PRO Alami belum terdapat prediksi yang tepat. Dengan demikian, "
        "model memperoleh nilai accuracy sebesar 50,42%. Pada kelas aktual PRO Stimulasi FET, seluruh 30 data berhasil "
        "diklasifikasikan dengan benar sehingga memperoleh recall sebesar 100,00%. Pada kelas aktual PRO IUI/PRO IVF "
        "yang terdiri atas 6 data, tidak terdapat prediksi yang benar; 1 data diprediksi sebagai PRO IUI, 2 data sebagai "
        "PRO Stimulasi IUI, dan 3 data sebagai PRO IVF. Pada kelas aktual PRO IUI, dari 24 data terdapat 10 data yang "
        "diklasifikasikan dengan benar, sedangkan 1 data diprediksi sebagai PRO IUI/PRO IVF, 7 data sebagai PRO IVF, "
        "dan 6 data sebagai PRO ST."
    ),
    16: (
        "Accuracy = (Jumlah Prediksi Benar / Jumlah Seluruh Data Uji) × 100%\n"
        "Accuracy = (60 / 119) × 100%\n"
        "Accuracy = 50,42%"
    ),
    17: (
        "Hasil tersebut menunjukkan bahwa model berhasil mengklasifikasikan 60 dari 119 data uji secara tepat, "
        "sedangkan 59 data lainnya diklasifikasikan ke dalam kelas yang tidak sesuai dengan label aktual. "
        "Dengan demikian, classification error model adalah sebesar 49,58%."
    ),
    21: (
        "Perhitungan precision, recall, dan F1-score untuk kelas PRO Stimulasi FET dilakukan sebagai berikut. "
        "Model menghasilkan 32 prediksi PRO Stimulasi FET, dengan 30 prediksi benar. "
        "Seluruh 30 data aktual PRO Stimulasi FET juga berhasil dikenali."
    ),
    22: (
        "Precision(SFET) = 30 / 32 = 0,9375 = 93,75%\n"
        "Recall(SFET) = 30 / 30 = 1,0000 = 100,00%\n"
        "F1(SFET) = 2 × (0,9375 × 1,0000) / (0,9375 + 1,0000) = 0,9677 = 96,77%"
    ),
    23: (
        "Pada kelas PRO IVF, model menghasilkan 34 prediksi, dengan 17 prediksi yang benar. "
        "Dari 35 data aktual PRO IVF, sebanyak 17 data berhasil dikenali."
    ),
    24: "Precision(IVF) = 17 / 34 = 0,5000 = 50,00%",
    25: "Recall(IVF) = 17 / 35 = 0,4857 = 48,57%",
    26: "F1(IVF) = 2 × (0,5000 × 0,4857) / (0,5000 + 0,4857) = 0,4927 = 49,27%",
    27: (
        "Pada kelas PRO ST, model menghasilkan 17 prediksi dan dua di antaranya benar. "
        "Dari tujuh data aktual PRO ST, dua data berhasil dikenali."
    ),
    28: "Precision(ST) = 2 / 17 = 0,1176 = 11,76%",
    29: "Recall(ST) = 2 / 7 = 0,2857 = 28,57%",
    30: "F1(ST) = 2 × (0,1176 × 0,2857) / (0,1176 + 0,2857) = 0,1667 = 16,67%",
    31: (
        "RapidMiner menghasilkan nilai weighted mean precision sebesar 36,40% dan weighted mean recall sebesar 28,91%, "
        "dengan bobot setiap kelas bernilai 1. Karena seluruh kelas memperoleh bobot yang sama, nilai tersebut ekuivalen "
        "dengan macro precision dan macro recall, yaitu rata-rata nilai precision dan recall dari delapan kelas tanpa "
        "mempertimbangkan banyaknya data pada setiap kelas."
    ),
    32: (
        "Macro Precision = (35,71 + 93,75 + 100,00 + 0 + 0 + 50,00 + 11,76 + 0) / 8 = 36,40%\n"
        "Macro Recall = (41,67 + 100,00 + 12,50 + 0 + 0 + 48,57 + 28,57 + 0) / 8 = 28,91%\n"
        "Macro F1-score = (38,46 + 96,77 + 22,22 + 0 + 0 + 49,27 + 16,67 + 0) / 8 = 27,92%"
    ),
    35: (
        "Berdasarkan hasil pengujian, model memperoleh accuracy sebesar 50,42%. Nilai tersebut menunjukkan bahwa "
        "kemampuan generalisasi model terhadap data yang tidak digunakan pada proses pelatihan masih terbatas, "
        "meskipun lebih tinggi dibandingkan hasil pembagian data sebelumnya. Sebagai konteks, kelas aktual terbanyak "
        "pada data uji adalah PRO IVF dengan 35 data atau 29,41% dari keseluruhan data uji. Model memberikan "
        "peningkatan ketepatan sebesar sekitar 21 poin persentase dibandingkan prediksi sederhana yang selalu "
        "memilih kelas terbanyak, tetapi peningkatan tersebut belum menunjukkan kemampuan klasifikasi yang merata "
        "pada seluruh kelas."
    ),
    36: (
        "Kinerja terbaik diperoleh pada kelas PRO Stimulasi FET. Model berhasil mengenali seluruh 30 data aktual "
        "pada kelas tersebut sehingga memperoleh recall sebesar 100,00%. Precision sebesar 93,75% menunjukkan bahwa "
        "dari 32 data yang diprediksi sebagai PRO Stimulasi FET, 30 data sesuai dengan kelas aktual. Nilai F1-score "
        "sebesar 96,77% memperlihatkan bahwa model memiliki keseimbangan precision dan recall yang relatif baik pada kelas ini."
    ),
    37: (
        "Kelas PRO IVF memperoleh precision sebesar 50,00% dan recall sebesar 48,57%, dengan F1-score sebesar 49,27%. "
        "Hasil tersebut menunjukkan bahwa model dapat mengenali sebagian pola kelas PRO IVF, tetapi masih terdapat "
        "kesalahan klasifikasi yang cukup besar, terutama ke kelas PRO IUI dan PRO ST. Sementara itu, kelas PRO IUI "
        "memperoleh precision sebesar 35,71% dan recall sebesar 41,67%. Precision yang relatif rendah pada beberapa "
        "kelas lain, misalnya PRO ST sebesar 11,76%, menunjukkan bahwa model masih sering menempatkan data dari kelas "
        "lain ke dalam prediksi kelas yang kurang tepat."
    ),
    38: (
        "Model belum berhasil mengenali kelas PRO IUI/PRO IVF, PRO Stimulasi IUI, dan PRO Alami secara benar pada data uji "
        "(recall 0,00%). Selain itu, kelas PRO Stimulasi IVF hanya memperoleh recall sebesar 12,50% meskipun precision-nya "
        "mencapai 100,00% karena hanya terdapat satu prediksi pada kelas tersebut. Kondisi tersebut menunjukkan bahwa pola "
        "antarkelas masih saling tumpang tindih berdasarkan atribut prediktor yang tersedia, yaitu Umur, Promil Saat Ini, "
        "Tindakan Hari Ini, dan PoA1."
    ),
    39: (
        "Nilai macro precision sebesar 36,40%, macro recall sebesar 28,91%, dan macro F1-score sebesar 27,92% lebih rendah "
        "dibandingkan accuracy keseluruhan. Perbedaan tersebut menunjukkan bahwa prediksi benar terkonsentrasi pada "
        "beberapa kelas tertentu, khususnya PRO Stimulasi FET dan sebagian PRO IVF serta PRO IUI, sedangkan sebagian kelas "
        "lainnya memiliki nilai precision dan/atau recall yang rendah hingga nol. Oleh karena itu, accuracy tidak dapat "
        "digunakan sebagai satu-satunya dasar penilaian kinerja model pada dataset multikelas yang distribusinya tidak seimbang."
    ),
    40: (
        "Distribusi kelas yang tidak seimbang dan jumlah data uji yang terdiri atas 119 data menjadi faktor penting dalam "
        "interpretasi hasil. Beberapa kelas hanya memiliki empat hingga delapan data aktual pada data uji, sehingga "
        "kesalahan pada satu atau dua data dapat mengubah nilai recall secara signifikan. Penggunaan shuffled sampling "
        "pada Split Validation menyebabkan pembagian data dilakukan secara acak tanpa menjamin bahwa setiap kelas "
        "memperoleh representasi yang proporsional pada data uji. Akibatnya, nilai performa per kelas dapat berubah "
        "ketika anggota data uji berubah."
    ),
}

for idx, text in replacements.items():
    set_para_text(doc.paragraphs[idx], text)

cm = [
    ["Prediksi / Aktual", "IUI", "SFET", "SIVF", "ALM", "SIUI", "IVF", "ST", "IUI/IVF", "Precision"],
    ["PRO IUI", "10", "0", "1", "2", "1", "10", "3", "1", "35,71%"],
    ["PRO Stimulasi FET", "0", "30", "0", "0", "1", "1", "0", "0", "93,75%"],
    ["PRO Stimulasi IVF", "0", "0", "1", "0", "0", "0", "0", "0", "100,00%"],
    ["PRO Alami", "0", "0", "1", "0", "0", "1", "0", "0", "0,00%"],
    ["PRO Stimulasi IUI", "0", "0", "1", "0", "0", "0", "1", "2", "0,00%"],
    ["PRO IVF", "7", "0", "3", "0", "3", "17", "1", "3", "50,00%"],
    ["PRO ST", "6", "0", "1", "2", "0", "6", "2", "0", "11,76%"],
    ["PRO IUI/PRO IVF", "1", "0", "0", "0", "0", "0", "0", "0", "0,00%"],
    ["Recall", "41,67%", "100,00%", "12,50%", "0,00%", "0,00%", "48,57%", "28,57%", "0,00%", ""],
]

t0 = doc.tables[0]
assert len(t0.rows) == 10 and len(t0.columns) == 10
for ri, row in enumerate(cm):
    for ci, val in enumerate(row):
        set_cell(t0.rows[ri].cells[ci], val)

t1_data = [
    ["Kelas", "TP", "Total Prediksi", "Total Aktual", "Precision", "Recall", "F1-Score"],
    ["PRO IUI", "10", "28", "24", "35,71%", "41,67%", "38,46%"],
    ["PRO Stimulasi FET", "30", "32", "30", "93,75%", "100,00%", "96,77%"],
    ["PRO Stimulasi IVF", "1", "1", "8", "100,00%", "12,50%", "22,22%"],
    ["PRO Alami", "0", "2", "4", "0,00%", "0,00%", "0,00%"],
    ["PRO Stimulasi IUI", "0", "4", "5", "0,00%", "0,00%", "0,00%"],
    ["PRO IVF", "17", "34", "35", "50,00%", "48,57%", "49,27%"],
    ["PRO ST", "2", "17", "7", "11,76%", "28,57%", "16,67%"],
    ["PRO IUI/PRO IVF", "0", "1", "6", "0,00%", "0,00%", "0,00%"],
]
t1 = doc.tables[1]
assert len(t1.rows) == 9 and len(t1.columns) == 7
for ri, row in enumerate(t1_data):
    for ci, val in enumerate(row):
        set_cell(t1.rows[ri].cells[ci], val)

t2_data = [
    ["Metrik", "Nilai", "Keterangan"],
    ["Accuracy", "50,42%", "60 dari 119 data uji diklasifikasikan dengan benar"],
    ["Macro Precision", "36,40%", "Output RapidMiner: weighted mean precision dengan bobot seluruh kelas = 1"],
    ["Macro Recall", "28,91%", "Output RapidMiner: weighted mean recall dengan bobot seluruh kelas = 1"],
    ["Macro F1-Score", "27,92%", "Dihitung dari rata-rata F1-score delapan kelas"],
]
t2 = doc.tables[2]
assert len(t2.rows) == 5 and len(t2.columns) == 3
for ri, row in enumerate(t2_data):
    for ci, val in enumerate(row):
        set_cell(t2.rows[ri].cells[ci], val)

os.makedirs(os.path.dirname(out), exist_ok=True)
doc.save(out)
print("saved", out)

doc2 = Document(out)
joined = "\n".join(p.text for p in doc2.paragraphs)
for needle in ["50,42%", "60 / 119", "36,40%", "28,91%", "27,92%", "36,00%", "9 / 25", "9 / 199"]:
    print(repr(needle), "->", needle in joined)
print("table2 accuracy", doc2.tables[2].rows[1].cells[1].text)
print("table0 SFET diagonal", doc2.tables[0].rows[2].cells[2].text)
