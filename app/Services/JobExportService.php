<?php

namespace App\Services;

use App\Enums\JobStatus;
use App\Models\Job;
use Barryvdh\DomPDF\Facade\Pdf;
use DomainException;
use Illuminate\Support\Str;
use ZipArchive;

class JobExportService
{
    public function exportJobAsZip(Job $job): string
    {
        if ($job->status !== JobStatus::Completed) {
            throw new DomainException('Only completed jobs can be exported.');
        }

        $exportsDir = storage_path('app/exports');
        if (! is_dir($exportsDir)) {
            mkdir($exportsDir, 0755, true);
        }

        $tempDir = storage_path('app/temp/export-'.$job->id.'-'.time());
        mkdir($tempDir, 0755, true);

        $pdfPaths = [];

        foreach ($job->blogs as $blog) {
            $pdf = Pdf::loadView('exports.blog-pdf', [
                'blog' => $blog,
                'job' => $job,
                'client' => $job->client,
            ]);

            $filename = Str::slug($blog->title).'.pdf';
            $path = $tempDir.'/'.$filename;
            $pdf->save($path);
            $pdfPaths[] = $path;
        }

        $slug = Str::slug($job->title);
        $zipPath = $exportsDir.'/'.$job->id.'-'.$slug.'.zip';

        if (file_exists($zipPath)) {
            unlink($zipPath);
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new DomainException('Unable to create export archive.');
        }

        foreach ($pdfPaths as $pdfPath) {
            $zip->addFile($pdfPath, basename($pdfPath));
        }

        $zip->close();

        foreach ($pdfPaths as $pdfPath) {
            @unlink($pdfPath);
        }
        @rmdir($tempDir);

        return $zipPath;
    }
}
