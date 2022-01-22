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

    loadGallery();
    initPaginator();
    initFilter();
    initLangSwitch();
});

function initLangSwitch() {
    const langSwitch = document.getElementById("langSwitch");
    const langSelect = document.getElementById("langSelect");
    const setLang = document.getElementById("setLang");
    const unsetLang = document.getElementById("unsetLang");
    if (!langSwitch || !langSelect || !setLang || !unsetLang) {
        throw "initLangSwitch - element(s) location error";
    }
    unsetLang.onclick = unsetLanguage;
    fetch("/api/gallery?langs").then(r => r.json())
        .then(j => {
            j.push("all");
            for (let lang of j) {
                let opt = document.createElement("option");
                opt.value = lang;
                opt.innerText = lang;
                langSelect.appendChild(opt);
            }
            langSelect.options[3].selected = true;
            setLang.onclick = langChange;
        });
}

function unsetLanguage() {
    const langSelect = document.getElementById("langSelect");
    langSelect[3].selected = true;
    loadGallery();
}

function langChange() {
    const opt = document.querySelector("#langSelect option:checked");
    if (!opt) {
        alert("Select lang before switching");
        return;
    }
    loadGallery({ 'lang': opt.value });
}

function initFilter() {
    const applyFilter = document.querySelector("#applyFilter");
    if (!applyFilter) {
        throw "applyFilter not found";
    }
    applyFilter.addEventListener("click", applyFilterClick);

    const resetFilter = document.querySelector("#resetFilters");
    if (!resetFilter) {
        throw "resetFilter not found";
    }
    resetFilter.addEventListener("click", resetFilterClick);
}

function unsetDateFilter() {

}

function resetFilterClick() {
    unsetDateFilter();
    unsetLanguage();
}

function applyFilterClick() {
    const datePicker = document.querySelector("#datePicker");
    if (!datePicker) {
        throw "datePicker not found";
    }
    const date = datePicker.value;
    if (date.length === 0) {
        alert("Select date to filter");
        return;
    }

    const cont = document.querySelector("gallery");
    if (!cont) {
        throw "Gallery container not found";
    }

    let currentPage = cont.getAttribute("pageNumber");
    currentPage = 1;
    let currentPageSpan = document.querySelector("#currentPage");
    currentPageSpan.innerText = currentPage;

    const opt = document.querySelector("#langSelect option:checked");
    if (!opt) {
        alert("Select lang before switching");
        return;
    }

    let langValue = opt.value;

    let params = (new URL(document.location)).searchParams; 
    if (params.get("lang") === "all") {
        langValue = "all";
    }

    loadGallery({ 'date': date, 'lang': langValue });
}

function initPaginator() {
    const prevButton = document.querySelector("#prevButton");
    if (!prevButton) {
        throw "prevButton not found";
    }
    prevButton.addEventListener("click", prevButtonClick);

    const nextButton = document.querySelector("#nextButton");
    if (!nextButton) {
        throw "nextButton not found";
    }
    nextButton.addEventListener("click", nextButtonClick);

}

function prevButtonClick(e) {
    const cont = document.querySelector("gallery");
    if (!cont) {
        throw "Gallery container not found";
    }

    const opt = document.querySelector("#langSelect option:checked");
    if (!opt) {
        alert("Select lang before switching");
        return;
    }

    let langValue = opt.value;

    let params = (new URL(document.location)).searchParams; 
    if (params.get("lang") === "all") {
        langValue = "all";
    }

    let currentPage = cont.getAttribute("pageNumber");
    if (currentPage > 1) {
        currentPage--;

        let currentPageSpan = document.querySelector("#currentPage");
        currentPageSpan.innerText = currentPage;

        const datePicker = document.querySelector("#datePicker");
        if (!datePicker) {
            throw "datePicker not found";
        }
        const date = datePicker.value;
        if (date.length !== 0) {
            loadGallery({ page: currentPage, date: date, lang: langValue });
            return;
        }
        loadGallery({ page: currentPage, lang: langValue });
    }
}
function nextButtonClick(e) {
    const cont = document.querySelector("gallery");
    if (!cont) {
        throw "Gallery container not found";
    }
    let currentPage = cont.getAttribute("pageNumber");
    let queryString = "";
    const datePicker = document.querySelector("#datePicker");
    if (!datePicker) {
        throw "datePicker not found";
    }
    if (datePicker.value.length !== 0) {
        queryString = "?" + "date=" + datePicker.value;
    }

    const opt = document.querySelector("#langSelect option:checked");
    if (!opt) {
        alert("Select lang before switching");
        return;
    }
    let langValue = opt.value;

    let params = (new URL(document.location)).searchParams; 
    if (params.get("lang") === "all") {
        langValue = "all";
    }

    let lastPage;
    fetch("/api/gallery" + queryString)
        .then(r => r.json())
        .then(r => {
            lastPage = r.meta.lastPage;
        })
        .then(() => {
            console.log(currentPage, lastPage);
            console.log(queryString);
            if (currentPage < lastPage) {
                currentPage++;

                if (currentPage > 1) {
                    let currentPageSpan = document.querySelector("#currentPage");
                    currentPageSpan.innerText = currentPage;
                }

                let date = datePicker.value;
                if (date.length !== 0) {
                    loadGallery({ page: currentPage, date: date, lang: langValue });
                    return;
                }
                loadGallery({ page: currentPage, lang: langValue });
            }
        });
}

function addPictureClick(e) {
    const picFile = e.target.parentNode.querySelector("[name=pictureFile]");
    if (!picFile) {
        throw "picFile not found";
    }

    if (picFile.files.length == 0) { 
        alert("Выберите файл");
        return;
    }
    const fd = new FormData();
    fd.append("pictureFile", picFile.files[0]);

    for (let elem of [
        "pictureDescriptionUk",
        "pictureDescriptionEn",
        "pictureDescriptionRu"
    ]) {
        let picDescr = e.target.parentNode.querySelector(`[name=${elem}]`);
        if (!picDescr) {
            throw `${elem} not found`;
        }
        fd.append(elem, picDescr.value);
    }

    fetch("/api/gallery", {
        method: "post",
        headers: {

        },
        body: fd
    })
        .then(r => r.text())
        .then(() => {
            const cont = document.querySelector("gallery");
            if (!cont) {
                throw "Gallery container not found";
            }

            let currentPage = cont.getAttribute("pageNumber");
            loadGallery({ page: currentPage });
        });
}

function loadGallery(params) {
    let queryString = "";
    if (typeof params == 'object') {
        delimiter = "?";
        for (let prop in params) {
            queryString += delimiter + prop + "=" + params[prop];
            delimiter = "&";
        }
    }

    fetch("/api/gallery" + queryString)
        .then(r => r.text())
        .then(showGallery);
}

function showGallery(t) {
    const cont = document.querySelector("gallery");
    if (!cont) {
        throw "Gallery container not found";
    }
    let j;
    try {
        j = JSON.parse(t);
//console.log(j); return;
        if (j.warn.length != 0) {  // show warnings if exist
            console.log(j.warn);
        }
    } catch {
        console.log("JSON parse error");
        console.log(t);
        return;
    }
    const picTpl = `
        <div class='picture'>
            <img src='/pictures/{{filename}}' />
            <b>{{moment}}</b>
            <p>{{descr}}</p>
        </div>
    `;
    var contHTML = "";
    for (let picId in j.data) {
// console.log(picId, j.data[picId]); continue;
        let descr = "";
        for (let lang in j.data[picId].descr) {
            descr += lang + ":" 
            + j.data[picId].descr[lang] + "<br/>";
        }
        contHTML += picTpl
            .replace("{{filename}}", j.data[picId].filename)
            .replace("{{moment}}", j.data[picId].moment)
            .replace("{{descr}}", descr);
    }
    cont.innerHTML = contHTML;
    cont.setAttribute("pageNumber", j.meta.page);
}