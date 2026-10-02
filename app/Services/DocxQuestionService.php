<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\Question;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use ZipArchive;

class DocxQuestionService
{
    /**
     * Hasilkan file template Word (.docx) resmi untuk pengisian soal ujian
     *
     * @return string Path file sementara template yang digenerate
     */
    public function createTemplateFile(): string
    {
        $phpWord = new PhpWord();

        // Atur orientasi halaman Landscape agar tabel leluasa dan mudah dibaca
        $section = $phpWord->addSection([
            'orientation' => 'landscape',
            'marginTop' => 720,
            'marginRight' => 720,
            'marginBottom' => 720,
            'marginLeft' => 720,
        ]);

        // Judul Dokumen
        $section->addTitle('TEMPLATE SOAL CBT MTsN 12 JAKARTA', 1);
        $section->addText(
            'Panduan Pengisian Soal Pilihan Ganda:',
            ['bold' => true, 'size' => 11, 'color' => '333333']
        );

        $instructions = [
            '1. Isikan butir soal Anda pada tabel di bawah ini. Satu baris mewakili 1 nomor soal.',
            '2. Kolom "Pertanyaan Soal" dapat memuat teks soal serta gambar (gambar dapat disalin/ditempel langsung ke dalam kotak soal).',
            '3. Kolom "Pilihan A", "Pilihan B", "Pilihan C", dan "Pilihan D" wajib diisi dengan teks pilihan jawaban.',
            '4. Kolom "Kunci Jawaban" WAJIB diisi dengan SATU huruf kapital: A, B, C, atau D.',
            '5. Jangan menghapus baris judul tabel (Header nomor 1).',
            '6. Hapus atau timpa 4 baris contoh soal di bawah ini dengan soal ujian Anda sebelum diunggah.',
        ];

        foreach ($instructions as $inst) {
            $section->addText($inst, ['size' => 9.5, 'color' => '555555', 'italic' => true]);
        }

        $section->addTextBreak(1);

        // Styling Tabel
        $tableStyle = [
            'borderSize' => 6,
            'borderColor' => 'CCCCCC',
            'cellMargin' => 80,
            'unit' => TblWidth::PERCENT,
            'width' => 100 * 50,
            'alignment' => Jc::CENTER,
        ];
        $headerStyle = ['bgColor' => 'EEF2FF'];

        $table = $section->addTable($tableStyle);

        // Header Tabel
        $table->addRow(400);
        $table->addCell(600, $headerStyle)->addText('No', ['bold' => true, 'color' => '1E293B', 'size' => 10]);
        $table->addCell(5000, $headerStyle)->addText('Pertanyaan Soal', ['bold' => true, 'color' => '1E293B', 'size' => 10]);
        $table->addCell(2000, $headerStyle)->addText('Pilihan A', ['bold' => true, 'color' => '1E293B', 'size' => 10]);
        $table->addCell(2000, $headerStyle)->addText('Pilihan B', ['bold' => true, 'color' => '1E293B', 'size' => 10]);
        $table->addCell(2000, $headerStyle)->addText('Pilihan C', ['bold' => true, 'color' => '1E293B', 'size' => 10]);
        $table->addCell(2000, $headerStyle)->addText('Pilihan D', ['bold' => true, 'color' => '1E293B', 'size' => 10]);
        $table->addCell(1000, $headerStyle)->addText('Kunci (A/B/C/D)', ['bold' => true, 'color' => '1E293B', 'size' => 10]);

        // Contoh Data Soal 1
        $table->addRow();
        $table->addCell(600)->addText('1');
        $table->addCell(5000)->addText('Ibu kota negara Indonesia saat ini berada di provinsi...');
        $table->addCell(2000)->addText('DKI Jakarta');
        $table->addCell(2000)->addText('Jawa Barat');
        $table->addCell(2000)->addText('Jawa Timur');
        $table->addCell(2000)->addText('Sumatera Utara');
        $table->addCell(1000)->addText('A', ['bold' => true, 'color' => '16A34A']);

        // Contoh Data Soal 2
        $table->addRow();
        $table->addCell(600)->addText('2');
        $table->addCell(5000)->addText('Hasil operasi perkalian dari 25 x 4 adalah...');
        $table->addCell(2000)->addText('50');
        $table->addCell(2000)->addText('75');
        $table->addCell(2000)->addText('100');
        $table->addCell(2000)->addText('125');
        $table->addCell(1000)->addText('C', ['bold' => true, 'color' => '16A34A']);

        // Contoh Data Soal 3
        $table->addRow();
        $table->addCell(600)->addText('3');
        $table->addCell(5000)->addText('Planet di tata surya yang memiliki jarak paling dekat dengan Matahari adalah...');
        $table->addCell(2000)->addText('Venus');
        $table->addCell(2000)->addText('Merkurius');
        $table->addCell(2000)->addText('Mars');
        $table->addCell(2000)->addText('Bumi');
        $table->addCell(1000)->addText('B', ['bold' => true, 'color' => '16A34A']);

        // Contoh Data Soal 4
        $table->addRow();
        $table->addCell(600)->addText('4');
        $table->addCell(5000)->addText('Lembaga negara yang memiliki wewenang membuat undang-undang bersama Presiden adalah...');
        $table->addCell(2000)->addText('Mahkamah Agung (MA)');
        $table->addCell(2000)->addText('Komisi Yudisial (KY)');
        $table->addCell(2000)->addText('Badan Pemeriksa Keuangan (BPK)');
        $table->addCell(2000)->addText('Dewan Perwakilan Rakyat (DPR)');
        $table->addCell(1000)->addText('D', ['bold' => true, 'color' => '16A34A']);

        $tempPath = tempnam(sys_get_temp_dir(), 'template_soal_') . '.docx';
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempPath);

        return $tempPath;
    }

    /**
     * Ekstrak dan impor soal dari file .docx ke database untuk ujian tertentu
     *
     * @param string|UploadedFile $file
     * @param Exam $exam
     * @return array
     */
    public function importFromDocx($file, Exam $exam): array
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        if (!file_exists($filePath)) {
            return [
                'success' => false,
                'count' => 0,
                'errors' => ['File Word tidak ditemukan atau gagal diunggah.'],
                'message' => 'File tidak ditemukan.',
            ];
        }

        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return [
                'success' => false,
                'count' => 0,
                'errors' => ['Format file DOCX tidak valid atau file terkorupsi.'],
                'message' => 'Gagal membuka file DOCX.',
            ];
        }

        // 1. Petakan relasi gambar (Relationships)
        $imageRelMap = [];
        $relsXml = $zip->getFromName('word/_rels/document.xml.rels');
        if ($relsXml) {
            $relsDom = new DOMDocument();
            @$relsDom->loadXML($relsXml);
            $relsXpath = new DOMXPath($relsDom);
            $relsXpath->registerNamespace('rel', 'http://schemas.openxmlformats.org/package/2006/relationships');

            foreach ($relsXpath->query('//rel:Relationship') as $relNode) {
                $id = $relNode->getAttribute('Id');
                $target = $relNode->getAttribute('Target');
                $type = $relNode->getAttribute('Type');
                if (str_contains($type, 'image')) {
                    $imageRelMap[$id] = $target;
                }
            }
        }

        // 2. Baca file utama document.xml
        $docXml = $zip->getFromName('word/document.xml');
        if (!$docXml) {
            $zip->close();
            return [
                'success' => false,
                'count' => 0,
                'errors' => ['Konten dokumen Word kosong atau tidak dapat dibaca.'],
                'message' => 'Dokumen kosong.',
            ];
        }

        $docDom = new DOMDocument();
        @$docDom->loadXML($docXml);
        $xpath = new DOMXPath($docDom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $xpath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
        $xpath->registerNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
        $xpath->registerNamespace('v', 'urn:schemas-microsoft-com:vml');

        $extractedQuestions = [];
        $errors = [];

        // 3. PRIORITAS A: Cek apakah ada Tabel di dokumen
        $tableRows = $xpath->query('//w:tbl/w:tr');
        if ($tableRows->length > 0) {
            foreach ($tableRows as $rowIndex => $rowNode) {
                $cells = $xpath->query('.//w:tc', $rowNode);
                if ($cells->length < 5) {
                    continue;
                }

                $cellTexts = [];
                $questionImageRelId = null;

                foreach ($cells as $cIdx => $cell) {
                    // Ambil seluruh teks pada cell
                    $tNodes = $xpath->query('.//w:t', $cell);
                    $cText = '';
                    foreach ($tNodes as $t) {
                        $cText .= $t->nodeValue;
                    }
                    $cellTexts[] = trim($cText);

                    // Khusus kolom pertanyaan (biasanya kolom ke-0 atau ke-1), cek jika ada gambar tersisip
                    if (($cIdx === 0 || $cIdx === 1) && !$questionImageRelId) {
                        $cellXml = $docDom->saveXML($cell);
                        if (preg_match('/(?:r:id|r:embed)="([^"]+)"/i', $cellXml, $matchBlip)) {
                            $questionImageRelId = $matchBlip[1];
                        }
                    }
                }

                // Lewati jika ini adalah baris header tabel
                $joined = strtolower(implode(' ', $cellTexts));
                if (
                    str_contains($joined, 'pertanyaan') || 
                    str_contains($joined, 'pilihan a') || 
                    str_contains($joined, 'kunci') || 
                    str_contains($joined, 'soal')
                ) {
                    continue;
                }

                // Normalisasi kolom
                if (count($cellTexts) >= 7) {
                    // Format 7 kolom: [No, Soal, A, B, C, D, Kunci]
                    $qText = $cellTexts[1];
                    $optA = $cellTexts[2];
                    $optB = $cellTexts[3];
                    $optC = $cellTexts[4];
                    $optD = $cellTexts[5];
                    $rawKey = $cellTexts[6];
                } elseif (count($cellTexts) >= 6) {
                    // Format 6 kolom: [Soal, A, B, C, D, Kunci]
                    $qText = $cellTexts[0];
                    $optA = $cellTexts[1];
                    $optB = $cellTexts[2];
                    $optC = $cellTexts[3];
                    $optD = $cellTexts[4];
                    $rawKey = $cellTexts[5];
                } else {
                    continue;
                }

                // Ekstrak huruf kunci jawaban (A, B, C, atau D)
                preg_match('/[ABCD]/i', $rawKey, $keyMatch);
                $key = isset($keyMatch[0]) ? strtoupper($keyMatch[0]) : null;

                if (empty($qText) && empty($questionImageRelId)) {
                    continue;
                }

                if (!$key) {
                    $errors[] = "Baris " . ($rowIndex + 1) . ": Kunci jawaban '{$rawKey}' tidak valid. Harus A, B, C, atau D.";
                    continue;
                }

                if (empty($optA) || empty($optB) || empty($optC) || empty($optD)) {
                    $errors[] = "Baris " . ($rowIndex + 1) . ": Pilihan jawaban A, B, C, atau D tidak boleh kosong.";
                    continue;
                }

                // Ekstrak gambar jika ada
                $storedImagePath = null;
                if ($questionImageRelId && isset($imageRelMap[$questionImageRelId])) {
                    $targetPath = 'word/' . ltrim($imageRelMap[$questionImageRelId], '/');
                    $imageBinary = $zip->getFromName($targetPath);
                    if ($imageBinary) {
                        $ext = pathinfo($targetPath, PATHINFO_EXTENSION) ?: 'png';
                        $filename = 'questions/import_' . Str::random(16) . '.' . $ext;
                        Storage::disk('public')->put($filename, $imageBinary);
                        $storedImagePath = $filename;
                    }
                }

                $extractedQuestions[] = [
                    'question_text' => $qText ?: 'Perhatikan gambar berikut ini:',
                    'image' => $storedImagePath,
                    'option_a' => $optA,
                    'option_b' => $optB,
                    'option_c' => $optC,
                    'option_d' => $optD,
                    'correct_answer' => $key,
                ];
            }
        }

        // 4. PRIORITAS B: Jika tabel tidak ditemukan atau kosong, coba fallback parsing paragraf/teks
        if (empty($extractedQuestions)) {
            $paragraphs = $xpath->query('//w:p');
            $lines = [];
            foreach ($paragraphs as $p) {
                $tNodes = $xpath->query('.//w:t', $p);
                $line = '';
                foreach ($tNodes as $t) {
                    $line .= $t->nodeValue;
                }
                $trimmed = trim($line);
                if ($trimmed !== '') {
                    $lines[] = $trimmed;
                }
            }

            $currentQ = null;
            foreach ($lines as $line) {
                // Deteksi nomor soal baru, misal "1. ", "1) ", "Soal 1: "
                if (preg_match('/^(?:Soal\s*)?\d+[\.\)]\s*(.+)/i', $line, $qMatch)) {
                    if ($currentQ && $this->isValidQuestionArray($currentQ)) {
                        $extractedQuestions[] = $currentQ;
                    }
                    $currentQ = [
                        'question_text' => trim($qMatch[1]),
                        'image' => null,
                        'option_a' => '',
                        'option_b' => '',
                        'option_c' => '',
                        'option_d' => '',
                        'correct_answer' => '',
                    ];
                    continue;
                }

                if ($currentQ) {
                    if (preg_match('/^[aA][\.\)]\s*(.+)/', $line, $optMatch)) {
                        $currentQ['option_a'] = trim($optMatch[1]);
                    } elseif (preg_match('/^[bB][\.\)]\s*(.+)/', $line, $optMatch)) {
                        $currentQ['option_b'] = trim($optMatch[1]);
                    } elseif (preg_match('/^[cC][\.\)]\s*(.+)/', $line, $optMatch)) {
                        $currentQ['option_c'] = trim($optMatch[1]);
                    } elseif (preg_match('/^[dD][\.\)]\s*(.+)/', $line, $optMatch)) {
                        $currentQ['option_d'] = trim($optMatch[1]);
                    } elseif (preg_match('/^(?:KUNCI|JAWABAN|KUNCI JAWABAN|KEY)\s*[:=]\s*([ABCD])/i', $line, $kMatch)) {
                        $currentQ['correct_answer'] = strtoupper(trim($kMatch[1]));
                    } else {
                        // Tambahan teks pada pertanyaan sebelum masuk pilihan
                        if (empty($currentQ['option_a'])) {
                            $currentQ['question_text'] .= "\n" . $line;
                        }
                    }
                }
            }

            if ($currentQ && $this->isValidQuestionArray($currentQ)) {
                $extractedQuestions[] = $currentQ;
            }
        }

        $zip->close();

        if (empty($extractedQuestions)) {
            return [
                'success' => false,
                'count' => 0,
                'errors' => !empty($errors) 
                    ? $errors 
                    : ['Tidak ada soal yang berhasil dibaca. Pastikan Anda menggunakan tabel template atau format soal yang sesuai.'],
                'message' => 'Tidak ada soal yang dapat diimpor.',
            ];
        }

        // 5. Simpan seluruh soal ke database dalam Database Transaction
        $importedCount = 0;
        DB::transaction(function () use ($extractedQuestions, $exam, &$importedCount) {
            foreach ($extractedQuestions as $qData) {
                Question::create([
                    'exam_id' => $exam->id,
                    'subject_id' => $exam->subject_id,
                    'question_text' => $qData['question_text'],
                    'image' => $qData['image'],
                    'option_a' => $qData['option_a'],
                    'option_b' => $qData['option_b'],
                    'option_c' => $qData['option_c'],
                    'option_d' => $qData['option_d'],
                    'correct_answer' => $qData['correct_answer'],
                ]);
                $importedCount++;
            }
        });

        return [
            'success' => true,
            'count' => $importedCount,
            'errors' => $errors,
            'message' => "Berhasil mengimpor {$importedCount} butir soal ke dalam ujian.",
        ];
    }

    /**
     * Periksa apakah susunan data soal lengkap
     */
    private function isValidQuestionArray(array $q): bool
    {
        return !empty($q['question_text']) &&
            !empty($q['option_a']) &&
            !empty($q['option_b']) &&
            !empty($q['option_c']) &&
            !empty($q['option_d']) &&
            in_array($q['correct_answer'], ['A', 'B', 'C', 'D']);
    }
}
