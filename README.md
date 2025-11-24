# Image Uploading — PHP + JavaScript (Dark theme) + optional imgbb integration

This is a minimal demo for selecting, previewing and uploading images using plain PHP, vanilla JavaScript and a dark CSS theme. It is designed to run locally under XAMPP (Apache + PHP). The repository shows a simple UI and two common upload approaches:

- Server-side upload handling (recommended) — `upload.php` receives the file and stores it locally or forwards it to an image hosting API (imgbb).
- Direct client uploads to image-hosting APIs (not recommended because it exposes API keys).

This README describes the technologies used, how to run the project locally, and step-by-step instructions to integrate with imgbb safely.

Technologies used
- PHP (server-side handling)
- JavaScript (client-side preview + upload)
- CSS (dark theme: `style.css`)
- XAMPP (Windows) or any PHP-enabled web server for local testing
- imgbb (optional) — third-party image hosting API (https://imgbb.com)

Repository layout (typical)
- `index.php` — front-end page with file input, upload button and preview container
- `script.js` — front-end JavaScript for previewing and uploading
- `style.css` — dark theme stylesheet
- `connection.php` — (optional) database/connection helper used by other server code
- `upload.php` — (optional) server-side upload handler (examples below)
- `README.md` — this file

Quick local setup (Windows + XAMPP)
1. Install XAMPP and start Apache (and MySQL if you use `connection.php`).
2. Copy this project folder into XAMPP's `htdocs`, e.g.: `C:\xampp\htdocs\image-uploading`.
3. Open in your browser:

   http://localhost/image-uploading/index.php

4. Select an image using the file input. If `script.js` contains preview logic, thumbnails will appear immediately. If `upload.php` is implemented, the Upload button will send the image to the server.

Choosing how to upload images
1) Local server storage (simple)
   - `upload.php` receives the uploaded file and moves it into an `uploads/` directory inside the project.
   - Good for local testing and simple demos.

2) Host with imgbb (recommended: via server-side proxy)
   - imgbb provides hosted image storage and returns a public URL for each uploaded image.
   - IMPORTANT: Never place imgbb API keys in client-side JS. Instead, store the API key on the server (in `config.php`, outside version control) and have `upload.php` perform the upload to imgbb.

Server-side example: upload to imgbb securely (recommended)
1. Create `config.php` (not committed) and add your API key:

```php
<?php
// config.php (keep this out of version control)
define('IMGBB_API_KEY', 'your-imgbb-api-key-here');
```

2. Example `upload.php` that accepts a file from the client, posts it to imgbb and returns the resulting URL (minimal — adapt & harden for production):

```php
<?php
require_once __DIR__ . '/config.php'; // contains IMGBB_API_KEY

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['image'];

// Basic validation — extend for production
$allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Unsupported file type']);
    exit;
}

// Read file and encode as base64 for imgbb
$imageData = base64_encode(file_get_contents($file['tmp_name']));

$postFields = [
    'key' => IMGBB_API_KEY,
    'image' => $imageData,
    // optional: 'name' => 'filename.jpg'
];

$ch = curl_init('https://api.imgbb.com/1/upload');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
$resp = curl_exec($ch);
if ($resp === false) {
    echo json_encode(['success' => false, 'message' => curl_error($ch)]);
    curl_close($ch);
    exit;
}
curl_close($ch);

$json = json_decode($resp, true);
if (!empty($json['success'])) {
    // Return the image url and thumb info to client
    $url = $json['data']['url'];
    echo json_encode(['success' => true, 'url' => $url, 'raw' => $json]);
} else {
    echo json_encode(['success' => false, 'message' => $json['error'] ?? 'Upload failed', 'raw' => $json]);
}
```

Client-side example (preview + upload to your `upload.php`)
1. A minimal `script.js` that previews the selected image and POSTs it to `upload.php` using Fetch:

```javascript
// script.js (preview + upload)
const fileInput = document.getElementById('image-file');
const preview = document.getElementById('preview');
const uploadBtn = document.getElementById('upload-btn');

fileInput.addEventListener('change', () => {
  preview.innerHTML = '';
  const files = fileInput.files;
  if (!files || files.length === 0) return;
  for (const f of files) {
    const img = document.createElement('img');
    img.src = URL.createObjectURL(f);
    img.onload = () => URL.revokeObjectURL(img.src);
    preview.appendChild(img);
  }
});

async function fileUpload() {
  const file = fileInput.files[0];
  if (!file) return alert('Please choose a file first');

  const form = new FormData();
  form.append('image', file);

  uploadBtn.disabled = true;
  uploadBtn.textContent = 'Uploading...';

  try {
    const res = await fetch('upload.php', { method: 'POST', body: form });
    const data = await res.json();
    if (data.success) {
      // show the hosted URL (imgbb) or saved path
      const a = document.createElement('a');
      a.href = data.url || '#';
      a.textContent = data.url || 'Uploaded';
      a.target = '_blank';
      preview.appendChild(a);
    } else {
      alert('Upload failed: ' + (data.message || 'unknown'));
    }
  } catch (err) {
    console.error(err);
    alert('Upload error: ' + err.message);
  } finally {
    uploadBtn.disabled = false;
    uploadBtn.textContent = 'Upload';
  }
}

// Wire the button if needed
uploadBtn.addEventListener('click', fileUpload);
```

Security and best practices
- NEVER include API keys or credentials in client-side code. Keep keys in server-side config and out of version control.
- Add `config.php` to `.gitignore` and provide `config.php.example` with placeholder values.
- Validate file type and size server-side. Check MIME type and file extension, and prefer inspecting file contents when possible.
- Limit upload size in `php.ini` (post_max_size and upload_max_filesize) and in server-side code.
- For local storage, save uploaded files outside web root or use generated filenames to avoid collisions and path traversal.

.gitignore suggestions
```
# Ignore config with API keys
config.php

# Ignore uploads if you don't want them in repo
uploads/

# Other system files
.DS_Store
node_modules/
```

What to commit and what to keep private
- Commit `index.php`, `style.css`, `script.js` (without API keys), `README.md` and `upload.php` (if it reads API key from `config.php`).
- DO NOT commit `config.php` with secrets; instead commit `config.php.example`.

Next steps I can do for you
- Add `config.php.example` and a `.gitignore` file now.
- Implement a ready-to-run `upload.php` and `script.js` (I can create them and test locally in the workspace).
- Add theme toggle and persist user preference in localStorage.

Tell me which of the above you'd like next and I will implement it.
