<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Uploading</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="upload-container">
        <h1 class="title">Image Uploading</h1>

        <input type="file" id="image-file" accept="image/*">
        <button id="upload-btn" type="button" onclick="fileUpload();">Upload</button>
        
        <div id="preview" class="preview" aria-live="polite"></div>
    
    </main>


    <script src="script.js"></script>
</body>

</html>