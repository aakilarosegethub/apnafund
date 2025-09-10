<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteData;
use App\Constants\ManageStatus;
use Illuminate\Http\Request;

class ReportFundraiserController extends Controller
{
    public function index()
    {
        $pageTitle = 'Report Fundraiser Content';
        
        // Get existing report fundraiser content
        $reportContent = SiteData::where('data_key', 'report_fundraiser.content')->first();
        
        return view('admin.report.fundraiser', compact('pageTitle', 'reportContent'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'boolean',
        ]);

        // Update or create report fundraiser content
        $reportContent = SiteData::updateOrCreate(
            ['data_key' => 'report_fundraiser.content'],
            [
                'data_info' => [
                    'title' => $request->title,
                    'content' => $request->content,
                    'status' => $request->status ? ManageStatus::ACTIVE : ManageStatus::INACTIVE,
                ]
            ]
        );

        $toast[] = ['success', 'Report Fundraiser content updated successfully'];
        return back()->withToasts($toast);
    }
}
