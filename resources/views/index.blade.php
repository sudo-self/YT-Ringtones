<!doctype html>
<html lang="en">
  <head>
    <!-- Favicon and Icons -->
    <link rel="icon" href="/favicon.ico" sizes="any" />
    <link rel="apple-touch-icon" href="/apple-touch-icon.png" />

    <!-- Basic Meta Tags -->
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"
    />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>YT Ringtone Creator</title>
    <meta name="title" content="YT Ringtone Creator" />
    <meta
      name="description"
      content="Create Ringtones from YT URLs for Android and Apple devices."
    />
    <meta name="author" content="JR" />

    <!-- Open Graph / Facebook Meta Tags -->
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://yt.jessejesse.xyz/" />
    <meta property="og:title" content="YT Ringtone Creator" />
    <meta
      property="og:description"
      content="Create Ringtones from YT URLs for Android and Apple devices."
    />
    <meta property="og:image" content="https://yt.jessejesse.xyz/og.png" />

    <!-- Twitter Meta Tags -->
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:url" content="https://yt.jessejesse.xyz/" />
    <meta property="twitter:title" content="YT Ringtone Creator" />
    <meta
      property="twitter:description"
      content="Create Ringtones from YT URLs for Android and Apple devices."
    />
    <meta property="twitter:image" content="https://yt.jessejesse.xyz/og.png" />

    <!-- CSS Links -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"
    />

    <link rel="manifest" href="/manifest.json" />

    <style>
      body {
        background-image: url("https://firebasestorage.googleapis.com/v0/b/svelte-forever.appspot.com/o/space.png?alt=media&token=69b3e1d6-7e5e-42b9-81a3-72cdbdc5142d");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        color: #fff;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
      }
      .main-content {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
      }
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

      .text-below-image {
        margin-top: 2px;
        margin-bottom: 10px;
      }

      input[type="text"] {
        background: transparent;
        color: #ccc;
        border: 2px dashed #fff;
      }

      input[type="text"]::placeholder {
        color: #888; /* Gray placeholder text */
        text-align: center;
      }
    </style>
  </head>

  <body>
    <body class="bg-gray-900 text-gray-200 flex flex-col min-h-screen mt-4">
      <div class="main-content flex-grow">
        <h2 class="text-4xl font-bold mb-4 text-center gradient-text">
          YT 🚀 Ringtones
        </h2>
        <form
          method="POST"
          action="/convert"
          class="bg-transparent shadow-md rounded px-8 pt-6 pb-8 mb-4 w-full max-w-screen-sm mx-auto"
        >
          <!-- Added mx-auto for centering -->
          @csrf
          <input
            type="text"
            id="url"
            name="url"
            placeholder="Enter a Youtube URL"
            required
            class="border border-gray-300 p-2 rounded w-full mb-4"
          />
          <div class="button-container flex justify-between">
            <button
              type="button"
              onclick="pasteUrl()"
              class="gradient-button font-semibold py-2 px-4 rounded"
            >
              Paste Link
            </button>
            <button
              type="submit"
              class="gradient-button font-semibold py-2 px-4 rounded"
            >
              Create Ringtone
            </button>
          </div>
        </form>

        @if(isset($downloadLinks))
        <div
          class="button-container flex flex-col items-center space-y-2 max-w-xs mx-auto"
        >
          <a
            href="{{ asset($mp3TrimmedFile) }}"
            download="Android_Ringtone.mp3"
          >
            <img
              src="https://pub-c1de1cb456e74d6bbbee111ba9e6c757.r2.dev/IcSharpAndroid.png"
              width="80px"
              height="auto"
            />
          </a>
          <h5 class="text-green-400 text-sm text-center text-below-image">
            Android open settings<br />
            select sounds and vibration.<br />
            open the Ringtone menu.<br />
            Tap + in the upper-right corner<br />
            select Ringtone.mp3 & restart.
          </h5>
        </div>
        @endif
        <br />
        <div class="mt-4">
          @if(isset($downloadLinks))
          <div
            class="button-container flex flex-col items-center space-y-2 max-w-xs mx-auto"
          >
            <a href="{{ url('/download-m4r') }}" download="iPhone_Ringtone.m4r">
              <img
                src="https://pub-c1de1cb456e74d6bbbee111ba9e6c757.r2.dev/FileIconsApple.png"
                width="80px"
                height="auto"
              />
            </a>
            <h5 class="text-gray-500 text-sm text-center text-below-image">
              iPhone connect with cable<br />
              open iTunes and click "Tones"<br />
              drag and drop Ringtone.m4r into<br />
              "Tones" section and click "Sync".
            </h5>
          </div>
          @endif
        </div>

        <body class="bg-gray-900 text-gray-200 flex flex-col min-h-screen">
          <div class="flex-grow">
            <div class="h-48 mt-8">
              <h2 class="text-center gradient-text">♬ Latest Ringtones ♬</h2>
              <br />
              <div
                class="overflow-y-auto max-h-60 w-full max-w-xs mx-auto border border-gray-600 rounded-lg p-2"
              >
                <ul class="space-y-2 text-center">
                  @foreach($recentRingtones as $ringtone) @if($ringtone) @php
                  $filename = pathinfo($ringtone->file_name, PATHINFO_FILENAME);
                  $extension = pathinfo($ringtone->file_name,
                  PATHINFO_EXTENSION); @endphp
                  <li class="text-gray-200">
                    <a
                      href="{{ asset($ringtone->file_path) }}"
                      class="footer-link truncate w-full inline-block"
                      style="max-width: 100%"
                    >
                      {{ Str::limit($filename, 25) }}.{{ $extension }}
                    </a>
                  </li>
                  @endif @endforeach
                </ul>
              </div>
            </div>
          </div>

          <footer
            class="text-sm text-gray-200 text-center mt-8 p-4 bg-transparent"
          >
            <a
              href="https://yt.jessejesse.xyz"
              target="_blank"
              class="footer-link"
            >
              <span>YT&nbsp;&#10085;&nbsp;JesseJesse.xyz</span>
            </a>
          </footer>

          <script>
            async function pasteUrl() {
              try {
                if (navigator.clipboard && navigator.clipboard.readText) {
                  const text = await navigator.clipboard.readText();
                  document.getElementById("url").value = text;
                } else {
                  alert(
                    "Clipboard access is not supported on this browser. Please paste the URL manually.",
                  );
                }
              } catch (err) {
                console.error("Failed to read clipboard contents: ", err);
                alert(
                  "Unable to paste from clipboard. Please paste the URL manually.",
                );
              }
            }
          </script>
        </body>
      </div>
    </body>
  </body>
</html>
