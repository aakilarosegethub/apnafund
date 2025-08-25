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

        // Update header code
        SiteData::updateOrCreate(
            ['data_key' => 'custom_code.header'],
            ['data_info' => ['code' => $request->header_code ?? '']]
        );

        // Update footer code
        SiteData::updateOrCreate(
            ['data_key' => 'custom_code.footer'],
            ['data_info' => ['code' => $request->footer_code ?? '']]
        );

        $toast[] = ['success', 'Custom code updated successfully'];
        return back()->withToasts($toast);
    }
} 