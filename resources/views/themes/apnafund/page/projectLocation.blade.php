@php
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
@endphp
@extends($activeTheme . 'layouts.frontend')

@section('style')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html {
        font-size: 62.5%;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        background: linear-gradient(135deg, #e0f7f4 0%, #b3e5fc 100%);
        min-height: 100vh;
        padding: 2rem;
    }

    .setup-container {
        background: white;
        border-radius: 12px;
        padding: 6rem 5rem;
        max-width: 700px;
        width: 70%;
        margin: 0 auto;
        box-shadow: 0px 10px 40px rgba(0, 0, 0, 0.08);
    }

    .setup-title {
        font-size: 3.2rem;
        font-weight: 900;
        color: #1d1d1d;
        margin-bottom: 1.5rem;
        line-height: 1.2;
    }

    .setup-description {
        font-size: 1.4rem;
        color: #777;
        margin-bottom: 3rem;
        line-height: 1.6;
    }

    .form-group {
        margin-bottom: 2rem;
    }

    .form-label {
        display: flex;
        align-items: center;
        gap: 1rem;
        font-size: 1.5rem;
        font-weight: 600;
        color: #1d1d1d;
        margin-bottom: 1rem;
    }

    .form-label i {
        font-size: 1.8rem;
        color: #00c6a7;
    }

    .form-select {
        width: 100%;
        padding: 1.4rem 1.6rem;
        font-size: 1.5rem;
        border: 2px solid #00c6a7;
        border-radius: 8px;
        background: white;
        color: #555;
        font-family: 'Inter', sans-serif;
        cursor: pointer;
        transition: all 0.3s ease;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%2300c6a7' d='M1 1l5 5 5-5'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1.5rem center;
        padding-right: 4rem;
    }

    .form-select:hover {
        border-color: #00a887;
        box-shadow: 0px 4px 12px rgba(0, 198, 167, 0.15);
    }

    .form-select:focus {
        outline: none;
        border-color: #00a887;
        box-shadow: 0px 4px 16px rgba(0, 198, 167, 0.25);
    }

    .info {
        font-size: 1.2rem;
        color: #666;
        margin-bottom: 3rem;
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }

    .info::before {
        content: "?";
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        background-color: #ccc;
        color: white;
        text-align: center;
        font-size: 1.2rem;
        font-weight: 600;
    }

    .button-group {
        display: flex;
        gap: 1rem;
        justify-content: space-between;
        align-items: center;
    }

    .continue-btn {
        background: #00c6a7;
        color: white;
        padding: 1.2rem 2.4rem;
        border: none;
        border-radius: 6px;
        font-size: 1.4rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-family: 'Inter', sans-serif;
        text-decoration: none;
        display: inline-block;
    }

    .continue-btn:hover {
        background: #00a887;
        transform: translateY(-2px);
        box-shadow: 0px 6px 16px rgba(0, 198, 167, 0.3);
    }

    .back-link {
        display: block;
        margin-top: 1.5rem;
        font-size: 1.3rem;
        color: #333;
        text-decoration: none;
        text-align: center;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .setup-container {
            padding: 4rem 2.5rem;
        }

        .setup-title {
            font-size: 2.4rem;
        }

        .setup-description {
            font-size: 1.3rem;
        }

        .form-select {
            font-size: 1.4rem;
            padding: 1.2rem 1.4rem;
        }

        .button-group {
            flex-direction: column;
        }

        .continue-btn {
            width: 100%;
        }
    }

    @media (max-width: 576px) {
        .setup-container {
            padding: 3rem 1.5rem;
        }

        .setup-title {
            font-size: 2rem;
        }

        .setup-description {
            font-size: 1.2rem;
        }

        .form-select {
            font-size: 1.3rem;
            padding: 1rem 1.2rem;
        }
    }
</style>
@endsection

@section('frontend')
    <!-- Project Location Page -->
    <div class="setup-container">
        <h1 class="setup-title">Last one—set a location for your project.</h1>
        <p class="setup-description">Pick your country of legal residence if you're raising funds as an individual. If you're raising funds for a business or nonprofit, select the country where the entity's tax ID is registered.</p>

        <form id="locationForm">
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-globe"></i>
                    Country
                </label>
                <select class="form-select" id="country" name="country" required>
                    <option value="">Select a country</option>
                    <option value="Spain">Spain</option>
                    <option value="United States">United States</option>
                    <option value="United Kingdom">United Kingdom</option>
                    <option value="Canada">Canada</option>
                    <option value="Pakistan">Pakistan</option>
                    <option value="India">India</option>
                    <!-- Add more countries as needed -->
                </select>
            </div>

            <div class="info">Who's eligible to run a Kickstarter project?</div>

            <div class="button-group">
                <button type="submit" class="continue-btn" id="continueBtn">
                    Continue
                </button>
            </div>

            <a href="{{ route('start.project') }}" class="back-link">← Additional subcategory</a>
        </form>
    </div>
@endsection

@section('script')
<script>
    document.getElementById('locationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const country = document.getElementById('country').value;
        const continueBtn = document.getElementById('continueBtn');
        
        if (country) {
            // Disable button during request
            continueBtn.disabled = true;
            continueBtn.textContent = 'Saving...';
            
            // Save country in session via AJAX
            fetch('{{ route("start.project.save.location") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    country: country
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect to terms page
                    window.location.href = data.redirect_url;
                } else {
                    alert('Error: ' + (data.message || 'Failed to save location'));
                    continueBtn.disabled = false;
                    continueBtn.textContent = 'Continue';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                continueBtn.disabled = false;
                continueBtn.textContent = 'Continue';
            });
        }
    });
</script>
@endsection

@push('page-style')
    <style>
        .start-project-container {
            padding: 80px 0;
            min-height: 70vh;
        }
    </style>
@endpush

