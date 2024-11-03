<?php

namespace App\Http\Controllers;

use App\Models\ConvertedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

class RingtoneController extends Controller
{
    public function getShortFilename($filename, $length = 20)
    {
        return strlen($filename) > $length ? substr($filename, 0, $length) . '...' : $filename;
    }

    public function showForm()
    {
        $latestMp3 = ConvertedFile::where('file_type', 'mp3')->latest()->first();
        $latestM4r = ConvertedFile::where('file_type', 'm4r')->latest()->first();

        // Get the latest 10 ringtones
          $recentRingtones = ConvertedFile::whereIn('file_type', ['mp3', 'm4r'])->latest()->take(10)->get();
          return view('index', compact('recentRingtones'));
      }


    public function convert(Request $request)
    {
        // Validate the request
        $validated = $request->validate(['url' => 'required|url']);
        $url = $validated['url'];
        
        // Fetch video title
        $titleCommand = "/opt/homebrew/bin/yt-dlp --get-title '$url'";
        $title = shell_exec($titleCommand);
        $title = trim($title); // Remove extra whitespace or new lines
        $sanitizedTitle = preg_replace('/[^A-Za-z0-9_\-]/', '_', $title); // Sanitize title

        // Define file paths with sanitized title
        $mp3File = public_path("output/{$sanitizedTitle}.mp3");
        $m4rFile = public_path("output/{$sanitizedTitle}.m4r");

        // Download and convert to MP3
        $ytDlpCommand = "/opt/homebrew/bin/yt-dlp -x --audio-format mp3 -o '$mp3File' '$url'";
        exec($ytDlpCommand . " 2>&1", $output, $return_var);

        // Log the output for debugging
        \Storage::put('yt-dlp-log.txt', implode("\n", $output));

        if ($return_var === 0 && file_exists($mp3File)) {
            // Convert to M4R and trimmed MP3
            $ffmpegCommand = "/opt/homebrew/bin/ffmpeg -i '$mp3File' -t 20 -acodec aac -b:a 128k -f mp4 '$m4rFile'";
            exec($ffmpegCommand . " 2>&1", $ffmpegOutput, $ffmpegReturnVar);

            if ($ffmpegReturnVar === 0) {
                // Save to database
                ConvertedFile::create([
                    'original_url' => $url,
                    'file_name' => "{$sanitizedTitle}.mp3",
                    'file_path' => "output/{$sanitizedTitle}.mp3",
                    'file_type' => 'mp3'
                ]);

                ConvertedFile::create([
                    'original_url' => $url,
                    'file_name' => "{$sanitizedTitle}.m4r",
                    'file_path' => "output/{$sanitizedTitle}.m4r",
                    'file_type' => 'm4r'
                ]);

                // Fetch the latest ringtones for the view
                $latestMp3 = ConvertedFile::where('file_type', 'mp3')->latest()->first();
                $latestM4r = ConvertedFile::where('file_type', 'm4r')->latest()->first();

                $recentRingtones = collect([$latestMp3, $latestM4r])->filter();

                return view('index')->with([
                    'downloadLinks' => true,
                    'm4rFile' => $m4rFile,
                    'mp3TrimmedFile' => $mp3File,
                    'recentRingtones' => $recentRingtones
                ]);
            }
        }

        // Error handling
        return view('index')->with([
            'error' => 'An error occurred during conversion.',
            'recentRingtones' => ConvertedFile::whereIn('file_type', ['mp3', 'm4r'])->latest()->take(2)->get(),
        ]);
    }


    public function downloadM4R()
    {
        // Get the latest M4R file from the database
        $latestM4r = ConvertedFile::where('file_type', 'm4r')->latest()->first();

        if ($latestM4r && file_exists(public_path($latestM4r->file_path))) {
            // Sanitize filename if necessary
            $safeFileName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $latestM4r->file_name);

            return Response::download(public_path($latestM4r->file_path), $safeFileName, [
                'Content-Type' => 'audio/m4r',
                'Content-Disposition' => 'attachment; filename="' . $safeFileName . '"',
            ]);
        }

        // Log the error for debugging
        Log::error('M4R file not found', ['latestM4r' => $latestM4r]);
        
        return redirect()->back()->with('error', 'File not found.');
    }
} // Closing brace for the RingtoneController class

