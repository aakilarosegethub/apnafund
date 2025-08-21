<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }} - Apna Fund</title>
    <link rel="stylesheet" href="{{ asset('apnafund/assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('apnafund/assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('apnafund/assets/images/fav-icon.png') }}" type="image/png">
    <!-- Add iziToast CSS -->
    <link rel="stylesheet" href="{{ asset('assets/universal/css/iziToast.min.css') }}">
    <style>
        body {
            background: #ffffff;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            line-height: 1.6;
            color: #333;
        }

        /* Header */
        .main-header {
            background: #05ce78;
            padding: 25px 0;
            margin: 0;
            border: none;
        }

        .main-header .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .main-header .logo {
            text-align: center;
            margin: 0;
        }

        .main-header .logo-img {
            height: 40px;
            width: auto;
        }

        .main-header .tagline {
            color: #fff;
            font-size: 12px;
            text-align: center;
            margin-top: 5px;
            font-weight: 400;
        }

        /* Registration Container */
        .registration-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 40px 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 40px;
            gap: 10px;
        }

        .step-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #e9ecef;
            transition: all 0.3s ease;
        }

        .step-dot.active {
            background: #05ce78;
            transform: scale(1.2);
        }

        .step-dot.completed {
            background: #05ce78;
        }

        .step-title {
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1rem;
        }

        .step-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 2rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .form-control {
            width: 100%;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #fff;
        }

        .form-control:focus {
            outline: none;
            border-color: #05ce78;
            box-shadow: 0 0 0 3px rgba(5, 206, 120, 0.1);
        }

        .form-control select {
            cursor: pointer;
        }

        .form-control textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* Button Styles */
        .btn-theme {
            background: linear-gradient(135deg, #05ce78 0%, #04a85f 100%);
            color: #fff;
            border: none;
            padding: 15px 30px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(5, 206, 120, 0.3);
        }

        .btn-theme:hover {
            background: linear-gradient(135deg, #04a85f 0%, #038c4f 100%);
            color: #fff;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(5, 206, 120, 0.4);
        }

        .btn-theme:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-secondary {
            background: #6c757d;
            color: #fff;
            border: none;
            padding: 15px 30px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-right: 15px;
        }

        .btn-secondary:hover {
            background: #5a6268;
            color: #fff;
            text-decoration: none;
            transform: translateY(-2px);
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
        }

        .button-group .btn-secondary {
            margin-right: 0;
        }

        /* Step Content */
        .step-content {
            display: none;
        }

        .step-content.active {
            display: block;
        }

        /* Progress Bar */
        .progress-container {
            margin-bottom: 30px;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #05ce78 0%, #04a85f 100%);
            transition: width 0.3s ease;
            width: 0%;
        }

        /* Success Message */
        .success-message {
            text-align: center;
            padding: 40px 20px;
        }

        .success-icon {
            font-size: 4rem;
            color: #05ce78;
            margin-bottom: 1rem;
        }

        .success-title {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1rem;
        }

        .success-subtitle {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .registration-container {
                margin: 20px auto;
                padding: 30px 20px;
            }

            .step-title {
                font-size: 1.5rem;
            }

            .step-subtitle {
                font-size: 1rem;
            }

            .button-group {
                flex-direction: column;
                gap: 1rem;
            }

            .button-group .btn-secondary,
            .button-group .btn-theme {
                width: 100%;
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <!-- Main Header -->
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="/"><img src="{{ asset('apnafund/assets/images/White Logo.png') }}" alt="Apna Fund Logo" class="logo-img"></a>
            </div>
        </div>
    </header>

    <!-- Registration Form -->
    <div class="registration-container">
        <!-- Progress Bar -->
        <div class="progress-container">
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
        </div>

        <!-- Step Indicator -->
        <div class="step-indicator">
            <div class="step-dot active" data-step="1"></div>
            <div class="step-dot" data-step="2"></div>
            <div class="step-dot" data-step="3"></div>
            <div class="step-dot" data-step="4"></div>
            <div class="step-dot" data-step="5"></div>
        </div>

        <!-- Step 1: Business Type -->
        <div class="step-content active" id="step1">
            <h2 class="step-title">What type of business are you?</h2>
            <p class="step-subtitle">This helps us understand your funding needs better</p>
            
            <div class="form-group">
                <label class="form-label">Business Type</label>
                <select class="form-control" id="businessType">
                    <option value="">Select your business type</option>
                    <option value="startup">Startup</option>
                    <option value="small-business">Small Business</option>
                    <option value="nonprofit">Non-Profit Organization</option>
                    <option value="creative-project">Creative Project</option>
                    <option value="tech-company">Technology Company</option>
                    <option value="manufacturing">Manufacturing</option>
                    <option value="retail">Retail Business</option>
                    <option value="service">Service Business</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="button-group">
                <button class="btn-secondary" onclick="goBack()">Back</button>
                <button class="btn-theme" onclick="nextStep()">Next</button>
            </div>
        </div>

        <!-- Step 2: Business Details -->
        <div class="step-content" id="step2">
            <h2 class="step-title">Tell us about your business</h2>
            <p class="step-subtitle">Share your story and vision</p>
            
            <div class="form-group">
                <label class="form-label">Business Name</label>
                <input type="text" class="form-control" id="businessName" placeholder="Enter your business name">
            </div>

            <div class="form-group">
                <label class="form-label">Business Description</label>
                <textarea class="form-control" id="businessDescription" placeholder="Describe what your business does, your mission, and what makes you unique"></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Industry</label>
                <select class="form-control" id="industry">
                    <option value="">Select your industry</option>
                    <option value="technology">Technology</option>
                    <option value="healthcare">Healthcare</option>
                    <option value="education">Education</option>
                    <option value="finance">Finance</option>
                    <option value="retail">Retail</option>
                    <option value="manufacturing">Manufacturing</option>
                    <option value="food-beverage">Food & Beverage</option>
                    <option value="creative-arts">Creative Arts</option>
                    <option value="environmental">Environmental</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="button-group">
                <button class="btn-secondary" onclick="prevStep()">Previous</button>
                <button class="btn-theme" onclick="nextStep()">Next</button>
            </div>
        </div>

        <!-- Step 3: Funding Details -->
        <div class="step-content" id="step3">
            <h2 class="step-title">What are your funding goals?</h2>
            <p class="step-subtitle">Help us understand your financial needs</p>
            
            <div class="form-group">
                <label class="form-label">Funding Amount Needed</label>
                <select class="form-control" id="fundingAmount">
                    <option value="">Select funding amount</option>
                    <option value="under-10k">Under $10,000</option>
                    <option value="10k-50k">$10,000 - $50,000</option>
                    <option value="50k-100k">$50,000 - $100,000</option>
                    <option value="100k-500k">$100,000 - $500,000</option>
                    <option value="500k-1m">$500,000 - $1,000,000</option>
                    <option value="over-1m">Over $1,000,000</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">How will you use the funds?</label>
                <select class="form-control" id="fundUsage">
                    <option value="">Select primary use</option>
                    <option value="product-development">Product Development</option>
                    <option value="marketing">Marketing & Advertising</option>
                    <option value="equipment">Equipment & Infrastructure</option>
                    <option value="inventory">Inventory & Supplies</option>
                    <option value="expansion">Business Expansion</option>
                    <option value="research">Research & Development</option>
                    <option value="operating-costs">Operating Costs</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Campaign Duration</label>
                <select class="form-control" id="campaignDuration">
                    <option value="">Select campaign length</option>
                    <option value="30-days">30 days</option>
                    <option value="60-days">60 days</option>
                    <option value="90-days">90 days</option>
                    <option value="120-days">120 days</option>
                </select>
            </div>

            <div class="button-group">
                <button class="btn-secondary" onclick="prevStep()">Previous</button>
                <button class="btn-theme" onclick="nextStep()">Next</button>
            </div>
        </div>

        <!-- Step 4: Contact Information -->
        <div class="step-content" id="step4">
            <h2 class="step-title">Your contact information</h2>
            <p class="step-subtitle">We'll use this to keep you updated</p>
            
            <div class="form-group">
                <label class="form-label">First Name</label>
                <input type="text" class="form-control" id="firstName" placeholder="Enter your first name">
            </div>

            <div class="form-group">
                <label class="form-label">Last Name</label>
                <input type="text" class="form-control" id="lastName" placeholder="Enter your last name">
            </div>

            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="tel" class="form-control" id="phone" placeholder="Enter your phone number">
            </div>

            <div class="form-group">
                <label class="form-label">Country</label>
                <select class="form-control" id="country">
                    <option value="">Select your country</option>
                    @php
                        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
                    @endphp
                    @foreach($countries as $code => $country)
                        <option value="{{ $code }}">{{ $country->country }}</option>
                    @endforeach
                </select>
            </div>

            <div class="button-group">
                <button class="btn-secondary" onclick="prevStep()">Previous</button>
                <button class="btn-theme" onclick="nextStep()">Next</button>
            </div>
        </div>

        <!-- Step 5: Account Creation -->
        <div class="step-content" id="step5">
            <h2 class="step-title">Create your account</h2>
            <p class="step-subtitle">Set up your login credentials</p>
            
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control" id="signupEmail" placeholder="Enter your email address">
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" id="password" placeholder="Create a strong password">
            </div>

            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm your password">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <input type="checkbox" id="termsCheckbox" style="margin-right: 8px;">
                    I agree to the <a target="_blank" href="{{ route('policy.pages', ['slug' => 'terms', 'id' => 1]) }}" style="color: #05ce78;">Terms of Service</a> and <a target="_blank" href="{{ route('policy.pages', ['slug' => 'privacy', 'id' => 2]) }}" style="color: #05ce78;">Privacy Policy</a>
                </label>
            </div>

            <div class="button-group">
                <button class="btn-secondary" onclick="prevStep()">Previous</button>
                <button class="btn-theme" onclick="createAccount()">Create Account</button>
            </div>
        </div>

        <!-- Success Step -->
        <div class="step-content" id="successStep">
            <div class="success-message">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 class="success-title">Account Created Successfully!</h2>
                <p class="success-subtitle">Welcome to Apna Fund! Your account has been created and you can now start building your crowdfunding campaign.</p>
                <button class="btn-theme" onclick="goToHome()">Go to Home</button>
            </div>
        </div>
    </div>

    <!-- Include toasts partial -->
    @include('partials.toasts')

    <!-- Add iziToast JS -->
    <script src="{{ asset('assets/universal/js/iziToast.min.js') }}"></script>

    <script>
        let currentStep = 1;
        const totalSteps = 5;

        function updateProgress() {
            const progress = (currentStep / totalSteps) * 100;
            document.getElementById('progressFill').style.width = progress + '%';
        }

        function updateStepIndicator() {
            document.querySelectorAll('.step-dot').forEach((dot, index) => {
                dot.classList.remove('active', 'completed');
                if (index + 1 < currentStep) {
                    dot.classList.add('completed');
                } else if (index + 1 === currentStep) {
                    dot.classList.add('active');
                }
            });
        }

        function showStep(step) {
            document.querySelectorAll('.step-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById('step' + step).classList.add('active');
        }

        function showToast(type, message) {
            if (typeof iziToast !== 'undefined') {
                iziToast[type]({
                    message: message,
                    position: "topRight"
                });
            } else {
                alert(message);
            }
        }

        function refreshCsrfToken() {
            // Fetch a new CSRF token from the server
            return fetch('/csrf-token', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update the meta tag with new token
                document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.token);
                return data.token;
            })
            .catch(error => {
                console.error('Error refreshing CSRF token:', error);
                // Fallback: reload the page
                window.location.reload();
            });
        }

        function getCsrfToken() {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            if (!token) {
                showToast('error', 'CSRF token not found. Please refresh the page.');
                return null;
            }
            return token;
        }

        function validateStep(step) {
            const errors = [];
            
            switch(step) {
                case 1:
                    if (document.getElementById('businessType').value === '') {
                        errors.push('Please select your business type');
                    }
                    break;
                case 2:
                    if (document.getElementById('businessName').value === '') {
                        errors.push('Please enter your business name');
                    }
                    if (document.getElementById('businessDescription').value === '') {
                        errors.push('Please enter your business description');
                    }
                    if (document.getElementById('industry').value === '') {
                        errors.push('Please select your industry');
                    }
                    break;
                case 3:
                    if (document.getElementById('fundingAmount').value === '') {
                        errors.push('Please select funding amount');
                    }
                    if (document.getElementById('fundUsage').value === '') {
                        errors.push('Please select how you will use the funds');
                    }
                    if (document.getElementById('campaignDuration').value === '') {
                        errors.push('Please select campaign duration');
                    }
                    break;
                case 4:
                    if (document.getElementById('firstName').value === '') {
                        errors.push('Please enter your first name');
                    }
                    if (document.getElementById('lastName').value === '') {
                        errors.push('Please enter your last name');
                    }
                    if (document.getElementById('phone').value === '') {
                        errors.push('Please enter your phone number');
                    }
                    if (document.getElementById('country').value === '') {
                        errors.push('Please select your country');
                    }
                    break;
                case 5:
                    const email = document.getElementById('signupEmail').value;
                    const password = document.getElementById('password').value;
                    const confirmPassword = document.getElementById('confirmPassword').value;
                    const termsAccepted = document.getElementById('termsCheckbox').checked;
                    
                    if (email === '') {
                        errors.push('Please enter your email address');
                    } else if (!isValidEmail(email)) {
                        errors.push('Please enter a valid email address');
                    }
                    
                    if (password === '') {
                        errors.push('Please enter a password');
                    } else if (password.length < 8) {
                        errors.push('Password must be at least 8 characters long');
                    }
                    
                    if (confirmPassword === '') {
                        errors.push('Please confirm your password');
                    } else if (password !== confirmPassword) {
                        errors.push('Passwords do not match');
                    }
                    
                    if (!termsAccepted) {
                        errors.push('Please accept the Terms of Service and Privacy Policy');
                    }
                    break;
            }
            
            return errors;
        }

        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function nextStep() {
            const errors = validateStep(currentStep);
            if (errors.length === 0) {
                if (currentStep < totalSteps) {
                    currentStep++;
                    showStep(currentStep);
                    updateProgress();
                    updateStepIndicator();
                }
            } else {
                // Show first error in toast
                showToast('error', errors[0]);
            }
        }

        function prevStep() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
                updateProgress();
                updateStepIndicator();
            }
        }

        function createAccount() {
            const errors = validateStep(currentStep);
            if (errors.length === 0) {
                // Collect all form data
                const formData = {
                    businessType: document.getElementById('businessType').value,
                    businessName: document.getElementById('businessName').value,
                    businessDescription: document.getElementById('businessDescription').value,
                    industry: document.getElementById('industry').value,
                    fundingAmount: document.getElementById('fundingAmount').value,
                    fundUsage: document.getElementById('fundUsage').value,
                    campaignDuration: document.getElementById('campaignDuration').value,
                    firstName: document.getElementById('firstName').value,
                    lastName: document.getElementById('lastName').value,
                    phone: document.getElementById('phone').value,
                    country: document.getElementById('country').value,
                    signupEmail: document.getElementById('signupEmail').value,
                    password: document.getElementById('password').value,
                    confirmPassword: document.getElementById('confirmPassword').value,
                    termsCheckbox: document.getElementById('termsCheckbox').checked
                };

                // Show loading state
                const createBtn = document.querySelector('#step5 .btn-theme');
                const originalText = createBtn.textContent;
                createBtn.textContent = 'Creating Account...';
                createBtn.disabled = true;

                // Send data to backend
                fetch('{{ route("user.register.business") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(JSON.stringify(data));
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Show success toast
                        showToast('success', data.message || 'Account created successfully!');
                        
                        // Go to success step
                        currentStep = 'successStep';
                        showStep(currentStep);
                        updateProgress();
                        updateStepIndicator();
                    } else {
                        // Show error message
                        let errorMessage = 'An error occurred while creating your account.';
                        if (data.errors) {
                            const errorList = Object.values(data.errors).flat();
                            errorMessage = errorList.join('\n');
                        } else if (data.message) {
                            errorMessage = data.message;
                        }
                        showToast('error', errorMessage);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    try {
                        // Try to parse the error as JSON to get validation errors
                        const errorData = JSON.parse(error.message);
                        if (errorData.errors) {
                            // Handle validation errors
                            const errorMessages = [];
                            Object.keys(errorData.errors).forEach(field => {
                                errorData.errors[field].forEach(message => {
                                    errorMessages.push(message);
                                });
                            });
                            showToast('error', errorMessages.join('\n'));
                        } else if (errorData.message) {
                            showToast('error', errorData.message);
                        } else {
                            showToast('error', 'An error occurred while creating your account. Please try again.');
                        }
                    } catch (parseError) {
                        showToast('error', 'An error occurred while creating your account. Please try again.');
                    }
                })
                .finally(() => {
                    // Reset button state
                    createBtn.textContent = originalText;
                    createBtn.disabled = false;
                });
            } else {
                // Show first error in toast
                showToast('error', errors[0]);
            }
        }

        function goBack() {
            window.location.href = '{{ route("business.resources") }}';
        }

        function goToHome() {
            window.location.href = '{{ route("home") }}';
        }

        // Initialize
        updateProgress();
        updateStepIndicator();
    </script>
</body>

</html> 