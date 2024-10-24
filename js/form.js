const signUpButton = document.getElementById('signUp');
const signInButton = document.getElementById('signIn');
const container = document.getElementById('container');

function toggleForm(section) {
    if (section === 'signup') {
        container.classList.add('right-panel-active');
    } else {
        container.classList.remove('right-panel-active');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const section = urlParams.get('section');
    if (section === 'signup') {
        toggleForm('signup');
    } else {
        toggleForm('signin');
    }
});

signUpButton.addEventListener('click', () => {
    toggleForm('signup');
});

signInButton.addEventListener('click', () => {
    toggleForm('signin');
});
