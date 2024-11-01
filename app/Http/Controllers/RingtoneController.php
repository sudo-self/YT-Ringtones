<?php

namespace App\Http\Controllers;

use App\Models\ConvertedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

class RingtoneController extends Controller
{
    public function showForm()
    {
        $latestMp3 = ConvertedFile::where('file_type', 'mp3')->latest()->first();
        $latestM4r = ConvertedFile::where('file_type', 'm4r')->latest()->first();

       
        $recentRingtones = collect([$latestMp3, $latestM4r])->filter();

        return view('index', compact('recentRingtones'));
    }

    public function convert(Request $request)
    {
      
        $validated = $request->validate(['url' => 'required|url']);
        $url = $validated['url'];
        
      
        $timestamp = time();
        $mp3File = public_path("output/Android_Ringtone_{$timestamp}.mp3");
        $m4rFile = public_path("output/iPhone_Ringtone_{$timestamp}.m4r");

       
        $ytDlpCommand = "/opt/homebrew/bin/yt-dlp -x --audio-format mp3 -o '$mp3File' '$url'";
        exec($ytDlpCommand . " 2>&1", $output, $return_var);

      
        \Storage::put('yt-dlp-log.txt', implode("\n", $output));

        if ($return_var === 0 && file_exists($mp3File)) {
           
            $ffmpegCommand = "/opt/homebrew/bin/ffmpeg -i '$mp3File' -t 20 -acodec aac -b:a 128k -f mp4 '$m4rFile'";
            $mp3TrimmedFile = public_path("output/Android_Ringtone_Trimmed_{$timestamp}.mp3");
            $mp3TrimCommand = "/opt/homebrew/bin/ffmpeg -i '$mp3File' -t 20 -c copy '$mp3TrimmedFile'";
            exec($ffmpegCommand . " 2>&1", $ffmpegOutput, $ffmpegReturnVar);
            exec($mp3TrimCommand . " 2>&1", $mp3TrimOutput, $mp3TrimReturnVar);

            if ($ffmpegReturnVar === 0 && $mp3TrimReturnVar === 0) {
             
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



