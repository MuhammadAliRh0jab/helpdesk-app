<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ConvertBase64ToFile
{
    public function handle(Request $request, Closure $next)
    {
        \Log::info('Middleware ConvertBase64ToFile running', ['input' => $request->all()]);

        $imagesInput = $request->input('images');
        if ($imagesInput && !empty($imagesInput)) {
            \Log::info('Base64 image detected', ['length' => strlen($imagesInput)]);

            $base64Image = $imagesInput;
            if (preg_match('/^data:image\/\w+;base64,/', $base64Image)) {
                $base64Image = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
            }

            $imageData = base64_decode($base64Image);
            if ($imageData === false) {
                \Log::error('Failed to decode Base64 image');
                return $next($request);
            }

            $tempFile = tempnam(sys_get_temp_dir(), 'img_');
            file_put_contents($tempFile, $imageData);
            \Log::info('Temp file created', ['path' => $tempFile, 'size' => filesize($tempFile)]);

            $file = new UploadedFile(
                $tempFile,
                'image_' . Str::random(10) . '.jpg',
                'image/jpeg',
                null,
                true
            );

            // Simulasi struktur seperti input file multiple: images[0]
            $request->files->add(['images' => [$file]]);
            \Log::info('Files set in request', ['files' => $request->files->all()]);
        } else {
            \Log::info('No valid images input detected', ['images' => $imagesInput]);
        }

        return $next($request);
    }
}
