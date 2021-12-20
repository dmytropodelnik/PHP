document.addEventListener("DOMContentLoaded", function() {
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
});

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

    // fetch("/api", {
    //     method: "post",
    //     headers: {
    //         "Content-Type": "application/x-www-form-urlencoded",
    //     },
    //     body: "x=10&y=20"
    // })
    //     .then(r => r.text())
    //     .then(t => {
    //         out.innerText = t;
    //     });
}

function testPut() {
    const out = document.querySelector("#out");
    if (!out) {
        throw "out not found";
    } 

    fetch("/api")
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

    fetch("/api")
        .then(r => r.text())
        .then(t => {
            out.innerText = t;
        });
}
