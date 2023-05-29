window.onload = function() {
  var searchResults = document.getElementById('search_result');
  document.onclick = function(e) {
    if (e.target.id !== 'search_container') {
      searchResults.style.visibility = "hidden";
    }
  };
};

function showPopup(state) {
  var popup = document.getElementById("reset_popup");
  if (state)
    popup.style.visibility = "visible";
  else
    popup.style.visibility = "hidden";
}

function likeToggle(state, id) {
  var like = document.getElementById(`like${id}`);
  var liked = document.getElementById(`liked${id}`);
  var count = document.getElementById(`like_count${id}`);

  var http = new XMLHttpRequest();
  var url = '../core/actions.php';
  var params = '';

  if (state) {
    like.style.display = "none";
    liked.style.display = "inline-block";
    ++count.innerText;
    var params = 'cmd=like&postid=' + id;
  } else {
    liked.style.display = "none";
    like.style.display = "inline-block";
    --count.innerText;
    var params = 'cmd=unlike&postid=' + id;
  }
  http.open('POST', url, true);
  http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  http.onreadystatechange = function() {
    if (http.readyState == 4 && http.status == 200) {
      //alert(http.responseText);
    }
  }
  http.send(params);
}

function followToggle(id) {
  var btn = document.getElementById("follow_btn");

  var http = new XMLHttpRequest();
  var url = '../core/actions.php';
  var params = '';

  if (btn.value == "true") {
    btn.value = "false";
    btn.innerHTML = "Follow";
    btn.style.backgroundColor = "#fff";
    var params = 'cmd=unfollow&uid=' + id;
  } else {
    btn.value = "true";
    btn.innerHTML = "Following";
    btn.style.backgroundColor = "#eee";
    var params = 'cmd=follow&uid=' + id;
  }

  http.open('POST', url, true);
  http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  http.onreadystatechange = function() {
    if (http.readyState == 4 && http.status == 200) {
      //alert(http.responseText);
    }
  }
  http.send(params);

}

function liveSearch(a) {
  var state = document.getElementById('search_result');
  var query = document.getElementById('search_query').value;

  var http = new XMLHttpRequest();
  var url = '../core/actions.php';
  var params = 'cmd=search&q=' + query;

  if (state.style.visibility == "hidden" || a == false)
    state.style.visibility = "visible";
  else
    state.style.visibility = "hidden";

  http.open('POST', url, true);
  http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  http.onreadystatechange = function() {
    if (http.readyState == 4 && http.status == 200) {
      document.getElementById('search_result').innerHTML = http.responseText;
    }
  }
  http.send(params);
}

function showPostEditOptions(id) {
  let popOverlay = document.getElementById("popOverlay");
  let postEditOptions = document.getElementById(`postEditOption${id}`);
  popOverlay.style.visibility = "visible";
  postEditOptions.style.visibility = "visible";

  popOverlay.onclick = function() {
    popOverlay.style.visibility = "hidden";
    postEditOptions.style.visibility = "hidden";
    document.body.style.filter = "inherit";
  }
}

function loadPreview(e) {
  const file = e.files[0];
  document.getElementById('file_name').innerHTML = file.name;
  if (file) {
    const reader = new FileReader();

    reader.addEventListener("load", function() {
      document.getElementById("file_upload_preview").setAttribute("src", this.result);
    });
    reader.readAsDataURL(file);
  }
}

function resetFileInput() {
  document.getElementById("file_name").innerHTML = 'Add Image';
  document.getElementById("file_upload_preview").setAttribute("src", "");
}

function postComment(e) {

  let id = e['postId'].value;
  let comment = e['comment'].value;

  if (id == "" || comment == "") return false;

  let comment_feed = document.getElementById(`comment_feed${id}`);
  let userTag = document.getElementById('user_name').innerText;

  var http = new XMLHttpRequest();
  var url = '../core/actions.php';
  var params = 'cmd=comment&text='+comment+'&id='+id;

  http.open('POST', url, true);
  http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  http.onreadystatechange = function() {
    if (http.readyState == 4 && http.status == 200) {
      comment_feed.innerHTML = http.responseText;
    }
  }
  http.send(params);
  e['comment'].value = "";
  return false;
}
