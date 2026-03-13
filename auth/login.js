// login.js
function togglePasswordVisibility() {
  const passwordInput = document.getElementById('passwordInput');
  const eyeIcon = document.getElementById('eyeIcon');
  const eyeOffIcon = document.getElementById('eyeOffIcon');
  
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    eyeIcon.classList.add('hidden');
    eyeOffIcon.classList.remove('hidden');
  } else {
    passwordInput.type = 'password';
    eyeIcon.classList.remove('hidden');
    eyeOffIcon.classList.add('hidden');
  }
}
      // Simple validation
      function handleSubmit(event) {
        event.preventDefault();
        
        const email = document.getElementById('email');
        const password = document.getElementById('password');
        const emailError = document.getElementById('emailError');
        const passwordError = document.getElementById('passwordError');
        
        let isValid = true;
        
        // Email validation
        if (!email.value) {
          emailError.textContent = 'Email is required';
          emailError.classList.remove('hidden');
          email.classList.add('border-red-500');
          isValid = false;
        } else if (!email.value.includes('@') || !email.value.includes('.')) {
          emailError.textContent = 'Enter a valid email';
          emailError.classList.remove('hidden');
          email.classList.add('border-red-500');
          isValid = false;
        } else {
          emailError.classList.add('hidden');
          email.classList.remove('border-red-500');
        }
        
        // Password validation
        if (!password.value) {
          passwordError.textContent = 'Password is required';
          passwordError.classList.remove('hidden');
          password.classList.add('border-red-500');
          isValid = false;
        } else if (password.value.length < 6) {
          passwordError.textContent = 'Password must be at least 6 characters';
          passwordError.classList.remove('hidden');
          password.classList.add('border-red-500');
          isValid = false;
        } else {
          passwordError.classList.add('hidden');
          password.classList.remove('border-red-500');
        }
        
        // If valid, show success message
        if (isValid) {
          alert('Login successful!');
          // Here you would normally submit to server
        }
      }