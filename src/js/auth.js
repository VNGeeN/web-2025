let isPasswordShown = false;

const togglePasswordVisibility = (event) => {
    const button = event.target;
    const input = document.getElementById("password");

    isPasswordShown = !isPasswordShown;

    if (isPasswordShown) {
        input?.setAttribute("type", "text");
        button?.classList.add("field__eye-button-active");
    } else {
        input?.setAttribute("type", "password");
        button?.classList.remove("field__eye-button-active");
    }
};

document.addEventListener("DOMContentLoaded", () => {
    const eyeButton = document.getElementById("eye-button");
    eyeButton.addEventListener("click", togglePasswordVisibility);
});