// Password visibility toggle
window.togglePasswordVisibility = function(button) {
  const container = button.closest('.relative');
  const passwordInput = container.querySelector('input[type="password"], input[type="text"]');
  const eyeIcon = button.querySelector('.eyeIcon');
  const eyeOffIcon = button.querySelector('.eyeOffIcon');
  
  if (passwordInput && eyeIcon && eyeOffIcon) {
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
};

// Form submission handler
window.handleSubmit = function(event) {
  event.preventDefault();
  
  // Get form elements
  const email = document.getElementById('email');
  const password = document.getElementById('password');
  const confirmPassword = document.getElementById('confirmPassword');
  
  // Get error elements
  const emailError = document.getElementById('emailError');
  const passwordError = document.getElementById('passwordError');
  const confirmPasswordError = document.getElementById('confirmPasswordError');
  
  // Reset all errors and borders
  emailError.classList.add('hidden');
  passwordError.classList.add('hidden');
  confirmPasswordError.classList.add('hidden');
  
  email.classList.remove('border-red-500');
  password.classList.remove('border-red-500');
  confirmPassword.classList.remove('border-red-500');
  
  let isValid = true;
  
  // Email validation
  if (!email.value.trim()) {
    showError(email, emailError, 'Email is required');
    isValid = false;
  } else if (!isValidEmail(email.value)) {
    showError(email, emailError, 'Please enter a valid email address');
    isValid = false;
  }
  
  // Password validation
  if (!password.value) {
    showError(password, passwordError, 'Password is required');
    isValid = false;
  } else if (password.value.length < 8) {
    showError(password, passwordError, 'Password must be at least 8 characters');
    isValid = false;
  } else if (!isStrongPassword(password.value)) {
    showError(password, passwordError, 'Password must contain at least 1 uppercase, 1 lowercase, and 1 number');
    isValid = false;
  }
  
  // Confirm password validation
  if (!confirmPassword.value) {
    showError(confirmPassword, confirmPasswordError, 'Please confirm your password');
    isValid = false;
  } else if (password.value !== confirmPassword.value) {
    showError(confirmPassword, confirmPasswordError, 'Passwords do not match');
    isValid = false;
  }
  
  // If valid, submit to server
  if (isValid) {
    signUpUser(email.value, password.value);
  }
};

// Helper function to show errors
function showError(inputElement, errorElement, message) {
  inputElement.classList.add('border-red-500');
  errorElement.textContent = message;
  errorElement.classList.remove('hidden');
}

// Email validation helper
function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

// Password strength validation
function isStrongPassword(password) {
  const hasUppercase = /[A-Z]/.test(password);
  const hasLowercase = /[a-z]/.test(password);
  const hasNumber = /\d/.test(password);
  
  return hasUppercase && hasLowercase && hasNumber;
}

// Show success message
function showSuccessMessage(message) {
  const existingMessage = document.querySelector('.success-message');
  if (existingMessage) {
    existingMessage.remove();
  }
  
  const successDiv = document.createElement('div');
  successDiv.className = 'success-message mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center gap-2';
  successDiv.innerHTML = `
    <svg class="h-5 w-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
    </svg>
    <span>${message}</span>
  `;
  
  const form = document.getElementById('signUpForm');
  form.insertBefore(successDiv, form.firstChild);
  
  setTimeout(() => {
    if (successDiv.parentNode) {
      successDiv.remove();
    }
  }, 5000);
}

// Sign up function
function signUpUser(email, password) {
  const submitButton = document.querySelector('button[type="submit"]');
  const originalText = submitButton.textContent;
  submitButton.textContent = 'Creating Account...';
  submitButton.disabled = true;
  
  setTimeout(() => {
    if (email === 'test@example.com') {
      alert('This email is already registered. Please use a different email.');
      submitButton.textContent = originalText;
      submitButton.disabled = false;
    } else {
      submitButton.textContent = originalText;
      submitButton.disabled = false;
      
      showSuccessMessage('Account created successfully! Redirecting to login...');
      
      document.getElementById('email').value = '';
      document.getElementById('password').value = '';
      document.getElementById('confirmPassword').value = '';
      
      setTimeout(() => {
        window.location.href = '../auth/login.html';
      }, 2000);
    }
  }, 1500);
}

// Real-time email validation
document.getElementById('email').addEventListener('input', function() {
  const emailError = document.getElementById('emailError');
  if (this.value && !isValidEmail(this.value)) {
    this.classList.add('border-red-500');
    emailError.textContent = 'Invalid email format';
    emailError.classList.remove('hidden');
  } else {
    this.classList.remove('border-red-500');
    emailError.classList.add('hidden');
  }
});

// Real-time password validation
document.getElementById('password').addEventListener('input', function() {
  const passwordError = document.getElementById('passwordError');
  
  if (this.value && this.value.length < 8) {
    this.classList.add('border-red-500');
    passwordError.textContent = 'Password must be at least 8 characters';
    passwordError.classList.remove('hidden');
  } else if (this.value && !isStrongPassword(this.value)) {
    this.classList.add('border-red-500');
    passwordError.textContent = 'Password needs uppercase, lowercase, and number';
    passwordError.classList.remove('hidden');
  } else {
    this.classList.remove('border-red-500');
    passwordError.classList.add('hidden');
  }
});

// Real-time confirm password validation
document.getElementById('confirmPassword').addEventListener('input', function() {
  const password = document.getElementById('password').value;
  const confirmPasswordError = document.getElementById('confirmPasswordError');
  
  if (this.value && this.value !== password) {
    this.classList.add('border-red-500');
    confirmPasswordError.textContent = 'Passwords do not match';
    confirmPasswordError.classList.remove('hidden');
  } else {
    this.classList.remove('border-red-500');
    confirmPasswordError.classList.add('hidden');
  }
});