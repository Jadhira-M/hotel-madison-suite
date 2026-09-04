// Navbar cambia al hacer scroll

window.addEventListener("scroll", function(){

    const navbar = document.querySelector(".navbar");

    if(window.scrollY > 80){

        navbar.classList.add("shadow");

    }else{

        navbar.classList.remove("shadow");

    }

});