@php
    $activeTheme = activeTheme();
    $activeThemeTrue = activeTheme();
@endphp
@extends($activeTheme . 'layouts.blank')

@section('custom-css')
<style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #d4f2f6;
            margin: 0;
            padding: 0;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            max-width: 500px;
            width: 90%;
               padding: 85px 95px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            background:white;
        }

        h1 {
            font-size: 30px;
    font-weight: 800;
            text-align: center;
            margin-bottom: 10px;
        }

        p {
            font-size: 14px;
            color: #555;
            text-align: center;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        select {
            width: 100%;
            padding: 12px;
            font-size: 14px;
            border: 2px solid #00c6a7;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .info {
            font-size: 12px;
            color: #666;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
        }

        .info::before {
            content: "?";
            display: inline-block;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background-color: #ccc;
            color: white;
            text-align: center;
            line-height: 16px;
            font-size: 10px;
            margin-right: 6px;
        }

        .continue-btn {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #333;
            color: white;
            text-align: center;
            font-size: 14px;
            font-weight: 500;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
        }

        .continue-btn:hover {
            background-color: #555;
        }

        .back-link {
            display: block;
            margin-top: 15px;
            font-size: 13px;
            color: #333;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
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
                    @if(isset($allowedCountries) && !empty($allowedCountries))
                        @foreach($allowedCountries as $country)
                            <option value="{{ $country }}">{{ $country }}</option>
                        @endforeach
                    @else
                        {{-- Fallback: Show common countries if settings not configured --}}
                        <option value="Spain">Spain</option>
                        <option value="United States">United States</option>
                        <option value="United Kingdom">United Kingdom</option>
                        <option value="Canada">Canada</option>
                        <option value="Pakistan">Pakistan</option>
                        <option value="India">India</option>
                    @endif
                </select>
            </div>

            <div class="info">Who's eligible to run a {{ bs('site_name') ?? 'ApnaCrowdfunding' }} project?</div>

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

