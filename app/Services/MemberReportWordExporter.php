<?php

namespace App\Services;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\IOFactory;

class MemberReportWordExporter
{
    protected array $headerCell = ['bgColor' => 'F3F4F6'];
    protected array $tableStyle = ['borderSize' => 6, 'borderColor' => 'D1D5DB', 'cellMargin' => 80];

    public function export(array $data): string
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        $section->addText('Lokal ng Lopez', ['bold' => true, 'size' => 16]);
        $section->addText('Talaan ng mga Mang-aawit ' . now()->format('F d, Y h:i A'), ['size' => 9, 'color' => '6B7280']);
        $section->addTextBreak(1);

        // Mang-aawit section
        $section->addText('Mang-aawit', ['bold' => true, 'size' => 13]);
        $table = $section->addTable($this->tableStyle);

        $table->addRow();
        foreach (['Last Name', 'First Name', 'Birthday', 'Purok-Grupo', 'Tungkulin', 'Status'] as $heading) {
            $table->addCell(2000, $this->headerCell)->addText($heading, ['bold' => true]);
        }

        foreach ($data['mangAawitList'] as $m) {
            $table->addRow();
            $table->addCell(2000)->addText($m['last_name']);
            $table->addCell(2000)->addText($m['first_name']);
            $table->addCell(2000)->addText($m['birthday']);
            $table->addCell(2000)->addText($m['purok_grupo']);
            $table->addCell(2000)->addText($m['tungkulin']);
            $table->addCell(2000)->addText($m['status']);
        }

        $section->addTextBreak(1);
        $s = $data['stats']['mangAawit'];
        $section->addText("Total Mang-aawit: {$s['total']}  (Male: {$s['totalMale']} · Female: {$s['totalFemale']})");
        $section->addText("Active: {$s['active']}  (Male: {$s['activeMale']} · Female: {$s['activeFemale']})");
        $section->addText("Inactive: {$s['inactive']}  (Male: {$s['inactiveMale']} · Female: {$s['inactiveFemale']})");

        $section->addTextBreak(2);

        // Nagsasanay section
        $section->addText('Nagsasanay', ['bold' => true, 'size' => 13]);
        $table2 = $section->addTable($this->tableStyle);

        $table2->addRow();
        foreach (['Last Name', 'First Name', 'Birthday', 'Purok-Grupo', 'Track', 'Progress Stage', 'Status'] as $heading) {
            $table2->addCell(1700, $this->headerCell)->addText($heading, ['bold' => true]);
        }

        foreach ($data['nagsasanayList'] as $n) {
            $table2->addRow();
            $table2->addCell(1700)->addText($n['last_name']);
            $table2->addCell(1700)->addText($n['first_name']);
            $table2->addCell(1700)->addText($n['birthday']);
            $table2->addCell(1700)->addText($n['purok_grupo']);
            $table2->addCell(1700)->addText($n['track']);
            $table2->addCell(1700)->addText($n['progress_stage']);
            $table2->addCell(1700)->addText($n['status']);
        }

        $section->addTextBreak(1);
        $ns = $data['stats']['nagsasanay'];
        $section->addText("Total Nagsasanay: {$ns['total']}");
        $section->addText("Mang-aawit track — Male: {$ns['mangAawitMale']} · Female: {$ns['mangAawitFemale']}");
        $section->addText("Organista track — Male: {$ns['organistaMale']} · Female: {$ns['organistaFemale']}");

        $tempPath = storage_path('app/temp_member_report_' . uniqid() . '.docx');
        IOFactory::createWriter($phpWord, 'Word2007')->save($tempPath);

        return $tempPath;
    }
}