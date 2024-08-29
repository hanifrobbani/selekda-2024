const nav = document.getElementById("nav");


window.addEventListener('scroll', function(){
    if (window.scrollY === 0) {
        nav.classList.remove("shadow-nav");
    }else {
      nav.classList.add("shadow-nav");
    }
})