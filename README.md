# Image Uploading (Simple PHP + XAMPP)

A small demo project to upload and preview images built with plain PHP, HTML, JavaScript and a dark-themed CSS.

This repository contains a minimal image uploading UI you can run locally with XAMPP (Apache + PHP). The project includes a dark theme (`style.css`) and a tiny JavaScript stub (`script.js`) for previews.

## Features
- Dark theme UI (in `style.css`)
- Single-file upload form (`index.php`) with a preview area
- Ready to wire to a backend upload handler (e.g., `connection.php` + file processing)

## Files of interest
- `index.php` — front-end page with file input and preview container
- `style.css` — dark theme stylesheet (new)
- `script.js` — client-side script (currently minimal/placeholder)
- `connection.php` — (existing) database/connection helper (if used)

If you add an upload handler, we recommend creating `upload.php` to receive the POST request and move uploaded files into a safe `uploads/` folder.

## Prerequisites
- XAMPP for Windows (Apache + PHP)
- A modern browser (Chrome, Edge, Firefox)

## Quick start (Windows / XAMPP)
1. Install and run XAMPP, start Apache.
2. Place this project folder in your XAMPP `htdocs` directory (example: `C:\xampp\htdocs\image-uploading`).
3. Open the app in your browser:

   http://localhost/image-uploading/index.php

4. Use the file input to select images. If `script.js` has preview logic, thumbnails will show in the preview area. To enable actual uploads, implement an `upload.php` server endpoint and submit the file input with a form POST or AJAX.

## Notes about the dark theme
- The project includes `style.css` with CSS custom properties for colors and accessible focus styles. Edit `style.css` to adjust tokens like `--bg`, `--text`, and `--accent` to customize the theme.

## Security and production notes
- Never trust client-side checks; always validate and sanitize uploads on the server.
- Limit allowed MIME types and file sizes.
- Store uploads outside the webroot or use generated filenames to avoid collisions.
- Consider running virus scanning on uploaded files in production.

## Contributing
1. Fork the repository.
2. Create a branch for your feature: `git checkout -b feat/your-feature`.
3. Commit changes and open a pull request with a short description.

## Example: Add a simple `upload.php`
Below is a minimal outline you can use in `upload.php` to accept an uploaded file (adapt for your needs):

```php
// Example sketch — validate & secure before using in production
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];
    // Validate file size, type, and errors
    // Move to uploads/ with a unique name
    $uploadsDir = __DIR__ . '/uploads';
    if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755);
    $target = $uploadsDir . '/' . uniqid('img_', true) . '_' . basename($file['name']);
    move_uploaded_file($file['tmp_name'], $target);
    echo json_encode(['success' => true, 'path' => $target]);
}
```

## License
This project is provided as-is for demo and learning purposes. Add a license file if you want to publish it publicly (MIT recommended for small demos).

---
If you'd like, I can:
- implement client-side preview logic in `script.js` now,
- add `upload.php` and server-side validation, or
- add a light/dark theme toggle persisted to localStorage.

Tell me which of the above you'd like next.
