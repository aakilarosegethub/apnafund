<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;
use App\Models\Comment;

class CommentController extends Controller
{
    public function index()
    {
        $pageTitle = 'Campaign Comments';
        $comments = Comment::with(['user', 'campaign', 'campaign.user'])->searchable(['name', 'campaign:name'])->latest()->paginate(getPaginate());

        return view('admin.page.comments', compact('pageTitle', 'comments'));
    }

    public function approve($id)
    {
        if (! admin_can('campaigns.comments_approve')) {
            $toast[] = ['error', 'You do not have permission to approve comments.'];

            return back()->withToasts($toast);
        }
        $comment = Comment::findOrFail($id);
        $comment->status = ManageStatus::CAMPAIGN_COMMENT_APPROVED;
        $comment->save();

        $this->sendNotification($comment, 'COMMENT_APPROVE');

        try {
            $camp = $comment->campaign;
            if ($camp && (int) $camp->user_id > 0) {
                \App\Models\UserNotification::notifyCreatorReviewPublished(
                    (int) $camp->user_id,
                    $camp,
                    (string) ($comment->name ?? '')
                );
            }
        } catch (\Throwable $e) {
            \Log::warning('Creator comment-approved UserNotification failed', ['error' => $e->getMessage()]);
        }

        $toast[] = ['success', 'Comment successfully approved'];

        return back()->withToasts($toast);
    }

    public function destroy($id)
    {
        if (! admin_can('campaigns.comments_approve')) {
            $toast[] = ['error', 'You do not have permission to reject/delete comments.'];

            return back()->withToasts($toast);
        }
        $comment = Comment::where('id', $id)->first();
        $temp = $comment;
        $comment->delete();

        $this->sendNotification($temp, 'CAMPAIGN_COMMENT_REJECTED');

        $toast[] = ['success', 'Comment successfully deleted'];

        return back()->withToasts($toast);
    }

    protected function sendNotification($comment, $template)
    {
        if ($comment->user) {
            $user = [
                'username' => $comment->user->username,
                'email' => $comment->user->email,
                'fullname' => $comment->user->fullname,
            ];
        } else {
            $user = [
                'username' => $comment->email,
                'email' => $comment->email,
                'fullname' => $comment->name,
            ];
        }

        notify($user, $template, [
            'campaign_name' => $comment->campaign ? $comment->campaign->name : 'Deleted Campaign',
        ], ['email']);
    }
}
