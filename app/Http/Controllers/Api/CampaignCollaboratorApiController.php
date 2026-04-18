<?php

namespace App\Http\Controllers\Api;

use App\Models\Campaign;
use App\Models\CampaignCollaborator;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Mobile API — campaign People tab: search users, list/add/remove collaborators (web parity: User\CampaignController).
 * Endpoint: /api/campaign_collaborators.php — op=list|search|add|remove (Bearer).
 */
class CampaignCollaboratorApiController extends BaseApiController
{
    /**
     * @param  array<string, mixed>  $extra  Additional payload keys for the mobile client
     */
    protected function jsonLegacy(int $http, string $code, bool $ok, string $msg, array $extra = []): JsonResponse
    {
        $payload = array_merge([
            'ResponseCode' => (string) $code,
            'Result' => $ok ? 'true' : 'false',
            'ResponseMsg' => $msg,
        ], $extra);

        return response()->json($payload, $http);
    }

    /**
     * **POST/GET** `/api/campaign_collaborators.php` — `op=list|search|add|remove` (Bearer). Manages campaign collaborators.
     *
     * @return \Illuminate\Http\JsonResponse Legacy envelope with `collaborators` or search hits per operation
     */
    public function collaborators(Request $request): JsonResponse
    {
        $uid = $this->getUserId($request);
        if (empty($uid)) {
            return $this->jsonLegacy(401, '401', false, 'Unauthenticated! Please provide a valid token.');
        }

        $data = $this->getRequestData($request);
        $op = strtolower(trim((string) ($data['op'] ?? $request->input('op', ''))));

        $cid = $data['campaign_id'] ?? $data['fund_id'] ?? $request->input('campaign_id') ?? $request->input('fund_id');
        $slug = $data['slug'] ?? $request->input('slug');
        $hasCampaign = ($cid !== null && $cid !== '') || (!empty($slug));

        if ($op === '' && $request->isMethod('GET') && $hasCampaign) {
            $op = 'list';
        }
        if ($op === '') {
            return $this->jsonLegacy(400, '400', false, 'Parameter op is required: list, search, add, remove (or GET with campaign_id/slug for list).');
        }

        if ($op === 'search') {
            return $this->searchUsers($request, (int) $uid);
        }

        $campaign = $this->resolveCampaignForCollaborators($request);
        if ($campaign instanceof JsonResponse) {
            return $campaign;
        }

        if ($campaign->isExpired()) {
            return $this->jsonLegacy(400, '400', false, 'This campaign has expired and cannot be edited.');
        }

        if (!$campaign->canBeEditedBy($uid)) {
            return $this->jsonLegacy(403, '403', false, 'You do not have permission to manage this campaign.');
        }

        if ($op === 'list') {
            $rows = $campaign->collaborators()->with('user')->get()->map(function (CampaignCollaborator $c) {
                $u = $c->user;
                if (!$u) {
                    return null;
                }

                return [
                    'user_id' => (int) $c->user_id,
                    'id' => (int) $u->id,
                    'name' => $u->fullname ?? $u->username,
                    'email' => $u->email,
                    'username' => $u->username,
                    'image_url' => $u->image ? getImage(getFilePath('userProfile') . '/' . $u->image, getFileSize('userProfile')) : null,
                ];
            })->filter()->values();

            return $this->jsonLegacy(200, '200', true, 'Collaborators list.', ['collaborators' => $rows->all()]);
        }

        if ($op === 'add') {
            if ((int) $campaign->user_id !== (int) $uid) {
                return $this->jsonLegacy(403, '403', false, 'Only the campaign owner can add collaborators.');
            }
            $payload = array_merge($data, $request->all());
            $v = Validator::make($payload, [
                'user_id' => 'required|exists:users,id',
            ]);
            if ($v->fails()) {
                return $this->jsonLegacy(422, '422', false, $v->errors()->first(), ['errors' => $v->errors()]);
            }
            $userId = (int) $payload['user_id'];
            if ($userId === (int) $uid) {
                return $this->jsonLegacy(422, '422', false, 'You cannot add yourself as a collaborator.');
            }
            if ($campaign->collaborators()->where('user_id', $userId)->exists()) {
                return $this->jsonLegacy(422, '422', false, 'User is already a collaborator.');
            }
            CampaignCollaborator::create([
                'campaign_id' => $campaign->id,
                'user_id' => $userId,
            ]);
            $user = User::find($userId);

            return $this->jsonLegacy(200, '200', true, 'Collaborator added successfully.', [
                'collaborator' => [
                    'id' => $user->id,
                    'user_id' => $user->id,
                    'name' => $user->fullname ?? $user->username,
                    'email' => $user->email,
                    'username' => $user->username,
                    'image_url' => $user->image ? getImage(getFilePath('userProfile') . '/' . $user->image, getFileSize('userProfile')) : null,
                ],
            ]);
        }

        if ($op === 'remove') {
            if ((int) $campaign->user_id !== (int) $uid) {
                return $this->jsonLegacy(403, '403', false, 'Only the campaign owner can remove collaborators.');
            }
            $payload = array_merge($data, $request->all());
            $targetId = (int) ($payload['user_id'] ?? 0);
            if ($targetId < 1) {
                return $this->jsonLegacy(400, '400', false, 'user_id is required.');
            }
            $collab = $campaign->collaborators()->where('user_id', $targetId)->first();
            if (!$collab) {
                return $this->jsonLegacy(404, '404', false, 'Collaborator not found.');
            }
            $collab->delete();

            return $this->jsonLegacy(200, '200', true, 'Collaborator removed successfully.');
        }

        return $this->jsonLegacy(400, '400', false, 'Invalid op. Use list, search, add, remove.');
    }

    protected function searchUsers(Request $request, int $uid): JsonResponse
    {
        $data = $this->getRequestData($request);
        $q = trim((string) ($data['q'] ?? $request->input('q', '')));
        if (strlen($q) < 2) {
            return $this->jsonLegacy(200, '200', true, 'Type at least 2 characters to search.', ['users' => []]);
        }

        $users = User::where('status', 1)
            ->where(function ($query) use ($q) {
                $query->where('username', 'like', '%' . $q . '%')
                    ->orWhere('email', 'like', '%' . $q . '%')
                    ->orWhere('firstname', 'like', '%' . $q . '%')
                    ->orWhere('lastname', 'like', '%' . $q . '%');
            })
            ->where('id', '!=', $uid)
            ->limit(10)
            ->get()
            ->map(function (User $user) {
                return [
                    'id' => $user->id,
                    'name' => $user->fullname ?? $user->username,
                    'email' => $user->email,
                    'username' => $user->username,
                    'image_url' => $user->image ? getImage(getFilePath('userProfile') . '/' . $user->image, getFileSize('userProfile')) : null,
                ];
            });

        return $this->jsonLegacy(200, '200', true, 'Users found.', ['users' => $users->values()->all()]);
    }

    /**
     * @return Campaign|JsonResponse
     */
    protected function resolveCampaignForCollaborators(Request $request)
    {
        $data = $this->getRequestData($request);
        $rawId = $data['campaign_id'] ?? $data['fund_id'] ?? $request->input('campaign_id') ?? $request->input('fund_id');
        $slug = trim((string) ($data['slug'] ?? $request->input('slug', '')));

        $campaign = null;
        if ($rawId !== null && $rawId !== '') {
            $campaign = Campaign::where('id', (int) $rawId)->first();
        } elseif ($slug !== '') {
            $campaign = Campaign::where('slug', $slug)->first();
            if (!$campaign && ctype_digit($slug)) {
                $campaign = Campaign::where('id', (int) $slug)->first();
            }
        }

        if (!$campaign) {
            return $this->jsonLegacy(404, '404', false, 'Campaign not found. Provide campaign_id, fund_id, or slug.');
        }

        return $campaign;
    }
}
