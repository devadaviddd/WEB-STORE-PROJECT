const announce = document.querySelector("#announce");
// const announceToast = document.querySelector("#announceToast");
function cancelOrder(orderId) {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", window.location.href);
  xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
  let data = { cancel_order_id: orderId };
  data = JSON.stringify(data);
  xhr.send(data);

  xhr.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      // Typical action to be performed when the document is ready:
      document.getElementById(`${orderId}-item-card`).remove();
      announce.innerHTML = `${orderId} is canceled`;
      turnOnToast();
    } else {
      document.getElementById("announceToast").classList.toggle("bg-danger");
      announce.innerHTML = `Server can't cancel ${orderId}`;
      turnOnToast();
    }
  };
}

function deliveriedOrder(orderId) {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", window.location.href);
  xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
  let data = { delivered_order_id: orderId };
  data = JSON.stringify(data);
  xhr.send(data);

  xhr.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      // Typical action to be performed when the document is ready:
      document.getElementById(`${orderId}-item-card`).remove();
      announce.innerHTML = `${orderId} is delivered`;
      turnOnToast();
    } else {
      document.getElementById("announceToast").classList.toggle("bg-danger");
      announce.innerHTML = `Server can't set  ${orderId} as delivered`;
      turnOnToast();
    }
  };
}

// learn from https://www.w3schools.com/bootstrap5/tryit.asp?filename=trybs_toast&stacked=h
function turnOnToast() {
  var announceToast = new bootstrap.Toast(document.querySelector(".toast"));
  announceToast.show();
}
