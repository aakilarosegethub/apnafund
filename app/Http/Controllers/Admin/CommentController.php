<?php

namespace App\Http\Controllers\Admin;

use App\Models\Comment;
use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{
    function index() {
        $pageTitle = 'Campaign Comments';
        $comments  = Comment::with(['user', 'campaign', 'campaign.user'])->searchable(['name', 'campaign:name'])->latest()->paginate(getPaginate());

        return view('admin.page.comments', compact('pageTitle', 'comments'));
    }

    function approve($id) {
        $comment         = Comment::findOrFail($id);
        $comment->status = ManageStatus::CAMPAIGN_COMMENT_APPROVED;
        $comment->save();

        $this->sendNotification($comment, 'COMMENT_APPROVE');

        $toast[] = ['success', 'Comment successfully approved'];

        return back()->withToasts($toast);
    }

    function destroy($id) {
        $comment = Comment::where('id', $id)->first();
        $temp    = $comment;
        $comment->delete();

        $this->sendNotification($temp, 'CAMPAIGN_COMMENT_REJECTED');

        $toast[] = ['success', 'Comment successfully deleted'];

        return back()->withToasts($toast);
    }

    protected function sendNotification($comment, $template) {
        if ($comment->user) {
            $user = [
                'username' => $comment->user->username,
                'email'    => $comment->user->email,
                'fullname' => $comment->user->fullname,
            ];
        } else {
            $user = [
                'username' => $comment->email,
                'email'    => $comment->email,
                'fullname' => $comment->name,
            ];
        }

        notify($user, $template, [
            'campaign_name' => $comment->campaign->name,
        ], ['email']);
    }
}
