document.addEventListener("DOMContentLoaded", function () {
    const testGetButton = document.querySelector("#testGetButton");
    if (!testGetButton) {
        throw "testGetButton not found";
    } else {
        testGetButton.onclick = testGet;
    }

    const testPostButton = document.querySelector("#testPostButton");
    if (!testPostButton) { 
        throw "testPostButton not found";
    } else {
        testPostButton.onclick = testPost;
    }

    const testPutButton = document.querySelector("#testPutButton");
    if (!testPutButton) {
        throw "testPutButton not found";
    } else {
        testPutButton.onclick = testPut;
    }

    const testDeleteButton = document.querySelector("#testDeleteButton");
    if (!testDeleteButton) {
        throw "testDeleteButton not found";
    } else {
        testDeleteButton.onclick = testDelete;
    }

    const filePostButton = document.querySelector("#filePostButton");
    if (!filePostButton) {
        throw "filePostButton not found";
    } else {
        filePostButton.onclick = filePost;
    }

    const filePutButton = document.querySelector("#filePutButton");
    if (!filePutButton) {
        throw "filePutButton not found";
    } else {
        filePutButton.onclick = filePut;
    }

    const localeUaButton = document.querySelector("#localeUaButton");
    if (!localeUaButton) {
        throw "localeUaButton not found";
    } else {
        localeUaButton.onclick = localeUaButtonClick;
    }

    const localeEnButton = document.querySelector("#localeEnButton");
    if (!localeEnButton) {
        throw "localeEnButton not found";
    } else {
        localeEnButton.onclick = localeEnButtonClick;
    }

    const localeRuButton = document.querySelector("#localeRuButton");
    if (!localeRuButton) {
        throw "localeRuButton not found";
    } else {
        localeRuButton.onclick = localeRuButtonClick;
    }

    const localeFrButton = document.querySelector("#localeFrButton");
    if (!localeFrButton) {
        throw "localeFrButton not found";
    } else {
        localeFrButton.onclick = localeFrButtonClick;
    }

    const noLocaleButton = document.querySelector("#noLocaleButton");
    if (!noLocaleButton) {
        throw "noLocaleButton not found";
    } else {
        noLocaleButton.onclick = noLocaleButtonClick;
    }
});

function noLocaleButtonClick() {
    fetch("/api/locale", {
        method: "GET",
        headers: {

        }
    })
        .then(r => r.text())
        .then(t => {
            out.innerHTML = t;
        });
}

function localeUaButtonClick() {
    fetch("/api/locale", {
        method: "GET",
        headers: {
            "Locale": "ua"
        }
    })
        .then(r => r.text())
        .then(t => {
            out.innerHTML = t;
        });
}

function localeEnButtonClick() {
    fetch("/api/locale", {
        method: "GET",
        headers: {
            "Locale": "en"
        }
    })
        .then(r => r.text())
        .then(t => {
            out.innerHTML = t;
        });
}

function localeRuButtonClick() {
    fetch("/api/locale", {
        method: "GET",
        headers: {
            "Locale": "ru"
        }
    })
        .then(r => r.text())
        .then(t => {
            out.innerHTML = t;
        });
}

function localeFrButtonClick() {
    fetch("/api/locale", {
        method: "GET",
        headers: {
            "Locale": "fr"
        }
    })
        .then(r => r.text())
        .then(t => {
            out.innerHTML = t;
        });
}

function testGet() {
    const out = document.querySelector("#out");
    if (!out) {
        throw "out not found";
    }

    fetch("/api?x=11&y=21")
        .then(r => r.text())
        .then(t => {
            out.innerText = t;
        });
}

function testPost() {
    const out = document.querySelector("#out");
    if (!out) {
        throw "out not found";
    }

    fetch("/api", {
        method: "post",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            "x": 10,
            "y": 20,
        })
    })
        .then(r => r.text())
        .then(t => {
            out.innerText = t;
        });
}

function testPut() {
    const out = document.querySelector("#out");
    if (!out) {
        throw "out not found";
    }

    fetch("/api", {
        method: "put",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            "x": 3,
            "y": 4,
        })
    })
        .then(r => r.text())
        .then(t => {
            out.innerText = t;
        });
}

function testDelete() {
    const out = document.querySelector("#out");
    if (!out) {
        throw "out not found";
    }

    fetch("/api", {
        method: "delete",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            "x": 5,
            "y": 6,
        })
    })
        .then(r => r.text())
        .then(t => {
            out.innerText = t;
        });
}

function filePost() {
    const fileInput = document.querySelector("input[name=userFile]");
    if (!fileInput) {
        throw "input[name=userFile] not found";
    }
    if (fileInput.files.length == 0) {
        alert("Select a file");
        return;
    }
    const fd = new FormData();
    fd.append("userFile", fileInput.files[0]);
    fetch("/api/file", {
        method: "post",
        body: fd
    })
        .then(r => r.text())
        .then(t => {
            const out = document.getElementById("out");
            if (!out) {
                throw "out not found";
            }
            out.innerHTML = t;
        })
}

function filePut() {
    const fileInput = document.querySelector("input[name=userFile]");
    if (!fileInput) {
        throw "input[name=userFile] not found";
    }
    if (fileInput.files.length == 0) {
        alert("Select a file");
        return;
    }
    const fd = new FormData();
    fd.append("userFile", fileInput.files[0]);
    fetch("/api/file", {
        method: "put",
        body: fd
    })
        .then(r => r.text())
        .then(t => {
            const out = document.getElementById("out");
            if (!out) {
                throw "out not found";
            }
            out.innerHTML = t;
        })
}