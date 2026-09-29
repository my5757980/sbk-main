<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Services\Chat\ChatService;
use Illuminate\Http\Request;

/**
 * Groups - making one, naming it, and who is in it (api/group.php of the SBK
 * chat). Writing INTO a group goes through send/upload like every message.
 * Only staff, and only members: seeing every conversation is not an
 * invitation to a room. Changing a group is its owner's; leaving is anyone's.
 */
class ChatGroupController extends Controller
{
    public function handle(Request $request, ChatService $chat)
    {
        $s = $chat->staff();
        if (!$s) {
            // A customer has no groups: as far as they can tell, this door is not there.
            return ChatService::json(['ok' => false, 'signedout' => true], 401);
        }
        $me = (int) $s['id'];
        $do = (string) $request->input('do', '');
        $gid = (int) $request->input('group', 0);

        if ($do === 'create') {
            $ids = $request->input('members', []);
            $ids = is_array($ids) ? $ids : [];
            if (count($ids) > 200) {
                return ChatService::json(['ok' => false, 'error' => 'That is too many people for one group.'], 400);
            }
            if (trim((string) $request->input('name', '')) === '') {
                return ChatService::json(['ok' => false, 'error' => 'Give the group a name.'], 400);
            }
            $made = $chat->createGroup((string) $request->input('name'), $me, $ids);

            return ChatService::json(['ok' => true, 'group' => $this->groupOut($chat, $chat->groupRow($made['id']), $me),
                                      'skipped' => $made['skipped']]);
        }

        if ($do === 'people') {
            return ChatService::json(['ok' => true, 'people' => $this->pickable($chat, $me)]);
        }

        // Everything past here is about a group that exists and that this person is in.
        $g = $chat->mayReadGroup($gid);
        if (!$g) {
            return ChatService::json(['ok' => false, 'error' => 'That group is not yours.'], 403);
        }

        if ($do === 'info') {
            return ChatService::json(['ok' => true, 'group' => $this->groupOut($chat, $g, $me),
                                      'people' => $this->pickable($chat, $me)]);
        }

        if ($do === 'leave') {
            $what = $chat->removeGroupMember($gid, $me);

            return ChatService::json(['ok' => true, 'left' => true, 'gone' => $what === 'gone']);
        }

        if (!$chat->ownsGroup($gid, $me)) {
            return ChatService::json(['ok' => false, 'error' => 'Only whoever made the group can change it.'], 403);
        }

        if ($do === 'rename') {
            if (!$chat->renameGroup($gid, (string) $request->input('name', ''))) {
                return ChatService::json(['ok' => false, 'error' => 'Give the group a name.'], 400);
            }

            return ChatService::json(['ok' => true, 'group' => $this->groupOut($chat, $chat->groupRow($gid), $me)]);
        }

        if ($do === 'add') {
            $ids = $request->input('members', []);
            $n = $chat->addGroupMembers($gid, is_array($ids) ? $ids : []);

            return ChatService::json(['ok' => true, 'added' => $n, 'group' => $this->groupOut($chat, $chat->groupRow($gid), $me)]);
        }

        if ($do === 'remove') {
            $who = (int) $request->input('staff', 0);
            if ($who === $me) {
                // Taking yourself out is leaving, which hands the group on.
                return ChatService::json(['ok' => false, 'error' => 'Use Leave group.'], 400);
            }
            if (!$chat->inGroup($gid, $who)) {
                return ChatService::json(['ok' => false, 'error' => 'They are not in this group.'], 400);
            }
            $chat->removeGroupMember($gid, $who);

            return ChatService::json(['ok' => true, 'group' => $this->groupOut($chat, $chat->groupRow($gid), $me)]);
        }

        return ChatService::json(['ok' => false, 'error' => 'Unknown request.'], 400);
    }

    /** The people this person may put in a group: the desk, minus themselves. */
    private function pickable(ChatService $chat, int $me): array
    {
        $out = [];
        foreach ($chat->staffDirectory() as $p) {
            if ((int) $p['id'] === $me) {
                continue;
            }
            $out[] = [
                'id' => (int) $p['id'],
                'name' => $p['name'],
                'role' => (string) $p['role_label'],
                'online' => $p['online'],
                'ini' => ChatService::initials($p['name']),
                'hue' => ChatService::avatarHue($p['id'] . $p['name']),
            ];
        }

        return $out;
    }

    /** A group, with everybody in it, shaped for the browser. */
    private function groupOut(ChatService $chat, array $g, int $me): array
    {
        $ids = $chat->groupMemberIds((int) $g['id']);
        $names = $chat->staffNames($ids);
        $on = [];
        foreach ($chat->staffDirectory() as $p) {
            if ($p['online']) {
                $on[(int) $p['id']] = true;
            }
        }
        $members = [];
        foreach ($ids as $id) {
            $name = (string) ($names[$id] ?? 'Someone');
            $members[] = [
                'id' => $id,
                'name' => $name,
                'ini' => ChatService::initials($name),
                'hue' => ChatService::avatarHue($id . $name),
                'online' => isset($on[$id]),
                'owner' => $chat->ownsGroup((int) $g['id'], $id),
                'me' => $id === $me,
            ];
        }

        return [
            'id' => (int) $g['id'],
            'name' => (string) $g['name'],
            'members' => $members,
            'count' => count($members),
            'owner' => $chat->ownsGroup((int) $g['id'], $me),
            'ini' => ChatService::initials((string) $g['name']),
            'hue' => ChatService::avatarHue('g' . $g['id'] . $g['name']),
        ];
    }
}
