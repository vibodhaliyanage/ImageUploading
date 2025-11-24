function fileUpload(){
    const imgFile = document.getElementById('image-file').files[0];
    

    const f = new FormData();
    f.append('image', imgFile);

    fetch("upload.php", {
      method: "post",
      body: f,
    })
      .then((response) => response.text())
      .then((data) => {
        console.log(data);
      });

}