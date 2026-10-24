

//back to top button
const backToTop = document.getElementById("backToTop");

window.addEventListener("scroll", () => {
    if (window.scrollY > 500) {
        backToTop.classList.remove("opacity-0", "translate-y-4", "pointer-events-none");
    } else {
        backToTop.classList.add("opacity-0", "translate-y-4", "pointer-events-none");
    }
});

backToTop.addEventListener("click", () => {
    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
});