// Slideshow functionality
let slideIndex = 0;
showSlides();

function showSlides() {
    let slides = document.getElementsByClassName("slide");
    for (let i = 0; i < slides.length; i++) {
        slides[i].style.opacity = "0";
    }
    slideIndex++;
    if (slideIndex > slides.length) {slideIndex = 1}
    slides[slideIndex-1].style.opacity = "1";
    setTimeout(showSlides, 5000); // Change image every 5 seconds
}

// Contact form submission
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const responseDiv = document.getElementById('formResponse');
    
    // Show loading state
    responseDiv.textContent = 'Sending your message...';
    responseDiv.className = 'form-response';
    responseDiv.style.display = 'block';
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            responseDiv.className = 'form-response success';
            responseDiv.textContent = data.message;
            form.reset();
        } else {
            responseDiv.className = 'form-response error';
            if (data.errors) {
                // Handle field-specific errors
                let errorMessages = [];
                for (const [field, message] of Object.entries(data.errors)) {
                    errorMessages.push(message);
                }
                responseDiv.textContent = errorMessages.join('\n');
            } else {
                responseDiv.textContent = data.message || 'An error occurred. Please try again.';
            }
        }
    })
    .catch(error => {
        responseDiv.className = 'form-response error';
        responseDiv.textContent = 'Network error. Please try again later.';
    });
    
    // Hide message after 5 seconds
    setTimeout(() => {
        if (responseDiv.className.includes('success')) {
            responseDiv.style.display = 'none';
        }
    }, 5000);
});

// Mobile menu toggle (for smaller screens)
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.createElement('div');
    menuToggle.className = 'menu-toggle';
    menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
    menuToggle.style.display = 'none';
    document.querySelector('header').appendChild(menuToggle);
    
    const nav = document.querySelector('nav ul');
    
    function toggleMenu() {
        if (nav.style.display === 'flex') {
            nav.style.display = 'none';
        } else {
            nav.style.display = 'flex';
        }
    }
    
    menuToggle.addEventListener('click', toggleMenu);
    
    function checkScreenSize() {
        if (window.innerWidth <= 768) {
            menuToggle.style.display = 'block';
            nav.style.display = 'none';
        } else {
            menuToggle.style.display = 'none';
            nav.style.display = 'flex';
        }
    }
    
    window.addEventListener('resize', checkScreenSize);
    checkScreenSize();
});