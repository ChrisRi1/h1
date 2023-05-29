function validateRegister(form = document.forms["registerForm"]) {
  let error = false;
  if(!validName())
    error = true;
  if(!validUserName())
    error = true;
  if(!validEmail())
    error = true;
  if(!validPassword())
    error = true;
  if(!validRePassword())
    error = true;
  if(!validBirthday())
    error = true;

  return !error;
}

function validateLogin(form = document.forms["loginForm"]) {
  let error = false;

  if(!validUserName(form["username"]))
    error = true;

  if(!validPassword(form["password"]))
    error = true;

  return !error;
}

function validName(n=document.forms["registerForm"]["fullname"]) {
  let error = document.getElementById('nameInputFormError');
  if(n.value!="") {
    if(n.value.length > 0 && n.value.length <= 30) {
      if(/^[a-zA-Z ]+$/.test(n.value)) {
        error.style.display = "none";
        return true;
      }
      else {
        error.innerText = "Name can only have alphabets and space.";
        error.style.display = "block";
        return false;
      }
    }
    else {
      error.innerText = "Name should be atmost 30 characters long.";
      error.style.display = "block";
      return false;
    }
  }
  else {
    error.innerText = "Name can't be empty!";
    error.style.display = "block";
    return false;
  }
}



function validUserName(n=document.forms["registerForm"]["username"]) {
  let error = document.getElementById('usernameInputFormError');
  if(n.value!="") {
    if(n.value.length >= 6 && n.value.length <= 20) {
      if(/^[a-zA-Z0-9]+$/.test(n.value)) {
        error.style.display = "none";
        return true;
      }
      else {
        error.innerText = "Username can only be alphanumeric!";
        error.style.display = "block";
        return false;
      }
    }
    else {
      error.innerText = "Username should be 6-20 characters long.";
      error.style.display = "block";
      return false;
    }
  }
  else {
    error.innerText = "Username can't be empty!";
    error.style.display = "block";
    return false;
  }
}



function validEmail(n=document.forms["registerForm"]["email"]) {
  let error = document.getElementById('emailInputFormError');
  if(n.value!="") {
    if(n.value.length > 7 && n.value.length <= 100) {
      var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      if( re.test( (n.value).toLowerCase() ) ) {
        error.style.display = "none";
        return true;
      }
      else {
        error.innerText = "Email is not valid!";
        error.style.display = "block";
        return false;
      }
    }
    else {
      error.innerText = "Email should be 7-100 characters long.";
      error.style.display = "block";
      return false;
    }
  }
  else {
    error.innerText = "Email can't be empty!";
    error.style.display = "block";
    return false;
  }
}


function goodPassword(s) {
  var re = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/;
  if(re.test(s)) return true;
  else return false;
}



function validPassword(n=document.forms["registerForm"]["password"]) {
  let error = document.getElementById('passwordInputFormError');
  if(n.value!="") {
    if(n.value.length >= 6 && n.value.length <= 20) {
      if( goodPassword(n.value) ) {
        error.style.display = "none";
        return true;
      }
      else {
        error.innerText = "Password should contain atleast an uppercase, a lowercase, a special character, and a number!";
        error.style.display = "block";
        return false;
      }
    }
    else {
      error.innerText = "Password should be 6-15 characters long.";
      error.style.display = "block";
      return false;
    }
  }
  else {
    error.innerText = "Password can't be empty!";
    error.style.display = "block";
    return false;
  }
}



function validRePassword(n=document.forms["registerForm"]) {
  let error = document.getElementById('rpasswordInputFormError');
  if(n["rpassword"].value!="") {
    if(n["rpassword"].value == n["password"].value) {
      error.style.display = "none";
      return true;
    }
    else {
      error.innerText = "Repeat Password should match Password!";
      error.style.display = "block";
      return false;
    }
  }
  else {
    error.innerText = "Repeated password can't be empty!";
    error.style.display = "block";
    return false;
  }
}



function validBirthday(n=document.forms["registerForm"]) {
  let error = document.getElementById('birthdayInputFormError');
  if(n["dob"].value!="") {
    let age = ~~( ( Date.now() - (+new Date(n["dob"].value)) ) / (31557600000) );
    if(age>13) {
      error.style.display = "none";
      return true;
    }
    else {
      error.innerText = "Your age is less than 13 year!";
      error.style.display = "block";
      return false;
    }
  }
  else {
      error.innerText = "Date of birth is required!";
      error.style.display = "block";
      return false;
  }
}
