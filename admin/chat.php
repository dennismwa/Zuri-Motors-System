<?php
$pageTitle = 'Chat Support';
require_once dirname(__DIR__) . '/functions.php';

$sessions = getChatSessions(50);
$activeSession = $_GET['session'] ?? '';
$messages = $activeSession ? getChatMessages($activeSession) : [];
if ($activeSession) markChatRead($activeSession);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $activeSession) {
    CSRF::verifyOrDie();
    $msg = $_POST['message'] ?? '';
    if ($msg) {
        sendChatMessage($activeSession, $msg, 'agent', $currentUser['first_name'], $currentUser['email'], Auth::id());
        redirect(BASE_URL . '/admin/chat?session=' . urlencode($activeSession));
    }
}
include __DIR__ . '/header.php';
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-0 bg-white rounded-xl border border-gray-200 overflow-hidden" style="height:calc(100vh - 160px)">
    <!-- Sessions List -->
    <div class="border-r border-gray-200 overflow-y-auto">
        <div class="p-4 border-b border-gray-100"><h3 class="font-bold text-dark-900 text-sm">Conversations (<?= count($sessions) ?>)</h3></div>
        <?php if (empty($sessions)): ?>
        <p class="p-4 text-sm text-gray-400 text-center">No chats yet</p>
        <?php else: foreach($sessions as $s): ?>
        <a href="?session=<?= urlencode($s['session_id']) ?>" class="flex items-center gap-3 px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition <?= $activeSession===$s['session_id']?'bg-primary-50':'' ?>">
            <div class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0 text-xs font-bold text-gray-500"><?= strtoupper(substr($s['sender_name']??'?',0,1)) ?></div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between"><span class="text-sm font-semibold text-dark-800 truncate"><?= clean($s['sender_name'] ?: 'Visitor') ?></span><span class="text-[10px] text-gray-400"><?= timeAgo($s['last_message']) ?></span></div>
                <p class="text-xs text-gray-400 truncate"><?= $s['message_count'] ?> messages</p>
            </div>
            <?php if($s['unread'] > 0): ?><span class="w-5 h-5 bg-primary-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center"><?= $s['unread'] ?></span><?php endif; ?>
        </a>
        <?php endforeach; endif; ?>
    </div>

    <!-- Chat Area -->
    <div class="lg:col-span-2 flex flex-col">
        <?php if ($activeSession): ?>
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <div><h4 class="font-semibold text-dark-900 text-sm"><?= clean($messages[0]['sender_name'] ?? 'Visitor') ?></h4><p class="text-xs text-gray-400"><?= clean($messages[0]['sender_email'] ?? '') ?></p></div>
        </div>
        <div class="flex-1 overflow-y-auto p-4 space-y-3" id="chat-area">
            <?php foreach($messages as $m): ?>
            <div class="flex <?= $m['sender_type']==='agent'?'justify-end':'justify-start' ?>">
                <div class="max-w-[75%] <?= $m['sender_type']==='agent'?'bg-primary-600 text-white':'bg-gray-100 text-dark-700' ?> text-sm px-4 py-2.5 rounded-2xl <?= $m['sender_type']==='agent'?'rounded-br-md':'rounded-bl-md' ?>">
                    <?= clean($m['message']) ?>
                    <div class="text-[10px] <?= $m['sender_type']==='agent'?'text-white/50':'text-gray-400' ?> mt-1"><?= formatDate($m['created_at'],'H:i') ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <form method="POST" class="p-3 border-t border-gray-100 flex gap-2">
            <?= CSRF::field() ?>
            <input type="text" name="message" class="form-control flex-1 py-2" placeholder="Type a reply..." required autofocus>
            <button type="submit" class="btn btn-primary"><i data-lucide="send" class="w-4 h-4"></i></button>
        </form>
        <?php else: ?>
        <div class="flex-1 flex items-center justify-center"><p class="text-gray-400 text-sm">Select a conversation</p></div>
        <?php endif; ?>
    </div>
</div>

<script>const chatArea=document.getElementById('chat-area');if(chatArea)chatArea.scrollTop=chatArea.scrollHeight;</script>

<?php include __DIR__ . '/footer.php'; ?>
