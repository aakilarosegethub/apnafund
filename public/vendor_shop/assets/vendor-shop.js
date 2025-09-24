jQuery(document).ready(function($) {
    'use strict';
    
    // Test Webhook Functionality
    $('#test-webhook').on('click', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var $result = $('#webhook-result');
        
        // Show loading state
        $button.prop('disabled', true).text('Testing...');
        $result.hide();
        
        // Test data
        var testData = {
            sku: 'TEST-' + Date.now(),
            name: 'Test Product ' + Date.now(),
            regular_price: 29.99,
            description: 'This is a test product created via webhook',
            category: 'Test Category',
            tags: 'test,webhook,product'
        };
        
        // Send test request
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'vendor_shop_webhook',
                data: JSON.stringify(testData)
            },
            success: function(response) {
                if (response.success) {
                    $result.removeClass('error').addClass('success')
                        .html('<strong>Success!</strong><br>' + 
                              'Product URL: <a href="' + response.data.url + '" target="_blank">' + response.data.url + '</a><br>' +
                              'Admin URL: <a href="' + response.data.admin_url + '" target="_blank">' + response.data.admin_url + '</a>')
                        .show();
                } else {
                    $result.removeClass('success').addClass('error')
                        .html('<strong>Error:</strong> ' + response.message)
                        .show();
                }
            },
            error: function(xhr, status, error) {
                $result.removeClass('success').addClass('error')
                    .html('<strong>Error:</strong> ' + error)
                    .show();
            },
            complete: function() {
                $button.prop('disabled', false).text('Test Webhook');
            }
        });
    });
    
    // Product Management Functions
    window.VendorShop = {
        // Create new product
        createProduct: function(data) {
            return $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'vendor_shop_create_product',
                    data: JSON.stringify(data)
                }
            });
        },
        
        // Update product
        updateProduct: function(id, data) {
            return $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'vendor_shop_update_product',
                    id: id,
                    data: JSON.stringify(data)
                }
            });
        },
        
        // Delete product
        deleteProduct: function(id) {
            return $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'vendor_shop_delete_product',
                    id: id
                }
            });
        },
        
        // Get products
        getProducts: function(params) {
            return $.ajax({
                url: ajaxurl,
                type: 'GET',
                data: {
                    action: 'vendor_shop_get_products',
                    ...params
                }
            });
        },
        
        // Test webhook with custom data
        testWebhook: function(data) {
            return $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'vendor_shop_webhook',
                    data: JSON.stringify(data)
                }
            });
        }
    };
    
    // Product Form Handler
    $('.vendor-shop-product-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submitBtn = $form.find('input[type="submit"]');
        var $result = $form.find('.form-result');
        
        // Show loading state
        $submitBtn.prop('disabled', true).val('Processing...');
        $result.hide();
        
        // Get form data
        var formData = {};
        $form.find('input, textarea, select').each(function() {
            var $field = $(this);
            var name = $field.attr('name');
            var value = $field.val();
            
            if (name && value) {
                formData[name] = value;
            }
        });
        
        // Send request
        VendorShop.createProduct(formData)
            .done(function(response) {
                if (response.success) {
                    $result.removeClass('error').addClass('success')
                        .html('<strong>Success!</strong> Product created successfully.<br>' +
                              'Product URL: <a href="' + response.data.url + '" target="_blank">' + response.data.url + '</a>')
                        .show();
                    
                    // Reset form
                    $form[0].reset();
                } else {
                    $result.removeClass('success').addClass('error')
                        .html('<strong>Error:</strong> ' + response.message)
                        .show();
                }
            })
            .fail(function(xhr, status, error) {
                $result.removeClass('success').addClass('error')
                    .html('<strong>Error:</strong> ' + error)
                    .show();
            })
            .always(function() {
                $submitBtn.prop('disabled', false).val('Create Product');
            });
    });
    
    // Product List Actions
    $('.vendor-shop-delete-product').on('click', function(e) {
        e.preventDefault();
        
        if (!confirm('Are you sure you want to delete this product?')) {
            return;
        }
        
        var $button = $(this);
        var productId = $button.data('product-id');
        var $row = $button.closest('tr');
        
        $button.prop('disabled', true).text('Deleting...');
        
        VendorShop.deleteProduct(productId)
            .done(function(response) {
                if (response.success) {
                    $row.fadeOut();
                } else {
                    alert('Error: ' + response.message);
                    $button.prop('disabled', false).text('Delete');
                }
            })
            .fail(function(xhr, status, error) {
                alert('Error: ' + error);
                $button.prop('disabled', false).text('Delete');
            });
    });
    
    // Auto-refresh product list
    if ($('.vendor-shop-products-table').length) {
        setInterval(function() {
            // Refresh product count or other dynamic data
            $('.vendor-shop-refresh').trigger('click');
        }, 30000); // Refresh every 30 seconds
    }
    
    // Webhook URL Generator
    $('.vendor-shop-generate-url').on('click', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var $urlField = $('.vendor-shop-webhook-url');
        
        var baseUrl = window.location.origin;
        var webhookUrl = baseUrl + '/wp-json/vendor-shop/v1/webhook';
        
        $urlField.val(webhookUrl);
        $button.text('URL Generated!').addClass('success');
        
        setTimeout(function() {
            $button.text('Generate URL').removeClass('success');
        }, 2000);
    });
    
    // Copy to Clipboard
    $('.vendor-shop-copy').on('click', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var text = $button.data('copy-text') || $button.prev('input').val();
        
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function() {
                $button.text('Copied!').addClass('success');
                setTimeout(function() {
                    $button.text('Copy').removeClass('success');
                }, 2000);
            });
        } else {
            // Fallback for older browsers
            var textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            
            $button.text('Copied!').addClass('success');
            setTimeout(function() {
                $button.text('Copy').removeClass('success');
            }, 2000);
        }
    });
    
    // Form Validation
    $('.vendor-shop-required').on('blur', function() {
        var $field = $(this);
        var value = $field.val().trim();
        
        if (value === '') {
            $field.addClass('error');
            $field.next('.field-error').remove();
            $field.after('<span class="field-error">This field is required</span>');
        } else {
            $field.removeClass('error');
            $field.next('.field-error').remove();
        }
    });
    
    // Price Formatting
    $('.vendor-shop-price').on('input', function() {
        var $field = $(this);
        var value = $field.val();
        
        // Remove non-numeric characters except decimal point
        value = value.replace(/[^0-9.]/g, '');
        
        // Ensure only one decimal point
        var parts = value.split('.');
        if (parts.length > 2) {
            value = parts[0] + '.' + parts.slice(1).join('');
        }
        
        $field.val(value);
    });
    
    // SKU Auto-generation
    $('.vendor-shop-name').on('input', function() {
        var $nameField = $(this);
        var $skuField = $('.vendor-shop-sku');
        
        if ($skuField.val() === '') {
            var name = $nameField.val();
            var sku = name.toLowerCase()
                .replace(/[^a-z0-9\s]/g, '')
                .replace(/\s+/g, '-')
                .substring(0, 20);
            
            $skuField.val(sku);
        }
    });
});