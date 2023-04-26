
const image_input = document.querySelector(".btnUpload");
const displayImage = document.querySelector(".image");
var uploaded_image = "";

image_input.addEventListener('change', function(){
    const reader = new FileReader();
    reader.addEventListener("load", () => {
        uploaded_image = reader.result;
        displayImage.style.backgroundImage = `url(${uploaded_image})`;
    });
    reader.readAsDataURL(this.files[0]);
})

// username: contains only letters (lower and upper case) and digits, has a length from 8 to 15 characters, unique
const usernameInput =document.querySelector("#username");
const usernameValid = document.querySelector("#usernameValid");
const user_pList = usernameValid.getElementsByTagName('p');

// password: contains at least one upper case letter, at least one lower case letter, at least one digit, at least one special letter in the set !@#$%^&*, NO other kind of characters, has a length from 8 to 20 characters
const passwordInput =document.querySelector("#password");
const passwordValid = document.querySelector("#passwordValid");
const password_pList = passwordValid.getElementsByTagName('p');

const otherInputs = document.getElementsByTagName("input");
const dropdowns = document.querySelectorAll(".dropdown");



for(let i = 0; i < dropdowns.length; i++) {
    if(dropdowns[i].classList.contains('remainValid')) {
        let p_list = dropdowns[i].getElementsByTagName('p');
        otherInputs[i].addEventListener('input', () => {
            let pattern = new RegExp('^.{5,}$');
            let val = otherInputs[i].value;
            if(val.match(pattern)) {
                p_list[0].style.color = "green";
            } else {
                p_list[0].style.color = "red";
            }
        })
        console.log(dropdowns[i]);

    }
}


usernameInput.addEventListener('input',() => {
    let pattern = new RegExp('^(?=.*\\d)(?=.*[A-Z])(?=.*[a-z])(.{8,15})$');
    let val = usernameInput.value;
    if(val.match(pattern)) {
        for(let i = 0 ; i < 4; i++) {
            user_pList[i].style.color = "green";
        }
    } else {
        for(let i = 0 ; i < 4; i++) {
            user_pList[i].style.color = "red";
        }
        if(val.match('[a-z]+')) {
            user_pList[0].style.color = "green";
        }
        if(val.match('[A-Z]+')) {
            user_pList[1].style.color = "green";
        }
        if(val.match('\\d')) {
            user_pList[2].style.color = "green";
        }
        if(val.match('^.{8,15}$')) {
            user_pList[3].style.color = "green";
        }
    }
})


passwordInput.addEventListener('input',() => {
    let pattern = new RegExp('^(?=.*\\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[!@#$%^&*])(.{8,20})$');
    let val = passwordInput.value;
    if(val.match(pattern)) {
        for(let i = 0 ; i < 5; i++) {
            password_pList[i].style.color = "green";
        }
    } else {
        for(let i = 0 ; i < 5; i++) {
            password_pList[i].style.color = "red";
        }
        if(val.match('[a-z]+')) {
            password_pList[0].style.color = "green";
        }
        if(val.match('[A-Z]+')) {
            password_pList[1].style.color = "green";
        }
        if(val.match('\\d')) {
            password_pList[2].style.color = "green";
        } 
        if(val.match('[!@#$%^&*]+')) {
            password_pList[3].style.color = "green";
        }
        if(val.match('^.{8,20}$')) {
            password_pList[4].style.color = "green";
        }
    }
})

const btnSubmit = document.querySelector('#submitBtn');

btnSubmit.addEventListener('click', (e) => {
    let username = document.querySelector("#username").value;
    let password = document.querySelector("#password").value;
    let password_repeat = document.querySelector("#password-repeat").value;
    let businessName = document.querySelector("#businessName").value;
    let businessAddress = document.querySelector("#businessAddress").value;
    let profile_picture = document.querySelector("#profile-picture").value;
    console.log(username);
    console.log(password);
    console.log(password_repeat);
    console.log(businessAddress);
    console.log(businessName);
    console.log(businessAddress);
    console.log("profile picture: " + profile_picture);

    const patternOther = new RegExp('^.{5,}$');
    const patternUser = new RegExp('^(?=.*\\d)(?=.*[A-Z])(?=.*[a-z])(.{8,15})$');
    const patternPass = new RegExp('^(?=.*\\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[!@#$%^&*])(.{8,20})$');

    if(username.match(patternUser) && password.match(patternPass) && (password === password_repeat) && businessName.match(patternOther) && (profile_picture.length != 0) && businessAddress.match(patternOther))  {
        console.log("pass");
    } else {
        var notification = document.getElementById("snackbar");
        

        if(username == "" || password == "" || password_repeat == "" || businessName == "" || profile_picture == "" || businessAddress == "") {
            notification.innerHTML = "Please Fill in the Form!";
            notification.className = "show";
            setTimeout(function(){ notification.className = notification.className.replace("show", ""); }, 3000);
        } else {
            if(password_repeat !== password) {
                notification.innerHTML = "Password does not match!";
                notification.className = "show";
                setTimeout(function(){ notification.className = notification.className.replace("show", ""); }, 3000);
            } else {
                notification.innerHTML = "Invalid Input!";
                notification.className = "show";
                setTimeout(function(){ notification.className = notification.className.replace("show", ""); }, 3000);
            }
        }
        console.log("failed");
        e.preventDefault();
    }
    
})