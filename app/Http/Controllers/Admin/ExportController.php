<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Services\JobExportService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function __construct(
        private JobExportService $jobExportService,
    ) {}

    public function download(Job $job): BinaryFileResponse|RedirectResponse
    {
        try {
            $zipPath = $this->jobExportService->exportJobAsZip($job);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return response()->download($zipPath, basename($zipPath))->deleteFileAfterSend(true);
    }
}
