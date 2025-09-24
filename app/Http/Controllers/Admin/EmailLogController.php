<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use App\Services\EmailLoggingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmailLogController extends Controller
{
    /**
     * Display a listing of email logs
     */
    public function index(Request $request)
    {
        $pageTitle = 'Email Logs';
        
        $query = EmailLog::with('user')->latest();
        
        // Filter by email type
        if ($request->filled('type')) {
            $query->where('email_type', $request->type);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Search by email or subject
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('to_email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('from_email', 'like', "%{$search}%");
            });
        }
        
        $emailLogs = $query->paginate(20);
        
        // Get statistics
        $stats = EmailLoggingService::getEmailStats(30);
        
        // Get email types for filter
        $emailTypes = EmailLog::select('email_type')
            ->distinct()
            ->pluck('email_type')
            ->filter()
            ->values();
        
        // Get statuses for filter
        $statuses = EmailLog::select('status')
            ->distinct()
            ->pluck('status')
            ->filter()
            ->values();
        
        return view('admin.email_logs.index', compact(
            'pageTitle',
            'emailLogs',
            'stats',
            'emailTypes',
            'statuses'
        ));
    }
    
    /**
     * Show email log details
     */
    public function show(EmailLog $emailLog)
    {
        $pageTitle = 'Email Details';
        
        return view('admin.email_logs.show', compact('pageTitle', 'emailLog'));
    }
    
    /**
     * Get email body preview (AJAX)
     */
    public function preview(EmailLog $emailLog)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'subject' => $emailLog->subject,
                'body' => $emailLog->body,
                'to_email' => $emailLog->to_email,
                'to_name' => $emailLog->to_name,
                'from_email' => $emailLog->from_email,
                'from_name' => $emailLog->from_name,
                'email_type' => $emailLog->email_type,
                'status' => $emailLog->status,
                'provider' => $emailLog->provider,
                'sent_at' => $emailLog->sent_at?->format('Y-m-d H:i:s'),
                'created_at' => $emailLog->created_at->format('Y-m-d H:i:s'),
                'error_message' => $emailLog->error_message,
                'provider_response' => $emailLog->provider_response,
            ]
        ]);
    }
    
    /**
     * Resend email
     */
    public function resend(EmailLog $emailLog)
    {
        try {
            // Create new email instance
            $email = new \App\Notify\Email();
            $email->email = $emailLog->to_email;
            $email->receiverName = $emailLog->to_name;
            $email->subject = $emailLog->subject;
            $email->finalMessage = $emailLog->body;
            $email->templateName = $emailLog->template_name;
            $email->setting = bs();
            
            // Send email
            $email->send();
            
            $notify[] = ['success', 'Email resent successfully'];
            
        } catch (\Exception $e) {
            $notify[] = ['error', 'Failed to resend email: ' . $e->getMessage()];
        }
        
        return back()->withNotify($notify);
    }
    
    /**
     * Delete email log
     */
    public function destroy(EmailLog $emailLog)
    {
        $emailLog->delete();
        
        $notify[] = ['success', 'Email log deleted successfully'];
        return back()->withNotify($notify);
    }
    
    /**
     * Get email statistics (AJAX)
     */
    public function stats(Request $request)
    {
        $days = $request->get('days', 30);
        $stats = EmailLoggingService::getEmailStats($days);
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
