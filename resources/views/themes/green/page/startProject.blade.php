@php
    $activeTheme = activeTheme();
    $activeThemeTrue = activeTheme();
@endphp
@extends($activeTheme . 'layouts.blank')

@section('custom-css')
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
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #e0f7f4 0%, #b3e5fc 100%);
            min-height: 100vh;
            align-items: center;
            justify-content: center;
        
        }

        .setup-container {
            background: white;
            border-radius: 12px;
            padding: 6rem 5rem;
            max-width: 700px;
            width: 100%;
            box-shadow: 0px 10px 40px rgba(0, 0, 0, 0.08);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-bottom: 4rem;
            font-size: 1.6rem;
            font-weight: 700;
            color: #1d1d1d;
        }

        .logo img {
            width: 35px;
            height: 35px;
        }

        .setup-title {
            font-size: 3.2rem;
            font-weight: 900;
            color: #1d1d1d;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .setup-subtitle {
            font-size: 1.6rem;
            color: #555;
            margin-bottom: 1rem;
            font-weight: 600;
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

        .form-select option {
            padding: 1rem;
        }

        .welcome-text {
            font-size: 1.4rem;
            color: #999;
            margin-bottom: 2rem;
            font-weight: 500;
        }

        .button-group {
            display: flex;
            gap: 1rem;
            justify-content: space-between;
            align-items: center;
        }

        .next-btn {
            background: #e0e0e0;
            color: #999;
            padding: 1.2rem 2.4rem;
            border: none;
            border-radius: 6px;
            font-size: 1.4rem;
            font-weight: 600;
            cursor: not-allowed;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .next-btn:hover:not(:disabled) {
            background: #00c6a7;
            color: white;
            cursor: pointer;
            transform: translateY(-2px);
            box-shadow: 0px 6px 16px rgba(0, 198, 167, 0.3);
        }

        .next-btn:disabled {
            opacity: 0.6;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .setup-container {
                padding: 4rem 2.5rem;
            }

            .setup-title {
                font-size: 2.4rem;
            }

            .setup-subtitle {
                font-size: 1.4rem;
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

            .next-btn {
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

            .setup-subtitle {
                font-size: 1.3rem;
            }

            .setup-description {
                font-size: 1.2rem;
            }

            .form-select {
                font-size: 1.3rem;
                padding: 1rem 1.2rem;
            }
        }
        .flex{
            display: flex;
            justify-content: center;
        }
    </style>
@endsection
@section('frontend')
    <!-- Start Project Page -->
    <div class="flex">
    <div class="setup-container">
        <!-- Logo -->

        <!-- Title & Description -->
        <h1 class="setup-title">First, let's get you set up.</h1>
        <p class="setup-subtitle">Select a primary category and subcategory for your new project.</p>
        <p class="setup-description">These will help backers find your project, and you can change them later if you need to.</p>

        <!-- Form -->
        <form id="setupForm">
            <!-- Primary Category -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-list"></i>
                    Primary Category
                </label>
                <select class="form-select" id="primaryCategory" required>
                    <option value="">Select a category</option>
                    @if(isset($categories) && $categories->count() > 0)
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(($savedCategoryId ?? null) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Subcategory -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-sitemap"></i>
                    Subcategory
                </label>
                <select class="form-select" id="subcategory" required data-saved-subcategory-id="{{ $savedSubcategoryId ?? '' }}">
                    <option value="">Select a subcategory</option>
                </select>
            </div>

            <!-- Welcome Text -->
            <p class="welcome-text">Nice to see you again.</p>

            <!-- Buttons -->
            <div class="button-group">
               <button type="button" class="next-btn" id="nextBtn" disabled>
    Next: Location
</button>

            </div>
        </form>
    </div>
    </div>
@endsection
@section('script')
    <script>
        (function() {
            const primaryCategory = document.getElementById('primaryCategory');
            const subcategory = document.getElementById('subcategory');
            const nextBtn = document.getElementById('nextBtn');
            const NEXT_BTN_LABEL = 'Next: Location';

            function loadSubcategories(categoryId, selectedSubcategoryId) {
                if (!categoryId) {
                    subcategory.innerHTML = '<option value="">Select a subcategory</option>';
                    subcategory.disabled = false;
                    return Promise.resolve();
                }

                subcategory.innerHTML = '<option value="">Loading subcategories...</option>';
                subcategory.disabled = true;

                return fetch(`/api/subcategories/${categoryId}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    subcategory.innerHTML = '<option value="">Select a subcategory</option>';

                    if (data.success && data.data && data.data.length > 0) {
                        data.data.forEach(sub => {
                            const option = document.createElement('option');
                            option.value = sub.id;
                            option.textContent = sub.name;
                            subcategory.appendChild(option);
                        });
                    } else {
                        subcategory.innerHTML = '<option value="">No subcategories available</option>';
                    }

                    if (selectedSubcategoryId) {
                        subcategory.value = String(selectedSubcategoryId);
                    }

                    subcategory.disabled = false;
                })
                .catch(error => {
                    console.error('Error fetching subcategories:', error);
                    subcategory.innerHTML = '<option value="">Error loading subcategories</option>';
                    subcategory.disabled = false;
                });
            }

            function updateNextBtn() {
                nextBtn.textContent = NEXT_BTN_LABEL;

                if (primaryCategory.value && subcategory.value) {
                    nextBtn.disabled = false;
                    nextBtn.style.background = '#00c6a7';
                    nextBtn.style.color = 'white';
                    nextBtn.style.cursor = 'pointer';
                } else {
                    nextBtn.disabled = true;
                    nextBtn.style.background = '#e0e0e0';
                    nextBtn.style.color = '#999';
                    nextBtn.style.cursor = 'not-allowed';
                }
            }

            let subcategoriesLoading = false;

            function restoreCategoryStep() {
                nextBtn.textContent = NEXT_BTN_LABEL;

                const categoryId = primaryCategory.value;
                const savedSubcategoryId = subcategory.dataset.savedSubcategoryId || '';
                const subcategoryAlreadyLoaded = subcategory.options.length > 1;
                const subcategoryIdToRestore = subcategory.value || savedSubcategoryId;

                if (categoryId && (!subcategoryAlreadyLoaded || (subcategoryIdToRestore && !subcategory.value))) {
                    if (subcategoriesLoading) {
                        return;
                    }
                    subcategoriesLoading = true;
                    loadSubcategories(categoryId, subcategoryIdToRestore)
                        .finally(function() {
                            subcategoriesLoading = false;
                            updateNextBtn();
                        });
                    return;
                }

                updateNextBtn();
            }

            primaryCategory.addEventListener('change', function() {
                loadSubcategories(this.value, null).then(updateNextBtn);
            });

            subcategory.addEventListener('change', updateNextBtn);

            window.addEventListener('pageshow', restoreCategoryStep);
            restoreCategoryStep();

            nextBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (!primaryCategory.value || !subcategory.value) {
                    return;
                }

                nextBtn.disabled = true;
                nextBtn.textContent = 'Saving...';

                fetch('{{ route("start.project.save.categories") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        category_id: primaryCategory.value,
                        subcategory_id: subcategory.value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = data.redirect_url;
                    } else {
                        alert('Error: ' + (data.message || 'Failed to save categories'));
                        updateNextBtn();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                    updateNextBtn();
                });
            });
        })();
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
