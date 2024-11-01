<!doctype html>
<html lang="en">
<head>
    <!-- Favicon and Icons -->
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    
    <!-- Basic Meta Tags -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>YT Ringtone Creator</title>
    <meta name="title" content="YT Ringtone Creator">
    <meta name="description" content="Create Ringtones from YT URLs for android and Apple devices.">
    <meta name="author" content="JR">

    <!-- Open Graph / Facebook Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://yt.jessejesse.xyz/">
    <meta property="og:title" content="YT Ringtone Creator">
    <meta property="og:description" content="Create Ringtones from YT URLs for android and Apple devices.">
    <meta property="og:image" content="https://yt.jessejesse.xyz/og.png">

    <!-- Twitter Meta Tags -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://yt.jessejesse.xyz/">
    <meta property="twitter:title" content="YT Ringtone Creator">
    <meta property="twitter:description" content="Create Ringtones from YT URLs for android and Apple devices.">
    <meta property="twitter:image" content="https://yt.jessejesse.xyz/og.png">

    <!-- CSS Links -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    
    <!-- Laravel Mix CSS -->

    <link rel="manifest" href="/manifest.json">


    <style>
      .gradient-text {
        background: linear-gradient(to right, #ff0000, #808080);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
      }

      .gradient-button {
        background: linear-gradient(to right, #ff0000, #808080);
        transition: background 0.3s;
        flex: 1;
        margin: 0 5px;
        color: #000;
      }

      .gradient-button:hover {
        background: linear-gradient(to right, #808080, #ff0000);
      }

      .footer-link {
        text-decoration: none;
        transition: text-decoration 0.3s;
      }

      .footer-link:hover {
        text-decoration: underline;
        color: red;
      }
    </style>
</head>

<body class="bg-black text-gray-900 flex flex-col min-h-screen p-8">
    <div class="flex-grow flex flex-col items-center justify-center">
        <h1 class="text-3xl font-bold mb-6 text-center gradient-text">YT Ringtones</h1>
        <form method="POST" action="/convert" class="bg-transparent shadow-md rounded px-8 pt-6 pb-8 mb-4 w-full max-w-md">
            @csrf
            <input type="text" id="url" name="url" placeholder="Enter a YouTube URL" required class="border border-gray-300 p-2 rounded w-full mb-4" />
            <div class="button-container flex justify-between">
                <button type="button" onclick="pasteUrl()" class="gradient-button font-semibold py-2 px-4 rounded">Paste Link</button>
                <button type="submit" class="gradient-button font-semibold py-2 px-4 rounded">Create Ringtone</button>
            </div>
        </form>

        <!-- Adjusted Android Download Button -->
        @if(isset($downloadLinks))
            <div class="button-container flex flex-col items-center space-y-2">
                <a href="{{ asset($mp3TrimmedFile) }}" download="Ringtone_Android.mp3" class="gradient-button android py-2 rounded w-full text-center">Android</a>
            </div>
        @endif

        <h5 class="mt-8 text-gray-600 text-sm text-center">
            Android open settings<br />
            select sounds and vibration.<br />
            open the Ringtone menu.<br />
            Tap + in the upper-right corner<br />
            select Ringtone.mp3 & restart.
        </h5>
        <br />

        <div class="mt-4">
            @if(isset($downloadLinks))
                <div class="button-container flex flex-col items-center space-y-2">
                    <a href="{{ url('/download-m4r') }}" download class="gradient-button apple py-2 rounded w-full text-center">iPhone</a>
                </div>
            @endif
        </div>

        <h5 class="mt-8 text-gray-600 text-sm text-center">
            iPhone connect with cable<br />
            open iTunes and click "Tones"<br />
            drag and drop Ringtone.m4r into<br />
            "Tones" section and click "Sync".
        </h5>
    </div><br />

    <ul class="space-y-2 text-center">
        <span class="gradient-text">Recent Ringtones</span>
        @foreach($recentRingtones as $ringtone)
            <li class="text-gray-200">
                <a href="{{ asset($ringtone->file_path) }}" class="footer-link">
                    {{ $ringtone->file_name }}
                </a>
            </li>
        @endforeach
    </ul>

    <footer class="mt-8 text-sm text-gray-200 text-center">
        <a href="https://yt.jessejesse.xyz" target="_blank" class="footer-link">
            <span>YT&nbsp;&#10085;&nbsp;JesseJesse.xyz</span>
        </a>
    </footer>

    <script>
        function pasteUrl() {
            navigator.clipboard
                .readText()
                .then(function (text) {
                    document.getElementById("url").value = text;
                })
                .catch(function (err) {
                    console.error("Failed to read clipboard contents: ", err);
                });
        }
    </script>
</body>
</html>
