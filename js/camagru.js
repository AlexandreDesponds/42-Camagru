var cam = {
    count: 0,
    iconSelect: 0,
    mouseIcon: 0,
    mouseIconZoom: 1,
    div: "",
    form: "",
    upload: "",
    mediaOption: {
        audio: false,
        video: {
            width: 500,
            height: 500
        }
    }
};

window.onload = function () {
    initCam();
    initIcons();
    initMouseIcon();
    document.querySelector("#save").addEventListener("submit", save);
    document.querySelector("#selfie").addEventListener("click", iconAdd);
    document.querySelector("#selfie").addEventListener("mousemove", mouseIcon);
    document.querySelector("#selfie").addEventListener("mouseout", mouseOut);
    document.querySelector("#selfie").addEventListener("wheel", zoom);
    document.querySelector("#open-upload").addEventListener("click", openUpload);
    document.querySelector("#upload").addEventListener("change", upload);
    cam.form = document.querySelector("#save");
}

function openUpload(){
    document.querySelector("#upload").click()
}

function upload(event){
    var reader = new FileReader();
    reader.onload = loaded;
    reader.readAsDataURL(this.files[0]);
    cam.div.className += 'hidden ';
}

function loaded(event){
    var upload = document.querySelector("#img-upload");
    upload.src = event.target.result;
    cam.upload = event.target.result;
    if (upload.width > 500 || upload.height > 375) {
        upload.src = '';
        var error = document.createElement('div');
        error.innerHTML = "l'image est supérieur à 500px / 375px";
        error.className = 'error';
        document.querySelector(".container").insertBefore(error, document.querySelector(".container").firstChild);
    } else if (upload.width < 30 || upload.height < 30) {
        upload.src = '';
        var error = document.createElement('div');
        error.innerHTML = "ce n'est pas une image";
        error.className = 'error';
        document.querySelector(".container").insertBefore(error, document.querySelector(".container").firstChild);
    }
}

function initMouseIcon() {
    cam.mouseIcon = document.createElement("img");
    cam.mouseIcon.style.top = '0px';
    cam.mouseIcon.style.left = '0px';
    cam.mouseIcon.id = "mouseIcon";
    document.querySelector("#selfie").appendChild(cam.mouseIcon);
}

function mouseOut(event) {
    cam.mouseIcon.style.display = 'none';
}

function mouseIcon(event) {
    if (cam.iconSelect.src){
        cam.mouseIcon.style.display = 'block';
        cam.mouseIcon.src = cam.iconSelect.src;
        var selfie = document.querySelector('#selfie');
        cam.mouseIcon.style.top = (event.pageY - cam.mouseIcon.height / 2) - selfie.offsetTop - selfie.offsetParent.offsetTop + 'px';
        cam.mouseIcon.style.left = (event.pageX - cam.mouseIcon.width / 2) - selfie.offsetLeft - selfie.offsetParent.offsetLeft + 'px';
    }
}

function zoom(event) {
    if (event.deltaY > 0) {
        cam.mouseIcon.width += 3;
    } if (event.deltaY < 0) {
        cam.mouseIcon.width -= 3;
    }
    var selfie = document.querySelector('#selfie');
    cam.mouseIcon.style.top = (event.pageY - cam.mouseIcon.height / 2) - selfie.offsetTop - selfie.offsetParent.offsetTop + 'px';
    cam.mouseIcon.style.left = (event.pageX - cam.mouseIcon.width / 2) - selfie.offsetLeft - selfie.offsetParent.offsetLeft + 'px'
    event.preventDefault();
}


function initIcons() {
    var icons = document.querySelectorAll("#icons img");
    for (var i = 0; i < icons.length; i++) {
        icons[i].addEventListener("click", function () {
            cam.iconSelect = this;
        });
    }
}

function iconAdd(event) {
    if (cam.iconSelect.src) {
        var picture = document.createElement("img");
        picture.src = cam.iconSelect.src;
        picture.width = cam.mouseIcon.width;
        picture.height = cam.mouseIcon.height;
        var selfie = document.querySelector('#selfie');
        picture.style.top = (event.pageY - picture.height / 2) - selfie.offsetTop - selfie.offsetParent.offsetTop + 'px';
        picture.style.left = (event.pageX - picture.width / 2) - selfie.offsetLeft - selfie.offsetParent.offsetLeft + 'px';
        document.querySelector("#selfie").appendChild(picture);

        //name
        var form = document.createElement("input");
        form.name = 'img-' + cam.count + '-name';
        form.setAttribute('value', cam.iconSelect.src);
        form.setAttribute('type', 'hidden');
        cam.form.appendChild(form);

        //position
        var form = document.createElement("input");
        form.name = 'img-' + cam.count + '-x';
        form.setAttribute('value', picture.style.top);
        form.setAttribute('type', 'hidden');
        cam.form.appendChild(form);
        var form = document.createElement("input");
        form.name = 'img-' + cam.count + '-y';
        form.setAttribute('value', picture.style.left);
        form.setAttribute('type', 'hidden');
        cam.form.appendChild(form);

        //size
        var form = document.createElement("input");
        form.name = 'img-' + cam.count + '-height';
        form.setAttribute('value', picture.height);
        form.setAttribute('type', 'hidden');
        cam.form.appendChild(form);
        var form = document.createElement("input");
        form.name = 'img-' + cam.count + '-width';
        form.setAttribute('value', picture.width);
        form.setAttribute('type', 'hidden');
        cam.form.appendChild(form);

        cam.count = cam.count + 1;
        document.querySelector("#takeSelfie").disabled = false;
        document.querySelector("#takeSelfie").className = '';
    }
}

function save(event) {
    var img = document.createElement("canvas");
    var form = document.querySelector("#img");
    if (cam.upload) {
        console.log(cam.upload);
        form.value = cam.upload;
    } else {
        img.width = cam.mediaOption.video.width;
        img.height = cam.mediaOption.video.height;
        img.getContext('2d').drawImage(cam.div, 0, 0, img.width, img.height);
        form.value = img.toDataURL("image/png", 1);
    }
    return true;
}

function saveIcones(img) {
    var icons = document.querySelectorAll("#selfie img");
    for (var i = 0; i < icons.length; i++) {
        if (icons[i].id != 'mouseIcon')
            img.getContext('2d').drawImage(icons[i], icons[i].offsetLeft, icons[i].offsetTop, icons[i].width, icons[i].height);
    }
}

function initCam() {
    cam.div = document.querySelector("#cam");
    navigator.getMedia = (
        navigator.getUserMedia ||
        navigator.webkitGetUserMedia ||
        navigator.mozGetUserMedia ||
        navigator.msGetUserMedia
    );
    if (navigator.getMedia) {
        navigator.getMedia(cam.mediaOption,
            function (stream) {
                //TODO: attention pour firefox ou IE
                cam.div.src = window.URL.createObjectURL(stream);
                cam.div.play();
            },
            function (error) {
                console.log("error getMedia " + error.name);
            }
        );
    } else {
        console.log('error Ho');
    }

    cam.div.addEventListener('canplay', function (ev) {
        cam.mediaOption.video.height = cam.div.videoHeight / (cam.div.videoWidth / cam.mediaOption.video.width);
        cam.div.setAttribute('width', cam.mediaOption.video.width);
        cam.div.setAttribute('height', cam.mediaOption.video.height);
        document.querySelector("#selfie").style.width = cam.mediaOption.video.width + 'px';
        document.querySelector("#selfie").style.height = cam.mediaOption.video.height + 'px';
    }, false);
}