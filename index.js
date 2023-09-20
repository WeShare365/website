window.onload = function() {
    const faqQuestions = document.querySelectorAll(".faq-question");

    faqQuestions.forEach(question => {
        question.addEventListener("click", function() {
            const answer = this.nextElementSibling;
            const icon = this.querySelector('.dropdown-icon');
            answer.style.display = answer.style.display === "block" ? "none" : "block";
            icon.classList.toggle('flipped');
        });
    });
};
