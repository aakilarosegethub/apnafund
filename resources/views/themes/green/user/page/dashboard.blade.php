@php
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
@endphp
@extends($activeTheme . 'layouts.dashboard')

@section('frontend')
    <!-- Overview Tab -->
    <div class="tab-pane fade show active" id="overview" role="tabpanel">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="mb-4">Dashboard Overview</h2>
                            
                            <!-- Stats Grid -->
                            <div class="stats-grid">
                                <div class="stat-card">
                                    <div class="stat-icon">
                                        <i class="fas fa-fire"></i>
                                    </div>
                                    <div class="stat-number">{{ @$widgetData['campaignCount'] }}</div>
                                    <div class="stat-label">Active Gigs</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon">
                                        <i class="fas fa-coins"></i>
                                    </div>
                                    <div class="stat-number">{{ $setting->cur_sym . showAmount(@$user->balance) }}</div>
                                    <div class="stat-label">Total Raised</div>
                                </div>
                                <!-- <div class="stat-card">
                                    <div class="stat-icon">
                                        <i class="fas fa-heart"></i>
                                    </div>
                                    <div class="stat-number">1,247</div>
                                    <div class="stat-label">Total Donors</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon">
                                        <i class="fas fa-trophy"></i>
                                    </div>
                                    <div class="stat-number">89%</div>
                                    <div class="stat-label">Success Rate</div>
                                </div> -->
                            </div>

                        </div>
                    </div>
                </div>

    <!-- Add Reward Modal -->
    <div class="modal fade" id="addRewardModal" tabindex="-1" aria-labelledby="addRewardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRewardModalLabel">Add New Reward</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addRewardForm">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="rewardTitle" class="form-label">Reward Title *</label>
                                    <input type="text" class="form-control" id="rewardTitle" placeholder="Enter reward title" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="rewardType" class="form-label">Reward Type *</label>
                                    <select class="form-select" id="rewardType" required>
                                        <option value="">Select Type</option>
                                        <option value="digital">Digital Reward</option>
                                        <option value="physical">Physical Reward</option>
                                        <option value="experience">Experience</option>
                                        <option value="recognition">Recognition</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="rewardDescription" class="form-label">Description *</label>
                            <textarea class="form-control" id="rewardDescription" rows="3" placeholder="Describe what donors will receive" required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="minimumAmount" class="form-label">Minimum Donation Amount ($) *</label>
                                    <input type="number" class="form-control" id="minimumAmount" placeholder="25" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rewardQuantity" class="form-label">Available Quantity</label>
                                    <input type="number" class="form-control" id="rewardQuantity" placeholder="Unlimited" min="1">
                                    <small class="text-muted">Leave empty for unlimited</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="rewardImage" class="form-label">Reward Image</label>
                            <div class="file-upload" onclick="document.getElementById('rewardImage').click()">
                                <i class="fas fa-image"></i>
                                <div class="file-upload-text">Click to upload reward image</div>
                                <small class="text-muted">Supports JPG, PNG, GIF (Max 2MB)</small>
                            </div>
                            <input type="file" id="rewardImage" accept="image/*" style="display: none;">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="deliveryDate" class="form-label">Estimated Delivery</label>
                                    <input type="date" class="form-control" id="deliveryDate">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rewardColor" class="form-label">Reward Color Theme</label>
                                    <select class="form-select" id="rewardColor">
                                        <option value="gradient-red">Red Gradient</option>
                                        <option value="gradient-blue">Blue Gradient</option>
                                        <option value="gradient-green">Green Gradient</option>
                                        <option value="gradient-purple">Purple Gradient</option>
                                        <option value="gradient-orange">Orange Gradient</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="rewardTerms" class="form-label">Terms & Conditions</label>
                            <textarea class="form-control" id="rewardTerms" rows="3" placeholder="Any special terms or conditions for this reward"></textarea>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="autoActivate" checked>
                            <label class="form-check-label" for="autoActivate">
                                Activate reward immediately after creation
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveReward()">
                        <i class="fas fa-save me-2"></i>Save Reward
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

