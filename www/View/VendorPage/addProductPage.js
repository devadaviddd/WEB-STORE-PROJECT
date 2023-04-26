const form = document.querySelector("#new_product_form");

// function sendNewProduct() {
//   var xhr = new XMLHttpRequest();
//   xhr.open("POST", window.location.href);
//   xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
//   form.enctype = "multipart/form-data";
//   let formData = new FormData(form);
//   xhr.send(formData);
//   xhr.onreadystatechange = function () {
//     if (this.readyState == 4 && this.status == 200) {
//       // Typical action to be performed when the document is ready:
//       document.querySelector("#serverResponds").innerHTML =
//         "Congrat! Your Product Has Been Posted";
//       turnOnToast();
//     } else {
//       document.getElementById("announceToast").classList.toggle("bg-danger");
//       document.querySelector("#serverResponds").innerHTML =
//         "Server Can't add your product";
//       turnOnToast();
//     }
//   };
// }

function turnOnToast() {
  let announceToast = new bootstrap.Toast(document.querySelector(".toast"));
  announceToast.show();
}
