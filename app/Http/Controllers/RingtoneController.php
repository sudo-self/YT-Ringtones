<?php

namespace App\Http\Controllers;

use App\Models\ConvertedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class RingtoneController extends Controller
{
    public function showForm()
    {
        $latestMp3 = ConvertedFile::where('file_type', 'mp3')->latest()->first();
        $latestM4r = ConvertedFile::where('file_type', 'm4r')->latest()->first();

        // Only keep non-null entries to prevent errors in the view
        $recentRingtones = collect([$latestMp3, $latestM4r])->filter();

        return view('index', compact('recentRingtones'));
    }

    public function convert(Request $request)
    {
        // Validate the request
        $validated = $request->validate(['url' => 'required|url']);
        $url = $validated['url'];
        
        // Define file paths with unique names based on timestamp
        $timestamp = time();
        $mp3File = public_path("output/Android_Ringtone_{$timestamp}.mp3");
        $m4rFile = public_path("output/iPhone_Ringtone_{$timestamp}.m4r");

        // Download and convert to MP3
        $ytDlpCommand = "/opt/homebrew/bin/yt-dlp -x --audio-format mp3 -o '$mp3File' '$url'";
        exec($ytDlpCommand . " 2>&1", $output, $return_var);

        // Log the output
        \Storage::put('yt-dlp-log.txt', implode("\n", $output));

        if ($return_var === 0 && file_exists($mp3File)) {
            // Convert to M4R and trimmed MP3
            $ffmpegCommand = "/opt/homebrew/bin/ffmpeg -i '$mp3File' -t 20 -acodec aac -b:a 128k -f mp4 '$m4rFile'";
            $mp3TrimmedFile = public_path("output/Android_Ringtone_Trimmed_{$timestamp}.mp3");
            $mp3TrimCommand = "/opt/homebrew/bin/ffmpeg -i '$mp3File' -t 20 -c copy '$mp3TrimmedFile'";
            exec($ffmpegCommand . " 2>&1", $ffmpegOutput, $ffmpegReturnVar);
            exec($mp3TrimCommand . " 2>&1", $mp3TrimOutput, $mp3TrimReturnVar);

            if ($ffmpegReturnVar === 0 && $mp3TrimReturnVar === 0) {
                // Save to database
                ConvertedFile::create([
                    'original_url' => $url,
                    'file_name' => "Android_Ringtone_{$timestamp}.mp3",
                    'file_path' => "output/Android_Ringtone_{$timestamp}.mp3",
                    'file_type' => 'mp3'
                ]);

                ConvertedFile::create([
                    'original_url' => $url,
                    'file_name' => "iPhone_Ringtone_{$timestamp}.m4r",
                    'file_path' => "output/iPhone_Ringtone_{$timestamp}.m4r",
                    'file_type' => 'm4r'
                ]);

                // Fetch the latest mp3 and m4r files for the view
                $latestMp3 = ConvertedFile::where('file_type', 'mp3')->latest()->first();
                $latestM4r = ConvertedFile::where('file_type', 'm4r')->latest()->first();

                $recentRingtones = collect([$latestMp3, $latestM4r])->filter();

                return view('index')->with([
                    'downloadLinks' => true,
                    'm4rFile' => "output/iPhone_Ringtone_{$timestamp}.m4r",
                    'mp3TrimmedFile' => "output/Android_Ringtone_Trimmed_{$timestamp}.mp3",
                    'recentRingtones' => $recentRingtones
                ]);
            }
        }

        // Handle error case
        $latestMp3 = ConvertedFile::where('file_type', 'mp3')->latest()->first();
        $latestM4r = ConvertedFile::where('file_type', 'm4r')->latest()->first();
        $recentRingtones = collect([$latestMp3, $latestM4r])->filter();

        return view('index')->with([
            'error' => 'An error occurred during conversion.',
            'recentRingtones' => $recentRingtones
        ]);
    }

    public function downloadM4R()
    {
        $m4rFile = public_path('output/iPhone_Ringtone.m4r');
        if (file_exists($m4rFile)) {
            return Response::download($m4rFile, 'iPhone_Ringtone.m4r', [
                'Content-Type' => 'audio/m4r',
                'Content-Disposition' => 'attachment; filename="iPhone_Ringtone.m4r"',
            ]);
        }

        return redirect()->back()->with('error', 'File not found.');
    }
}


