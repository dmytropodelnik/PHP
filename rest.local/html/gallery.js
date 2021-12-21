document.addEventListener("DOMContentLoaded", function () {
    const uploader = document.querySelector("uploader");
    if (!uploader) {
        throw "uploader-container not found";
    }
    const addPictureButton = uploader.querySelector("[name=addPicture]");
    if (!addPictureButton) {
        throw "addPictureButton not found";
    }
    addPictureButton.addEventListener("click", addPictureClick);
    
    fetch("/api/gallery")
        .then(r =>r.text())
        .then(console.log);
});

function addPictureClick(e) {
    const picFile = e.target.parentNode.querySelector("[name=pictureFile]");
    if (!picFile) {
        throw "picFile not found";
    }

    const picDescr = e.target.parentNode.querySelector("[name=pictureDescription]");
    if (!picDescr) {
        throw "picDescr not found";
    }

    if (picFile.files.length === 0) {
        alert("Выберите файл");
        return;
    }
    const descr = picDescr.value.trim();
    if (descr.length == 0) {
        alert("Введите описание");
        return;
    }
    //console.log(picFile.files[0], descr);
    const fd = new FormData();
    fd.append("pictureFile", picFile.files[0]);
    fd.append("pictureDescription", descr);
    fetch("/api/gallery", { 
        method: "post",
        headers: {
            
        },
        body: fd
     })
    .then(r =>r.text())
    .then(console.log);
}