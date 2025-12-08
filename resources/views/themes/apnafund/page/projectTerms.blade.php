@php
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
@endphp
@extends($activeTheme . 'layouts.frontend')

@section('style')
<style>
    body {
        font-family: Arial, sans-serif;
        background: #fafafa;
        margin: 0;
        padding: 0;
        transition: all 0.3s ease-in-out;
    }

    /* ------------ CENTER BOX WRAPPER ------------ */
    .centerWrapper {
        min-height: calc(100vh - 100px);
        display: flex;
        justify-content: center;
        align-items: center;
        transition: opacity 0.5s ease, visibility 0.5s ease;
        padding-top: 100px;
    }

    .centerWrapper.hidden {
        opacity: 0;
        visibility: hidden;
        height: 0;
        padding-top: 0;
    }

    /* ------------ RULE BOX ------------ */
    .rulesBox {
        width: 650px;
        border: 1px solid #dcdcdc;
        padding: 50px;
        text-align: center;
        border-radius: 6px;
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
        background: #fff;
    }

    .topIcon img {
        width: 42px;
        margin-bottom: 18px;
    }

    .mainHeading {
        font-size: 26px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .subHeading {
        color: #444;
        margin-bottom: 40px;
        font-size: 15px;
    }

    .ruleLine {
        display: flex;
        align-items: center;
        margin: 25px 0;
        text-align: left;
    }

    .lineNumber {
        font-size: 28px;
        width: 50px;
        color: #c6c6c6;
    }

    .lineText {
        font-size: 15px;
        color: #333;
    }

    .lineText a {
        color: #0b6f3c;
        text-decoration: none;
        font-weight: bold;
    }

    .dividerLine {
        width: 100%;
        height: 1px;
        background: #ececec;
        margin-top: 10px;
    }

    .actionBtn {
        margin-top: 30px;
    }

    .actionBtn button {
        padding: 12px 35px;
        font-size: 16px;
        border: none;
        background-color: #1c8e50;
        color: #fff;
        border-radius: 3px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .actionBtn button:hover {
        background-color: #156f3d;
    }

    .actionBtn button:disabled {
        background-color: #c6c6c6;
        cursor: not-allowed;
    }

    .footerText {
        margin-top: 20px;
    }

    .footerText a {
        color: #0b6f3c;
        text-decoration: none;
    }

    .footerText a:hover {
        text-decoration: underline;
    }

    /* ------------ BELOW PROJECT CONTENT ------------ */
    .container {
        width: 70%;
        margin: auto;
        padding: 30px 0;
        opacity: 0;
        transition: opacity 0.6s ease;
        padding-top: 120px;
    }

    .container.visible {
        opacity: 1;
        margin-top: 40px;
        padding-top: 40px;
    }

    .project-title {
        font-size: 36px;
        margin-bottom: 5px;
    }

    .byline {
        color: #555;
        font-size: 16px;
    }

    .preview {
        margin-top: 20px;
        font-size: 16px;
        display: flex;
        align-items: center;
        color: #444;
    }

    .eye {
        margin-right: 5px;
        font-size: 18px;
    }

    .overview-heading {
        margin-top: 40px;
        font-size: 22px;
        color: #333;
    }

    .section {
        background: #fff;
        border: 1px solid #e5e5e5;
        padding: 20px;
        margin-top: 20px;
        display: flex;
        border-radius: 5px;
    }

    .icon-check {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        border: 2px solid #dcdcdc;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-right: 15px;
        font-size: 16px;
        color: #0c9d63;
    }

    .section-text h3 {
        margin: 0;
        font-size: 18px;
    }

    .section-text p {
        margin: 5px 0 0;
        color: #666;
        font-size: 14px;
    }

    /* Checkbox styling */
    .checkbox-wrapper {
        margin-top: 30px;
        text-align: left;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 5px;
    }

    .checkbox-wrapper input[type="checkbox"] {
        width: 18px;
        height: 18px;
        margin-right: 10px;
        cursor: pointer;
    }

    .checkbox-wrapper label {
        font-size: 14px;
        color: #333;
        cursor: pointer;
    }

    .confirm-section {
        margin-top: 30px;
        text-align: center;
    }

    .confirm-btn {
        padding: 12px 35px;
        font-size: 16px;
        border: none;
        background-color: #c6c6c6;
        color: #fff;
        border-radius: 3px;
        cursor: not-allowed;
        transition: background-color 0.3s ease;
    }

    .confirm-btn:enabled {
        background-color: #1c8e50;
        cursor: pointer;
    }

    .confirm-btn:enabled:hover {
        background-color: #156f3d;
    }

    .back-link {
        display: block;
        margin-top: 20px;
        font-size: 14px;
        color: #0b6f3c;
        text-decoration: none;
        text-align: center;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .rulesBox {
            width: 90%;
            padding: 30px;
        }

        .container {
            width: 90%;
        }

        .mainHeading {
            font-size: 22px;
        }

        .project-title {
            font-size: 28px;
        }
    }

    @media (max-width: 576px) {
        .rulesBox {
            width: 95%;
            padding: 20px;
        }

        .mainHeading {
            font-size: 20px;
        }

        .lineNumber {
            font-size: 24px;
            width: 40px;
        }

        .lineText {
            font-size: 14px;
        }
    }
</style>
@endsection

@section('frontend')
<!-- =================== CENTER BOX =================== -->
<div class="centerWrapper" id="ruleBox">
    <div class="rulesBox">
        <div class="topIcon">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Icon" onerror="this.style.display='none'">
        </div>
        <div class="mainHeading">Take a moment to review our rules</div>
        <div class="subHeading">Here are five rules every project must follow.</div>

        <div class="ruleLine">
            <div class="lineNumber">1</div>
            <div class="lineText">
                Projects must <a href="#" onclick="return false;">create something to share</a> with others.
            </div>
        </div>
        <div class="dividerLine"></div>

        <div class="ruleLine">
            <div class="lineNumber">2</div>
            <div class="lineText">
                Projects must be <a href="#" onclick="return false;">honest and clearly presented</a>.
            </div>
        </div>
        <div class="dividerLine"></div>

        <div class="ruleLine">
            <div class="lineNumber">3</div>
            <div class="lineText">Projects can't fundraise for charity.</div>
        </div>
        <div class="dividerLine"></div>

        <div class="ruleLine">
            <div class="lineNumber">4</div>
            <div class="lineText">Projects can't offer equity.</div>
        </div>
        <div class="dividerLine"></div>

        <div class="ruleLine">
            <div class="lineNumber">5</div>
            <div class="lineText">Projects can't involve <a href="#" onclick="return false;">prohibited items</a>.</div>
        </div>

        <div class="actionBtn">
            <button onclick="hideRules()" id="gotItBtn">Got it</button>
        </div>

        <div class="footerText">
            <a href="#" onclick="return false;">Read more</a> about our rules.
        </div>
    </div>
</div>

<!-- =================== PROJECT CONTENT =================== -->
<div class="container" id="projectBox">
    <h1 class="project-title">Create Your Campaign</h1>
    <p class="byline">Complete the following steps to launch your project</p>

    <div class="preview">
        <span class="eye">👁</span> Preview
    </div>

    <hr>

    <h2 class="overview-heading">Project overview</h2>

    <div class="section">
        <div class="icon-check">✔</div>
        <div class="section-text">
            <h3>Basics</h3>
            <p>Name your project, upload an image or video, and establish your campaign details.</p>
        </div>
    </div>

    <div class="section">
        <div class="icon-check">✔</div>
        <div class="section-text">
            <h3>Rewards</h3>
            <p>Set your rewards and shipping costs.</p>
        </div>
    </div>

    <div class="section">
        <div class="icon-check">✔</div>
        <div class="section-text">
            <h3>Story</h3>
            <p>Add a detailed project description and convey your risks and challenges.</p>
        </div>
    </div>

    <form id="termsForm">
        <div class="checkbox-wrapper">
            <label>
                <input type="checkbox" id="acceptTerms" name="acceptTerms" required>
                I have read and agree to all the rules stated above. I understand that I am responsible for my campaign and will comply with all platform rules and regulations.
            </label>
        </div>

        <div class="confirm-section">
            <button type="submit" class="confirm-btn" id="confirmBtn" disabled>
                Confirm & Create Campaign
            </button>
        </div>

        <a href="{{ route('start.project.location') }}" class="back-link">← Back to Location</a>
    </form>
</div>
@endsection

@section('script')
<script>
    function hideRules() {
        document.getElementById("ruleBox").classList.add("hidden");
        document.getElementById("projectBox").classList.add("visible");
    }

    const acceptTerms = document.getElementById('acceptTerms');
    const confirmBtn = document.getElementById('confirmBtn');
    const termsForm = document.getElementById('termsForm');

    // Enable/disable confirm button based on checkbox
    acceptTerms.addEventListener('change', function() {
        if (this.checked) {
            confirmBtn.disabled = false;
        } else {
            confirmBtn.disabled = true;
        }
    });

    // Handle form submission
    termsForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (acceptTerms.checked) {
            // Disable button during request
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Creating Campaign...';
            
            // Create campaign from session data
            fetch('{{ route("start.project.create.campaign") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect to edit campaign page
                    window.location.href = data.redirect_url;
                } else {
                    alert('Error: ' + (data.message || 'Failed to create campaign'));
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = 'Confirm & Create Campaign';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Confirm & Create Campaign';
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
