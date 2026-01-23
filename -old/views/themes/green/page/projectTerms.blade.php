@php
die('OKK');
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
@endphp
@extends($activeTheme . 'layouts.')

@section('custom-css')

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
