@php
    $activeTheme = activeTheme();
    $activeThemeTrue = activeTheme();
    
    // Check if campaign belongs to current user or user is a collaborator
    if (isset($campaign) && !$campaign->canBeEditedBy(auth()->id())) {
        abort(403, 'Unauthorized access to this campaign.');
    }
    
    // Set default section if not provided
    $section = $section ?? 'basics';
    $currentSection = $section;
@endphp
@extends($activeTheme . 'layouts.blank')
@section('custom-css')
<style>
    .input-group{
        overflow :unset !important;
        border-radius:2px !important
    }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: #fff;
        }

        /* TOP TABS */
        .top-tabs {
            display: flex;
            border-bottom: 1px solid #e6e6e6;
            background: #fafafa;
            padding: 15px 40px;
            gap: 32px;
            font-size: 15px;
            font-weight: 500;
            flex-wrap: wrap;
            align-items: center;
        }

        .top-tabs a {
            text-decoration: none;
            color: #777;
        }

        .top-tabs a.active {
            color: #000;
            font-weight: 600;
        }

        /* Preview Button Styles */
        .top-tabs .btn:hover {
            background: #f5f5f5 !important;
            border-color: #16a34a !important;
            color: #16a34a !important;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .top-tabs {
                padding: 10px 15px;
                gap: 15px;
                font-size: 13px;
            }

            .top-tabs .btn {
                padding: 6px 15px !important;
                font-size: 13px !important;
            }
        }

        /* MAIN AREA */
        .main {
            display: flex;
            width: 100%;
            margin-top: 20px;
            flex-wrap: wrap;
            justify-content:center;
        }

        /* LEFT SIDEBAR */
        .sidebar {
            width: 220px;
            border-right: 1px solid #e6e6e6;
            padding: 20px 25px;
            height: auto;
            flex-shrink: 0;
        }

        .sidebar a {
            display: block;
            margin-bottom: 14px;
            text-decoration: none;
            color: #333;
            font-size: 15px;
        }

        .sidebar a.active {
            font-weight: 600;
        }

        /* CONTENT AREA */
        .content {
            padding: 30px 40px;
            width: calc(100% - 220px);
            max-width: 750px;
        }

        h1 {
            font-size: 36px;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
        }

        .box {
            border: 1px solid #e3e3e3;
            border-radius: 10px;
            padding: 22px;
            margin-bottom: 25px;
        }

        label {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 6px;
            display: block;
        }

        input, textarea, select {
            width: 100%;
            padding: 14px;
            border-radius: 8px;
            border: 1px solid #d9d9d9;
            font-size: 15px;
        }

        textarea {
            height: 130px;
        }

        .note {
            font-size: 13px;
            margin-top: 5px;
            color: #777;
        }

        .next-btn {
            background: #028858;
            color: #fff;
            padding: 14px 34px;
            border-radius: 40px;
            border: none;
            cursor: pointer;
            opacity: 0.6;
            font-size: 15px;
            margin-top: 25px;
            pointer-events: none;
        }

        .next-btn.active {
            opacity: 1;
            pointer-events: auto;
        }

        #topActionButtons {
            display: none;
            gap: 10px;
            align-items: center;
        }

        #topActionButtons.visible {
            display: flex;
        }

        #topExitBtn {
            background: #666 !important;
            opacity: 1 !important;
            pointer-events: auto !important;
        }

        #topExitBtn:hover {
            background: #555 !important;
        }

        /* UPLOAD BOX */
        .upload-box {
            border: 2px dashed #d9d9d9;
            border-radius: 12px;
            padding: 45px 20px;
            text-align: center;
            margin-top: 25px;
            position: relative;
        }

        .upload-btn {
            padding: 12px 26px;
            border-radius: 6px;
            background: white;
            border: 1px solid #bcbcbc;
            cursor: pointer;
            font-size: 15px;
        }

        .preview-img {
            max-width: 100%;
            max-height: 250px;
            margin-top: 15px;
            border-radius: 10px;
            display: none;
            object-fit: cover;
        }

        /* Launch Date Grid */
        .date-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto;
            gap: 15px;
            margin-top: 15px;
            max-width: 400px;
        }

        .date-grid input {
            text-align: center;
        }

        .calendar-btn {
            width: 50px;
            height: 48px;
            border: 1px solid #d9d9d9;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            background: #fff;
        }

        .calendar-btn:hover {
            background: #f5f5f5;
        }

        .small-note {
            font-size: 14px;
            margin-top: 10px;
            color: #028858;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .small-note img {
            width: 18px;
        }

        /* Campaign Duration Notes */
        .radio-option {
            margin-bottom: 14px;
            font-size: 16px;
        }

        .green-note {
            color: #028858;
            font-size: 14px;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        /* SHIPPING SECTION — NOW BOX STYLE */
        .shipping-container {
            border: 1px solid #e3e3e3;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            display: flex;
            gap: 40px;
            flex-wrap: wrap;
        }

        .shipping-left {
            width: 40%;
        }

        .shipping-left h2 {
            font-size: 26px;
            margin: 0 0 10px;
        }

        .shipping-left p {
            font-size: 15px;
            color: #555;
            line-height: 1.6;
        }

        .shipping-left a {
            color: #007bff;
            text-decoration: none;
        }

        .shipping-right {
            width: 60%;
        }

        .shipping-box {
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 20px;
        }

        .ship-option {
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 10px;
            border: 1px solid transparent;
            cursor: pointer;
        }

        .ship-option.active {
            border: 1px solid #000;
            background: #fafafa;
        }

        .ship-option input {
            margin-right: 10px;
            transform: scale(1.2);
        }

        .ship-description {
            font-size: 14px;
            color: #666;
            margin-left: 28px;
            margin-top: 6px;
        }

        /* POST CAMPAIGN SECTION — NOW BOX STYLE */
        .post-container {
            border: 1px solid #e3e3e3;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            display: flex;
            gap: 40px;
            flex-wrap: wrap;
        }

        .post-left {
            width: 40%;
        }

        .post-left h2 {
            font-size: 26px;
            margin-bottom: 10px;
        }

        .post-left p {
            font-size: 15px;
            line-height: 1.6;
            color: #555;
        }

        .post-left a {
            color: #007bff;
            text-decoration: none;
        }

        .post-right {
            width: 60%;
        }

        .post-box {
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 20px;
        }

        .post-option {
            padding: 16px;
            border-radius: 10px;
            border: 1px solid transparent;
            margin-bottom: 12px;
            cursor: pointer;
        }

        .post-option.active {
            border: 1px solid #000;
            background: #fafafa;
        }

        .post-option input {
            margin-right: 10px;
            transform: scale(1.2);
        }

        .recommended-tag {
            background: #d1f5d8;
            color: #028858;
            padding: 3px 10px;
            border-radius: 8px;
            font-size: 13px;
            margin-left: 10px;
        }

        @media (max-width: 1024px) {
            .content {
                width: 100%;
                max-width: 100%;
                padding: 20px;
            }

            .sidebar {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid #e6e6e6;
                display: flex;
                overflow-x: auto;
                padding: 10px 15px;
                height: auto;
            }

            .sidebar a {
                margin-right: 20px;
            }

            .shipping-left, .shipping-right,
            .post-left, .post-right {
                width: 100%;
            }
        }
    </style>
    <link rel="stylesheet" href="{{ asset($activeThemeTrue . 'css/dropzone.min.css') }}">
    @if($currentSection == 'story')
    <!-- include libraries(jQuery, bootstrap) -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- include summernote css/js -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
    @endif
@endsection
@section('frontend')


    <!-- TOP TABS -->
    <div class="top-tabs">
        <div style="display: flex; align-items: center; gap: 32px; flex: 1;">
            <a href="{{ route('user.campaign.edit.basics', $campaign->slug) }}" class="{{ ($section ?? 'basics') == 'basics' ? 'active' : '' }}">Basics</a>
            <a href="{{ route('user.campaign.edit.reward', $campaign->slug) }}" class="{{ ($section ?? 'basics') == 'reward' ? 'active' : '' }}">Rewards</a>
            <a href="{{ route('user.campaign.edit.story', $campaign->slug) }}" class="{{ ($section ?? 'basics') == 'story' ? 'active' : '' }}">Story</a>
            <a href="{{ route('user.campaign.edit.people', $campaign->slug) }}" class="{{ ($section ?? 'basics') == 'people' ? 'active' : '' }}">People</a>
            <a href="{{ route('user.campaign.edit.documents', $campaign->slug) }}" class="{{ ($section ?? 'basics') == 'documents' ? 'active' : '' }}">Documents</a>
            <a href="{{ route('user.campaign.edit.payment', $campaign->slug) }}" class="{{ ($section ?? 'basics') == 'payment' ? 'active' : '' }}">Payment</a>
            <a href="{{ route('user.campaign.edit.boost', $campaign->slug) }}" class="{{ ($section ?? 'basics') == 'boost' ? 'active' : '' }}">Boost</a>
            <a href="{{ route('user.campaign.edit.faq', $campaign->slug) }}" class="{{ ($section ?? 'basics') == 'faq' ? 'active' : '' }}">FAQ</a>
            <a href="{{ route('user.campaign.edit.updates', $campaign->slug) }}" class="{{ ($section ?? 'basics') == 'updates' ? 'active' : '' }}">Updates</a>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <!-- Preview Button -->
            <a href="{{ route('user.campaign.show', $campaign->slug) }}" target="_blank" class="btn" style="padding: 8px 20px; font-size: 14px; background: #fff; border: 1px solid #ddd; color: #333; text-decoration: none; border-radius: 5px; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;">
                <i class="fas fa-eye"></i> Preview
            </a>
            
            <!-- Action Buttons (Save/Exit - shown when editing) -->
            <div id="topActionButtons" style="display: none; gap: 10px; align-items: center;">
                <button type="button" id="topExitBtn" class="next-btn" style="margin: 0; padding: 8px 20px; font-size: 14px; background: #666;">Exit</button>
                <button type="button" id="topSaveBtn" class="next-btn active" style="margin: 0; padding: 8px 20px; font-size: 14px;">Save</button>
            </div>
            <button type="button" id="topEditBtn" class="d-none next-btn active" style="margin: 0; padding: 8px 20px; font-size: 14px;">Edit</button>
        </div>
    </div>
    </div>
    <div class="main">
        

        <!-- LEFT SIDEBAR -->
  

        <!-- RIGHT CONTENT -->
        <div class="content">
            @php
                $currentSection = $section ?? 'basics';
            @endphp

            @if(session('toasts'))
                <div class="alert alert-success" style="background: #d1f5d8; color: #028858; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    @foreach(session('toasts') as $toast)
                        {{ $toast[1] ?? $toast }}
                    @endforeach
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger" style="background: #fee; color: #c33; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($currentSection == 'basics')
            <form action="{{ route('user.campaign.update', $campaign->id) }}" method="POST" enctype="multipart/form-data" id="basicsForm">
                @csrf
                <input type="hidden" name="section" value="basics">
            <h1>Basics</h1>
            <p class="subtitle">Get started with the essential information about your project.</p>

            <!-- PROJECT TITLE -->
            <div class="box">
                <label>Project Title *</label>
                <input type="text" name="name" value="{{ old('name', $campaign->name) }}" placeholder="Enter your project title..." required>
                <p class="note">Write a clear title so people understand what you are creating.</p>
                @error('name')
                    <p class="note" style="color: red;">{{ $message }}</p>
                @enderror
            </div>

            <!-- SHORT DESCRIPTION -->
            <div class="box">
                <label>Short Description *</label>
                <textarea name="short_description" rows="4" placeholder="Campaign description will be added here. Describe your project in one or two sentences..." required>{{ old('short_description', $campaign->short_description) }}</textarea>
                <p class="note">This will show on your project card.</p>
                @error('short_description')
                    <p class="note" style="color: red;">{{ $message }}</p>
                @enderror
            </div>

            <!-- CATEGORY -->
            <div class="box">
                <label>Project Category *</label>
                <select name="category_id" required>
                    <option value="">Select category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $campaign->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="note" style="color: red;">{{ $message }}</p>
                @enderror
            </div>

            <!-- LOCATION -->
            <div class="box">
                <label>Location</label>
                <input type="text" name="location" value="{{ old('location', $campaign->location) }}" placeholder="City, Country">
                <p class="note">Where is your project based?</p>
                @error('location')
                    <p class="note" style="color: red;">{{ $message }}</p>
                @enderror
            </div>

            <!-- PROJECT IMAGE -->
            <div class="box">
                <label>Project Image *</label>
                <p class="note">Add an image that clearly represents your project.</p>

                <div id="campaignImagePreview" style="margin-bottom: 15px;">
                    @if($campaign->image)
                        <img src="{{ getImage(getFilePath('campaign') . '/' . $campaign->image, getFileSize('campaign')) }}" 
                             alt="Current Image" 
                             id="currentCampaignImage"
                             style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd;">
                    @endif
                </div>

                <input type="file" name="image" id="campaignImageInput" accept="image/*" style="margin-top: 10px;">
                <input type="hidden" name="uploaded_image" id="uploadedImageName" value="">
                <input type="hidden" name="uploaded_image_original" id="uploadedImageOriginalName" value="">
                <p class="note">JPG, PNG, GIF, or WEBP under {{ ini_get('upload_max_filesize') }}.</p>
                <div id="imageErrorMsg" class="note" style="color: red; display: none; margin-top: 5px;"></div>
                <div id="imageUploadStatus" class="note" style="display: none; margin-top: 5px;"></div>
                @error('image')
                    <p class="note" style="color: red;">{{ $message }}</p>
                @enderror
            </div>

            <!-- VIDEO UPLOAD (Optional) -->
            <div class="box">
                <label>Project Video (Optional)</label>
                <p class="note">Upload a video file to showcase your project.</p>
                
                @if($campaign->video)
                    <div style="margin-bottom: 15px; padding: 10px; background: #f0f9f4; border-radius: 6px; border: 1px solid #16a34a;">
                        <i class="fas fa-video" style="color: #16a34a;"></i>
                        <span style="margin-left: 8px; color: #16a34a;">Video uploaded</span>
                    </div>
                @endif
                
                <input type="file" name="video" id="campaignVideoInput" accept="video/*" style="margin-top: 10px;">
                <input type="hidden" name="uploaded_video" id="uploadedVideoName" value="">
                <p class="note">Accepted formats: MP4, AVI, MOV. Max size: 500 MB.</p>
                <div id="videoErrorMsg" class="note" style="color: red; display: none; margin-top: 5px;"></div>
                <div id="videoUploadStatus" class="note" style="display: none; margin-top: 5px;"></div>
                @error('video')
                    <p class="note" style="color: red;">{{ $message }}</p>
                @enderror
            </div>

            <!-- YOUTUBE URL -->
            <div class="box">
                <label>YouTube Video URL (Optional)</label>
                <input type="url" name="youtube_url" value="{{ old('youtube_url', $campaign->youtube_url) }}" placeholder="https://www.youtube.com/watch?v=...">
                <p class="note">Add a YouTube link to your project video (e.g., https://www.youtube.com/watch?v=dQw4w9WgXcQ)</p>
                @error('youtube_url')
                    <p class="note" style="color: red;">{{ $message }}</p>
                @enderror
            </div>

            <!-- FUNDING GOAL -->
            <div class="box">
                <label>Funding Goal ({{ $creatorSymbol ?? $setting->cur_sym }}) - Enter amount in {{ $creatorCurrency ?? 'USD' }} *</label>
                <p class="note">Set an achievable goal that covers what you need to complete your project.</p>
                <input type="number" name="goal_amount" id="goalAmountInput" step="0.01" min="0" value="{{ old('goal_amount', round($goalAmountInCreatorCurrency ?? $campaign->goal_amount, 2)) }}" placeholder="0.00" required>
                <input type="hidden" name="input_currency" value="{{ $creatorCurrency ?? 'USD' }}">
                @error('goal_amount')
                    <p class="note" style="color: red;">{{ $message }}</p>
                @enderror
                @if(!empty($showRealtimeConversion) && isset($rateCreatorToPlatform))
                <div id="goalRealtimeConversion" style="margin-top:12px; padding:12px; background:#f0f9ff; border:1px solid #0ea5e9; border-radius:8px; font-size:14px;"
                     data-rate="{{ $rateCreatorToPlatform ?? 1 }}"
                     data-creator-sym="{{ $creatorSymbol ?? '$' }}"
                     data-creator-code="{{ $creatorCurrency ?? 'USD' }}"
                     data-platform-sym="{{ $platformSymbol ?? '$' }}"
                     data-platform-code="{{ $platformCurrency ?? 'USD' }}">
                    <div><strong>Real-time conversion:</strong> <span id="conversionResult">—</span></div>
                    <div style="margin-top:6px; color:#0369a1;">Current rate: 1 {{ $creatorCurrency ?? 'USD' }} = {{ number_format($rateCreatorToPlatform ?? 1, 6) }} {{ $platformCurrency ?? 'USD' }}</div>
                </div>
                @endif
            </div>

            <!-- CAMPAIGN DURATION -->
            <div class="box">
                <label>Campaign Start Date *</label>
                <input type="date" name="start_date" value="{{ old('start_date', $campaign->start_date ? $campaign->start_date->format('Y-m-d') : '') }}" required>
                @error('start_date')
                    <p class="note" style="color: red;">{{ $message }}</p>
                @enderror

                <label style="margin-top: 15px;">Campaign End Date *</label>
                <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $campaign->end_date ? $campaign->end_date->format('Y-m-d') : '') }}" required>
                <p class="note">💡 Campaigns can last maximum 30 days from start date.</p>
                @error('end_date')
                    <p class="note" style="color: red;">{{ $message }}</p>
                @enderror
            </div>

            <!-- SAVE AND NEXT BUTTON -->
            <div class="box" style="text-align: right; background: transparent; border: none; padding: 0; margin-top: 30px;">
                <input type="hidden" name="next_tab" value="reward">
                <button type="submit" id="basicsSubmitBtn" class="btn btn-success" style="padding: 12px 40px; font-size: 16px; font-weight: 600; background: #16a34a; border: none; border-radius: 6px; cursor: pointer;">
                    <i class="fas fa-save"></i> Save and Next
                </button>
            </div>

            </form>
            @endif

            @if($currentSection == 'reward')
            @php
                $rewards = $rewards ?? $campaign->rewards()->orderBy('minimum_amount')->get();
            @endphp
            
            <!-- MAIN CONTENT -->
            <div id="rewardsMainContent">
                <h1>Create your rewards</h1>
                
                <div class="tabs" style="display: flex; gap: 30px; margin-bottom: 10px;">
                    <button type="button" id="rewardTabItems" class="tab active" data-tab="items" onclick="window.switchRewardTab && window.switchRewardTab('items')" style="font-size: 16px; color: #555; padding-bottom: 8px; cursor: pointer; border: none; background: none; font-weight: 600; color: #000;">Items</button>
                    <button type="button" id="rewardTabTiers" class="tab" data-tab="tiers" onclick="window.switchRewardTab && window.switchRewardTab('tiers')" style="font-size: 16px; color: #555; padding-bottom: 8px; cursor: pointer; border: none; background: none;">Reward tiers</button>
                    <button type="button" id="rewardTabAddons" class="tab" data-tab="addons" onclick="window.switchRewardTab && window.switchRewardTab('addons')" style="font-size: 16px; color: #555; padding-bottom: 8px; cursor: pointer; border: none; background: none;">Add-ons</button>
                </div>
                
                <div class="underline" style="width: 50px; height: 3px; background: black; margin-top: -6px; margin-bottom: 20px;"></div>
                
                <p class="desc" id="rewardTabDescription" style="width: 70%; color: #444; line-height: 1.5; font-size: 15px;">
                    Including items in your rewards and add-ons makes it easy for backers to understand and 
                    compare your offerings. An item can be anything you plan to offer your backers. Some 
                    examples include playing cards, a digital copy of a book, a ticket to a play, or even a 
                    thank-you in your documentary.
                </p>
                
                <a href="#" id="rewardTabLearnLink" class="learn" style="color: #009b5b; text-decoration: none; font-size: 15px;">Learn about creating items</a>
                <script>
                    (function () {
                        const tabContentMap = {
                            items: {
                                text: "Including items in your rewards and add-ons makes it easy for backers to understand and compare your offerings. An item can be anything you plan to offer your backers. Some examples include playing cards, a digital copy of a book, a ticket to a play, or even a thank-you in your documentary.",
                                link: "Learn about creating items",
                                href: "/help#items"
                            },
                            tiers: {
                                text: "Reward tiers let you bundle one or more items with a clear minimum pledge amount. Use tiers to present simple choices so backers can quickly pick the right reward level.",
                                link: "Learn about reward tiers",
                                href: "/help#reward-tiers"
                            },
                            addons: {
                                text: "Add-ons are optional extras backers can select on top of their chosen reward tier. Use add-ons for upgrades, bonus merchandise, or limited extras to increase average pledge value.",
                                link: "Learn about add-ons",
                                href: "/help#add-ons"
                            }
                        };

                        window.switchRewardTab = function (tabKey) {
                            const tabTypeInput = document.getElementById('rewardTabType');
                            if (tabTypeInput) tabTypeInput.value = tabKey || 'items';
                            const map = tabContentMap[tabKey] || tabContentMap.items;
                            const descEl = document.getElementById('rewardTabDescription');
                            const linkEl = document.getElementById('rewardTabLearnLink');
                            const tabs = document.querySelectorAll('#rewardsMainContent .tab');

                            tabs.forEach(function (btn) {
                                const active = btn.getAttribute('data-tab') === tabKey;
                                btn.classList.toggle('active', active);
                                btn.style.fontWeight = active ? '600' : '400';
                                btn.style.color = active ? '#000' : '#555';
                            });

                            if (descEl) descEl.textContent = map.text;
                            if (linkEl) {
                                linkEl.textContent = map.link;
                                linkEl.setAttribute('href', map.href || '#');
                            }
                        };

                        window.switchRewardTab('items');
                    })();
                </script>
                
                <button class="new-item" id="newItemBtn" style="float: right; background: black; color: white; border: none; padding: 10px 18px; font-size: 15px; border-radius: 5px; cursor: pointer; margin-top: -60px;">+ New item</button>
                
                <div class="table-head" style="display: grid; grid-template-columns: 1fr 1fr 1fr; margin-top: 90px; font-size: 15px; color: #555; padding: 15px 0; border-bottom: 1px solid #ddd; font-weight: 600;">
                    <span>Details</span>
                    <span>Included in</span>
                    <span>Image</span>
                </div>

                <!-- REWARDS LIST -->
                <div id="rewardsList">
                    @forelse($rewards as $reward)
                    <div class="reward-item" data-reward-id="{{ $reward->id }}" style="display: grid; grid-template-columns: 1fr 1fr 1fr; padding: 20px 0; border-bottom: 1px solid #eee; align-items: center;">
                        <div class="reward-details" style="display: flex; align-items: center; gap: 15px;">
                            @if($reward->image)
                                <img src="{{ getImage(getFilePath('reward') . '/' . $reward->image, getThumbSize('reward')) }}" 
                                     alt="{{ $reward->title }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd;">
                            @else
                                <div style="width: 60px; height: 60px; background: #f0f0f0; border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-gift" style="color: #999;"></i>
                                </div>
                            @endif
                            <div class="reward-info">
                                <h3 style="margin: 0; font-size: 16px; font-weight: 600; margin-bottom: 5px;">{{ $reward->title }}</h3>
                                <p style="margin: 0; font-size: 14px; color: #666;">{{ strLimit($reward->description, 60) }}</p>
                            </div>
                        </div>
                        <div class="reward-included" style="font-size: 14px; color: #555;">
                            {{ $setting->cur_sym }}{{ showAmount($reward->minimum_amount) }} minimum
                        </div>
                        <div class="reward-actions" style="display: flex; gap: 10px; align-items: center;">
                            <img src="{{ $reward->image ? getImage(getFilePath('reward') . '/' . $reward->image, getThumbSize('reward')) : asset('assets/images/default-reward.png') }}" 
                                 alt="{{ $reward->title }}" 
                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd;">
                            <button class="btn-edit" onclick="editReward({{ $reward->id }})" style="padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; background: #007bff; color: white;">Edit</button>
                            <button class="btn-delete" onclick="deleteReward({{ $reward->id }})" style="padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; background: #dc3545; color: white;">Delete</button>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state" style="text-align: center; padding: 60px 20px; color: #888;">
                        <i class="fas fa-gift" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                        <p>No rewards found. Create your first reward to get started.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- ADD/EDIT NEW ITEM SECTION -->
            <div class="item-box" id="itemForm" style="margin-top: 50px; border: 1px solid #ddd; border-radius: 8px; padding: 25px; display: none;">
                <div class="section-title" id="formTitle" style="font-size: 22px; font-weight: 600; margin-bottom: 20px;">Add a new item</div>
                
                <form id="rewardForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="rewardId" name="reward_id">
                    <input type="hidden" id="formAction" name="form_action" value="create">
                    <input type="hidden" id="rewardTabType" name="reward_tab_type" value="items">
                    
                    <div class="input-group" style="margin-bottom: 25px;">
                        <label style="font-size: 15px; font-weight: 600; display: block; margin-bottom: 8px;">Item title *</label>
                        <input type="text" name="title" id="itemTitle" placeholder="Digital photo" class="item-input" style="width: 100%; padding: 12px; font-size: 15px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;" required>
                        <div class="note" style="font-size: 13px; color: #777; margin-top: 5px;">Add a title that quickly and clearly describes this item</div>
                    </div>

                    <div class="input-group" style="margin-bottom: 25px;">
                        <label style="font-size: 15px; font-weight: 600; display: block; margin-bottom: 8px;">Description *</label>
                        <textarea name="description" id="itemDescription" placeholder="Describe your item..." class="item-textarea" style="width: 100%; padding: 12px; font-size: 15px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; min-height: 100px; resize: vertical;" required></textarea>
                        <div class="note" style="font-size: 13px; color: #777; margin-top: 5px;">Provide a detailed description of this item</div>
                    </div>

                    <div class="input-group" style="margin-bottom: 25px;">
                        <label style="font-size: 15px; font-weight: 600; display: block; margin-bottom: 8px;">Minimum Amount *</label>
                        <input type="number" name="minimum_amount" id="itemAmount" step="0.01" min="0" placeholder="0.00" class="item-input" style="width: 100%; padding: 12px; font-size: 15px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;" required>
                        <div class="note" style="font-size: 13px; color: #777; margin-top: 5px;">Minimum pledge amount required for this reward</div>
                    </div>

                    <div class="input-group" style="margin-bottom: 25px;">
                        <label style="font-size: 15px; font-weight: 600; display: block; margin-bottom: 8px;">Quantity (optional)</label>
                        <input type="number" name="quantity" id="itemQuantity" min="1" placeholder="Leave empty for unlimited" class="item-input" style="width: 100%; padding: 12px; font-size: 15px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
                        <div class="note" style="font-size: 13px; color: #777; margin-top: 5px;">Leave empty if you have unlimited quantity</div>
                    </div>

                    <div class="input-group" style="margin-bottom: 25px;">
                        <label style="font-size: 15px; font-weight: 600; display: block; margin-bottom: 8px;">Item image (optional)</label>
                        <div class="note" style="font-size: 13px; color: #777; margin-top: 5px;">Add a picture of your item to help backers understand exactly what comes with their rewards.</div>
                        <div class="upload-box" style="border: 2px dashed #ccc; border-radius: 10px; padding: 40px; text-align: center; margin-top: 15px;">
                            <input type="file" id="fileInput" name="image" accept="image/*" style="display:none">
                            <button type="button" class="upload-btn" onclick="document.getElementById('fileInput').click();" style="background: #efefef; border: 1px solid #bbb; padding: 10px 16px; border-radius: 6px; cursor: pointer; font-size: 14px;">
                                Upload a file
                            </button>
                            <div id="fileName" class="file-name" style="font-size: 14px; margin-top: 12px; color: #444; font-weight: 600;"></div>
                            <div id="imagePreview"></div>
                            <div class="upload-info" style="font-size: 13px; color: #888; margin-top: 15px;">
                                Image specifications: JPG, PNG, GIF, or WEBP, 3:2 ratio, 348 × 232 pixels, 50 MB maximum
                            </div>
                        </div>
                    </div>

                    <div class="form-actions" style="display: flex; gap: 10px; margin-top: 25px;">
                        <button type="submit" class="btn-save" id="saveBtn" style="padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 15px; background: #009b5b; color: white;">Save</button>
                        <button type="button" class="btn-cancel" onclick="cancelRewardForm()" style="padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 15px; background: #666; color: white;">Cancel</button>
                    </div>
                </form>
            </div>

            <script>
                // SHOW/HIDE LOGIC
                const newItemBtn = document.getElementById('newItemBtn');
                const rewardsMainContent = document.getElementById('rewardsMainContent');
                const itemForm = document.getElementById('itemForm');
                const rewardForm = document.getElementById('rewardForm');
                const formTitle = document.getElementById('formTitle');
                const formAction = document.getElementById('formAction');
                const rewardId = document.getElementById('rewardId');

                newItemBtn.addEventListener('click', function () {
                    resetRewardForm();
                    rewardsMainContent.style.display = "none";
                    itemForm.style.display = "block";
                    formTitle.textContent = "Add a new item";
                    formAction.value = "create";
                });

                function cancelRewardForm() {
                    rewardsMainContent.style.display = "block";
                    itemForm.style.display = "none";
                    resetRewardForm();
                }

                function resetRewardForm() {
                    rewardForm.reset();
                    document.getElementById('fileName').textContent = '';
                    document.getElementById('imagePreview').innerHTML = '';
                    rewardId.value = '';
                }

                // UPLOAD FILE NAME DISPLAY & PREVIEW
                const fileInput = document.getElementById('fileInput');
                const fileName = document.getElementById('fileName');
                const imagePreview = document.getElementById('imagePreview');

                fileInput.addEventListener('change', function () {
                    if (this.files.length > 0) {
                        fileName.textContent = "Selected file: " + this.files[0].name;
                        
                        // Show preview
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            imagePreview.innerHTML = '<img src="' + e.target.result + '" style="max-width: 200px; max-height: 200px; margin-top: 15px; border-radius: 5px; border: 1px solid #ddd;" alt="Preview">';
                        };
                        reader.readAsDataURL(this.files[0]);
                    } else {
                        fileName.textContent = '';
                        imagePreview.innerHTML = '';
                    }
                });

                // FORM SUBMISSION
                rewardForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const action = formAction.value;
                    const saveBtn = document.getElementById('saveBtn');
                    const originalText = saveBtn.textContent;
                    
                    saveBtn.disabled = true;
                    saveBtn.textContent = "Saving...";
                    
                    let url;
                    if (action === 'create') {
                        url = "{{ route('user.rewards.store', $campaign->slug) }}";
                    } else {
                        url = "{{ route('user.rewards.update', [$campaign->slug, ':id']) }}".replace(':id', rewardId.value);
                    }
                    
                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'An error occurred');
                            saveBtn.disabled = false;
                            saveBtn.textContent = originalText;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                        saveBtn.disabled = false;
                        saveBtn.textContent = originalText;
                    });
                });

                // EDIT REWARD
                function editReward(id) {
                    fetch("{{ route('user.rewards.edit', [$campaign->slug, ':id']) }}".replace(':id', id), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const reward = data.reward;
                            document.getElementById('itemTitle').value = reward.title || '';
                            document.getElementById('itemDescription').value = reward.description || '';
                            document.getElementById('itemAmount').value = reward.minimum_amount || '';
                            document.getElementById('itemQuantity').value = reward.quantity || '';
                            document.getElementById('rewardId').value = reward.id;
                            formAction.value = 'edit';
                            const tabTypeInput = document.getElementById('rewardTabType');
                            if (tabTypeInput) tabTypeInput.value = reward.reward_tab_type || 'items';
                            formTitle.textContent = "Edit item";
                            
                            // Clear file input
                            document.getElementById('fileInput').value = '';
                            document.getElementById('fileName').textContent = '';
                            
                            // Show existing image if available
                            if (reward.image_url) {
                                imagePreview.innerHTML = '<img src="' + reward.image_url + '" style="max-width: 200px; max-height: 200px; margin-top: 15px; border-radius: 5px; border: 1px solid #ddd;" alt="Preview"><br><small style="color: #666; margin-top: 5px; display: block;">Current image</small>';
                            } else {
                                imagePreview.innerHTML = '';
                            }
                            
                            rewardsMainContent.style.display = "none";
                            itemForm.style.display = "block";
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to load reward data');
                    });
                }

                // DELETE REWARD
                function deleteReward(id) {
                    if (!confirm('Are you sure you want to delete this reward?')) {
                        return;
                    }
                    
                    fetch("{{ route('user.rewards.destroy', [$campaign->slug, ':id']) }}".replace(':id', id), {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]').value
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Failed to delete reward');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
                }
            </script>
            @endif

            @if($currentSection == 'story')
            <form action="{{ route('user.campaign.update', $campaign->id) }}" method="POST" enctype="multipart/form-data" id="storyForm">
                @csrf
                <input type="hidden" name="section" value="story">
            <h1>Story</h1>
            <p class="subtitle">Tell your project's story and connect with backers.</p>
            
            <div class="box">
                <label>Project Story * <span id="charCount" style="color: #666; font-weight: normal; font-size: 14px;">(Minimum 30 characters)</span></label>
                <!-- Summernote Editor -->
                <textarea id="summernote" name="description" required>{{ old('description', $campaign->description ?? '') }}</textarea>
                
                <p class="note">Share the story behind your project and why it matters. Must be at least 30 characters.</p>
                @error('description')
                    <p class="note" style="color: red;">{{ $message }}</p>
                @enderror
                <p id="storyError" class="note" style="color: red; display: none;"></p>
            </div>

            <!-- SAVE AND NEXT BUTTON -->
            <div class="box" style="text-align: right; background: transparent; border: none; padding: 0; margin-top: 30px;">
                <input type="hidden" name="next_tab" value="people">
                <button type="submit" class="btn btn-success" id="storySubmitBtn" style="padding: 12px 40px; font-size: 16px; font-weight: 600; background: #16a34a; border: none; border-radius: 6px; cursor: pointer;">
                    <i class="fas fa-save"></i> Save and Next
                </button>
            </div>

            </form>
            @endif

            @if($currentSection == 'people')
            <h1>People</h1>
            <p class="subtitle">Manage team members and collaborators.</p>
            
            <div class="box">
                <h2 style="margin-top: 0; font-size: 22px; margin-bottom: 15px;">Campaign Creator</h2>
                <div style="display: flex; align-items: center; gap: 15px; padding: 15px; background: #f9f9f9; border-radius: 8px; margin-bottom: 25px;">
                    @php
                        $creator = $campaign->user;
                    @endphp
                    <div style="width: 50px; height: 50px; border-radius: 50%; overflow: hidden; background: #ddd; display: flex; align-items: center; justify-content: center;">
                        @if($creator->image)
                            <img src="{{ getImage(getFilePath('userProfile') . '/' . $creator->image, getFileSize('userProfile')) }}" alt="{{ $creator->fullname ?? $creator->username }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <span style="font-size: 20px; color: #666;">{{ strtoupper(substr($creator->fullname ?? $creator->username, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div style="flex: 1;">
                        <h3 style="margin: 0; font-size: 16px; font-weight: 600;">{{ $creator->fullname ?? $creator->username }}</h3>
                        <p style="margin: 5px 0 0; font-size: 14px; color: #666;">{{ $creator->email }}</p>
                    </div>
                    <span style="padding: 5px 12px; background: #028858; color: white; border-radius: 20px; font-size: 12px; font-weight: 600;">Creator</span>
                </div>
            </div>

            <div class="box">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0; font-size: 22px;">Collaborators</h2>
                    @if($campaign->user_id == auth()->id())
                        <button type="button" id="addCollaboratorBtn" style="padding: 10px 20px; background: #028858; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; font-weight: 600;">+ Add Collaborator</button>
                    @endif
                </div>

                <div id="collaboratorsList">
                    @forelse($collaborators ?? [] as $collaborator)
                        <div class="collaborator-item" data-user-id="{{ $collaborator->user_id }}" style="display: flex; align-items: center; gap: 15px; padding: 15px; background: #f9f9f9; border-radius: 8px; margin-bottom: 10px;">
                            <div style="width: 50px; height: 50px; border-radius: 50%; overflow: hidden; background: #ddd; display: flex; align-items: center; justify-content: center;">
                                @if($collaborator->user->image)
                                    <img src="{{ getImage(getFilePath('userProfile') . '/' . $collaborator->user->image, getFileSize('userProfile')) }}" alt="{{ $collaborator->user->fullname ?? $collaborator->user->username }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <span style="font-size: 20px; color: #666;">{{ strtoupper(substr($collaborator->user->fullname ?? $collaborator->user->username, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div style="flex: 1;">
                                <h3 style="margin: 0; font-size: 16px; font-weight: 600;">{{ $collaborator->user->fullname ?? $collaborator->user->username }}</h3>
                                <p style="margin: 5px 0 0; font-size: 14px; color: #666;">{{ $collaborator->user->email }}</p>
                            </div>
                            @if($campaign->user_id == auth()->id())
                                <button type="button" onclick="removeCollaborator({{ $collaborator->user_id }})" style="padding: 8px 16px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">Remove</button>
                            @endif
                        </div>
                    @empty
                        <div style="text-align: center; padding: 40px; color: #888;">
                            <p>No collaborators added yet.</p>
                            @if($campaign->user_id == auth()->id())
                                <p style="font-size: 14px; margin-top: 10px;">Click "Add Collaborator" to invite team members.</p>
                            @endif
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Add Collaborator Modal/Form -->
            @if($campaign->user_id == auth()->id())
            <div class="box" id="addCollaboratorForm" style="display: none; margin-top: 25px;">
                <h2 style="margin-top: 0; font-size: 22px; margin-bottom: 20px;">Add Collaborator</h2>
                
                <div style="margin-bottom: 20px;">
                    <label>Search User</label>
                    <input type="text" id="userSearchInput" placeholder="Search by name, email, or username..." style="width: 100%; padding: 12px; font-size: 15px; border: 1px solid #d9d9d9; border-radius: 8px;">
                    <p class="note">Start typing to search for users in the system.</p>
                </div>

                <div id="userSearchResults" style="max-height: 300px; overflow-y: auto; border: 1px solid #e3e3e3; border-radius: 8px; display: none;">
                    <!-- Search results will appear here -->
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="button" id="saveCollaboratorBtn" style="padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 15px; background: #028858; color: white; display: none;">Add Selected User</button>
                    <button type="button" onclick="cancelAddCollaborator()" style="padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 15px; background: #666; color: white;">Cancel</button>
                </div>
            </div>
            @endif

            <script>
                let selectedUserId = null;
                let searchTimeout = null;

                @if($campaign->user_id == auth()->id())
                // Show add collaborator form
                document.getElementById('addCollaboratorBtn')?.addEventListener('click', function() {
                    document.getElementById('addCollaboratorForm').style.display = 'block';
                    document.getElementById('addCollaboratorBtn').style.display = 'none';
                    document.getElementById('userSearchInput').focus();
                });

                // User search functionality
                document.getElementById('userSearchInput')?.addEventListener('input', function() {
                    const query = this.value.trim();
                    const resultsDiv = document.getElementById('userSearchResults');
                    const saveBtn = document.getElementById('saveCollaboratorBtn');

                    // Clear previous timeout
                    if (searchTimeout) {
                        clearTimeout(searchTimeout);
                    }

                    if (query.length < 2) {
                        resultsDiv.style.display = 'none';
                        saveBtn.style.display = 'none';
                        selectedUserId = null;
                        return;
                    }

                    // Debounce search
                    searchTimeout = setTimeout(() => {
                        fetch("{{ route('user.campaign.collaborators.search') }}?q=" + encodeURIComponent(query), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.users.length > 0) {
                                let html = '';
                                data.users.forEach(user => {
                                    html += `
                                        <div class="user-result-item" data-user-id="${user.id}" style="padding: 15px; cursor: pointer; border-bottom: 1px solid #eee; display: flex; align-items: center; gap: 15px;" onclick="selectUser(${user.id}, '${user.name.replace(/'/g, "\\'")}', '${user.email.replace(/'/g, "\\'")}')">
                                            <div style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; background: #ddd; display: flex; align-items: center; justify-content: center;">
                                                ${user.image ? `<img src="${user.image}" alt="${user.name}" style="width: 100%; height: 100%; object-fit: cover;">` : `<span style="font-size: 16px; color: #666;">${user.name.charAt(0).toUpperCase()}</span>`}
                                            </div>
                                            <div style="flex: 1;">
                                                <h4 style="margin: 0; font-size: 15px; font-weight: 600;">${user.name}</h4>
                                                <p style="margin: 3px 0 0; font-size: 13px; color: #666;">${user.email}</p>
                                            </div>
                                        </div>
                                    `;
                                });
                                resultsDiv.innerHTML = html;
                                resultsDiv.style.display = 'block';
                            } else {
                                resultsDiv.innerHTML = '<div style="padding: 20px; text-align: center; color: #888;">No users found</div>';
                                resultsDiv.style.display = 'block';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            resultsDiv.innerHTML = '<div style="padding: 20px; text-align: center; color: #dc3545;">Error searching users</div>';
                            resultsDiv.style.display = 'block';
                        });
                    }, 300);
                });

                function selectUser(userId, userName, userEmail) {
                    selectedUserId = userId;
                    document.getElementById('userSearchResults').innerHTML = `
                        <div style="padding: 15px; background: #d1f5d8; border-radius: 8px; display: flex; align-items: center; gap: 15px;">
                            <div style="flex: 1;">
                                <h4 style="margin: 0; font-size: 15px; font-weight: 600;">${userName}</h4>
                                <p style="margin: 3px 0 0; font-size: 13px; color: #666;">${userEmail}</p>
                            </div>
                            <span style="color: #028858; font-size: 14px;">Selected</span>
                        </div>
                    `;
                    document.getElementById('saveCollaboratorBtn').style.display = 'block';
                }

                function cancelAddCollaborator() {
                    document.getElementById('addCollaboratorForm').style.display = 'none';
                    document.getElementById('addCollaboratorBtn').style.display = 'block';
                    document.getElementById('userSearchInput').value = '';
                    document.getElementById('userSearchResults').innerHTML = '';
                    document.getElementById('userSearchResults').style.display = 'none';
                    document.getElementById('saveCollaboratorBtn').style.display = 'none';
                    selectedUserId = null;
                }

                // Add collaborator
                document.getElementById('saveCollaboratorBtn')?.addEventListener('click', function() {
                    if (!selectedUserId) {
                        alert('Please select a user');
                        return;
                    }

                    const btn = this;
                    btn.disabled = true;
                    btn.textContent = 'Adding...';

                    fetch("{{ route('user.campaign.collaborators.add', $campaign->slug) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]').value
                        },
                        body: JSON.stringify({
                            user_id: selectedUserId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Error adding collaborator');
                            btn.disabled = false;
                            btn.textContent = 'Add Selected User';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                        btn.disabled = false;
                        btn.textContent = 'Add Selected User';
                    });
                });

                // Remove collaborator
                function removeCollaborator(userId) {
                    if (!confirm('Are you sure you want to remove this collaborator?')) {
                        return;
                    }

                    fetch("{{ route('user.campaign.collaborators.remove', [$campaign->slug, ':userId']) }}".replace(':userId', userId), {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]').value
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Error removing collaborator');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
                }
                @endif
            </script>
            @endif

            @if($currentSection == 'documents')
            <form action="{{ route('user.campaign.update', $campaign->id) }}" method="POST" enctype="multipart/form-data" id="documentsForm">
                @csrf
                <input type="hidden" name="section" value="documents">
                <h1>Documents</h1>
                <p class="subtitle">Upload CNIC and business documents so admin can verify and approve your campaign.</p>
                @php $requiredDocuments = getCampaignDocumentRequirements(true, auth()->user()->country_name ?? session('user_detected_country')); @endphp

                <div class="box">
                    <label>Required Documents (Admin Defined)</label>
                    <ul style="margin: 10px 0 0 18px; color: #555;">
                        @foreach($requiredDocuments as $docItem)
                            <li style="margin-bottom: 6px;">
                                {{ $docItem['label'] }}
                                @if(!empty($docItem['is_required']))
                                    <span style="color:#c00; font-size:12px;">(Required)</span>
                                @else
                                    <span style="color:#777; font-size:12px;">(Optional)</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    <p class="note">Please upload the required documents below for admin verification.</p>
                </div>
                @php
                    $existingDocs = is_array($campaign->verification_documents ?? null) ? $campaign->verification_documents : [];
                @endphp
                @foreach($requiredDocuments as $docItem)
                    @php $fieldKey = $docItem['field_key']; @endphp
                    <div class="box">
                        <label>{{ $docItem['label'] }} @if(!empty($docItem['is_required']))* @endif</label>
                        @if(!empty($existingDocs[$fieldKey]))
                            <p class="note" style="margin-bottom: 10px;">
                                Existing file:
                                <a href="{{ asset(getFilePath('document') . '/' . $existingDocs[$fieldKey]) }}" target="_blank">View current file</a>
                            </p>
                        @endif
                        <input type="file" name="documents[{{ $fieldKey }}]" accept=".pdf,.jpg,.jpeg,.png,.webp">
                        <p class="note">Allowed: PDF, JPG, JPEG, PNG, WEBP (max 10MB)</p>
                        @error('documents.' . $fieldKey)
                            <p class="note" style="color: red;">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach

                <div class="box" style="text-align: right; background: transparent; border: none; padding: 0; margin-top: 30px;">
                    <input type="hidden" name="next_tab" value="payment">
                    <button type="submit" class="btn btn-success" style="padding: 12px 40px; font-size: 16px; font-weight: 600; background: #16a34a; border: none; border-radius: 6px; cursor: pointer;">
                        <i class="fas fa-save"></i> Save and Next
                    </button>
                </div>
            </form>
            @endif

            @if($currentSection == 'payment')
            <h1>Payment</h1>
            <p class="subtitle">Configure payment settings for your campaign. Select your payout bank and provide account details.</p>
            
            <div class="box">
                <form id="paymentForm" method="POST" action="{{ route('user.campaign.edit.payment.update', $campaign->slug) }}">
                    @csrf
                    
                    <div style="margin-bottom: 20px;">
                        <label>Select Payout Bank *</label>
                        <select name="payout_bank_id" id="payout_bank_id" required style="width: 100%; padding: 12px; font-size: 15px; border: 1px solid #d9d9d9; border-radius: 8px;">
                            <option value="">-- Select Bank --</option>
                            @foreach($payoutBanks ?? [] as $bank)
                                <option value="{{ $bank->id }}" {{ $campaign->payout_bank_id == $bank->id ? 'selected' : '' }}>
                                    {{ $bank->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="note">Select the bank where you want to receive payouts.</p>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label>Account Number or Email *</label>
                        <input type="text" name="bank_account_number" id="bank_account_number" 
                               value="{{ $campaign->bank_account_number ?? '' }}" 
                               placeholder="Enter account number or email" 
                               required 
                               style="width: 100%; padding: 12px; font-size: 15px; border: 1px solid #d9d9d9; border-radius: 8px;">
                        <p class="note">Enter your bank account number or email address for receiving payouts.</p>
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 25px;">
                        <button type="submit" style="padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 15px; background: #028858; color: white;">Save Payment Details</button>
                    </div>
                </form>
            </div>
            @endif

            @if($currentSection == 'boost')
            <h1>Boost</h1>
            <p class="subtitle">Promote your campaign and reach more backers.</p>
            
            <div class="box" style="text-align: center; padding: 60px 40px;">
                <!-- Coming Soon Icon -->
                <div style="margin-bottom: 25px;">
                    <svg width="80" height="80" viewBox="0 0 100 100" style="opacity: 0.7;">
                        <circle cx="50" cy="50" r="45" fill="none" stroke="#16a34a" stroke-width="3"/>
                        <path d="M35 50 L45 60 L65 35" fill="none" stroke="#16a34a" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="75" cy="25" r="8" fill="#16a34a"/>
                        <text x="75" y="29" font-size="12" fill="white" text-anchor="middle" font-weight="bold">?</text>
                    </svg>
                </div>

                <!-- Coming Soon Text -->
                <h2 style="font-size: 32px; margin-bottom: 15px; color: #16a34a; font-weight: 600;">
                    Coming Soon
                </h2>
                
                <p style="font-size: 18px; color: #666; margin-bottom: 30px; max-width: 600px; margin-left: auto; margin-right: auto; line-height: 1.6;">
                    We're working on powerful promotion features to help you reach more backers and boost your campaign's visibility.
                </p>

                <!-- Features Preview -->
                <div style="display: flex; gap: 30px; justify-content: center; flex-wrap: wrap; margin-top: 40px; text-align: left; max-width: 700px; margin-left: auto; margin-right: auto;">
                    <div style="flex: 1; min-width: 200px;">
                        <div style="width: 50px; height: 50px; background: #e8f5e9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                            <i class="fas fa-bullhorn" style="color: #16a34a; font-size: 22px;"></i>
                        </div>
                        <h4 style="font-size: 16px; margin-bottom: 8px; color: #333;">Social Media Ads</h4>
                        <p style="font-size: 14px; color: #666; line-height: 1.5; margin: 0;">
                            Promote your campaign on Facebook, Instagram, and other platforms.
                        </p>
                    </div>

                    <div style="flex: 1; min-width: 200px;">
                        <div style="width: 50px; height: 50px; background: #e8f5e9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                            <i class="fas fa-chart-line" style="color: #16a34a; font-size: 22px;"></i>
                        </div>
                        <h4 style="font-size: 16px; margin-bottom: 8px; color: #333;">Analytics</h4>
                        <p style="font-size: 14px; color: #666; line-height: 1.5; margin: 0;">
                            Track your campaign performance with detailed insights.
                        </p>
                    </div>

                    <div style="flex: 1; min-width: 200px;">
                        <div style="width: 50px; height: 50px; background: #e8f5e9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                            <i class="fas fa-users" style="color: #16a34a; font-size: 22px;"></i>
                        </div>
                        <h4 style="font-size: 16px; margin-bottom: 8px; color: #333;">Audience Targeting</h4>
                        <p style="font-size: 14px; color: #666; line-height: 1.5; margin: 0;">
                            Reach the right people who are interested in your campaign.
                        </p>
                    </div>
                </div>

                <!-- Call to Action -->
                <div style="margin-top: 50px; padding: 25px; background: #f8f9fa; border-radius: 12px; border-left: 4px solid #16a34a;">
                    <p style="font-size: 15px; color: #555; margin: 0; line-height: 1.6;">
                        <i class="fas fa-lightbulb" style="color: #16a34a; margin-right: 8px;"></i>
                        <strong>In the meantime:</strong> Share your campaign on social media, reach out to your network, and engage with your backers to build momentum.
                    </p>
                </div>
            </div>
            @endif

            @if($currentSection == 'faq')
            @php
                $faqs = $faqs ?? $campaign->faqs()->orderBy('order')->orderBy('id')->get();
            @endphp
            
            <h1>Frequently Asked Questions</h1>
            <p class="subtitle">Add FAQs to help backers understand your campaign better.</p>
            
            <!-- FAQ List -->
            <div id="faqList" style="margin-bottom: 30px;">
                @forelse($faqs as $faq)
                <div class="box faq-item" data-faq-id="{{ $faq->id }}" style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                        <div style="flex: 1;">
                            <h3 style="margin: 0 0 8px; font-size: 18px; font-weight: 600;">{{ $faq->question }}</h3>
                            <p style="margin: 0; color: #666; font-size: 15px; line-height: 1.6;">{{ $faq->answer }}</p>
                        </div>
                        <div style="display: flex; gap: 10px; margin-left: 20px;">
                            <button type="button" onclick="editFaq({{ $faq->id }})" style="padding: 8px 16px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; background: #007bff; color: white;">Edit</button>
                            <button type="button" onclick="deleteFaq({{ $faq->id }})" style="padding: 8px 16px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; background: #dc3545; color: white;">Delete</button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="box" style="text-align: center; padding: 40px; color: #888;">
                    <p>No FAQs added yet. Click "Add FAQ" to create your first FAQ.</p>
                </div>
                @endforelse
            </div>

            <!-- Add/Edit FAQ Form -->
            <div class="box" id="faqForm" style="display: none;">
                <h2 id="faqFormTitle" style="margin-top: 0; font-size: 22px; margin-bottom: 20px;">Add FAQ</h2>
                
                <form id="faqFormElement">
                    @csrf
                    <input type="hidden" id="faqId" name="faq_id">
                    
                    <div style="margin-bottom: 20px;">
                        <label>Question *</label>
                        <input type="text" id="faqQuestion" name="question" placeholder="Enter your question..." required style="width: 100%; padding: 12px; font-size: 15px; border: 1px solid #d9d9d9; border-radius: 8px;">
                        <p class="note">Write a clear and concise question.</p>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label>Answer *</label>
                        <textarea id="faqAnswer" name="answer" placeholder="Enter the answer..." required style="width: 100%; padding: 12px; font-size: 15px; border: 1px solid #d9d9d9; border-radius: 8px; min-height: 120px; resize: vertical;"></textarea>
                        <p class="note">Provide a detailed answer to help backers understand.</p>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label>Order (optional)</label>
                        <input type="number" id="faqOrder" name="order" value="0" min="0" placeholder="0" style="width: 100%; padding: 12px; font-size: 15px; border: 1px solid #d9d9d9; border-radius: 8px;">
                        <p class="note">Lower numbers appear first. Leave as 0 for default order.</p>
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 25px;">
                        <button type="submit" id="faqSaveBtn" style="padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 15px; background: #028858; color: white;">Save FAQ</button>
                        <button type="button" onclick="cancelFaqForm()" style="padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 15px; background: #666; color: white;">Cancel</button>
                    </div>
                </form>
            </div>

            <!-- Add FAQ Button -->
            <button type="button" id="addFaqBtn" onclick="showFaqForm()" style="padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 15px; background: #028858; color: white; margin-top: 20px;">+ Add FAQ</button>

            <script>
                function showFaqForm() {
                    document.getElementById('faqForm').style.display = 'block';
                    document.getElementById('addFaqBtn').style.display = 'none';
                    document.getElementById('faqFormTitle').textContent = 'Add FAQ';
                    document.getElementById('faqFormElement').reset();
                    document.getElementById('faqId').value = '';
                    document.getElementById('faqOrder').value = '0';
                    
                    // Scroll to form
                    document.getElementById('faqForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
                    
                    if (typeof window.showActionButtons === 'function') {
                        window.showActionButtons();
                    }
                }

                function cancelFaqForm() {
                    document.getElementById('faqForm').style.display = 'none';
                    document.getElementById('addFaqBtn').style.display = 'block';
                    document.getElementById('faqFormElement').reset();
                    document.getElementById('faqId').value = '';
                }

                function editFaq(id) {
                    fetch("{{ route('user.campaign.faq.get', [$campaign->slug, ':id']) }}".replace(':id', id), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const faq = data.faq;
                            document.getElementById('faqQuestion').value = faq.question || '';
                            document.getElementById('faqAnswer').value = faq.answer || '';
                            document.getElementById('faqOrder').value = faq.order || '0';
                            document.getElementById('faqId').value = faq.id;
                            document.getElementById('faqFormTitle').textContent = 'Edit FAQ';
                            document.getElementById('faqForm').style.display = 'block';
                            document.getElementById('addFaqBtn').style.display = 'none';
                            
                            // Scroll to form
                            document.getElementById('faqForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
                            
                            if (typeof window.showActionButtons === 'function') {
                                window.showActionButtons();
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to load FAQ data');
                    });
                }

                function deleteFaq(id) {
                    if (!confirm('Are you sure you want to delete this FAQ?')) {
                        return;
                    }
                    
                    fetch("{{ route('user.campaign.faq.delete', [$campaign->slug, ':id']) }}".replace(':id', id), {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]').value
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Failed to delete FAQ');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
                }

                // FAQ Form Submission
                document.getElementById('faqFormElement').addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const faqId = document.getElementById('faqId').value;
                    const saveBtn = document.getElementById('faqSaveBtn');
                    const originalText = saveBtn.textContent;
                    
                    saveBtn.disabled = true;
                    saveBtn.textContent = "Saving...";
                    
                    let url;
                    if (faqId) {
                        url = "{{ route('user.campaign.faq.update', [$campaign->slug, ':id']) }}".replace(':id', faqId);
                    } else {
                        url = "{{ route('user.campaign.faq.store', $campaign->slug) }}";
                    }
                    
                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'An error occurred');
                            saveBtn.disabled = false;
                            saveBtn.textContent = originalText;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                        saveBtn.disabled = false;
                        saveBtn.textContent = originalText;
                    });
                });

                // Track form changes
                const faqForm = document.getElementById('faqFormElement');
                if (faqForm) {
                    const formFields = faqForm.querySelectorAll('input, textarea');
                    formFields.forEach(field => {
                        field.addEventListener('input', function() {
                            if (typeof window.showActionButtons === 'function') {
                                window.showActionButtons();
                            }
                        });
                    });
                }
            </script>
            @endif

            @if($currentSection == 'updates')
            @php
                $updates = $updates ?? $campaign->updates()->latest()->get();
            @endphp
            
            <h1>Creator Blog / Updates</h1>
            <p class="subtitle">Share updates and progress with your backers through blog posts.</p>
            
            <!-- Updates List -->
            <div id="updatesList" style="margin-bottom: 30px;">
                @forelse($updates as $update)
                <div class="box update-item" data-update-id="{{ $update->id }}" style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                        <div style="flex: 1;">
                            <h3 style="margin: 0 0 8px; font-size: 18px; font-weight: 600;">{{ $update->title }}</h3>
                            <p style="margin: 0; color: #666; font-size: 14px; line-height: 1.6;">{{ strLimit(strip_tags($update->content), 150) }}</p>
                            @if($update->image)
                                <img src="{{ getImage(getFilePath('campaign') . '/' . $update->image, getFileSize('campaign')) }}" 
                                     alt="{{ $update->title }}" 
                                     style="max-width: 200px; max-height: 150px; margin-top: 10px; border-radius: 8px; border: 1px solid #ddd;">
                            @endif
                            <p style="margin: 5px 0 0; color: #999; font-size: 12px;">{{ $update->created_at->format('M d, Y') }} • {{ $update->is_published ? 'Published' : 'Draft' }}</p>
                        </div>
                        <div style="display: flex; gap: 10px; margin-left: 20px;">
                            <button type="button" onclick="editUpdate({{ $update->id }})" style="padding: 8px 16px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; background: #007bff; color: white;">Edit</button>
                            <button type="button" onclick="deleteUpdate({{ $update->id }})" style="padding: 8px 16px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; background: #dc3545; color: white;">Delete</button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="box" style="text-align: center; padding: 40px; color: #888;">
                    <p>No updates posted yet. Click "Add Update" to create your first blog post.</p>
                </div>
                @endforelse
            </div>

            <!-- Add/Edit Update Form -->
            <div class="box" id="updateForm" style="display: none;">
                <h2 id="updateFormTitle" style="margin-top: 0; font-size: 22px; margin-bottom: 20px;">Add Update</h2>
                
                <form id="updateFormElement">
                    @csrf
                    <input type="hidden" id="updateId" name="update_id">
                    
                    <div style="margin-bottom: 20px;">
                        <label>Title *</label>
                        <input type="text" id="updateTitle" name="title" placeholder="Enter update title..." required style="width: 100%; padding: 12px; font-size: 15px; border: 1px solid #d9d9d9; border-radius: 8px;">
                        <p class="note">Write a clear and engaging title for your update.</p>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label>Content *</label>
                        <textarea id="updateContent" name="content" placeholder="Write your update content..." required style="width: 100%; min-height: 200px;"></textarea>
                        <p class="note">Share your progress, milestones, or any news with your backers.</p>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label>Image (optional)</label>
                        <div class="upload-box" style="border: 2px dashed #ccc; border-radius: 10px; padding: 20px; text-align: center; margin-top: 10px;">
                            <input type="file" id="updateImageInput" name="image" accept="image/*" style="display:none">
                            <button type="button" onclick="document.getElementById('updateImageInput').click();" style="background: #efefef; border: 1px solid #bbb; padding: 10px 16px; border-radius: 6px; cursor: pointer; font-size: 14px;">
                                Upload Image
                            </button>
                            <div id="updateImagePreview" style="margin-top: 15px;"></div>
                            <div class="note" style="font-size: 13px; color: #888; margin-top: 10px;">
                                JPG, PNG, GIF, or WEBP, 50 MB maximum
                            </div>
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: flex; align-items: center; gap: 10px;">
                            <input type="checkbox" id="updateIsPublished" name="is_published" value="1" checked style="width: auto;">
                            <span>Publish immediately</span>
                        </label>
                        <p class="note">Uncheck to save as draft.</p>
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 25px;">
                        <button type="submit" id="updateSaveBtn" style="padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 15px; background: #028858; color: white;">Save Update</button>
                        <button type="button" onclick="cancelUpdateForm()" style="padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 15px; background: #666; color: white;">Cancel</button>
                    </div>
                </form>
            </div>

            <!-- Add Update Button -->
            <button type="button" id="addUpdateBtn" onclick="showUpdateForm()" style="padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 15px; background: #028858; color: white; margin-top: 20px;">+ Add Update</button>

            <script>
                // Initialize Summernote for Updates
                function initializeUpdatesSummernote() {
                    if (typeof jQuery === 'undefined' || typeof jQuery.fn.summernote === 'undefined') {
                        setTimeout(initializeUpdatesSummernote, 100);
                        return;
                    }
                    
                    try {
                        jQuery('#updateContent').summernote({
                            height: 250,
                            minHeight: 250,
                            maxHeight: 500,
                            placeholder: 'Write your update content here...',
                            toolbar: [
                                ['style', ['style', 'bold', 'italic', 'underline', 'clear']],
                                ['font', ['strikethrough', 'superscript', 'subscript']],
                                ['fontsize', ['fontsize']],
                                ['color', ['color']],
                                ['para', ['ul', 'ol', 'paragraph']],
                                ['height', ['height']],
                                ['insert', ['link', 'picture', 'video']],
                                ['view', ['fullscreen', 'codeview', 'help']]
                            ]
                        });
                        console.log('Updates Summernote Editor initialized successfully');
                    } catch (error) {
                        console.error('Error initializing Updates Summernote Editor:', error);
                    }
                }
                
                // Wait for jQuery and Summernote library to load
                function waitForSummernoteAndInitializeUpdates() {
                    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.summernote !== 'undefined') {
                        initializeUpdatesSummernote();
                    } else {
                        setTimeout(waitForSummernoteAndInitializeUpdates, 100);
                    }
                }
                
                // Initialize on page load
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', waitForSummernoteAndInitializeUpdates);
                } else {
                    waitForSummernoteAndInitializeUpdates();
                }

                function showUpdateForm() {
                    document.getElementById('updateForm').style.display = 'block';
                    document.getElementById('addUpdateBtn').style.display = 'none';
                    document.getElementById('updateFormTitle').textContent = 'Add Update';
                    document.getElementById('updateFormElement').reset();
                    document.getElementById('updateId').value = '';
                    document.getElementById('updateImagePreview').innerHTML = '';
                    document.getElementById('updateIsPublished').checked = true;
                    
                    // Clear Summernote editor
                    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.summernote !== 'undefined') {
                        jQuery('#updateContent').summernote('code', '');
                    }
                    
                    // Scroll to form
                    document.getElementById('updateForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
                    
                    if (typeof window.showActionButtons === 'function') {
                        window.showActionButtons();
                    }
                }

                function cancelUpdateForm() {
                    document.getElementById('updateForm').style.display = 'none';
                    document.getElementById('addUpdateBtn').style.display = 'block';
                    document.getElementById('updateFormElement').reset();
                    document.getElementById('updateId').value = '';
                    document.getElementById('updateImagePreview').innerHTML = '';
                    
                    // Clear Summernote editor
                    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.summernote !== 'undefined') {
                        jQuery('#updateContent').summernote('code', '');
                    }
                }

                // Image preview
                document.getElementById('updateImageInput').addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            document.getElementById('updateImagePreview').innerHTML = 
                                '<img src="' + e.target.result + '" style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd; margin-top: 10px;" alt="Preview">';
                        };
                        reader.readAsDataURL(file);
                    }
                });

                function editUpdate(id) {
                    fetch("{{ route('user.campaign.updates.get', [$campaign->slug, ':id']) }}".replace(':id', id), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const update = data.update;
                            document.getElementById('updateTitle').value = update.title || '';
                            
                            // Set Summernote content
                            if (typeof jQuery !== 'undefined' && typeof jQuery.fn.summernote !== 'undefined') {
                                jQuery('#updateContent').summernote('code', update.content || '');
                            } else {
                                document.getElementById('updateContent').value = update.content || '';
                            }
                            
                            document.getElementById('updateIsPublished').checked = update.is_published == 1;
                            document.getElementById('updateId').value = update.id;
                            document.getElementById('updateFormTitle').textContent = 'Edit Update';
                            document.getElementById('updateForm').style.display = 'block';
                            document.getElementById('addUpdateBtn').style.display = 'none';
                            
                            // Clear file input
                            document.getElementById('updateImageInput').value = '';
                            
                            // Show existing image if available
                            if (update.image_url) {
                                document.getElementById('updateImagePreview').innerHTML = 
                                    '<img src="' + update.image_url + '" style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd; margin-top: 10px;" alt="Preview"><br><small style="color: #666; margin-top: 5px; display: block;">Current image</small>';
                            } else {
                                document.getElementById('updateImagePreview').innerHTML = '';
                            }
                            
                            // Scroll to form
                            document.getElementById('updateForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
                            
                            if (typeof window.showActionButtons === 'function') {
                                window.showActionButtons();
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to load update data');
                    });
                }

                function deleteUpdate(id) {
                    if (!confirm('Are you sure you want to delete this update?')) {
                        return;
                    }
                    
                    fetch("{{ route('user.campaign.updates.delete', [$campaign->slug, ':id']) }}".replace(':id', id), {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]').value
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Failed to delete update');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
                }

                // Update Form Submission
                document.getElementById('updateFormElement').addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Sync Summernote content to textarea before submission
                    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.summernote !== 'undefined') {
                        const summernoteContent = jQuery('#updateContent').summernote('code');
                        document.getElementById('updateContent').value = summernoteContent;
                    }
                    
                    const formData = new FormData(this);
                    const updateId = document.getElementById('updateId').value;
                    const saveBtn = document.getElementById('updateSaveBtn');
                    const originalText = saveBtn.textContent;
                    
                    saveBtn.disabled = true;
                    saveBtn.textContent = "Saving...";
                    
                    let url;
                    if (updateId) {
                        url = "{{ route('user.campaign.updates.update', [$campaign->slug, ':id']) }}".replace(':id', updateId);
                    } else {
                        url = "{{ route('user.campaign.updates.store', $campaign->slug) }}";
                    }
                    
                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'An error occurred');
                            saveBtn.disabled = false;
                            saveBtn.textContent = originalText;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                        saveBtn.disabled = false;
                        saveBtn.textContent = originalText;
                    });
                });

                // Track form changes
                const updateForm = document.getElementById('updateFormElement');
                if (updateForm) {
                    const formFields = updateForm.querySelectorAll('input, textarea');
                    formFields.forEach(field => {
                        field.addEventListener('input', function() {
                            if (typeof window.showActionButtons === 'function') {
                                window.showActionButtons();
                            }
                        });
                    });
                    
                    // Track Summernote changes
                    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.summernote !== 'undefined') {
                        jQuery('#updateContent').on('summernote.change', function() {
                            if (typeof window.showActionButtons === 'function') {
                                window.showActionButtons();
                            }
                        });
                    }
                }
            </script>
            @endif

        </div>
    </div>

    <!-- JS -->
    <script>
        // Top Edit Button Handler - Works for all sections
        (function() {
            const topEditBtn = document.getElementById("topEditBtn");
            const topActionButtons = document.getElementById("topActionButtons");
            const topSaveBtn = document.getElementById("topSaveBtn");
            const topExitBtn = document.getElementById("topExitBtn");
            const currentSection = "{{ $currentSection }}";
            
            // Function to show action buttons
            function showActionButtons() {
                if (topEditBtn && topActionButtons) {
                    topEditBtn.style.display = 'none';
                    topActionButtons.style.display = 'flex';
                }
            }

            // Edit button click handler
            if (topEditBtn && topActionButtons) {
                topEditBtn.addEventListener('click', function() {
                    showActionButtons();
                });
            }

            // Top Save Button - Submit form based on section
            if (topSaveBtn) {
                topSaveBtn.addEventListener('click', function() {
                    if (currentSection === 'basics') {
                        const basicsForm = document.getElementById("basicsForm");
                        if (basicsForm) {
                            basicsForm.submit();
                        }
                    } else if (currentSection === 'story') {
                        const storyForm = document.getElementById("storyForm");
                        if (storyForm) {
                            storyForm.submit();
                        }
                    } else if (currentSection === 'documents') {
                        const documentsForm = document.getElementById("documentsForm");
                        if (documentsForm) {
                            documentsForm.submit();
                        }
                    } else if (currentSection === 'faq') {
                        const faqForm = document.getElementById("faqFormElement");
                        if (faqForm && document.getElementById("faqForm").style.display !== 'none') {
                            faqForm.dispatchEvent(new Event('submit'));
                        }
                    }
                });
            }

            // Top Exit Button
            if (topExitBtn) {
                topExitBtn.addEventListener('click', function() {
                    if (confirm('Are you sure you want to exit?')) {
                        window.location.href = "{{ route('user.campaign.index') }}";
                    }
                });
            }

            // Make showActionButtons available globally
            window.showActionButtons = showActionButtons;
        })();

        @if($currentSection == 'basics')
        // Real-time goal amount conversion (when ?test=1)
        (function() {
            const convDiv = document.getElementById("goalRealtimeConversion");
            const goalInput = document.getElementById("goalAmountInput");
            if (convDiv && goalInput) {
                const rate = parseFloat(convDiv.dataset.rate) || 1;
                const creatorSym = convDiv.dataset.creatorSym || '';
                const platformSym = convDiv.dataset.platformSym || '';
                const creatorCode = convDiv.dataset.creatorCode || '';
                const platformCode = convDiv.dataset.platformCode || '';
                const resultEl = document.getElementById("conversionResult");
                function updateConversion() {
                    const amount = parseFloat(goalInput.value) || 0;
                    const platformAmount = amount * rate;
                    if (amount > 0) {
                        resultEl.textContent = creatorSym + amount.toLocaleString() + ' ' + creatorCode + ' ≈ ' + platformSym + platformAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' ' + platformCode;
                    } else {
                        resultEl.textContent = '—';
                    }
                }
                goalInput.addEventListener('input', updateConversion);
                goalInput.addEventListener('change', updateConversion);
                updateConversion();
            }
        })();

        // Date validation - max 30 days from start date
        (function() {
            const startDateField = document.querySelector('input[name="start_date"]');
            const endDateField = document.querySelector('input[name="end_date"]');
            
            function updateEndDateConstraints() {
                if (startDateField && endDateField && startDateField.value) {
                    const startDate = new Date(startDateField.value);
                    const maxEndDate = new Date(startDate);
                    maxEndDate.setDate(maxEndDate.getDate() + 30);
                    
                    // Set min date for end date (day after start date)
                    const minEndDate = new Date(startDate);
                    minEndDate.setDate(minEndDate.getDate() + 1);
                    
                    endDateField.setAttribute('min', minEndDate.toISOString().split('T')[0]);
                    endDateField.setAttribute('max', maxEndDate.toISOString().split('T')[0]);
                }
            }
            
            if (startDateField) {
                startDateField.addEventListener('change', updateEndDateConstraints);
                // Initial call to set constraints on page load
                updateEndDateConstraints();
            }
        })();
        
        // Basics form handling
        (function() {
            const basicsForm = document.getElementById("basicsForm");
            let initialValues = {};
            
            if (basicsForm) {
                // Capture initial form values
                const allFields = basicsForm.querySelectorAll('input, textarea, select');
                allFields.forEach(field => {
                    if (field.name) {
                        if (field.type === 'file') {
                            initialValues[field.name] = null;
                        } else {
                            initialValues[field.name] = field.value || '';
                        }
                    }
                });

                // Function to check if form has changed
                function checkFormChanges() {
                    let formChanged = false;
                    
                    const allFields = basicsForm.querySelectorAll('input, textarea, select');
                    allFields.forEach(field => {
                        if (!field.name) return;
                        
                        const fieldName = field.name;
                        let currentValue = '';
                        
                        if (field.type === 'file') {
                            if (field.files && field.files.length > 0) {
                                formChanged = true;
                                return;
                            }
                        } else if (field.type === 'checkbox' || field.type === 'radio') {
                            currentValue = field.checked ? field.value : '';
                        } else {
                            currentValue = field.value || '';
                        }
                        
                        const initialValue = initialValues[fieldName] || '';
                        
                        if (currentValue !== initialValue) {
                            formChanged = true;
                        }
                    });

                    // Show buttons if form changed
                    if (formChanged && typeof window.showActionButtons === 'function') {
                        window.showActionButtons();
                    }
                }

                // Add event listeners to all form fields
                const formFields = basicsForm.querySelectorAll('input, textarea, select');
                formFields.forEach(field => {
                    field.addEventListener('input', checkFormChanges);
                    field.addEventListener('change', checkFormChanges);
                    field.addEventListener('keyup', checkFormChanges);
                });

                // Form submission handling
                basicsForm.addEventListener("submit", function(e) {
                    const topSaveBtn = document.getElementById("topSaveBtn");
                    if (topSaveBtn) {
                        topSaveBtn.disabled = true;
                        topSaveBtn.textContent = "Saving...";
                    }
                });
            }

            // Image preview functionality
            const projectImage = document.getElementById("projectImage");
            const preview = document.getElementById("preview");

            if (projectImage) {
                projectImage.addEventListener("change", function() {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = e => {
                            preview.src = e.target.result;
                            preview.style.display = "block";
                        }
                        reader.readAsDataURL(file);
                        // Show buttons when image is selected
                        if (typeof window.showActionButtons === 'function') {
                            window.showActionButtons();
                        }
                    } else {
                        if (!preview.src || preview.src === '') {
                            preview.style.display = "none";
                        }
                    }
                });
            }

            // Image validation function for campaign image
            function validateCampaignImage(file) {
                // For now, always return true as requested
                // This function can be extended later for AI image safety check
                return true;
            }

            // Campaign image validation on selection
            const campaignImageInput = document.getElementById("campaignImageInput");
            const imageErrorMsg = document.getElementById("imageErrorMsg");
            const basicsSubmitBtn = document.getElementById("basicsSubmitBtn");
            const campaignImagePreview = document.getElementById("campaignImagePreview");

            if (campaignImageInput && imageErrorMsg && basicsSubmitBtn) {
                const uploadedImageName = document.getElementById("uploadedImageName");
                const uploadedImageOriginalName = document.getElementById("uploadedImageOriginalName");
                const imageUploadStatus = document.getElementById("imageUploadStatus");
                
                campaignImageInput.addEventListener("change", function() {
                    const file = this.files[0];
                    if (file) {
                        // Validate image
                        const isValid = validateCampaignImage(file);
                        
                        if (!isValid) {
                            imageErrorMsg.textContent = "Image is not valid. Please select a different image.";
                            imageErrorMsg.style.display = "block";
                            imageUploadStatus.style.display = "none";
                            basicsSubmitBtn.disabled = true;
                            basicsSubmitBtn.style.opacity = "0.5";
                            basicsSubmitBtn.style.cursor = "not-allowed";
                            
                            // Hide preview if invalid
                            if (campaignImagePreview) {
                                const currentImg = document.getElementById("currentCampaignImage");
                                if (currentImg) currentImg.style.display = "none";
                            }
                        } else {
                            imageErrorMsg.style.display = "none";
                            
                            // Show uploading status
                            imageUploadStatus.textContent = "Uploading image...";
                            imageUploadStatus.style.color = "#16a34a";
                            imageUploadStatus.style.display = "block";
                            
                            // Disable submit button while uploading
                            basicsSubmitBtn.disabled = true;
                            basicsSubmitBtn.style.opacity = "0.5";
                            basicsSubmitBtn.style.cursor = "not-allowed";
                            
                            // Show preview immediately
                            if (campaignImagePreview) {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    // Hide current image if exists
                                    const currentImg = document.getElementById("currentCampaignImage");
                                    if (currentImg) currentImg.style.display = "none";
                                    
                                    // Create or update preview
                                    let previewImg = campaignImagePreview.querySelector('img:not(#currentCampaignImage)');
                                    if (!previewImg) {
                                        previewImg = document.createElement('img');
                                        previewImg.style.cssText = "max-width: 300px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd;";
                                        campaignImagePreview.appendChild(previewImg);
                                    }
                                    previewImg.src = e.target.result;
                                    previewImg.alt = "Image Preview";
                                };
                                reader.readAsDataURL(file);
                            }
                            
                            // Upload image immediately via AJAX
                            const formData = new FormData();
                            formData.append('image', file);
                            formData.append('_token', '{{ csrf_token() }}');
                            
                            fetch('{{ route("user.campaign.upload-campaign-image") }}', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Store uploaded image name
                                    if (uploadedImageName) {
                                        uploadedImageName.value = data.image;
                                    }
                                    if (uploadedImageOriginalName) {
                                        uploadedImageOriginalName.value = data.image_original || "";
                                    }
                                    
                                    // Update preview with uploaded image URL
                                    if (campaignImagePreview && data.image_url) {
                                        let previewImg = campaignImagePreview.querySelector('img:not(#currentCampaignImage)');
                                        if (previewImg) {
                                            previewImg.src = data.image_url;
                                        }
                                    }
                                    
                                    // Show success message
                                    imageUploadStatus.textContent = "✓ Image uploaded successfully";
                                    imageUploadStatus.style.color = "#16a34a";
                                    
                                    // Enable submit button
                                    basicsSubmitBtn.disabled = false;
                                    basicsSubmitBtn.style.opacity = "1";
                                    basicsSubmitBtn.style.cursor = "pointer";
                                } else {
                                    // Show error
                                    imageErrorMsg.textContent = data.message || "Image upload failed";
                                    imageErrorMsg.style.display = "block";
                                    imageUploadStatus.style.display = "none";
                                    basicsSubmitBtn.disabled = true;
                                    basicsSubmitBtn.style.opacity = "0.5";
                                    basicsSubmitBtn.style.cursor = "not-allowed";
                                }
                            })
                            .catch(error => {
                                console.error('Upload error:', error);
                                imageErrorMsg.textContent = "Failed to upload image. Please try again.";
                                imageErrorMsg.style.display = "block";
                                imageUploadStatus.style.display = "none";
                                basicsSubmitBtn.disabled = true;
                                basicsSubmitBtn.style.opacity = "0.5";
                                basicsSubmitBtn.style.cursor = "not-allowed";
                            });
                        }
                    } else {
                        // No file selected, enable submit button
                        imageErrorMsg.style.display = "none";
                        imageUploadStatus.style.display = "none";
                        if (uploadedImageName) uploadedImageName.value = "";
                        if (uploadedImageOriginalName) uploadedImageOriginalName.value = "";
                        basicsSubmitBtn.disabled = false;
                        basicsSubmitBtn.style.opacity = "1";
                        basicsSubmitBtn.style.cursor = "pointer";
                        
                        // Show current image again if exists
                        if (campaignImagePreview) {
                            const currentImg = document.getElementById("currentCampaignImage");
                            if (currentImg) currentImg.style.display = "block";
                            const previewImg = campaignImagePreview.querySelector('img:not(#currentCampaignImage)');
                            if (previewImg) previewImg.remove();
                        }
                    }
                });
            }

            // Campaign video upload on selection
            const campaignVideoInput = document.getElementById("campaignVideoInput");
            const videoErrorMsg = document.getElementById("videoErrorMsg");
            const videoUploadStatus = document.getElementById("videoUploadStatus");
            const uploadedVideoName = document.getElementById("uploadedVideoName");

            if (campaignVideoInput && videoErrorMsg && basicsSubmitBtn) {
                campaignVideoInput.addEventListener("change", function() {
                    const file = this.files[0];
                    if (file) {
                        videoErrorMsg.style.display = "none";
                        videoUploadStatus.textContent = "Uploading video...";
                        videoUploadStatus.style.color = "#16a34a";
                        videoUploadStatus.style.display = "block";

                        basicsSubmitBtn.disabled = true;
                        basicsSubmitBtn.style.opacity = "0.5";
                        basicsSubmitBtn.style.cursor = "not-allowed";

                        const formData = new FormData();
                        formData.append('video', file);
                        formData.append('_token', '{{ csrf_token() }}');

                        fetch('{{ route("user.campaign.upload-campaign-video") }}', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                if (uploadedVideoName) {
                                    uploadedVideoName.value = data.video;
                                }
                                videoUploadStatus.textContent = "✓ Video uploaded successfully";
                                videoUploadStatus.style.color = "#16a34a";
                                basicsSubmitBtn.disabled = false;
                                basicsSubmitBtn.style.opacity = "1";
                                basicsSubmitBtn.style.cursor = "pointer";
                            } else {
                                videoErrorMsg.textContent = data.message || "Video upload failed";
                                videoErrorMsg.style.display = "block";
                                videoUploadStatus.style.display = "none";
                                basicsSubmitBtn.disabled = true;
                                basicsSubmitBtn.style.opacity = "0.5";
                                basicsSubmitBtn.style.cursor = "not-allowed";
                            }
                        })
                        .catch(() => {
                            videoErrorMsg.textContent = "Failed to upload video. Please try again.";
                            videoErrorMsg.style.display = "block";
                            videoUploadStatus.style.display = "none";
                            basicsSubmitBtn.disabled = true;
                            basicsSubmitBtn.style.opacity = "0.5";
                            basicsSubmitBtn.style.cursor = "not-allowed";
                        });
                    } else {
                        videoErrorMsg.style.display = "none";
                        videoUploadStatus.style.display = "none";
                        if (uploadedVideoName) uploadedVideoName.value = "";
                        basicsSubmitBtn.disabled = false;
                        basicsSubmitBtn.style.opacity = "1";
                        basicsSubmitBtn.style.cursor = "pointer";
                    }
                });
            }
        })();
        @endif

        @if($currentSection == 'story')
        // Story form handling with Summernote Editor
        (function() {
            // Initialize Summernote Editor
            function initializeSummernote() {
                if (typeof jQuery === 'undefined' || typeof jQuery.fn.summernote === 'undefined') {
                    console.error('Summernote library not loaded');
                    setTimeout(initializeSummernote, 100);
                    return false;
                }
                
                try {
                    const uploadUrl = "{{ route('user.campaign.story.media', $campaign->slug) }}";

                    function uploadStoryFile(file) {
                        const formData = new FormData();
                        formData.append('file', file);
                        formData.append('_token', '{{ csrf_token() }}');

                        return fetch(uploadUrl, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json());
                    }

                    function insertVideo(url, mimeType) {
                        const safeUrl = String(url || '').replace(/"/g, '&quot;');
                        const safeType = String(mimeType || 'video/mp4').replace(/"/g, '&quot;');
                        const videoHtml = `
                            <video controls style="max-width: 100%;">
                                <source src="${safeUrl}" type="${safeType}">
                            </video>
                        `;
                        jQuery('#summernote').summernote('pasteHTML', videoHtml);
                    }

                    const uploadVideoButton = function(context) {
                        const ui = jQuery.summernote.ui;
                        return ui.button({
                            contents: '<i class="fa fa-video"></i>',
                            tooltip: 'Upload Video',
                            click: function () {
                                const input = document.createElement('input');
                                input.type = 'file';
                                input.accept = 'video/*';
                                input.onchange = function () {
                                    const file = input.files && input.files[0];
                                    if (!file) return;
                                    uploadStoryFile(file)
                                        .then(data => {
                                            if (data && data.type === 'video' && data.location) {
                                                insertVideo(data.location, data.mime);
                                            } else {
                                                alert(data?.message || 'Video upload failed');
                                            }
                                        })
                                        .catch(() => alert('Video upload failed'));
                                };
                                input.click();
                            }
                        }).render();
                    };

                    jQuery('#summernote').summernote({
                        toolbar: [
                            // [groupName, [list of button]]
                            ['style', ['bold', 'italic', 'underline', 'clear']],
                            ['font', ['strikethrough', 'superscript', 'subscript']],
                            ['fontsize', ['fontsize']],
                            ['color', ['color']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['insert', ['picture', 'link', 'uploadVideo']],
                            ['height', ['height']]
                        ],
                        height: 500,
                        buttons: {
                            uploadVideo: uploadVideoButton
                        },
                        callbacks: {
                            onImageUpload: function(files) {
                                Array.from(files).forEach(function(file) {
                                    uploadStoryFile(file)
                                        .then(data => {
                                            if (data && data.type === 'image' && data.location) {
                                                jQuery('#summernote').summernote('insertImage', data.location);
                                            } else {
                                                alert(data?.message || 'Image upload failed');
                                            }
                                        })
                                        .catch(() => alert('Image upload failed'));
                                });
                            },
                            onChange: function(contents, $editable) {
                                // Update character count
                                updateCharCount();
                                // Show action buttons when content changes
                                if (typeof window.showActionButtons === 'function') {
                                    window.showActionButtons();
                                }
                            }
                        }
                    });
                    
                    // Initial character count
                    updateCharCount();
                    
                    console.log('Summernote Editor initialized successfully');
                    return true;
                } catch (error) {
                    console.error('Error initializing Summernote Editor:', error);
                    return false;
                }
            }

            // Function to update character count
            function updateCharCount() {
                const content = jQuery('#summernote').summernote('code');
                // Strip HTML tags to get plain text
                const plainText = content.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').trim();
                const charCount = plainText.length;
                const charCountElement = document.getElementById('charCount');
                
                if (charCountElement) {
                    if (charCount < 30) {
                        charCountElement.textContent = `(${charCount}/30 characters - ${30 - charCount} more needed)`;
                        charCountElement.style.color = '#c33';
                    } else {
                        charCountElement.textContent = `(${charCount} characters ✓)`;
                        charCountElement.style.color = '#16a34a';
                    }
                }
            }

            // Function to validate story content
            function validateStoryContent() {
                const content = jQuery('#summernote').summernote('code');
                // Strip HTML tags to get plain text
                const plainText = content.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').trim();
                const charCount = plainText.length;
                const errorElement = document.getElementById('storyError');
                
                if (charCount < 30) {
                    if (errorElement) {
                        errorElement.textContent = `Story must be at least 30 characters. You have ${charCount} characters (${30 - charCount} more needed).`;
                        errorElement.style.display = 'block';
                    }
                    
                    // Show alert
                    alert(`Error: Story must be at least 30 characters.\n\nYou have ${charCount} characters.\nYou need ${30 - charCount} more characters.`);
                    
                    return false;
                }
                
                if (errorElement) {
                    errorElement.style.display = 'none';
                }
                
                return true;
            }

            // Wait for jQuery and Summernote library to load
            function waitForSummernoteAndInitialize() {
                if (typeof jQuery !== 'undefined' && typeof jQuery.fn.summernote !== 'undefined') {
                    initializeSummernote();
                } else {
                    setTimeout(waitForSummernoteAndInitialize, 100);
                }
            }

            // Start initialization
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', waitForSummernoteAndInitialize);
            } else {
                waitForSummernoteAndInitialize();
            }

            // Story form handling with validation
            const storyForm = document.getElementById("storyForm");
            if (storyForm) {
                // Form submission - validate before submitting
                storyForm.addEventListener("submit", function(e) {
                    // Validate content length
                    if (!validateStoryContent()) {
                        e.preventDefault();
                        return false;
                    }
                    
                    // Summernote automatically updates the textarea value
                    const submitBtn = document.getElementById("storySubmitBtn");
                    const topSaveBtn = document.getElementById("topSaveBtn");
                    
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                    }
                    
                    if (topSaveBtn) {
                        topSaveBtn.disabled = true;
                        topSaveBtn.textContent = "Saving...";
                    }
                });
            }

            // Update top save button to handle Summernote content with validation
            const topSaveBtn = document.getElementById("topSaveBtn");
            if (topSaveBtn) {
                topSaveBtn.addEventListener('click', function() {
                    if (storyForm) {
                        // Validate before submitting
                        if (validateStoryContent()) {
                            // Summernote automatically syncs content to textarea on form submit
                            storyForm.submit();
                        }
                    }
                });
            }
        })();
        @endif

        // Shipping options
        const shipOptions = document.querySelectorAll(".ship-option");
        shipOptions.forEach(option => {
            option.addEventListener("click", () => {
                shipOptions.forEach(o => o.classList.remove("active"));
                option.classList.add("active");
                const input = option.querySelector("input");
                if (input) input.checked = true;
            });
        });

        // Post campaign options
        const postOptions = document.querySelectorAll(".post-option");
        postOptions.forEach(option => {
            option.addEventListener("click", () => {
                postOptions.forEach(o => o.classList.remove("active"));
                option.classList.add("active");
                const input = option.querySelector("input");
                if (input) input.checked = true;
            });
        });
    </script>

@endsection

@section('script')
    <script src="{{ asset($activeThemeTrue . 'js/dropzone.min.js') }}"></script>
    @if($currentSection == 'story')
    <!-- include libraries(jQuery, bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <!-- include summernote css/js -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
    @endif
    @include($activeTheme . 'user.campaign.commonStyleScript')
@endsection




