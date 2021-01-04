// simple js
const form = document.querySelector("#osom-form");
let firstname = form.elements.namedItem("firstname");
let lastname = form.elements.namedItem("lastname");
let user_login = form.elements.namedItem("user_login");
let e_mail = form.elements.namedItem("e_mail");
let city = form.elements.namedItem("city");
let policy_agree = form.elements.namedItem("policy_agree");
const pattern_mail = /[\w-\.]+@([\w-]+\.)+[\w-]{2,4}/g;
form.addEventListener('submit', function (e) {

  alert('Formularz zapisany');
});
let submit = form.elements.namedItem('submit');


firstname.addEventListener('input',validate);
lastname.addEventListener('input',validate);
user_login.addEventListener('input',validate);
e_mail.addEventListener('input',validate);
policy_agree.addEventListener('input',validate_policy);

firstname.addEventListener('input',validateBtn);
lastname.addEventListener('input',validateBtn);
user_login.addEventListener('input',validateBtn);
e_mail.addEventListener('input',validateBtn);
policy_agree.addEventListener('input',validateBtn);

function validate(e) {
  let target = e.target;
  if(target.name != 'e_mail') {
    if (target.checkValidity()) {
        target.classList.add("valid");
        target.classList.remove("invalid");
    } else {
        target.classList.remove("valid");
        target.classList.add("invalid");
    }
  }
  if(target.name == 'e_mail') {
    if (target.checkValidity()) {
        target.classList.add("valid");
        target.classList.remove("invalid");
    } else {
        target.classList.remove("valid");
        target.classList.add("invalid");
    }
  }
}
function validate_policy(e) {
  let target = e.target;
  if (target.checkValidity()) {
      target.classList.add("valid");
      target.classList.remove("invalid");
  } else {
      target.classList.remove("valid");
      target.classList.add("invalid");
  }
}
function validateBtn(e) {
  if (firstname.checkValidity() && lastname.checkValidity() && user_login.checkValidity() && e_mail.checkValidity() && policy_agree.checkValidity()) {
    document.getElementById('submit').disabled = false;
  } else {
    document.getElementById('submit').disabled = true;
  }
}
