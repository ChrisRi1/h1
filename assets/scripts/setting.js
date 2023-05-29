function showPanel(n) {
  let panel = document.getElementsByClassName('panel');
  for(let i=0; i<panel.length; i++) {
    if(i==n)
      panel[i].style.display="inline-block";
    else
      panel[i].style.display="none";
  }
}

function togglePanel(e,name) {
  document.getElementById('activePanel').id="";
  e.id="activePanel";
  showPanel(name)
}
window.onload = function() {
  showPanel(0);
};
