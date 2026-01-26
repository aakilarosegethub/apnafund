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
        Last one—set a location for your project.
    </h1>

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
                    <option value="Spain">Spain</option>
                    <option value="United States">United States</option>
                    <option value="United Kingdom">United Kingdom</option>
                    <option value="Canada">Canada</option>
                    <option value="Pakistan">Pakistan</option>
                    <option value="India">India</option>
                @endif
            </select>
        </div>

        <div class="info">
            Who's eligible to run a {{ bs('site_name') ?? 'ApnaCrowdfunding' }} project?
        </div>

        <button type="submit" class="continue-btn" id="continueBtn">
            Continue
        </button>

        <a href="{{ route('start.project') }}" class="back-link">
            ← Additional subcategory
        </a>

    </form>
</div>
@endsection


@section('script')
<script>
document.getElementById('locationForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const country = document.getElementById('country').value;
    const btn = document.getElementById('continueBtn');

    if (!country) return;

    btn.disabled = true;
    btn.innerText = 'Saving...'; 

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
            btn.disabled = false;
            btn.innerText = 'Continue';
        }
    })
    .catch(() => {
        alert('Server error, try again');
        btn.disabled = false;
        btn.innerText = 'Continue';
    });
});
</script>
@endsection
