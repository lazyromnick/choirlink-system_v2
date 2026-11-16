<?php

namespace App\Filament\Resources\MangAawitResource\Pages;

use App\Filament\Resources\MangAawitResource;
use App\Services\MemberReportService;
use App\Services\MemberReportWordExporter;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;

class ListMangAawits extends ListRecords
{
    protected static string $resource = MangAawitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Enroll Mang-aawit'),

            Actions\Action::make('viewArchived')
                ->label('View Archived')
                ->icon('heroicon-o-archive-box')
                ->color('gray')
                ->url(fn () => MangAawitResource::getUrl('archived')),

            Actions\Action::make('exportRecord')
                ->label('Export Record')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    Forms\Components\Radio::make('format')
                        ->label('Export Format')
                        ->options([
                            'word' => 'Word (.docx)',
                            'pdf'  => 'PDF',
                            'csv'  => 'CSV - coming soon',
                        ])
                        ->descriptions([
                            'csv' => 'For backup import/export - not yet available.',
                        ])
                        ->default('word')
                        ->disableOptionWhen(fn (string $value) => $value === 'csv')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $reportData = (new MemberReportService())->getData();

                    if ($data['format'] === 'pdf') {
                        $reportData = $this->sanitizeForPdf($reportData);

                        $pdf = Pdf::loadView('exports.member-report', $reportData);

                        $tempPath = storage_path('app/temp_member_report_' . uniqid() . '.pdf');
                        file_put_contents($tempPath, $pdf->output());

                        $fileName = 'Lokal-ng-Lopez-Member-Report-' . now()->format('Y-m-d') . '.pdf';

                        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
                    }

                    if ($data['format'] === 'word') {
                        $path = (new MemberReportWordExporter())->export($reportData);
                        $fileName = 'Talaan-ng-mga-Mang-aawit-Lokal-ng-Lopez' . now()->format('Y-m-d') . '.docx';

                        return response()->download($path, $fileName)->deleteFileAfterSend(true);
                    }
                }),
        ];
    }

    protected function sanitizeForPdf(array $data): array
    {
        array_walk_recursive($data, function (&$value) {
            if (is_string($value)) {
                $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            }
        });

        return $data;
    }
}