<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;
use App\Notifications\S3ImageUploadNotification;

class S3ImageUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected  $path;

    /**
     * Create a new job instance.
     *
     * @param $image
     */
    public function __construct( $path)
    {
        $this->path =$path;


    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {


        $fileContent = file_get_contents($this->path);

        $fileName = basename($this->path);

      $s3= Storage::disk('s3')->put('images/' . $fileName, $fileContent);
        if ($s3) {
            Log::info("File {$this->path} has been uploaded to AWS S3.");

         // Supprime le fichier local une fois qu'il est téléchargé sur S3
            unlink($this->path);
        } else {
            Log::error("Failed to upload file {$this->path} to AWS S3.");

        }


}}

