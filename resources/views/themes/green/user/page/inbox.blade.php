@php
    $activeTheme = activeTheme();
@endphp
@extends($activeTheme . 'layouts.dashboard')

@section('style')
<style>
    .inbox-wrapper { display: flex; height: calc(100vh - 200px); min-height: 480px; background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.06); overflow: hidden; border: 1px solid #eee; }
    .inbox-list { width: 300px; min-width: 260px; border-right: 1px solid #eee; display: flex; flex-direction: column; background: #fafbfc; }
    .inbox-list-header { padding: 18px 20px; border-bottom: 1px solid #eee; font-weight: 700; font-size: 1.05rem; color: #1a1a1a; background: #fff; }
    .inbox-conversations { flex: 1; overflow-y: auto; min-height: 0; }
    .inbox-conv-item { display: flex; align-items: center; gap: 12px; padding: 14px 18px; cursor: pointer; border-bottom: 1px solid #f0f0f0; transition: all .2s; background: #fff; }
    .inbox-conv-item:hover { background: #f5f7f9; }
    .inbox-conv-item.active { background: linear-gradient(90deg, rgba(5, 206, 120, 0.08) 0%, #fff 100%); border-left: 4px solid #05ce78; }
    .inbox-conv-avatar { width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #05ce78, #04b367); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem; flex-shrink: 0; overflow: hidden; }
    .inbox-conv-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .inbox-conv-body { flex: 1; min-width: 0; }
    .inbox-conv-name { font-weight: 600; color: #1a1a1a; margin-bottom: 2px; font-size: 0.95rem; }
    .inbox-conv-preview { font-size: 0.82rem; color: #6c757d; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .inbox-conv-time { font-size: 0.72rem; color: #999; }
    .inbox-conv-unread { background: #05ce78; color: #fff; font-size: 0.65rem; padding: 2px 7px; border-radius: 10px; margin-left: auto; }
    .inbox-conv-delete { background: none; border: none; color: #999; cursor: pointer; padding: 4px 8px; font-size: 0.9rem; opacity: 0.6; transition: opacity .2s; flex-shrink: 0; }
    .inbox-conv-delete:hover { color: #dc3545; opacity: 1; }
    .inbox-chat { flex: 1; display: flex; flex-direction: column; min-width: 0; background: #fff; }
    .inbox-chat-empty { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #6c757d; font-size: 1rem; gap: 18px; padding: 32px; text-align: center; }
    .inbox-chat-empty .btn-browse { background: linear-gradient(135deg, #05ce78, #04b367); color: #fff; padding: 12px 28px; border-radius: 10px; font-weight: 600; text-decoration: none; display: inline-block; border: none; cursor: pointer; transition: all .2s; box-shadow: 0 2px 8px rgba(5, 206, 120, 0.3); }
    .inbox-chat-empty .btn-browse:hover { background: linear-gradient(135deg, #04b367, #039a55); color: #fff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(5, 206, 120, 0.35); }
    .inbox-chat-header { padding: 16px 20px; border-bottom: 1px solid #eee; font-weight: 600; color: #1a1a1a; display: flex; align-items: center; justify-content: space-between; gap: 12px; background: #fff; flex-shrink: 0; }
    .inbox-chat-header-title { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 1rem; }
    .inbox-chat-menu { position: relative; }
    .inbox-chat-menu-btn { background: none; border: none; color: #666; cursor: pointer; padding: 8px 10px; border-radius: 8px; font-size: 1.25rem; transition: all .2s; display: flex; align-items: center; justify-content: center; }
    .inbox-chat-menu-btn:hover { background: rgba(0,0,0,0.06); color: #1a1a1a; }
    .inbox-chat-menu-dropdown { position: absolute; top: 100%; right: 0; margin-top: 4px; background: #fff; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); border: 1px solid #eee; min-width: 160px; z-index: 1000; display: none; overflow: hidden; }
    .inbox-chat-menu-dropdown.open { display: block; }
    .inbox-chat-menu-dropdown a, .inbox-chat-menu-dropdown button { display: flex; align-items: center; gap: 10px; width: 100%; padding: 12px 16px; border: none; background: none; cursor: pointer; font-size: 0.9rem; color: #333; text-align: left; transition: background .2s; }
    .inbox-chat-menu-dropdown a:hover, .inbox-chat-menu-dropdown button:hover { background: #f5f7f9; }
    .inbox-chat-menu-dropdown .inbox-chat-delete-opt { color: #dc3545; }
    .inbox-chat-menu-dropdown .inbox-chat-delete-opt:hover { background: rgba(220,53,69,0.08); }
    .inbox-chat-messages { flex: 1; min-height: 0; overflow-y: auto; overflow-x: hidden; padding: 20px; display: flex; flex-direction: column; gap: 14px; background: #f8f9fa; }
    .inbox-msg-row { display: flex; align-items: flex-end; gap: 10px; max-width: 85%; }
    .inbox-msg-row.sent { align-self: flex-end; flex-direction: row-reverse; }
    .inbox-msg-row.received { align-self: flex-start; }
    .inbox-msg-avatar { width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0; overflow: hidden; background: linear-gradient(135deg, #05ce78, #04b367); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.85rem; }
    .inbox-msg-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .inbox-msg-wrap { display: flex; flex-direction: column; align-items: flex-start; gap: 4px; }
    .inbox-msg-row.sent .inbox-msg-wrap { align-items: flex-end; }
    .inbox-msg { padding: 12px 16px; border-radius: 16px; font-size: 0.95rem; line-height: 1.45; word-wrap: break-word; }
    .inbox-msg-delete-span { color: #dc3545; cursor: pointer; font-size: 0.75rem; font-weight: 500; opacity: 0.9; transition: opacity .2s; }
    .inbox-msg-delete-span:hover { opacity: 1; text-decoration: underline; }
    .inbox-msg.sent { background: linear-gradient(135deg, #05ce78, #04b367); color: #fff; border-bottom-right-radius: 4px; box-shadow: 0 1px 3px rgba(5, 206, 120, 0.25); }
    .inbox-msg.received { background: #fff; color: #333; border: 1px solid #e9ecef; border-bottom-left-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.04); }
    .inbox-msg-time { font-size: 0.7rem; opacity: 0.85; margin-top: 6px; }
    .inbox-chat-form { padding: 14px 20px; border-top: 1px solid #eee; display: flex; gap: 12px; align-items: flex-end; background: #fff; flex-shrink: 0; min-height: 74px; box-sizing: border-box; }
    .inbox-chat-form textarea { flex: 1; min-width: 0; resize: none; border: 2px solid #e9ecef; border-radius: 12px; padding: 12px 16px; min-height: 46px; max-height: 120px; font-size: 0.95rem; transition: border-color .2s; font-family: inherit; }
    .inbox-chat-form textarea:focus { outline: none; border-color: #05ce78; }
    .inbox-chat-form .btn-send { flex-shrink: 0; background: linear-gradient(135deg, #05ce78, #04b367); color: #fff; border: none; padding: 12px 24px; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all .2s; box-shadow: 0 2px 8px rgba(5, 206, 120, 0.25); }
    .inbox-chat-form .btn-send:hover { background: linear-gradient(135deg, #04b367, #039a55); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(5, 206, 120, 0.3); }
    .inbox-loading { padding: 40px; text-align: center; color: #6c757d; }
    .inbox-notice { padding: 12px 16px; border-radius: 10px; margin-bottom: 16px; font-size: 0.9rem; }
    @media (max-width: 768px) {
        .inbox-wrapper { flex-direction: column; height: auto; min-height: 520px; }
        .inbox-list { width: 100%; max-height: 220px; min-width: 0; }
    }
</style>
@endsection

@section('frontend')
<div class="content-card">
    <h2 class="mb-4">{{ __($pageTitle) }}</h2>

    @if(empty($firebaseConfig['apiKey']) || empty($firebaseConfig['projectId']))
        <div class="inbox-notice">
            <strong>Chat setup:</strong> Add Firebase Web config (FIREBASE_API_KEY, FIREBASE_PROJECT_ID, etc.) in .env. See config/firebase.php.
        </div>
    @else
        <div class="inbox-notice" style="background:#e7f3ff;border:1px solid #b3d7ff;">
            <strong>Messages not sending?</strong> Enable <a href="https://console.developers.google.com/apis/api/firestore.googleapis.com/overview?project={{ $firebaseConfig['projectId'] ?? '' }}" target="_blank" rel="noopener">Cloud Firestore API</a> in Google Cloud Console for your project.
        </div>
    @endif

    <div class="inbox-wrapper" id="inboxApp">
        <div class="inbox-list">
            <div class="inbox-list-header">Conversations</div>
            <div class="inbox-conversations" id="conversationList">
                <div class="inbox-loading" id="convLoading">Loading...</div>
            </div>
        </div>
        <div class="inbox-chat">
            <div class="inbox-chat-empty" id="chatEmpty" style="{{ ($startCreatorId && $startCampaignId) ? 'display: none;' : '' }}">
                <span>{{ $inboxLabels['empty_state_message'] ?? 'Select a conversation from the list, or browse campaigns to contact a creator.' }}</span>
                <a href="{{ route('campaign') }}" class="btn-browse">{{ $inboxLabels['browse_button_text'] ?? 'Browse Campaigns' }}</a>
            </div>
            <div id="chatPanel" style="display: {{ ($startCreatorId && $startCampaignId) ? 'flex' : 'none' }}; flex: 1; flex-direction: column; min-width: 0; min-height: 0; overflow: hidden;">
                <div class="inbox-chat-header">
                    <span class="inbox-chat-header-title" id="chatHeader">{{ ($startCreatorId && $startCampaignId) ? ($creatorFullname ?? $startCampaignTitle ?? '—') : '—' }}</span>
                    <div class="inbox-chat-menu" id="chatMenuWrap" style="display: none;">
                        <button type="button" class="inbox-chat-menu-btn" id="chatMenuBtn" title="Options"><i class="fas fa-bars"></i></button>
                        <div class="inbox-chat-menu-dropdown" id="chatMenuDropdown">
                            <button type="button" class="inbox-chat-delete-opt" id="chatDeleteBtn"><i class="fas fa-trash-alt"></i> Delete chat</button>
                        </div>
                    </div>
                </div>
                <div class="inbox-chat-messages" id="chatMessages">@if($startCreatorId && $startCampaignId)<div class="text-muted text-center py-4" id="chatConnecting">Starting chat...</div>@endif</div>
                <div class="inbox-chat-form">
                    <textarea id="messageInput" placeholder="{{ $inboxLabels['message_placeholder'] ?? 'Type a message...' }}" rows="1"></textarea>
                    <button type="button" class="btn-send" id="sendBtn">{{ $inboxLabels['send_button_text'] ?? 'Send' }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://www.gstatic.com/firebasejs/10.7.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.7.0/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.7.0/firebase-firestore-compat.js"></script>
<script>
(function() {
    const firebaseConfig = @json($firebaseConfig ?? []);
    @php $currentUserData = ['id' => (string) $user->id, 'fullname' => $user->fullname ?? $user->username ?? 'User', 'image' => $user->image ?? null, 'imageUrl' => $currentUserImageUrl ?? '']; @endphp
    const currentUser = @json($currentUserData);
    const startParams = {
        creatorId: @json($startCreatorId ?? null),
        campaignId: @json($startCampaignId ?? null),
        campaignSlug: @json($startCampaignSlug ?? null),
        campaignTitle: @json($startCampaignTitle ?? null),
        creatorImageUrl: @json($creatorImageUrl ?? null),
        creatorFullname: @json($creatorFullname ?? null),
    };
    const tokenUrl = @json(route('user.inbox.firebase.token'));
    const creatorNamesUrl = @json(route('user.inbox.creator.names'));

    if (!firebaseConfig.apiKey || !firebaseConfig.projectId) {
        var convLoad = document.getElementById('convLoading');
        if (convLoad) convLoad.textContent = 'Firebase not configured.';
        return;
    }

    var app = (typeof firebase !== 'undefined' && firebase.apps && firebase.apps.length > 0) ? firebase.app() : firebase.initializeApp(firebaseConfig);
    const auth = firebase.auth(app);
    const db = firebase.firestore(app);
    const prefix = firebaseConfig.chatCollectionPrefix || 'apnacrowdfunding';
    const convColl = prefix + '_conversations';

    let selectedConvId = null;
    let selectedOtherId = null;
    let selectedCampaignId = null;
    let selectedOtherImageUrl = null;
    let selectedOtherName = null;
    let unsubMsg = null;
    var lastKnownMessageAt = {};

    document.getElementById('conversationList')?.addEventListener('click', function(e) {
        var delBtn = e.target.closest('.inbox-conv-delete');
        if (delBtn) {
            e.stopPropagation();
            var item = delBtn.closest('.inbox-conv-item');
            if (!item) return;
            var convId = item.dataset.convId;
            if (!convId) return;
            if (!confirm('Delete this conversation? All messages will be removed. This cannot be undone.')) return;
            var ref = db.collection(convColl).doc(convId);
            ref.delete().then(function() {
                if (selectedConvId === convId) {
                    selectedConvId = null;
                    if (unsubMsg) { unsubMsg(); unsubMsg = null; }
                    document.getElementById('chatEmpty').style.display = 'flex';
                    document.getElementById('chatPanel').style.display = 'none';
                    var mw = document.getElementById('chatMenuWrap');
                    if (mw) mw.style.display = 'none';
                }
                item.remove();
                showInboxMsg('Conversation deleted.', false);
            }).catch(function(err) {
                console.error(err);
                showInboxMsg('Failed to delete conversation.', true);
            });
            return;
        }
        var item = e.target.closest('.inbox-conv-item');
        if (!item) return;
        var convId = item.dataset.convId;
        var otherId = item.dataset.otherId;
        var otherName = item.dataset.otherName || '';
        var otherImageUrl = item.dataset.otherImageUrl || null;
        var campaignId = item.dataset.campaignId || null;
        if (convId) selectConversation(convId, otherId, otherName, true, otherImageUrl, campaignId);
    });

    document.getElementById('chatMessages')?.addEventListener('click', function(e) {
        var btn = e.target.closest('.inbox-msg-delete-span');
        if (!btn) return;
        var row = btn.closest('.inbox-msg-row');
        if (!row) return;
        var msgId = row.dataset.msgId;
        if (!msgId || !selectedConvId) return;
        if (!confirm('Delete this message?')) return;
        var ref = db.collection(convColl).doc(selectedConvId);
        ref.collection('messages').doc(msgId).delete().then(function() {
            ref.collection('messages').orderBy('created_at', 'desc').limit(1).get().then(function(snap) {
                if (snap.empty) {
                    ref.update({ last_message: '', last_message_at: firebase.firestore.FieldValue.serverTimestamp(), last_sender_id: null }).catch(function() {});
                } else {
                    var last = snap.docs[0].data();
                    ref.update({
                        last_message: last.text || '',
                        last_message_at: last.created_at,
                        last_sender_id: last.sender_id || null,
                    }).catch(function() {});
                }
            });
            showInboxMsg('Message deleted.', false);
        }).catch(function(err) {
            console.error(err);
            showInboxMsg('Failed to delete message.', true);
        });
    });

    function getConversationId(creatorId, campaignId) {
        const uid = currentUser.id;
        const parts = [String(creatorId), uid].sort();
        if (campaignId) return 'c_' + campaignId + '_' + parts[0] + '_' + parts[1];
        return 'conv_' + parts[0] + '_' + parts[1];
    }

    function getInitials(name) {
        if (!name || typeof name !== 'string') return '?';
        var parts = name.trim().split(/\s+/).filter(Boolean);
        if (parts.length === 0) return '?';
        if (parts.length === 1) return parts[0].charAt(0).toUpperCase();
        return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
    }

    function signInAndRun() {
        fetch(tokenUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (!data.token) {
                    document.getElementById('convLoading').textContent = 'Could not get chat token. Please refresh.';
                    return;
                }
                return auth.signInWithCustomToken(data.token);
            })
            .then(function() { /* onAuthStateChanged will fire and run loadConversations + openOrCreateStartConversation */ })
            .catch(function(err) {
                console.error(err);
                document.getElementById('convLoading').textContent = 'Failed to load chat. Refresh the page.';
            });
    }

    function loadConversations() {
        const listEl = document.getElementById('convLoading');
        listEl.textContent = 'No conversations yet.';
        const q = firebase.firestore().collection(convColl)
            .where('participants', 'array-contains', currentUser.id)
            .orderBy('last_message_at', 'desc');
        q.onSnapshot(function(snap) {
            if (snap.empty) {
                listEl.textContent = 'No conversations yet.';
                listEl.parentElement.querySelectorAll('.inbox-conv-item').forEach(function(n) { n.remove(); });
                return;
            }
            listEl.style.display = 'none';
            var container = listEl.parentElement;
            var ids = snap.docs.map(function(d) { return d.id; });
            container.querySelectorAll('.inbox-conv-item').forEach(function(node) {
                if (ids.indexOf(node.dataset.convId) === -1) node.remove();
            });
            var changes = snap.docChanges ? snap.docChanges() : [];
            changes.forEach(function(change) {
                var doc = change.doc;
                var d = doc.data();
                var lastAt = d.last_message_at ? (d.last_message_at.toDate ? d.last_message_at.toDate().getTime() : 0) : 0;
                lastKnownMessageAt[doc.id] = lastAt;
            });
            if (changes.length === 0) {
                snap.docs.forEach(function(doc) {
                    var d = doc.data();
                    var lastAt = d.last_message_at ? (d.last_message_at.toDate ? d.last_message_at.toDate().getTime() : 0) : 0;
                    lastKnownMessageAt[doc.id] = lastAt;
                });
            }
            snap.docs.forEach(function(doc) {
                const d = doc.data();
                var el = container.querySelector('[data-conv-id="' + doc.id + '"]');
                var otherId = (d.participants || []).find(function(p) { return p !== currentUser.id; });
                var names = d.participant_names || {};
                var images = d.participant_images || {};
                var otherImageUrl = images[otherId] || null;
                var otherName = names[otherId] || d.campaign_title || otherId || '';
                if ((!otherName || otherName === 'Creator') && otherId && String(otherId) === String(startParams.creatorId) && startParams.creatorFullname) {
                    otherName = startParams.creatorFullname;
                }
                const last = d.last_message || '';
                const time = d.last_message_at ? (d.last_message_at.toDate ? d.last_message_at.toDate() : new Date(d.last_message_at)) : null;
                const timeStr = time ? (time.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) || '') : '';
                var unread = (d.last_sender_id && d.last_sender_id !== currentUser.id && !(d.read_by && d.read_by[currentUser.id]));
                var avatarHtml = otherImageUrl ? '<img src="' + (otherImageUrl.replace(/&/g,'&amp;').replace(/"/g,'&quot;')) + '" alt="">' : getInitials(otherName);
                if (!el) {
                    el = document.createElement('div');
                    el.className = 'inbox-conv-item';
                    el.dataset.convId = doc.id;
                    el.dataset.otherId = otherId || '';
                    el.dataset.otherName = otherName || '';
                    el.dataset.otherImageUrl = otherImageUrl || '';
                    el.dataset.campaignId = d.campaign_id || '';
                    el.innerHTML = '<div class="inbox-conv-avatar">' + avatarHtml + '</div><div class="inbox-conv-body"><div class="inbox-conv-name"></div><div class="inbox-conv-preview"></div></div><div class="inbox-conv-time"></div>' + (unread ? '<span class="inbox-conv-unread">New</span>' : '') + '<button type="button" class="inbox-conv-delete" title="Delete conversation"><i class="fas fa-trash-alt"></i></button>';
                    el.querySelector('.inbox-conv-name').textContent = otherName;
                    el.querySelector('.inbox-conv-preview').textContent = last;
                    el.querySelector('.inbox-conv-time').textContent = timeStr;
                    container.appendChild(el);
                } else {
                    el.dataset.otherId = otherId || '';
                    el.dataset.otherName = otherName || '';
                    el.dataset.otherImageUrl = otherImageUrl || '';
                    el.dataset.campaignId = d.campaign_id || '';
                    var av = el.querySelector('.inbox-conv-avatar');
                    if (av) av.innerHTML = avatarHtml;
                    el.querySelector('.inbox-conv-name').textContent = otherName;
                    el.querySelector('.inbox-conv-preview').textContent = last;
                    el.querySelector('.inbox-conv-time').textContent = timeStr;
                    var un = el.querySelector('.inbox-conv-unread');
                    if (unread && !un) el.insertAdjacentHTML('beforeend', '<span class="inbox-conv-unread">New</span>');
                    else if (!unread && un) un.remove();
                    if (!el.querySelector('.inbox-conv-delete')) el.insertAdjacentHTML('beforeend', '<button type="button" class="inbox-conv-delete" title="Delete conversation"><i class="fas fa-trash-alt"></i></button>');
                }
            });
            var needNames = [];
            container.querySelectorAll('.inbox-conv-item').forEach(function(item) {
                var oid = item.dataset.otherId;
                var oname = item.dataset.otherName || '';
                if (oid && (!oname || oname === 'Creator')) needNames.push(oid);
            });
            if (needNames.length > 0 && creatorNamesUrl) {
                fetch(creatorNamesUrl + '?ids=' + needNames.join(','), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function(r) { return r.json(); })
                    .then(function(namesMap) {
                        container.querySelectorAll('.inbox-conv-item').forEach(function(item) {
                            var oid = item.dataset.otherId;
                            var newName = namesMap && namesMap[String(oid)];
                            if (newName) {
                                item.dataset.otherName = newName;
                                var nameEl = item.querySelector('.inbox-conv-name');
                                if (nameEl) nameEl.textContent = newName;
                                var av = item.querySelector('.inbox-conv-avatar');
                                if (av && !av.querySelector('img')) av.innerHTML = getInitials(newName);
                            }
                        });
                    })
                    .catch(function() {});
            }
        }, function(err) {
            console.error(err);
            var msg = 'Error loading conversations.';
            if (err && err.message && err.message.indexOf('index') !== -1) {
                var link = err.message.match(/https:\/\/[^\s]+/);
                if (link) {
                    listEl.innerHTML = 'Firestore index required. <a href="' + link[0] + '" target="_blank" rel="noopener" style="color:#05ce78;text-decoration:underline;">Create index</a> (opens in new tab). Wait 2-5 min after creating.';
                    return;
                }
            }
            listEl.textContent = msg;
        });
    }

    function openOrCreateStartConversation() {
        var cid = getConversationId(startParams.creatorId, startParams.campaignId);
        var otherId = String(startParams.creatorId);
        var otherName = startParams.creatorFullname || startParams.campaignTitle || '';
        var creatorImg = startParams.creatorImageUrl || null;
        var ref = db.collection(convColl).doc(cid);
        ref.get().then(function(doc) {
            if (!doc.exists) {
                var participantImages = {}; participantImages[currentUser.id] = currentUser.imageUrl || ''; participantImages[otherId] = creatorImg || '';
                var participantNames = {}; participantNames[currentUser.id] = currentUser.fullname; participantNames[otherId] = otherName || startParams.campaignTitle || '';
                ref.set({
                    participants: [currentUser.id, otherId],
                    sender_id: String(currentUser.id),
                    receiver_id: String(otherId),
                    campaign_id: startParams.campaignId || null,
                    campaign_slug: startParams.campaignSlug || null,
                    campaign_title: startParams.campaignTitle || null,
                    participant_names: participantNames,
                    participant_images: participantImages,
                    last_message: '',
                    last_message_at: firebase.firestore.FieldValue.serverTimestamp(),
                    last_sender_id: null,
                    created_at: firebase.firestore.FieldValue.serverTimestamp(),
                }).then(function() { selectConversation(cid, otherId, otherName, true, creatorImg, startParams.campaignId); });
            } else {
                var d = doc.data();
                var storedName = (d.participant_names && d.participant_names[otherId]) || '';
                var displayName = (storedName && storedName !== 'Creator') ? storedName : (otherName || d.campaign_title || '');
                var otherImg = (d.participant_images && d.participant_images[otherId]) || creatorImg;
                if (otherName && (!storedName || storedName === 'Creator')) {
                    ref.update({ ['participant_names.' + otherId]: otherName }).catch(function() {});
                }
                selectConversation(cid, otherId, displayName, true, otherImg, d.campaign_id || null);
            }
        });
    }

    function selectConversation(convId, otherId, otherName, focusInput, otherImageUrl, campaignId) {
        if (unsubMsg) unsubMsg();
        selectedConvId = convId;
        selectedOtherId = otherId || null;
        selectedCampaignId = campaignId || null;
        selectedOtherImageUrl = otherImageUrl || null;
        selectedOtherName = otherName || null;
        document.querySelectorAll('.inbox-conv-item').forEach(function(n) { n.classList.toggle('active', n.dataset.convId === convId); });
        document.getElementById('chatEmpty').style.display = 'none';
        var panel = document.getElementById('chatPanel');
        panel.style.display = 'flex';
        var headerEl = document.getElementById('chatHeader');
        if (headerEl) headerEl.textContent = otherName;
        var menuWrap = document.getElementById('chatMenuWrap');
        if (menuWrap) menuWrap.style.display = 'block';
        var dd = document.getElementById('chatMenuDropdown');
        if (dd) dd.classList.remove('open');
        var messagesEl = document.getElementById('chatMessages');
        var connectingEl = document.getElementById('chatConnecting');
        if (connectingEl) connectingEl.remove();
        messagesEl.innerHTML = '';
        if (focusInput) {
            var inp = document.getElementById('messageInput');
            if (inp) setTimeout(function() { inp.focus(); }, 150);
        }
        db.collection(convColl).doc(convId).update({ ['read_by.' + currentUser.id]: true }).catch(function() {});
        unsubMsg = db.collection(convColl).doc(convId).collection('messages').orderBy('created_at', 'asc').onSnapshot(function(snap) {
            messagesEl.innerHTML = '';
            snap.docs.forEach(function(doc) {
                var m = doc.data();
                var isSent = m.sender_id === currentUser.id;
                var t = m.created_at && m.created_at.toDate ? m.created_at.toDate() : new Date();
                var timeStr = t.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                var senderImg = isSent ? (m.sender_image || currentUser.imageUrl) : (m.sender_image || selectedOtherImageUrl);
                var senderName = m.sender_name || (isSent ? currentUser.fullname : selectedOtherName);
                var avatarHtml = senderImg ? '<img src="' + (senderImg.replace(/&/g,'&amp;').replace(/"/g,'&quot;')) + '" alt="">' : getInitials(senderName);
                var txt = (m.text || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
                var deleteSpan = '<span class="inbox-msg-delete-span" title="Delete message">Delete</span>';
                var row = document.createElement('div');
                row.className = 'inbox-msg-row ' + (isSent ? 'sent' : 'received');
                row.dataset.msgId = doc.id;
                row.innerHTML = '<div class="inbox-msg-avatar">' + avatarHtml + '</div><div class="inbox-msg-wrap"><div class="inbox-msg ' + (isSent ? 'sent' : 'received') + '"><div>' + txt + '</div><div class="inbox-msg-time">' + timeStr + '</div></div>' + deleteSpan + '</div>';
                messagesEl.appendChild(row);
            });
            messagesEl.scrollTop = messagesEl.scrollHeight;
        });
    }

    function showInboxMsg(msg, isError) {
        var el = document.getElementById('inboxToast');
        if (el) el.remove();
        el = document.createElement('div');
        el.id = 'inboxToast';
        el.style.cssText = 'position:fixed;bottom:20px;right:20px;padding:12px 20px;border-radius:8px;z-index:9999;font-size:14px;box-shadow:0 4px 12px rgba(0,0,0,0.15);';
        el.style.background = isError ? '#dc3545' : '#28a745';
        el.style.color = '#fff';
        el.textContent = msg;
        document.body.appendChild(el);
        setTimeout(function() { if (el.parentNode) el.remove(); }, 4000);
    }

    function sendMessage() {
        var input = document.getElementById('messageInput');
        if (!input) return;
        var text = (input.value || '').trim();
        if (!text) return;
        if (!selectedConvId) {
            showInboxMsg('Connecting to chat... Please wait a moment.', false);
            return;
        }
        var ref = db.collection(convColl).doc(selectedConvId);
        var msgData = {
            sender_id: String(currentUser.id),
            sender_name: String(currentUser.fullname || ''),
            sender_image: String(currentUser.imageUrl || ''),
            text: String(text),
            created_at: firebase.firestore.Timestamp.now(),
            campaign_id: selectedCampaignId || null,
            receiver_id: selectedOtherId || null,
        };
        ref.collection('messages').add(msgData).then(function() {
            input.value = '';
            var up = {}; up.last_message = text; up.last_message_at = firebase.firestore.Timestamp.now(); up.last_sender_id = currentUser.id; up['read_by.' + currentUser.id] = true;
            ref.update(up).catch(function() {});
        }).catch(function(err) {
            console.error(err);
            showInboxMsg('Failed to send. Enable Firestore API in Google Cloud Console and check your connection.', true);
        });
    }

    var sendBtnEl = document.getElementById('sendBtn');
    var messageInputEl = document.getElementById('messageInput');
    var chatMenuBtn = document.getElementById('chatMenuBtn');
    var chatMenuDropdown = document.getElementById('chatMenuDropdown');
    var chatMenuWrap = document.getElementById('chatMenuWrap');
    if (sendBtnEl) sendBtnEl.addEventListener('click', sendMessage);
    if (messageInputEl) messageInputEl.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
    });
    if (chatMenuBtn && chatMenuDropdown && chatMenuWrap) {
        chatMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            chatMenuDropdown.classList.toggle('open');
        });
        document.addEventListener('click', function(e) {
            if (!chatMenuWrap.contains(e.target)) chatMenuDropdown.classList.remove('open');
        });
    }
    if (chatMenuWrap) chatMenuWrap.addEventListener('click', function(e) {
        if (e.target.closest('#chatDeleteBtn') || e.target.closest('.inbox-chat-delete-opt')) {
            e.stopPropagation();
            var cid = selectedConvId;
            if (!cid) return;
            if (chatMenuDropdown) chatMenuDropdown.classList.remove('open');
            if (!confirm('Delete this conversation? This cannot be undone.')) return;
            var ref = db.collection(convColl).doc(cid);
            ref.delete().then(function() {
                selectedConvId = null;
                if (unsubMsg) { unsubMsg(); unsubMsg = null; }
                document.getElementById('chatEmpty').style.display = 'flex';
                document.getElementById('chatPanel').style.display = 'none';
                var mw = document.getElementById('chatMenuWrap');
                if (mw) mw.style.display = 'none';
                var convItem = document.querySelector('.inbox-conv-item[data-conv-id="' + cid + '"]');
                if (convItem) convItem.remove();
                showInboxMsg('Conversation deleted.', false);
            }).catch(function(err) {
                console.error(err);
                showInboxMsg('Failed to delete conversation.', true);
            });
        }
    });

    auth.onAuthStateChanged(function(user) {
        if (user) {
            if (startParams.creatorId && startParams.campaignId) {
                openOrCreateStartConversation();
            }
            loadConversations();
        } else {
            signInAndRun();
        }
    });
})();
</script>
@endsection
