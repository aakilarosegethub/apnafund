@php
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
    
    // Check if campaign belongs to current user
    if (isset($campaign) && $campaign->user_id !== auth()->id()) {
        abort(403, 'Unauthorized access to this campaign.');
    }
    
    // Set default section if not provided
    $section = $section ?? 'basics';
@endphp
@extends($activeTheme . 'layouts.frontend')
@section('style')
<style>
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
        }

        .top-tabs a {
            text-decoration: none;
            color: #777;
        }

        .top-tabs a.active {
            color: #000;
            font-weight: 600;
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
@endsection
@section('frontend')


    <!-- TOP TABS -->
    <div class="top-tabs">
        <div style="display: flex; align-items: center; gap: 32px; flex: 1;">
            <a href="{{ route('user.campaign.edit.basics', $campaign->slug) }}" class="{{ ($section ?? 'basics') == 'basics' ? 'active' : '' }}">Basics</a>
            <a href="{{ route('user.campaign.edit.reward', $campaign->slug) }}" class="{{ ($section ?? 'basics') == 'reward' ? 'active' : '' }}">Rewards</a>
            <a href="{{ route('user.campaign.edit.story', $campaign->slug) }}" class="{{ ($section ?? 'basics') == 'story' ? 'active' : '' }}">Story</a>
            <a href="{{ route('user.campaign.edit.people', $campaign->slug) }}" class="{{ ($section ?? 'basics') == 'people' ? 'active' : '' }}">People</a>
            <a href="{{ route('user.campaign.edit.payment', $campaign->slug) }}" class="{{ ($section ?? 'basics') == 'payment' ? 'active' : '' }}">Payment</a>
            <a href="{{ route('user.campaign.edit.boost', $campaign->slug) }}" class="{{ ($section ?? 'basics') == 'boost' ? 'active' : '' }}">Boost</a>
        </div>
        <div id="topActionButtons" style="display: none; gap: 10px; align-items: center;">
            <button type="button" id="topExitBtn" class="next-btn" style="margin: 0; padding: 8px 20px; font-size: 14px; background: #666;">Exit</button>
            <button type="button" id="topSaveBtn" class="next-btn active" style="margin: 0; padding: 8px 20px; font-size: 14px;">Save</button>
        </div>
        <button type="button" id="topEditBtn" class="d-none next-btn active" style="margin: 0; padding: 8px 20px; font-size: 14px;">Edit</button>
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
                    <textarea name="description" placeholder="Describe your project in one or two sentences..." required>{{ old('description', $campaign->description) }}</textarea>
                <p class="note">This will show on your project card.</p>
                    @error('description')
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
                    @error('location')
                        <p class="note" style="color: red;">{{ $message }}</p>
                    @enderror
            </div>

            <!-- PROJECT IMAGE -->
            <div class="box">
                <h2 style="margin-top:0; font-size:22px;">Project image</h2>

                <p style="color:#555; line-height:1.6; font-size:15px;">
                    Add an image that clearly represents your project...
                </p>

                @if($campaign->image)
                    <div style="margin-bottom: 15px;">
                        <img src="{{ getImage(getFilePath('campaign') . '/' . $campaign->image, getFileSize('campaign')) }}" 
                             alt="Current Image" 
                             style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd;">
                    </div>
                @endif

                <div class="upload-box">
                    <label for="projectImage" class="upload-btn">Upload an image</label>
                    <input type="file" id="projectImage" name="image" accept="image/*">
                    <img id="preview" class="preview-img" alt="Image Preview" 
                         @if($campaign->image) 
                             src="{{ getImage(getFilePath('campaign') . '/' . $campaign->image, getFileSize('campaign')) }}" 
                             style="display: block;"
                         @endif>
                    <p style="margin-top: 12px; font-size: 14px; color:#666;">
                        Drop an image here, or select a file.<br>Must be JPG, PNG, GIF, or WEBP under 50 MB.
                    </p>
                    @error('image')
                        <p class="note" style="color: red;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- FUNDING GOAL -->
            <div class="box">
                <h2 style="margin-top:0; font-size:22px;">Funding goal</h2>

                <p style="font-size:15px; color:#555;">
                    Set an achievable goal that covers what you need to complete your project.
                </p>

                <label>Goal amount *</label>
                <input type="number" name="goal_amount" step="0.01" min="0" value="{{ old('goal_amount', $campaign->goal_amount) }}" placeholder="0.00" required>
                @error('goal_amount')
                    <p class="note" style="color: red;">{{ $message }}</p>
                @enderror

                <div style="margin-top:18px; border-top:1px solid #eee; padding-top:18px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/992/992651.png"
                         style="width:40px; vertical-align:middle; margin-right:10px;">
                    <a href="#" class="calc-link">Use our calculator</a>
                    to estimate total costs.
                </div>
            </div>

            <!-- CAMPAIGN DURATION -->
            <div class="box">
                <h2 style="margin-top:0; font-size:22px;">Campaign duration *</h2>
                <p style="font-size:15px; color:#555;">
                    Set a time limit for your campaign. You won't be able to change this after you launch.
                </p>

                <label>Start Date *</label>
                <input type="date" name="start_date" value="{{ old('start_date', $campaign->start_date ? $campaign->start_date->format('Y-m-d') : '') }}" required>
                @error('start_date')
                    <p class="note" style="color: red;">{{ $message }}</p>
                @enderror

                <label style="margin-top: 15px;">End Date *</label>
                <input type="date" name="end_date" value="{{ old('end_date', $campaign->end_date ? $campaign->end_date->format('Y-m-d') : '') }}" required>
                @error('end_date')
                    <p class="note" style="color: red;">{{ $message }}</p>
                @enderror

                <div class="green-note" style="margin-top: 15px;">✔ Campaigns that last 30 days or less are more likely to be successful.</div>
                <div class="green-note">✔ Pledge Over Time won't be available for Late Pledge backers.</div>
            </div>

            <!-- SHIPPING SECTION -->
            <div class="shipping-container">

                <div class="shipping-left">
                    <h2>Shipping</h2>
                    <p>Choose when backers pay shipping costs.</p>
                    <p>
                        Decide whether charging shipping at pledge, or post-campaign through a pledge manager,
                        works best for your project.
                    </p>
                    <a href="#">Learn more</a>
                </div>

                <div class="shipping-right">
                    <div class="shipping-box">
                        
                        <div class="ship-option active">
                            <input type="radio" name="ship" checked>
                            Charge shipping at pledge
                            <div class="ship-description">
                                Set actual shipping costs when adding rewards and add-ons. Backers will pay their
                                pledge total, including any applicable shipping costs, when your campaign ends successfully.
                            </div>
                        </div>

                        <div class="ship-option">
                            <input type="radio" name="ship">
                            Charge shipping post-campaign
                        </div>

                    </div>
                </div>

            </div>

            <!-- POST-CAMPAIGN SECTION -->
            <div class="post-container">

                <div class="post-left">
                    <h2>Post-campaign</h2>
                    <p>Set up what is next for your project, after your campaign ends successfully.</p>

                    <p>
                        <strong>Late Pledges:</strong> Keep the momentum going and continue collecting pledges after your
                        campaign ends. Learn how to maximize funding with this feature in our
                        <a href="#">Late Pledges guide</a>.
                    </p>

                    <p>
                        <strong>Pledge Manager:</strong> The all-in-one toolkit for all your post-campaign needs. Discover more
                        about the <a href="#">Kickstarter Pledge Manager</a>.
                    </p>
                </div>

                <div class="post-right">
                    <div class="post-box">

                        <div class="post-option">
                            <input type="radio" name="latePledge">
                            Yes, enable Late Pledges
                            <span class="recommended-tag">Recommended</span>
                        </div>

                        <div class="post-option active">
                            <input type="radio" name="latePledge" checked>
                            No, don't enable Late Pledges
                            <div class="ship-description">
                                Backers will only be able to pledge to your project until it reaches its deadline.
                            </div>
                        </div>

                    </div>
                </div>

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
                    <button class="tab active" data-tab="items" style="font-size: 16px; color: #555; padding-bottom: 8px; cursor: pointer; border: none; background: none; font-weight: 600; color: #000;">Items</button>
                    <button class="tab" data-tab="tiers" style="font-size: 16px; color: #555; padding-bottom: 8px; cursor: pointer; border: none; background: none;">Reward tiers</button>
                    <button class="tab" data-tab="addons" style="font-size: 16px; color: #555; padding-bottom: 8px; cursor: pointer; border: none; background: none;">Add-ons</button>
                </div>
                
                <div class="underline" style="width: 50px; height: 3px; background: black; margin-top: -6px; margin-bottom: 20px;"></div>
                
                <p class="desc" style="width: 70%; color: #444; line-height: 1.5; font-size: 15px;">
                    Including items in your rewards and add-ons makes it easy for backers to understand and 
                    compare your offerings. An item can be anything you plan to offer your backers. Some 
                    examples include playing cards, a digital copy of a book, a ticket to a play, or even a 
                    thank-you in your documentary.
                </p>
                
                <a href="#" class="learn" style="color: #009b5b; text-decoration: none; font-size: 15px;">Learn about creating items</a>
                
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
                <label>Project Story *</label>
                <!-- Froala Editor -->
                <textarea name="description" id="storyEditor" required>{{ old('description', $campaign->description ?? '') }}</textarea>
                
                <p class="note">Share the story behind your project and why it matters.</p>
                @error('description')
                    <p class="note" style="color: red;">{{ $message }}</p>
                @enderror
            </div>

            </form>
            @endif

            @if($currentSection == 'people')
            <h1>People</h1>
            <p class="subtitle">Manage team members and collaborators.</p>
            
            <div class="box">
                <p>People section content will be here. You can manage team members for your campaign.</p>
            </div>
            @endif

            @if($currentSection == 'payment')
            <h1>Payment</h1>
            <p class="subtitle">Configure payment settings for your campaign.</p>
            
            <div class="box">
                <p>Payment section content will be here. Configure how you'll receive payments.</p>
            </div>
            @endif

            @if($currentSection == 'boost')
            <h1>Boost</h1>
            <p class="subtitle">Promote and boost your campaign visibility.</p>
            
            <div class="box">
                <p>Boost section content will be here. Promote your campaign to reach more backers.</p>
            </div>
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
        })();
        @endif

        @if($currentSection == 'story')
        // Story form handling with Quill Editor
        (function() {
            // Hidden file input for custom image handler
            const imageInput = document.createElement('input');
            imageInput.type = 'file';
            imageInput.accept = 'image/*';
            imageInput.style.display = 'none';
            imageInput.id = 'storyImageInput';
            document.body.appendChild(imageInput);

            // Custom image handler
            function customImageHandler() {
                imageInput.click();
                imageInput.onchange = function() {
                    const file = imageInput.files[0];
                    if (!file) return;
                    
                    // Validate file type
                    if (!file.type.match('image.*')) {
                        alert('Please select an image file');
                        return;
                    }
                    
                    // Validate file size (5MB max)
                    if (file.size > 5 * 1024 * 1024) {
                        alert('Image size must be less than 5MB');
                        return;
                    }
                    
                    // Create FormData for file upload
                    const formData = new FormData();
                    formData.append('files', file);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                    // Upload file to server
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '{{ route("user.campaign.upload-image") }}');
                    
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.location && window.storyQuill) {
                                    const range = window.storyQuill.getSelection();
                                    if (range) {
                                        window.storyQuill.insertEmbed(range.index, 'image', response.location);
                                        window.storyQuill.setSelection(range.index + 1, 0);
                                    } else {
                                        const length = window.storyQuill.getLength();
                                        window.storyQuill.insertEmbed(length - 1, 'image', response.location);
                                        window.storyQuill.setSelection(length, 0);
                                    }
                                } else {
                                    alert('Upload failed: ' + (response.message || 'Invalid response'));
                                }
                            } catch (e) {
                                console.error('JSON parse error:', e);
                                alert('Upload failed: Invalid JSON response');
                            }
                        } else {
                            alert('Upload failed: Server error ' + xhr.status);
                        }
                    };
                    
                    xhr.onerror = function() {
                        alert('Upload failed: Network error');
                    };
                    
                    xhr.send(formData);
                };
            }

            // Custom video handler
            function customVideoHandler() {
                const url = prompt('Paste video URL (YouTube, Vimeo, MP4, etc.)');
                if (!url) return;
                
                // Convert YouTube URL to embeddable format
                let embedUrl = url;
                if (url.includes('youtube.com/watch?v=')) {
                    const videoId = url.split('v=')[1].split('&')[0];
                    embedUrl = `https://www.youtube.com/embed/${videoId}`;
                } else if (url.includes('youtu.be/')) {
                    const videoId = url.split('youtu.be/')[1].split('?')[0];
                    embedUrl = `https://www.youtube.com/embed/${videoId}`;
                }
                
                if (window.storyQuill) {
                    const range = window.storyQuill.getSelection(true);
                    if (range) {
                        if (embedUrl.includes('youtube.com/embed/')) {
                            const iframe = document.createElement('iframe');
                            iframe.src = embedUrl;
                            iframe.width = '560';
                            iframe.height = '315';
                            iframe.frameBorder = '0';
                            iframe.allowFullscreen = true;
                            iframe.style.border = 'none';
                            iframe.style.borderRadius = '8px';
                            iframe.style.margin = '10px 0';
                            window.storyQuill.clipboard.dangerouslyPasteHTML(range.index, iframe.outerHTML);
                            window.storyQuill.setSelection(range.index + 1, 0);
                        } else {
                            window.storyQuill.insertEmbed(range.index, 'video', url, 'user');
                            window.storyQuill.setSelection(range.index + 1, 0);
                        }
                    }
                }
            }

            // Initialize Quill Editor
            function initializeStoryQuill() {
                const editorElement = document.getElementById('storyEditor');
                if (!editorElement) {
                    console.error('Story editor element not found');
                    return false;
                }
                
                if (typeof Quill === 'undefined') {
                    console.error('Quill library not loaded');
                    return false;
                }
                
                try {
                    window.storyQuill = new Quill('#storyEditor', {
                        theme: 'snow',
                        placeholder: 'Tell your story...',
                        modules: {
                            toolbar: {
                                container: '#storyToolbar',
                                handlers: {
                                    image: customImageHandler,
                                    video: customVideoHandler
                                }
                            }
                        }
                    });
                    
                    // Load existing content
                    const existingContent = document.getElementById('storyDescription').value || '';
                    if (existingContent) {
                        window.storyQuill.root.innerHTML = existingContent;
                        window.storyQuill.update();
                    }
                    
                    // Track initial content for change detection
                    const initialContent = window.storyQuill.root.innerHTML;
                    
                    // Monitor changes
                    window.storyQuill.on('text-change', function() {
                        const currentContent = window.storyQuill.root.innerHTML;
                        if (currentContent !== initialContent) {
                            if (typeof window.showActionButtons === 'function') {
                                window.showActionButtons();
                            }
                        }
                    });
                    
                    console.log('Story Quill editor initialized successfully');
                    return true;
                } catch (error) {
                    console.error('Error initializing Story Quill editor:', error);
                    return false;
                }
            }

            // Wait for Quill library to load
            function waitForQuillAndInitialize() {
                if (typeof Quill !== 'undefined' && document.getElementById('storyEditor')) {
                    initializeStoryQuill();
                } else {
                    setTimeout(waitForQuillAndInitialize, 100);
                }
            }

            // Start initialization
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', waitForQuillAndInitialize);
            } else {
                waitForQuillAndInitialize();
            }

            // Story form handling
            const storyForm = document.getElementById("storyForm");
            if (storyForm) {
                // Form submission - copy Quill content to textarea
                storyForm.addEventListener("submit", function(e) {
                    if (window.storyQuill) {
                        const editorContent = window.storyQuill.root.innerHTML;
                        document.getElementById('storyDescription').value = editorContent;
                    }
                    
                    const topSaveBtn = document.getElementById("topSaveBtn");
                    if (topSaveBtn) {
                        topSaveBtn.disabled = true;
                        topSaveBtn.textContent = "Saving...";
                    }
                });
            }

            // Update top save button to handle Quill content
            const topSaveBtn = document.getElementById("topSaveBtn");
            if (topSaveBtn) {
                const originalClick = topSaveBtn.onclick;
                topSaveBtn.addEventListener('click', function() {
                    if (window.storyQuill && storyForm) {
                        const editorContent = window.storyQuill.root.innerHTML;
                        document.getElementById('storyDescription').value = editorContent;
                        storyForm.submit();
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

@include($activeTheme . 'user.campaign.commonStyleScript')

@push('page-style-lib')
    @if($currentSection == 'story')
    <!-- Froala Editor CSS -->
    <link href="https://cdn.jsdelivr.net/npm/froala-editor@latest/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/froala-editor@latest/css/froala_style.min.css" rel="stylesheet" type="text/css" />
    @endif
@endpush

@push('page-script-lib')
    @if($currentSection == 'story')
    <!-- Froala Editor JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/froala-editor@latest/js/froala_editor.pkgd.min.js"></script>
    @endif
@endpush




