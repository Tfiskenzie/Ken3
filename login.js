// Handle the form submission
const form = document.getElementById('login-form');
const googleLogin = document.querySelector('.google-login');

form.addEventListener('submit', (e) => {
  e.preventDefault();
  const email = document.getElementById('email').value;
  const password = document.getElementById('password').value;

  // Example validation
  if (email && password) {
    alert('Login successful');
  } else {
    alert('Please fill in all fields');
  }
});

googleLogin.addEventListener('click', () => {
  alert('Redirecting to Google login...');
});
