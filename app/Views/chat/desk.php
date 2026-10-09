<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    /* Full viewport chat desk */
    .desk-container {
        display: flex;
        height: calc(100vh - var(--topbar-height));
        overflow: hidden;
        background: #ffffff;
    }

    /* Col 1: Queue list */
    .desk-queue {
        width: 330px;
        min-width: 300px;
        border-right: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        background: #ffffff;
    }

    .queue-header {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .queue-tabs {
        display: flex;
        border-bottom: 1px solid #f1f5f9;
        background: #fafbfc;
        padding: 0 0.5rem;
    }

    .queue-tab-btn {
        flex: 1;
        padding: 0.65rem 0.25rem;
        border: none;
        background: transparent;
        font-size: 0.8rem;
        font-weight: 500;
        color: #64748b;
        border-bottom: 2px solid transparent;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        transition: all 0.2s;
    }

    .queue-tab-btn.active {
        color: var(--skj-pink);
        font-weight: 600;
        border-bottom-color: var(--skj-pink);
        background: #ffffff;
    }

    .queue-tab-btn .badge {
        font-size: 0.68rem;
        padding: 2px 6px;
    }

    .queue-list {
        flex: 1;
        overflow-y: auto;
    }

    .queue-item {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid #f8fafc;
        cursor: pointer;
        transition: all 0.15s ease;
        display: flex;
        gap: 12px;
        position: relative;
    }

    .queue-item:hover {
        background-color: #f8fafc;
    }

    .queue-item.active {
        background-color: #fff1f5;
        border-left: 3px solid var(--skj-pink);
    }

    .queue-avatar {
        width: 42px;
        height: 42px;
        aspect-ratio: 1 / 1;
        border-radius: 50%;
        background: linear-gradient(135deg, #fce4ec 0%, #e3f2fd 100%);
        color: #c2185b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .queue-info {
        flex: 1;
        min-width: 0;
    }

    .queue-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2px;
    }

    .queue-name {
        font-weight: 600;
        font-size: 0.88rem;
        color: #1e293b;
        line-height: 1.4;
    }

    .queue-time {
        font-size: 0.72rem;
        color: #94a3b8;
    }

    .queue-preview {
        font-size: 0.8rem;
        color: #64748b;
        line-height: 1.4;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
    }

    /* Col 2: Chat Stream */
    .desk-chat {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #f8fafc;
        border-right: 1px solid #e2e8f0;
        min-width: 0;
        position: relative;
    }

    .chat-header {
        height: 68px;
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1.5rem;
        gap: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        z-index: 10;
    }

    .chat-header-actions .btn {
        height: 36px;
        border-radius: 12px;
        font-size: 0.82rem;
        font-weight: 500;
        white-space: nowrap;
        padding: 0 14px;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 16px;
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
    }

    .chat-bubble-row {
        display: flex;
        gap: 12px;
        max-width: 84%;
        align-items: flex-start;
        animation: fadeInMsg 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes fadeInMsg {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .chat-bubble-row.user {
        align-self: flex-start;
    }

    .chat-bubble-row.admin {
        align-self: flex-end;
        flex-direction: row-reverse;
    }

    .chat-bubble-row.system {
        align-self: center;
        max-width: 90%;
        text-align: center;
        margin: 4px 0;
    }

    .bubble-avatar {
        width: 38px;
        height: 38px;
        aspect-ratio: 1 / 1;
        border-radius: 50%;
        flex-shrink: 0;
        object-fit: cover;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .bubble-wrapper {
        min-width: 0;
        max-width: 100%;
        display: flex;
        flex-direction: column;
    }

    .chat-bubble-row.admin .bubble-wrapper {
        align-items: flex-end;
    }

    .bubble-sender-name {
        font-size: 0.78rem;
        font-weight: 600;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 0 4px;
    }

    .chat-bubble-row.user .bubble-sender-name {
        color: #1976d2;
    }

    .chat-bubble-row.admin .bubble-sender-name {
        color: #c2185b;
        justify-content: flex-end;
    }

    .bubble-content {
        padding: 0.85rem 1.15rem;
        border-radius: 18px;
        font-size: 0.94rem;
        line-height: 1.65;
        position: relative;
        overflow-wrap: anywhere;
        word-break: break-word;
        max-width: 100%;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        letter-spacing: 0.01em;
    }

    .msg-list-item {
        display: flex;
        align-items: flex-start;
        gap: 6px;
        margin: 2px 0;
        line-height: 1.6;
    }

    .msg-bullet {
        color: var(--skj-pink);
        font-weight: 700;
        font-size: 0.95rem;
        line-height: 1.5;
        flex-shrink: 0;
    }

    .chat-bubble-row.admin:not(.bot) .msg-bullet {
        color: #ffffff;
    }

    /* Visitor (User) Bubble */
    .chat-bubble-row.user .bubble-content {
        background: #ffffff;
        color: #0f172a;
        border: 1px solid #e2e8f0;
        border-top-left-radius: 4px;
    }

    /* Staff Human Agent Bubble */
    .chat-bubble-row.admin .bubble-content {
        background: linear-gradient(135deg, #e91e63 0%, #c2185b 100%);
        color: #ffffff;
        border-top-right-radius: 4px;
        box-shadow: 0 4px 16px rgba(233, 30, 99, 0.22);
    }

    .chat-bubble-row.admin .bubble-content a.msg-link-badge {
        background: rgba(255, 255, 255, 0.22);
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.35);
        text-decoration: none;
    }

    .chat-bubble-row.admin .bubble-content a.msg-tel-badge {
        background: #ffffff;
        color: #c2185b !important;
        text-decoration: none;
        font-weight: 600;
    }

    /* AI Assistant (น้องกุหลาบ) Bubble */
    .chat-bubble-row.admin.bot {
        align-self: flex-start;
        flex-direction: row;
    }

    .chat-bubble-row.admin.bot .bubble-wrapper {
        align-items: flex-start;
    }

    .chat-bubble-row.admin.bot .bubble-sender-name {
        color: #0f172a;
        justify-content: flex-start;
    }

    .chat-bubble-row.admin.bot .bubble-content {
        background: #ffffff;
        color: #0f172a;
        border: 1.5px solid #e2e8f0;
        border-left: 4px solid var(--skj-pink);
        border-top-left-radius: 4px;
        border-top-right-radius: 18px;
        box-shadow: 0 4px 18px rgba(233, 30, 99, 0.06);
    }

    /* System Notice */
    .chat-bubble-row.system .bubble-content {
        background: #e2e8f0;
        color: #475569;
        font-size: 0.78rem;
        font-weight: 500;
        border-radius: 20px;
        padding: 5px 16px;
        box-shadow: none;
        border: none;
    }

    .bubble-meta {
        font-size: 0.72rem;
        margin-top: 4px;
        color: #94a3b8;
        padding: 0 4px;
        font-family: 'Inter', sans-serif;
    }

    .chat-bubble-row.admin .bubble-meta {
        text-align: right;
    }

    .bubble-attachment-img {
        max-width: 320px;
        max-height: 240px;
        width: auto;
        height: auto;
        border-radius: 12px;
        margin-top: 8px;
        cursor: pointer;
        display: block;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid rgba(0, 0, 0, 0.06);
    }
    .bubble-attachment-img:hover {
        transform: scale(1.02);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
    }

    .bubble-attachment-file {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        background: rgba(0, 0, 0, 0.06);
        border-radius: 10px;
        margin-top: 8px;
        text-decoration: none;
        color: inherit;
        font-size: 0.85rem;
        font-weight: 500;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        transition: background 0.15s ease;
    }
    .bubble-attachment-file:hover {
        background: rgba(0, 0, 0, 0.1);
    }

    /* Chat Footer & Input */
    .chat-footer {
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        padding: 1rem 1.5rem;
        position: relative;
    }

    .chat-input-bar {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 18px;
        padding: 8px 12px;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .chat-input-bar:focus-within {
        border-color: var(--skj-pink);
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(233, 30, 99, 0.12);
    }

    .chat-textarea {
        flex: 1;
        border: none;
        background: transparent;
        outline: none;
        resize: none;
        font-family: inherit;
        font-size: 0.94rem;
        max-height: 140px;
        min-height: 38px;
        line-height: 1.6;
        padding: 6px 4px;
        color: #0f172a;
    }

    .chat-textarea::placeholder {
        color: #94a3b8;
    }

    .chat-action-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: transparent;
        color: #64748b;
        cursor: pointer;
        transition: all 0.15s ease;
        flex-shrink: 0;
        margin-bottom: 2px;
    }

    .chat-action-btn:hover {
        background: #f1f5f9;
        color: var(--skj-pink);
        transform: translateY(-1px);
    }

    .chat-action-btn:active {
        transform: scale(0.95);
    }

    .chat-send-btn {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: linear-gradient(135deg, var(--skj-pink) 0%, #c2185b 100%);
        color: #ffffff;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(233, 30, 99, 0.25);
        transition: all 0.15s ease;
        flex-shrink: 0;
    }

    .chat-send-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(233, 30, 99, 0.35);
    }

    .chat-send-btn:active {
        transform: scale(0.94);
    }

    .pulse-unread {
        animation: skjDeskPulse 1.8s infinite;
    }
    @keyframes skjDeskPulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.18); box-shadow: 0 0 10px rgba(239, 68, 68, 0.55); }
        100% { transform: scale(1); }
    }

    .canned-autocomplete {
        position: absolute;
        bottom: calc(100% - 10px);
        left: 20px;
        right: 20px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
        max-height: 240px;
        overflow-y: auto;
        z-index: 1050;
        display: none;
    }
    .canned-auto-item {
        padding: 9px 14px;
        border-bottom: 1px solid #f8fafc;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: background 0.15s;
    }
    .canned-auto-item:hover, .canned-auto-item.active {
        background: #fff1f5;
    }
    .canned-auto-shortcut {
        font-weight: 600;
        color: var(--skj-pink);
        font-family: monospace;
        font-size: 0.85rem;
    }
    .canned-auto-title {
        font-size: 0.82rem;
        color: #1e293b;
        font-weight: 500;
        margin-left: 8px;
    }
    .canned-auto-preview {
        font-size: 0.76rem;
        color: #64748b;
        max-width: 50%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Col 3: Details & Tools */
    .desk-details {
        width: 320px;
        min-width: 280px;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
    }

    .details-card {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .details-title {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #94a3b8;
        margin-bottom: 0.75rem;
    }

    .canned-item {
        padding: 6px 10px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.84rem;
        transition: background 0.15s;
        border: 1px solid #f1f5f9;
        margin-bottom: 6px;
    }

    .canned-item:hover {
        background: #fdf2f8;
        border-color: #fbcfe8;
    }

    .canned-item .shortcut {
        font-weight: 600;
        color: var(--skj-pink);
    }

    /* Empty Desk State */
    .empty-desk {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        padding: 2rem;
        text-align: center;
    }

    .empty-desk i {
        font-size: 3.5rem;
        margin-bottom: 1rem;
        color: #cbd5e1;
    }

    /* Mobile-First Chat Desk Layout */
    @media (max-width: 991.98px) {
        .desk-container {
            position: relative;
            height: calc(100vh - var(--topbar-height));
        }
        .desk-queue {
            width: 100% !important;
            min-width: 100% !important;
            border-right: none !important;
            display: flex;
        }
        .desk-chat {
            display: none !important;
            width: 100% !important;
            border-right: none !important;
        }
        .desk-details {
            display: none !important;
        }
        .desk-container.mobile-chat-open .desk-queue {
            display: none !important;
        }
        .desk-container.mobile-chat-open .desk-chat {
            display: flex !important;
        }
        #chatInputMessage {
            font-size: 16px !important; /* Prevents iOS Safari auto-zoom */
        }
        .chat-header {
            padding: 0 0.85rem;
        }
        .chat-messages {
            padding: 0.85rem;
        }
    }
</style>

<div class="desk-container">
    <!-- 1. Left Queue Column -->
    <div class="desk-queue">
        <?php if (($aiConfig->ai_status ?? 'off') !== 'on'): ?>
            <div class="alert alert-warning py-2 px-3 mb-0 border-0 border-bottom rounded-0 small d-flex align-items-center justify-content-between" style="font-size: 0.76rem; background: #fff8e1; border-color: #ffe082 !important;">
                <div><i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> AI ปิดใช้งานในระบบหลัก</div>
                <a href="<?= base_url('settings/ai') ?>" class="btn btn-sm btn-outline-warning py-0 px-2 rounded-pill fw-semibold" style="font-size: 0.7rem;">เปิดใช้งาน</a>
            </div>
        <?php endif; ?>
        <div class="queue-header">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-search"></i></span>
                <input type="text" class="form-control bg-light border-start-0" id="queueSearchInput" placeholder="ค้นหาชื่อ, เบอร์โทร..." autocomplete="off">
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="queue-tabs">
            <button class="queue-tab-btn active" data-filter="active" onclick="switchFilter('active', this)">
                สนทนา <span class="badge rounded-pill bg-danger-subtle text-danger" id="badgeActive">0</span>
            </button>
            <button class="queue-tab-btn" data-filter="unassigned" onclick="switchFilter('unassigned', this)">
                รอรับ <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis" id="badgeUnassigned">0</span>
            </button>
            <button class="queue-tab-btn" data-filter="mine" onclick="switchFilter('mine', this)">
                ของฉัน <span class="badge rounded-pill bg-primary-subtle text-primary" id="badgeMine">0</span>
            </button>
            <button class="queue-tab-btn" data-filter="closed" onclick="switchFilter('closed', this)">
                ปิดแล้ว
            </button>
        </div>

        <!-- Queue Item List -->
        <div class="queue-list" id="queueListContainer">
            <div class="p-4 text-center text-muted small">
                <i class="fa-solid fa-spinner fa-spin me-1"></i> กำลังโหลดรายการแชท...
            </div>
        </div>
    </div>

    <!-- 2. Middle Chat Stream Column -->
    <div class="desk-chat" id="deskChatArea">
        <div class="empty-desk" id="emptyDeskView">
            <i class="fa-regular fa-comments"></i>
            <h6 class="fw-semibold text-secondary">เลือกการสนทนาเพื่อเริ่มต้น</h6>
            <p class="small text-muted mb-0">คลิกที่รายการแชททางด้านซ้ายเพื่อพูดคุยกับผู้ใช้งาน</p>
        </div>

        <div class="d-none flex-column h-100" id="activeChatView">
            <!-- Header -->
            <div class="chat-header">
                <div class="d-flex align-items-center gap-2 min-w-0" style="flex: 1; margin-right: 8px;">
                    <button class="btn btn-sm btn-light border d-lg-none me-1 btn-icon-sm" onclick="closeMobileChat()" title="กลับหน้ารายการแชท">
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                    <div class="queue-avatar flex-shrink-0" id="activeUserAvatar" style="width: 38px; height: 38px; font-size: 0.95rem;">U</div>
                    <div style="min-width: 0; flex: 1;">
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="mb-0 fw-semibold text-dark text-truncate" id="activeUserName" style="max-width: 180px;">ผู้ใช้งาน</h6>
                            <span class="badge bg-success-subtle text-success flex-shrink-0 small" id="activeSessionStatus">Active</span>
                        </div>
                        <div class="small text-muted text-truncate" id="activeUserTel"><i class="fa-solid fa-phone me-1"></i> -</div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-shrink-0 chat-header-actions">
                    <!-- Trigger AI Now Button -->
                    <button class="btn btn-sm btn-outline-primary" id="triggerAiBtn" onclick="triggerCurrentAi()" title="สั่งให้น้องกุหลาบ AI ตอบคำถามล่าสุดของผู้ใช้ทันที">
                        <i class="fa-solid fa-wand-magic-sparkles me-1" style="color: var(--skj-pink);"></i> <span class="d-none d-md-inline">ให้ AI ตอบทันที</span>
                    </button>

                    <!-- Toggle Bot Button -->
                    <button class="btn btn-sm btn-outline-info" id="toggleBotBtn" onclick="toggleCurrentBot()" title="สลับเปิด/พักการทำงานของ AI">
                        <i class="fa-solid fa-robot"></i> <span class="d-none d-sm-inline ms-1" id="toggleBotText">พักบอท</span>
                    </button>

                    <!-- Close/Reopen Chat Button -->
                    <button class="btn btn-sm btn-outline-secondary" id="toggleStatusBtn" onclick="toggleCurrentStatus()">
                        <i class="fa-solid fa-circle-check"></i> <span class="d-none d-sm-inline ms-1" id="toggleStatusText">ปิดการสนทนา</span>
                    </button>
                </div>
            </div>

            <!-- Messages Stream -->
            <div class="chat-messages" id="chatMessagesContainer"></div>

            <!-- Footer / Input bar -->
            <div class="chat-footer">
                <!-- Autocomplete Dropdown for / shortcut -->
                <div class="canned-autocomplete" id="cannedAutocompleteBox"></div>

                <div id="attachmentPreviewChip" class="d-none mb-2 p-1 px-2 bg-light border rounded-pill d-inline-flex align-items-center gap-2 small">
                    <i class="fa-solid fa-paperclip text-muted"></i>
                    <span id="attachmentFileName" class="text-truncate" style="max-width: 200px;"></span>
                    <button type="button" class="btn-close btn-close-sm" style="font-size: 0.6rem;" onclick="removeAttachment()"></button>
                </div>

                <div class="chat-input-bar">
                    <input type="file" id="chatFileInput" class="d-none" onchange="handleFileUpload(this)">
                    <button type="button" class="chat-action-btn" onclick="document.getElementById('chatFileInput').click()" title="แนบรูปภาพหรือไฟล์">
                        <i class="fa-solid fa-paperclip fs-6"></i>
                    </button>
                    
                    <button type="button" class="chat-action-btn" onclick="openCannedModal()" title="คลังคำตอบด่วน (/)">
                        <i class="fa-solid fa-bolt fs-6"></i>
                    </button>

                    <button type="button" class="chat-action-btn" onclick="triggerCurrentAi()" title="สั่งให้น้องกุหลาบ AI ตอบคำถามล่าสุด">
                        <i class="fa-solid fa-wand-magic-sparkles fs-6" style="color: var(--skj-pink);"></i>
                    </button>

                    <textarea id="chatInputMessage" class="chat-textarea" placeholder="พิมพ์ข้อความตอบกลับ (กด / เพื่อเลือกข้อความด่วน, Enter ส่ง, Shift+Enter ขึ้นบรรทัดใหม่)..." rows="1"></textarea>

                    <button type="button" class="chat-send-btn" onclick="sendAgentMessage()" title="ส่งข้อความ (Enter)">
                        <i class="fa-solid fa-paper-plane fs-6"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Right Details & Tools Column -->
    <div class="desk-details d-none d-xl-flex" id="deskDetailsArea">
        <div class="details-card">
            <div class="details-title">ข้อมูลผู้ติดต่อ</div>
            <table class="table table-sm table-borderless small mb-0">
                <tr><td class="text-muted" width="80">ชื่อ:</td><td class="fw-semibold" id="detailName">-</td></tr>
                <tr><td class="text-muted">เบอร์โทร:</td><td id="detailTel">-</td></tr>
                <tr><td class="text-muted">IP Address:</td><td class="font-monospace text-muted" id="detailIp">-</td></tr>
                <tr><td class="text-muted">ผู้รับผิดชอบ:</td><td id="detailAgent">-</td></tr>
            </table>

            <!-- Assign Agent Dropdown -->
            <div class="mt-3">
                <label class="form-label small text-muted mb-1">ส่งต่องานให้เจ้าหน้าที่</label>
                <select class="form-select form-select-sm" id="assignAgentSelect" onchange="assignCurrentAgent(this.value)">
                    <option value="">-- เลือกเจ้าหน้าที่ --</option>
                    <?php foreach ($agents as $ag): ?>
                        <option value="<?= $ag->agent_id ?>"><?= esc($ag->fullname) ?> (<?= esc($ag->role) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Internal Notes -->
        <div class="details-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="details-title mb-0">บันทึกภายใน (Internal Notes)</div>
                <button class="btn btn-xs btn-link p-0 text-decoration-none small" onclick="saveAgentNotes()">บันทึก</button>
            </div>
            <textarea class="form-control form-control-sm bg-light" id="agentNotesInput" rows="3" placeholder="จดโน้ตเกี่ยวกับผู้ติดต่อ (ผู้ใช้จะไม่เห็นส่วนนี้)..."></textarea>
        </div>

        <!-- Quick Canned Replies -->
        <div class="details-card flex-grow-1">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="details-title mb-0">ข้อความด่วน (Canned)</div>
                <button class="btn btn-xs btn-outline-primary py-0 px-1 small" onclick="openCannedModal()" style="font-size: 0.72rem;">ดูทั้งหมด</button>
            </div>
            <div style="max-height: 250px; overflow-y: auto;">
                <?php if (!empty($cannedReplies)): ?>
                    <?php foreach ($cannedReplies as $r): ?>
                        <div class="canned-item" onclick="insertCannedText(<?= htmlspecialchars(json_encode($r->message)) ?>)">
                            <div class="d-flex justify-content-between">
                                <span class="shortcut"><?= esc($r->shortcut) ?></span>
                                <span class="text-muted small"><?= esc($r->title) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted small">ยังไม่มีข้อความด่วน สามารถเพิ่มได้ที่เมนู <em>ข้อความด่วน</em></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Canned Replies Picker Modal -->
<div class="modal fade" id="cannedRepliesModal" tabindex="-1" aria-labelledby="cannedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-bottom-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold" id="cannedModalLabel">
                        <i class="fa-solid fa-bolt text-warning me-2"></i>คลังข้อความตอบกลับด่วน (Canned Replies)
                    </h5>
                    <p class="text-muted small mb-0">คลิกที่ข้อความเพื่อนำไปใส่ในกล่องข้อความตอบกลับทันที (หรือพิมพ์ / ในกล่องข้อความ)</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control form-control-sm mb-3" id="cannedModalSearch" placeholder="🔍 ค้นหาคำย่อ หรือเนื้อหาข้อความ..." oninput="filterCannedModal(this.value)">
                <div class="list-group" id="cannedModalList" style="max-height: 380px; overflow-y: auto;">
                    <?php if (!empty($cannedReplies)): ?>
                        <?php foreach ($cannedReplies as $r): ?>
                            <button type="button" class="list-group-item list-group-item-action p-3 canned-modal-row" onclick="selectCannedFromModal(<?= htmlspecialchars(json_encode($r->message)) ?>)">
                                <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 fw-semibold text-primary"><span class="badge bg-light text-danger border me-1"><?= esc($r->shortcut) ?></span> <?= esc($r->title) ?></h6>
                                    <small class="text-muted"><?= esc($r->category ?? 'ทั่วไป') ?></small>
                                </div>
                                <p class="mb-1 text-secondary small text-break"><?= nl2br(esc($r->message)) ?></p>
                            </button>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center p-4 text-muted">ยังไม่มีข้อความด่วน</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const cannedRepliesData = <?= json_encode($cannedReplies ?? []) ?>;
    const initialSessionId  = <?= json_encode($targetSessionId ?? null) ?>;
    let currentFilter = 'active';
    let currentSessionId = null;
    let currentSessionData = null;
    let pollingInterval = null;
    let pendingAttachment = null;
    let deskLastMsgId = 0;
    let renderedDeskMsgIds = new Set();
    let selectedAutoIndex = -1;

    document.addEventListener('DOMContentLoaded', () => {
        loadQueue().then(() => {
            if (initialSessionId) {
                selectSession(initialSessionId);
            }
        });
        pollingInterval = setInterval(loadQueue, 3000);

        const textarea = document.getElementById('chatInputMessage');
        
        // Keydown handlers: Enter to send, Up/Down/Enter/Escape for canned autocomplete
        textarea.addEventListener('keydown', (e) => {
            const autoBox = document.getElementById('cannedAutocompleteBox');
            const isOpen = autoBox && autoBox.style.display === 'block';

            if (isOpen) {
                const items = autoBox.querySelectorAll('.canned-auto-item');
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (items.length > 0) {
                        selectedAutoIndex = (selectedAutoIndex + 1) % items.length;
                        highlightAutoItem(items);
                    }
                    return;
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (items.length > 0) {
                        selectedAutoIndex = (selectedAutoIndex - 1 + items.length) % items.length;
                        highlightAutoItem(items);
                    }
                    return;
                } else if (e.key === 'Enter' && !e.shiftKey) {
                    if (selectedAutoIndex >= 0 && items[selectedAutoIndex]) {
                        e.preventDefault();
                        items[selectedAutoIndex].click();
                        return;
                    }
                } else if (e.key === 'Escape') {
                    e.preventDefault();
                    hideCannedAutocomplete();
                    return;
                }
            }

            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendAgentMessage();
            }
        });

        // Input listener for / canned reply shortcut and auto-resize
        textarea.addEventListener('input', () => {
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 140) + 'px';

            const val = textarea.value;
            if (val.startsWith('/')) {
                const query = val.substring(1).toLowerCase().trim();
                showCannedAutocomplete(query);
            } else {
                hideCannedAutocomplete();
            }
        });

        // Search queue
        document.getElementById('queueSearchInput')?.addEventListener('input', () => {
            loadQueue();
        });
    });

    // Canned Autocomplete Helper
    function showCannedAutocomplete(query) {
        const autoBox = document.getElementById('cannedAutocompleteBox');
        if (!autoBox) return;

        const matches = cannedRepliesData.filter(r => {
            const sc = (r.shortcut || '').toLowerCase().replace(/^\//, '');
            const title = (r.title || '').toLowerCase();
            const msg = (r.message || '').toLowerCase();
            return !query || sc.includes(query) || title.includes(query) || msg.includes(query);
        });

        if (matches.length === 0) {
            hideCannedAutocomplete();
            return;
        }

        selectedAutoIndex = 0;
        let html = '';
        matches.slice(0, 6).forEach((r, idx) => {
            html += `
                <div class="canned-auto-item ${idx === 0 ? 'active' : ''}" data-index="${idx}" onclick="applyCannedReply(${escapeHtml(JSON.stringify(r.message))})">
                    <div class="d-flex align-items-center">
                        <span class="canned-auto-shortcut">${escapeHtml(r.shortcut || '')}</span>
                        <span class="canned-auto-title">${escapeHtml(r.title || '')}</span>
                    </div>
                    <span class="canned-auto-preview">${escapeHtml(r.message || '')}</span>
                </div>
            `;
        });

        autoBox.innerHTML = html;
        autoBox.style.display = 'block';
    }

    function hideCannedAutocomplete() {
        const autoBox = document.getElementById('cannedAutocompleteBox');
        if (autoBox) {
            autoBox.style.display = 'none';
            autoBox.innerHTML = '';
            selectedAutoIndex = -1;
        }
    }

    function highlightAutoItem(items) {
        items.forEach((it, idx) => {
            it.classList.toggle('active', idx === selectedAutoIndex);
            if (idx === selectedAutoIndex) {
                it.scrollIntoView({ block: 'nearest' });
            }
        });
    }

    function applyCannedReply(text) {
        const input = document.getElementById('chatInputMessage');
        input.value = text;
        hideCannedAutocomplete();
        input.focus();
    }

    // Modal Canned Replies
    function openCannedModal() {
        const modalEl = document.getElementById('cannedRepliesModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
            setTimeout(() => {
                document.getElementById('cannedModalSearch')?.focus();
            }, 300);
        }
    }

    function filterCannedModal(query) {
        const q = (query || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.canned-modal-row');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = (!q || text.includes(q)) ? 'block' : 'none';
        });
    }

    function selectCannedFromModal(text) {
        const modalEl = document.getElementById('cannedRepliesModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        }
        applyCannedReply(text);
    }

    function switchFilter(filter, btn) {
        currentFilter = filter;
        document.querySelectorAll('.queue-tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        loadQueue();
    }

    function loadQueue() {
        const search = document.getElementById('queueSearchInput')?.value || '';
        return fetch(`${BASE_URL}/chat/queue?filter=${currentFilter}&search=${encodeURIComponent(search)}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    renderQueue(data.sessions);
                    updateBadges(data.stats);

                    // If a session is open, refresh messages incrementally
                    if (currentSessionId) {
                        refreshCurrentSession(false);
                    }
                }
            })
            .catch(err => console.error('Queue load error:', err));
    }

    function updateBadges(stats) {
        if (!stats) return;
        const bActive = document.getElementById('badgeActive');
        if (bActive) bActive.innerText = stats.active || 0;
        const bUnassigned = document.getElementById('badgeUnassigned');
        if (bUnassigned) bUnassigned.innerText = stats.unassigned || 0;
        const bMine = document.getElementById('badgeMine');
        if (bMine) bMine.innerText = stats.mine || 0;

        const sidebarBadge = document.getElementById('sidebarUnreadBadge');
        if (sidebarBadge) {
            const unread = stats.unassigned || 0;
            sidebarBadge.innerText = unread;
            sidebarBadge.classList.toggle('d-none', unread === 0);
        }
    }

    function renderQueue(sessions) {
        const container = document.getElementById('queueListContainer');
        if (!sessions || sessions.length === 0) {
            container.innerHTML = `<div class="p-4 text-center text-muted small">ไม่พบรายการสนทนา</div>`;
            return;
        }

        let html = '';
        sessions.forEach(s => {
            const isActive = (s.session_id == currentSessionId) ? 'active' : '';
            const unreadAdmin = parseInt(s.unread_admin_count, 10) || 0;
            const initial = (s.user_name || 'U').charAt(0).toUpperCase();

            let lastMsg = s.last_message || '';
            if (s.last_attachment) {
                lastMsg = '📎 แนบไฟล์/รูปภาพ';
            } else if (!lastMsg) {
                lastMsg = 'เริ่มการสนทนาใหม่';
            }

            html += `
                <div class="queue-item ${isActive}" onclick="selectSession(${s.session_id})">
                    <div class="queue-avatar">${initial}</div>
                    <div class="queue-info">
                        <div class="queue-title">
                            <span class="queue-name text-truncate" style="flex: 1; min-width: 0; margin-right: 6px;">${escapeHtml(s.user_name || 'ผู้ใช้งาน')}</span>
                            <span class="queue-time flex-shrink-0">${formatTime(s.last_message_time || s.updated_at)}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <span class="queue-preview text-truncate" style="flex: 1; min-width: 0;">${escapeHtml(lastMsg)}</span>
                            ${unreadAdmin > 0 ? `<span class="badge bg-danger rounded-pill pulse-unread flex-shrink-0">${unreadAdmin}</span>` : ''}
                        </div>
                        <div class="mt-1 d-flex gap-1 align-items-center justify-content-between flex-wrap" style="font-size: 0.72rem;">
                            ${s.is_bot_paused == 1 
                                ? '<span class="badge bg-warning-subtle text-warning-emphasis" style="font-size: 0.65rem;"><i class="fa-solid fa-user me-1"></i>จนท.ดูแล</span>' 
                                : '<span class="badge bg-info-subtle text-info" style="font-size: 0.65rem;"><i class="fa-solid fa-robot me-1"></i>น้องกุหลาบ AI</span>'}
                            <span class="text-truncate text-muted" style="max-width: 130px;">
                                ${s.assigned_agent_name 
                                    ? `👤 ${escapeHtml(s.assigned_agent_name)}` 
                                    : '<span class="text-danger fw-semibold">ยังไม่มอบหมาย</span>'}
                            </span>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    function selectSession(sessionId) {
        currentSessionId = sessionId;
        deskLastMsgId = 0;
        renderedDeskMsgIds.clear();

        document.querySelector('.desk-container')?.classList.add('mobile-chat-open');

        document.getElementById('emptyDeskView').classList.add('d-none');
        document.getElementById('activeChatView').classList.remove('d-none');
        document.getElementById('activeChatView').classList.add('d-flex');

        refreshCurrentSession(true);
    }

    function closeMobileChat() {
        document.querySelector('.desk-container')?.classList.remove('mobile-chat-open');
        currentSessionId = null;
        document.getElementById('emptyDeskView').classList.remove('d-none');
        document.getElementById('activeChatView').classList.add('d-none');
        document.getElementById('activeChatView').classList.remove('d-flex');
        loadQueue();
    }

    // Incremental Message Polling
    function refreshCurrentSession(isInitial = false) {
        if (!currentSessionId) return;

        const url = `${BASE_URL}/chat/session/${currentSessionId}${isInitial ? '' : `?after_id=${deskLastMsgId}`}`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    currentSessionData = data.session;
                    updateChatHeader(data.session);
                    updateDetailsPane(data.session);

                    const messages = data.messages || [];
                    if (isInitial) {
                        const container = document.getElementById('chatMessagesContainer');
                        container.innerHTML = '';
                        renderedDeskMsgIds.clear();
                        messages.forEach(m => appendSingleDeskMessage(m, false));
                        container.scrollTop = container.scrollHeight;
                    } else if (messages.length > 0) {
                        let hasNewUserMsg = false;
                        messages.forEach(m => {
                            const mid = parseInt(m.message_id, 10);
                            if (!renderedDeskMsgIds.has(mid)) {
                                appendSingleDeskMessage(m, true);
                                if (m.sender_type === 'user') {
                                    hasNewUserMsg = true;
                                }
                            }
                        });

                        if (hasNewUserMsg) {
                            playNotifySound();
                        }
                    }
                }
            })
            .catch(err => console.error('Session refresh error:', err));
    }

    function updateChatHeader(s) {
        if (!s) return;
        const nameEl = document.getElementById('activeUserName');
        if (nameEl) nameEl.innerText = s.user_name || 'ผู้ใช้งาน';

        const avatarEl = document.getElementById('activeUserAvatar');
        if (avatarEl) avatarEl.innerText = (s.user_name || 'U').charAt(0).toUpperCase();

        const telEl = document.getElementById('activeUserTel');
        if (telEl) {
            telEl.innerHTML = s.user_tel 
                ? `<i class="fa-solid fa-phone me-1"></i> <a href="tel:${escapeHtml(s.user_tel)}" class="text-decoration-none text-secondary">${escapeHtml(s.user_tel)}</a>` 
                : '<span class="text-muted"><i class="fa-solid fa-phone me-1"></i> ไม่ได้ระบุเบอร์โทร</span>';
        }

        const statusBadge = document.getElementById('activeSessionStatus');
        if (statusBadge) {
            statusBadge.innerText = (s.status === 'active') ? 'กำลังสนทนา' : 'ปิดแล้ว';
            statusBadge.className = `badge ${(s.status === 'active') ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'} small`;
        }

        // Bot toggle button
        const toggleBotBtn = document.getElementById('toggleBotBtn');
        if (toggleBotBtn) {
            const isPaused = (parseInt(s.is_bot_paused, 10) === 1);
            if (isPaused) {
                toggleBotBtn.className = 'btn btn-sm btn-outline-success';
                toggleBotBtn.innerHTML = '<i class="fa-solid fa-robot me-1"></i> <span class="d-none d-sm-inline">เปิดให้ AI ตอบแทน</span>';
                toggleBotBtn.title = 'สถานะปัจจุบัน: จนท. ดูแลอยู่ (คลิกเพื่อให้ AI น้องกุหลาบเริ่มตอบแทนอัตโนมัติ)';
            } else {
                toggleBotBtn.className = 'btn btn-sm btn-outline-warning';
                toggleBotBtn.innerHTML = '<i class="fa-solid fa-pause me-1"></i> <span class="d-none d-sm-inline">พัก AI (จนท.ดูแล)</span>';
                toggleBotBtn.title = 'สถานะปัจจุบัน: AI กำลังตอบอัตโนมัติ (คลิกเพื่อพัก AI ให้ จนท. ดูแล)';
            }
        }

        // Close/reopen button
        const toggleStatusBtn = document.getElementById('toggleStatusBtn');
        if (toggleStatusBtn) {
            const isActive = (s.status === 'active');
            if (isActive) {
                toggleStatusBtn.className = 'btn btn-sm btn-outline-secondary';
                toggleStatusBtn.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i> <span class="d-none d-sm-inline">ปิดการสนทนา</span>';
            } else {
                toggleStatusBtn.className = 'btn btn-sm btn-outline-primary';
                toggleStatusBtn.innerHTML = '<i class="fa-solid fa-rotate-left me-1"></i> <span class="d-none d-sm-inline">เปิดสนทนาใหม่</span>';
            }
        }
    }

    function appendSingleDeskMessage(m, autoScroll = true) {
        const mid = parseInt(m.message_id, 10);
        if (mid && renderedDeskMsgIds.has(mid)) return;
        if (mid) {
            renderedDeskMsgIds.add(mid);
            if (mid > deskLastMsgId) deskLastMsgId = mid;
        }

        const container = document.getElementById('chatMessagesContainer');
        if (!container) return;

        const rowClass = m.sender_type; // user, admin, system
        const isBot = (m.is_bot == 1);
        const botClass = isBot ? 'bot' : '';
        const senderName = isBot ? 'น้องกุหลาบ AI' : (m.sender_name || (m.sender_type === 'user' ? 'ผู้ใช้งาน' : 'เจ้าหน้าที่'));

        let avatar = (m.sender_type === 'admin') 
            ? (m.agent_avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(m.sender_name || 'Agent')}&background=fce4ec&color=c2185b`) 
            : `https://ui-avatars.com/api/?name=${encodeURIComponent(m.sender_name || 'User')}&background=e3f2fd&color=1976d2`;

        if (isBot) {
            avatar = `${BASE_URL}/public/assets/images/logo-skj.png`;
        }

        let attachmentHtml = '';
        if (m.attachment_url) {
            if (m.attachment_type === 'image' || m.attachment_url.match(/\.(jpg|jpeg|png|gif|webp)$/i)) {
                attachmentHtml = `<a href="${m.attachment_url}" target="_blank"><img src="${m.attachment_url}" class="bubble-attachment-img" alt="attachment" onerror="this.onerror=null;this.parentNode.innerHTML='<span class=\\'text-danger small\\'>[รูปภาพไม่สามารถแสดงได้]</span>';"></a>`;
            } else {
                attachmentHtml = `<a href="${m.attachment_url}" target="_blank" class="bubble-attachment-file"><i class="fa-solid fa-file-arrow-down text-primary"></i> <span>ดาวน์โหลดไฟล์แนบ</span></a>`;
            }
        }

        const div = document.createElement('div');
        if (m.sender_type === 'system' && !m.is_bot) {
            div.className = 'chat-bubble-row system';
            div.innerHTML = `<div class="bubble-content"><i class="fa-solid fa-circle-info me-1 text-muted"></i> ${escapeHtml(m.message)}</div>`;
        } else {
            div.className = `chat-bubble-row ${rowClass} ${botClass}`;
            
            let headerTag = '';
            if (isBot) {
                headerTag = `
                    <div class="bubble-sender-name">
                        <span class="badge" style="background: linear-gradient(135deg, #e91e63 0%, #1976d2 100%); color: #fff; font-size: 0.68rem; padding: 2px 8px; border-radius: 8px;">
                            <i class="fa-solid fa-wand-magic-sparkles me-1"></i> น้องกุหลาบ AI (ระบบอัตโนมัติ)
                        </span>
                    </div>`;
            } else if (m.sender_type === 'admin') {
                headerTag = `
                    <div class="bubble-sender-name">
                        <span>${escapeHtml(senderName)}</span>
                        <span class="badge bg-danger-subtle text-danger" style="font-size: 0.65rem; padding: 2px 6px; border-radius: 6px;">เจ้าหน้าที่</span>
                    </div>`;
            } else {
                headerTag = `
                    <div class="bubble-sender-name">
                        <span>${escapeHtml(senderName)}</span>
                        <span class="badge bg-primary-subtle text-primary" style="font-size: 0.65rem; padding: 2px 6px; border-radius: 6px;">ผู้ติดต่อ</span>
                    </div>`;
            }

            div.innerHTML = `
                <img src="${avatar}" class="bubble-avatar" alt="Avatar" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(senderName)}&background=fce4ec&color=c2185b';">
                <div class="bubble-wrapper">
                    ${headerTag}
                    <div class="bubble-content">
                        <div>${parseDeskMarkdown(m.message || '')}</div>
                        ${attachmentHtml}
                    </div>
                    <div class="bubble-meta"><i class="fa-regular fa-clock me-1" style="font-size: 0.68rem;"></i>${formatTime(m.created_at)}</div>
                </div>
            `;
        }

        container.appendChild(div);
        if (autoScroll) {
            container.scrollTop = container.scrollHeight;
        }
    }

    // Markdown Parser for Chat Desk (Natural reading typography, compact line breaks)
    function parseDeskMarkdown(text) {
        if (!text) return '';
        let escaped = escapeHtml(text.trim());

        // 1. Collapse 2 or more consecutive blank lines into single clean break
        escaped = escaped.replace(/\n{3,}/g, '\n\n');

        // 2. Bold & Italic
        escaped = escaped.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        escaped = escaped.replace(/\*(.*?)\*/g, '<em>$1</em>');

        // 3. Standalone Phone numbers
        escaped = escaped.replace(/(^|[^0-9])((?:0[2-9]\d{1}-\d{3}-\d{3,4})|(?:0[2-9]\d{7,8})|(?:0[689]\d{1}-\d{3}-\d{4}))(?=$|[^0-9])/g, function(match, prefix, phone) {
            const clean = phone.replace(/[^0-9]/g, '');
            return prefix + '<a href="tel:' + clean + '" class="msg-tel-badge badge bg-success-subtle text-success text-decoration-none px-2 py-1 flex-shrink-0" style="vertical-align: middle;"><i class="fa-solid fa-phone me-1"></i>' + phone + '</a>';
        });

        // 4. Links [text](url)
        escaped = escaped.replace(/\[(.*?)\]\((https?:\/\/[^\s\)]+|tel:[0-9]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer" class="msg-link-badge badge bg-primary-subtle text-primary text-decoration-none px-2 py-1 word-break-all text-truncate" style="max-width: 280px; vertical-align: middle;">$1 <i class="fa-solid fa-arrow-up-right-from-square ms-1" style="font-size:0.7em;"></i></a>');

        // 5. Raw URLs
        escaped = escaped.replace(/(^|[^"'>])(https?:\/\/[^\s<]+)/g, '$1<a href="$2" target="_blank" rel="noopener noreferrer" class="msg-link-badge badge bg-primary-subtle text-primary text-decoration-none px-2 py-1 word-break-all text-truncate" style="max-width: 280px; vertical-align: middle;">$2 <i class="fa-solid fa-arrow-up-right-from-square ms-1" style="font-size:0.7em;"></i></a>');

        // 6. Bullet lists
        escaped = escaped.replace(/^[ \t]*[-*•][ \t]+(.*)$/gm, '<div class="msg-list-item"><span class="msg-bullet">•</span><div>$1</div></div>');

        // 7. Clean newlines: don't double break after div list items, collapse duplicate breaks
        escaped = escaped.replace(/<\/div>\n/g, '</div>');
        escaped = escaped.replace(/\n{2,}/g, '<br>');
        escaped = escaped.replace(/\n/g, '<br>');

        return escaped;
    }

    function updateDetailsPane(s) {
        if (!s) return;
        const nameEl = document.getElementById('detailName');
        if (nameEl) nameEl.innerText = s.user_name || '-';

        const telEl = document.getElementById('detailTel');
        if (telEl) telEl.innerText = s.user_tel || '-';

        const ipEl = document.getElementById('detailIp');
        if (ipEl) ipEl.innerText = s.user_ip || '-';

        const agentEl = document.getElementById('detailAgent');
        if (agentEl) agentEl.innerText = s.assigned_agent_name || 'ยังไม่มอบหมาย';

        const notesInput = document.getElementById('agentNotesInput');
        if (notesInput) notesInput.value = s.notes || '';

        const assignSelect = document.getElementById('assignAgentSelect');
        if (assignSelect) assignSelect.value = s.assigned_agent_id || '';
    }

    function sendAgentMessage() {
        if (!currentSessionId) return;

        const input = document.getElementById('chatInputMessage');
        const text = input.value.trim();

        if (!text && !pendingAttachment) return;

        const formData = new FormData();
        formData.append('session_id', currentSessionId);
        formData.append('message', text);
        if (pendingAttachment) {
            formData.append('attachment_url', pendingAttachment.url);
            formData.append('attachment_type', pendingAttachment.type);
        }

        input.value = '';
        input.style.height = 'auto';
        removeAttachment();
        hideCannedAutocomplete();

        fetch(`${BASE_URL}/chat/send`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                if (data.data) {
                    appendSingleDeskMessage(data.data, true);
                }
                refreshCurrentSession(false);
                loadQueue();
            }
        });
    }

    function handleFileUpload(input) {
        if (!input.files || input.files.length === 0) return;

        const file = input.files[0];
        const formData = new FormData();
        formData.append('file', file);

        Swal.fire({
            title: 'กำลังอัปโหลดไฟล์...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        fetch(`${BASE_URL}/chat/upload`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            Swal.close();
            if (data.status === 'success') {
                pendingAttachment = {
                    url: data.attachment_url,
                    type: data.attachment_type,
                    name: data.file_name
                };
                document.getElementById('attachmentFileName').innerText = data.file_name;
                document.getElementById('attachmentPreviewChip').classList.remove('d-none');
            } else {
                Swal.fire('อัปโหลดไม่สำเร็จ', data.message, 'error');
            }
        })
        .catch(err => {
            Swal.close();
            Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถอัปโหลดไฟล์ได้', 'error');
        });

        input.value = '';
    }

    function removeAttachment() {
        pendingAttachment = null;
        document.getElementById('attachmentPreviewChip').classList.add('d-none');
    }

    function toggleCurrentBot() {
        if (!currentSessionId) return;
        const btn = document.getElementById('toggleBotBtn');
        const origContent = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> <span class="d-none d-sm-inline">กำลังสลับ...</span>';
        }

        fetch(`${BASE_URL}/chat/toggle-bot/${currentSessionId}`, { method: 'POST' })
            .then(res => res.json())
            .then(data => {
                if (btn) {
                    btn.disabled = false;
                }
                if (data.status === 'success') {
                    if (data.bot_message) {
                        appendSingleDeskMessage(data.bot_message, true);
                        playNotifySound();
                    }
                    if (currentSessionData) {
                        currentSessionData.is_bot_paused = data.is_bot_paused;
                        updateChatHeader(currentSessionData);
                    }
                    refreshCurrentSession(false);
                    loadQueue();

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: data.is_bot_paused == 1 ? 'info' : 'success',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 2500
                    });
                } else {
                    if (currentSessionData) updateChatHeader(currentSessionData);
                    else if (btn) btn.innerHTML = origContent;
                    Swal.fire('แจ้งเตือน', data.message || 'ไม่สามารถเปลี่ยนสถานะได้', 'warning');
                }
            })
            .catch(err => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = origContent;
                }
                if (currentSessionData) updateChatHeader(currentSessionData);
                console.error('toggleCurrentBot error:', err);
            });
    }

    function triggerCurrentAi() {
        if (!currentSessionId) return;
        const btn = document.getElementById('triggerAiBtn');
        const origContent = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span class="d-none d-md-inline ms-1">AI กำลังคิด...</span>';
        }

        Swal.fire({
            title: 'น้องกุหลาบ AI กำลังประมวลผลคำตอบ...',
            html: '<div class="text-muted small">กำลังค้นหาข้อมูลจากคลังความรู้และสร้างคำตอบให้ผู้ใช้</div>',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        fetch(`${BASE_URL}/chat/trigger-ai/${currentSessionId}`, { method: 'POST' })
            .then(res => res.json())
            .then(data => {
                Swal.close();
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = origContent;
                }
                if (data.status === 'success' && data.data) {
                    appendSingleDeskMessage(data.data, true);
                    playNotifySound();
                    refreshCurrentSession(false);
                    loadQueue();
                } else {
                    Swal.fire('ไม่สามารถตอบกลับได้', data.message || 'เกิดข้อผิดพลาดในการเรียกใช้ AI', 'error');
                }
            })
            .catch(err => {
                Swal.close();
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = origContent;
                }
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
            });
    }

    function toggleCurrentStatus() {
        if (!currentSessionId) return;
        fetch(`${BASE_URL}/chat/toggle-status/${currentSessionId}`, { method: 'POST' })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    refreshCurrentSession(false);
                    loadQueue();
                }
            });
    }

    function assignCurrentAgent(agentId) {
        if (!currentSessionId || !agentId) return;
        const formData = new FormData();
        formData.append('session_id', currentSessionId);
        formData.append('agent_id', agentId);

        fetch(`${BASE_URL}/chat/assign`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'มอบหมายงานสำเร็จ',
                    text: data.message,
                    timer: 1200,
                    showConfirmButton: false
                });
                refreshCurrentSession(false);
                loadQueue();
            }
        });
    }

    function saveAgentNotes() {
        if (!currentSessionId) return;
        const notes = document.getElementById('agentNotesInput').value;
        const formData = new FormData();
        formData.append('session_id', currentSessionId);
        formData.append('notes', notes);

        fetch(`${BASE_URL}/chat/notes`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'บันทึกสำเร็จ',
                    timer: 1000,
                    showConfirmButton: false
                });
            }
        });
    }

    function insertCannedText(text) {
        applyCannedReply(text);
    }

    function escapeHtml(string) {
        if (!string) return '';
        const entityMap = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };
        return String(string).replace(/[&<>"']/g, s => entityMap[s]);
    }

    function formatTime(dateStr) {
        if (!dateStr) return '';
        const d = new Date(dateStr.replace(/-/g, '/'));
        if (isNaN(d.getTime())) return dateStr;
        return d.toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit' });
    }
</script>
<?= $this->endSection() ?>
