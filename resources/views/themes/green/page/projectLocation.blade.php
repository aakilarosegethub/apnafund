@php
    $activeTheme = activeTheme();
@endphp

@extends($activeTheme . 'layouts.blank')

@section('custom-css')
<style>
    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        background-color: #d4f2f6;
        margin: 0;
        padding: 0;
        min-height: 100vh;
    }

    /* MAIN CARD */
    .setup-container {
        max-width: 520px;
        width: 90%;
        margin: 80px auto;
        padding: 65px 60px;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 18px 40px rgba(0,0,0,.08);
        text-align: center;
    }

    /* HEADER */
    .setup-title {
        font-size: 30px;
        font-weight: 800;
        margin-bottom: 12px;
    }

    .setup-description {
        font-size: 14px;
        color: #555;
        line-height: 1.6;
        margin-bottom: 35px;
    }

    /* FORM */
    .form-group {
        text-align: left;
        margin-bottom: 20px;
    }

    .form-label {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 6px;
        display: block;
    }

    .form-select {
        width: 100%;
        padding: 12px;
        font-size: 14px;
        border: 2px solid #00c6a7;
        border-radius: 8px;
    }

    /* INFO TEXT */
    .info {
        font-size: 12px;
        color: #666;
        margin: 30px 0;
        text-align: left;
        display: flex;
        align-items: center;
    }

    .info::before {
        content: "?";
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #bbb;
        color: #fff;
        text-align: center;
        line-height: 16px;
        font-size: 10px;
        margin-right: 8px;
    }

    /* BUTTON */
    .continue-btn {
        width: 100%;
        padding: 13px;
        background: #333;
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: .3s;
    }

    .continue-btn:hover {
        background: #555;
    }

    /* BACK LINK */
    .back-link {
        display: block;
        margin-top: 18px;
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
<div class="setup-container">

    <h1 class="setup-title">
        set a location for your project.  </h1>

    <p class="setup-description">
        Pick your country of legal residence if you're raising funds as an individual.
        If you're raising funds for a business or nonprofit, select the country where
        the entity's tax ID is registered.
    </p>

    <form id="locationForm">

        <div class="form-group">
            <label class="form-label">
                <i class="fas fa-globe"></i> Country
            </label>

            <select class="form-select" id="country" name="country" required>
                <option value="">Select a country</option>

                @if(isset($allowedCountries) && !empty($allowedCountries))
                    @foreach($allowedCountries as $country)
                        <option value="{{ $country }}">{{ $country }}</option>
                    @endforeach
                @else
                    @foreach(['Pakistan', 'United States', 'United Kingdom', 'Canada', 'Australia', 'United Arab Emirates', 'Saudi Arabia', 'India', 'Germany', 'France', 'Singapore'] as $country)
                        <option value="{{ $country }}">{{ $country }}</option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="info">
            <a href="{{ route('apnacrowdfunding-rules') }}" target="_blank" rel="noopener" style="color: inherit; text-decoration: underline;">
                Who's eligible to run a {{ bs('site_name') ?? 'ApnaCrowdfunding' }} project?
            </a>
        </div>

        <button type="submit" class="continue-btn" id="continueBtn">
            Continue
        </button>

        <a href="{{ route('start.project') }}" class="back-link">
            ← category
        </a>

    </form>
</div>
@endsection


@section('script')
<script>
(function() {
    const countrySelect = document.getElementById('country');
    const continueBtn = document.getElementById('continueBtn');
    const CONTINUE_BTN_LABEL = 'Continue';

    function updateContinueBtn() {
        continueBtn.textContent = CONTINUE_BTN_LABEL;
        continueBtn.disabled = !countrySelect.value;
    }

    countrySelect.addEventListener('change', updateContinueBtn);

    window.addEventListener('pageshow', function() {
        updateContinueBtn();
    });

    updateContinueBtn();

    document.getElementById('locationForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const country = countrySelect.value;
        if (!country) return;

        continueBtn.disabled = true;
        continueBtn.textContent = 'Saving...';

        fetch('{{ route("start.project.save.location") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ country })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect_url;
            } else {
                alert(data.message || 'Something went wrong');
                updateContinueBtn();
            }
        })
        .catch(() => {
            alert('Server error, try again');
            updateContinueBtn();
        });
    });
})();
</script>
@endsection
