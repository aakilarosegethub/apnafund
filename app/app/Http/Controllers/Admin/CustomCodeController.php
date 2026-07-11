<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteData;
use Illuminate\Http\Request;

class CustomCodeController extends Controller
{
    public function index()
    {
        $pageTitle = 'Custom Code Management';

        // Get existing custom code data
        $headerCode = SiteData::where('data_key', 'custom_code.header')->first();
        $footerCode = SiteData::where('data_key', 'custom_code.footer')->first();

        return view('admin.customcode.index', compact('pageTitle', 'headerCode', 'footerCode'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'header_code' => 'nullable|string',
            'footer_code' => 'nullable|string',
        ]);

        try {
            // Get header code value (handle null and empty strings)
            $headerCodeValue = $request->input('header_code', '');
            $headerCodeValue = $headerCodeValue !== null ? $headerCodeValue : '';

            // Update header code
            SiteData::updateOrCreate(
                ['data_key' => 'custom_code.header'],
                ['data_info' => ['code' => $headerCodeValue]]
            );

            // Get footer code value (handle null and empty strings)
            $footerCodeValue = $request->input('footer_code', '');
            $footerCodeValue = $footerCodeValue !== null ? $footerCodeValue : '';

            // Update footer code
            SiteData::updateOrCreate(
                ['data_key' => 'custom_code.footer'],
                ['data_info' => ['code' => $footerCodeValue]]
            );

            $toast[] = ['success', 'Custom code updated successfully'];

            return back()->withToasts($toast);
        } catch (\Exception $e) {
            \Log::error('Custom code update failed: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            $toast[] = ['error', 'Failed to update custom code. Please try again.'];

            return back()->withToasts($toast);
        }
    }
}
