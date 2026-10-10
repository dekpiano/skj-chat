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

    .chat-header-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .chat-header-actions .btn {
        height: 34px;
        font-size: 0.82rem;
        font-weight: 500;
        white-space: nowrap;
        flex-shrink: 0;
        transition: all 0.18s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .chat-header-actions .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
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

    .msg-link-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #2563eb;
        color: #ffffff !important;
        border: 1px solid #1d4ed8;
        border-radius: 10px;
        padding: 3px 11px;
        font-size: 0.84rem;
        font-weight: 600;
        text-decoration: none !important;
        transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        margin: 2px 3px;
        vertical-align: middle;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
    }
    .msg-link-badge:hover {
        background: #1d4ed8;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.35);
    }
    .msg-tel-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #059669;
        color: #ffffff !important;
        border: 1px solid #047857;
        border-radius: 10px;
        padding: 3px 11px;
        font-size: 0.84rem;
        font-weight: 700;
        text-decoration: none !important;
        transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        margin: 2px 3px;
        vertical-align: middle;
        box-shadow: 0 2px 6px rgba(5, 150, 105, 0.25);
    }
    .msg-tel-badge:hover {
        background: #047857;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(5, 150, 105, 0.35);
    }
    .bubble-content a:not(.msg-link-badge):not(.msg-tel-badge) {
        color: #2563eb;
        font-weight: 600;
        text-decoration: underline;
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

    .chat-bubble-row.admin:not(.bot) .bubble-content a.msg-link-badge {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.4);
        box-shadow: none;
    }

    .chat-bubble-row.admin:not(.bot) .bubble-content a.msg-tel-badge {
        background: #ffffff;
        color: #c2185b !important;
        border: 1px solid #ffffff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
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
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .chat-bubble-row.admin .bubble-meta {
        justify-content: flex-end;
    }

    .badge-live-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #10b981;
        display: inline-block;
        box-shadow: 0 0 6px #10b981;
        animation: skjLiveGlow 1.8s infinite ease-in-out;
    }
    @keyframes skjLiveGlow {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.35); opacity: 0.6; }
    }

    .msg-read-status {
        font-size: 0.7rem;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        margin-left: 4px;
        padding: 1px 6px;
        border-radius: 6px;
        background: rgba(0, 0, 0, 0.03);
    }
    .msg-read-status.read {
        color: var(--skj-blue, #1976d2);
        font-weight: 600;
        background: rgba(25, 118, 210, 0.08);
    }
    .msg-read-status.sent {
        color: #94a3b8;
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

    /* Drag & Drop Visual Overlay */
    .chat-area.drag-over {
        position: relative;
    }
    .chat-area.drag-over::after {
        content: '📥 ปล่อยไฟล์ที่นี่เพื่อแนบรูปภาพหรือเอกสาร';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(233, 30, 99, 0.08);
        border: 2px dashed var(--skj-pink);
        backdrop-filter: blur(2px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1.05rem;
        color: var(--skj-pink-dark);
        z-index: 999;
        pointer-events: none;
        border-radius: 12px;
    }

    .attachment-preview-thumb {
        width: 24px;
        height: 24px;
        border-radius: 4px;
        object-fit: cover;
        border: 1px solid #e2e8f0;
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
            padding: 0 0.75rem;
            height: 62px;
        }
        .chat-messages {
            padding: 0.85rem;
        }
        .chat-footer {
            padding: 0.5rem 0.75rem;
        }
    }

    /* Extra Small Smartphones (< 576px) */
    @media (max-width: 575.98px) {
        .chat-header {
            padding: 0 0.5rem;
            height: 58px;
            gap: 6px;
        }
        .chat-header .queue-avatar {
            width: 34px !important;
            height: 34px !important;
            font-size: 0.85rem !important;
        }
        #activeUserName {
            max-width: 120px !important;
            font-size: 0.88rem !important;
        }
        .chat-header-actions {
            gap: 4px;
            flex-shrink: 0;
        }
        .chat-header-actions .btn {
            height: 32px !important;
            min-width: 32px !important;
            padding: 0 8px !important;
            font-size: 0.76rem !important;
        }
        .chat-header-actions .btn.rounded-circle,
        .chat-header-actions .btn.rounded-pill {
            width: 32px !important;
            height: 32px !important;
            padding: 0 !important;
            border-radius: 50% !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .chat-bubble-row {
            max-width: 92% !important;
            gap: 8px;
        }
        .bubble-content {
            padding: 0.65rem 0.85rem !important;
            font-size: 0.88rem !important;
        }
        .chat-input-bar {
            padding: 4px 6px;
            border-radius: 14px;
            gap: 4px;
        }
        .chat-action-btn {
            width: 30px;
            height: 30px;
        }
        .chat-send-btn {
            width: 34px;
            height: 34px;
            border-radius: 10px;
        }

        /* Modal Mobile Optimization */
        #aiKnowledgeModal .modal-dialog {
            margin: 0.5rem;
        }
        #aiKnowledgeModal .modal-body {
            padding: 0.85rem !important;
        }
        #aiKnowledgeModal .modal-footer {
            flex-direction: column-reverse;
            gap: 8px;
            align-items: stretch !important;
        }
        #aiKnowledgeModal .modal-footer > div {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        #aiKnowledgeModal .modal-footer .btn {
            width: 100%;
        }
    }

    /* ==========================================================================
       CHAT DESK DARK MODE (โหมดมืดสำหรับโต๊ะแชท)
       ========================================================================== */
    [data-bs-theme="dark"] .desk-container {
        background: #0b0f19 !important;
    }
    [data-bs-theme="dark"] .desk-queue {
        background: #111827 !important;
        border-right-color: #1f2937 !important;
    }
    [data-bs-theme="dark"] .queue-header,
    [data-bs-theme="dark"] .queue-tabs {
        background: #111827 !important;
        border-bottom-color: #1f2937 !important;
    }
    [data-bs-theme="dark"] .queue-tab-btn {
        color: #9ca3af;
    }
    [data-bs-theme="dark"] .queue-tab-btn.active {
        color: var(--skj-pink);
        background: #111827;
        border-bottom-color: var(--skj-pink);
    }
    [data-bs-theme="dark"] .queue-item {
        border-bottom-color: #1f2937;
    }
    [data-bs-theme="dark"] .queue-item:hover {
        background-color: #1a2333;
    }
    [data-bs-theme="dark"] .queue-item.active {
        background-color: #271424;
        border-left-color: var(--skj-pink);
    }
    [data-bs-theme="dark"] .queue-name {
        color: #f8fafc;
    }
    [data-bs-theme="dark"] .queue-preview {
        color: #9ca3af;
    }
    [data-bs-theme="dark"] .desk-chat {
        background: #0b0f19 !important;
        border-right-color: #1f2937 !important;
    }
    [data-bs-theme="dark"] .chat-header {
        background: #111827 !important;
        border-bottom-color: #1f2937 !important;
    }
    [data-bs-theme="dark"] .chat-messages {
        background: linear-gradient(180deg, #0b0f19 0%, #080c14 100%) !important;
    }
    [data-bs-theme="dark"] .chat-bubble-row.user .bubble-content {
        background: #161f30 !important;
        color: #f1f5f9 !important;
        border-color: #1f2937 !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25) !important;
    }
    [data-bs-theme="dark"] .chat-bubble-row.admin.bot .bubble-content {
        background: #161f30 !important;
        color: #f8fafc !important;
        border: 1.5px solid #1f2937 !important;
        border-left: 4px solid var(--skj-pink) !important;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.35) !important;
    }
    [data-bs-theme="dark"] .chat-bubble-row.admin.bot .bubble-sender-name {
        color: #f8fafc !important;
    }
    [data-bs-theme="dark"] .chat-bubble-row.user .bubble-sender-name {
        color: #60a5fa !important;
    }
    [data-bs-theme="dark"] .msg-link-badge {
        background: #1d4ed8 !important;
        color: #ffffff !important;
        border: 1px solid #60a5fa !important;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.4) !important;
    }
    [data-bs-theme="dark"] .msg-link-badge:hover {
        background: #2563eb !important;
        color: #ffffff !important;
        border-color: #93c5fd !important;
    }
    [data-bs-theme="dark"] .msg-tel-badge {
        background: #047857 !important;
        color: #ffffff !important;
        border: 1px solid #34d399 !important;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4) !important;
    }
    [data-bs-theme="dark"] .msg-tel-badge:hover {
        background: #059669 !important;
        color: #ffffff !important;
        border-color: #6ee7b7 !important;
    }
    [data-bs-theme="dark"] .bubble-content a:not(.msg-link-badge):not(.msg-tel-badge) {
        color: #60a5fa !important;
        font-weight: 600;
        text-decoration: underline;
    }
    [data-bs-theme="dark"] .chat-bubble-row.system .system-pill {
        background: #161f30 !important;
        color: #9ca3af !important;
        border-color: #1f2937 !important;
    }
    [data-bs-theme="dark"] .bubble-sender-name {
        color: #cbd5e1 !important;
    }
    [data-bs-theme="dark"] .bubble-time {
        color: #9ca3af !important;
    }
    [data-bs-theme="dark"] .chat-footer {
        background: #111827 !important;
        border-top-color: #1f2937 !important;
    }
    [data-bs-theme="dark"] .chat-input-bar {
        background: #0b0f19 !important;
        border-color: #1f2937 !important;
    }
    [data-bs-theme="dark"] #chatInputMessage {
        color: #f8fafc !important;
        background: transparent !important;
    }
    [data-bs-theme="dark"] .chat-action-btn {
        color: #9ca3af !important;
    }
    [data-bs-theme="dark"] .chat-action-btn:hover {
        color: #f472b6 !important;
        background: rgba(244, 114, 182, 0.15) !important;
    }
    [data-bs-theme="dark"] .desk-details {
        background: #111827 !important;
        border-left-color: #1f2937 !important;
    }
    [data-bs-theme="dark"] .details-card {
        background: #161f30 !important;
        border-color: #1f2937 !important;
    }
    [data-bs-theme="dark"] .canned-autocomplete {
        background: #111827 !important;
        border-color: #1f2937 !important;
        color: #f1f5f9 !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.45) !important;
    }
    [data-bs-theme="dark"] .canned-autocomplete-item:hover,
    [data-bs-theme="dark"] .canned-autocomplete-item.active {
        background: #1f2937 !important;
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
                <div class="d-flex align-items-center gap-2 min-w-0" style="flex: 1; overflow: hidden; margin-right: 8px;">
                    <button class="btn btn-sm btn-light border d-lg-none flex-shrink-0" onclick="closeMobileChat()" title="กลับหน้ารายการแชท" style="width: 32px; height: 32px; padding: 0; border-radius: 50%;">
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                    <div class="queue-avatar flex-shrink-0" id="activeUserAvatar" style="width: 38px; height: 38px; font-size: 0.95rem; font-weight: 700;">U</div>
                    <div style="min-width: 0; flex: 1; overflow: hidden;">
                        <div class="d-flex align-items-center gap-1.5 text-truncate">
                            <h6 class="mb-0 fw-bold text-dark text-truncate" id="activeUserName" style="max-width: 180px; font-size: 0.92rem;">ผู้ใช้งาน</h6>
                            <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5 d-none d-sm-inline-flex align-items-center" id="activeSessionStatus" style="font-size: 0.68rem;"><span class="badge-live-dot me-1"></span> กำลังสนทนา</span>
                        </div>
                        <div class="small text-muted text-truncate d-flex align-items-center gap-1.5 mt-0.5" style="font-size: 0.76rem;">
                            <span id="activeUserTel" class="text-truncate"><i class="fa-solid fa-phone me-1"></i> -</span>
                            <span class="text-muted opacity-40 d-none d-sm-inline">•</span>
                            <span id="activeBotModeBadge" class="d-none d-sm-inline-flex"><i class="fa-solid fa-robot text-primary me-1"></i>น้องกุหลาบ AI</span>
                            <span class="text-muted opacity-40 d-none d-md-inline">•</span>
                            <span id="activeAssignedAgent" class="d-none d-md-inline text-truncate" style="max-width: 140px;"><i class="fa-solid fa-user me-1 text-secondary"></i>ยังไม่มอบหมาย</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-1.5 flex-shrink-0 chat-header-actions">
                    <!-- Trigger AI Now Button -->
                    <button class="btn btn-sm btn-light border text-primary fw-medium rounded-pill px-2 px-xl-3" id="triggerAiBtn" onclick="triggerCurrentAi()" title="สั่งให้น้องกุหลาบ AI ตอบคำถามล่าสุดทันที">
                        <i class="fa-solid fa-wand-magic-sparkles" style="color: var(--skj-pink);"></i> <span class="d-none d-xl-inline ms-1">ให้ AI ตอบ</span>
                    </button>

                    <!-- Toggle Bot Button -->
                    <button class="btn btn-sm btn-light border fw-medium rounded-pill px-2 px-xl-3" id="toggleBotBtn" onclick="toggleCurrentBot()" title="สลับเปิด/พักการทำงานของ AI">
                        <i class="fa-solid fa-robot text-info"></i> <span id="toggleBotText" class="d-none d-xl-inline ms-1">พักบอท</span>
                    </button>

                    <!-- Extract to Knowledge Base Button -->
                    <button class="btn btn-sm btn-light border text-success rounded-circle p-0" style="width: 34px; height: 34px;" id="extractKnowledgeBtn" onclick="extractCurrentSessionKnowledge()" title="สกัดและบันทึกข้อมูลสำคัญในแชทนี้เข้าคลังความรู้ AI อัตโนมัติ">
                        <i class="fa-solid fa-brain" style="font-size: 0.88rem;"></i>
                    </button>

                    <!-- Close/Reopen Chat Button -->
                    <button class="btn btn-sm btn-light border text-secondary rounded-circle p-0" style="width: 34px; height: 34px;" id="toggleStatusBtn" onclick="toggleCurrentStatus()" title="ปิดการสนทนา / สิ้นสุดเคส">
                        <i class="fa-solid fa-circle-check" style="font-size: 0.9rem;"></i>
                    </button>

                    <!-- Delete Chat Button -->
                    <button class="btn btn-sm btn-light border text-danger rounded-circle p-0" style="width: 34px; height: 34px;" id="deleteSessionBtn" onclick="deleteCurrentSession()" title="ลบประวัติการสนทนานี้และไฟล์แนบทั้งหมด">
                        <i class="fa-solid fa-trash-can" style="font-size: 0.82rem;"></i>
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
                    <img id="attachmentThumb" class="attachment-preview-thumb d-none" alt="preview">
                    <i class="fa-solid fa-paperclip text-muted" id="attachmentDefaultIcon"></i>
                    <span id="attachmentFileName" class="text-truncate" style="max-width: 220px; font-weight: 500;"></span>
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
                <button class="btn btn-xs btn-link p-0 text-decoration-none small" id="saveNotesBtn" onclick="saveAgentNotes()">บันทึก</button>
            </div>
            <textarea class="form-control form-control-sm bg-light" id="agentNotesInput" rows="3" placeholder="จดโน้ตเกี่ยวกับผู้ติดต่อ (ผู้ใช้จะไม่เห็นส่วนนี้)..."></textarea>
        </div>

        <!-- AI Knowledge Extraction & Data Tools -->
        <div class="details-card">
            <div class="details-title mb-2">คลังความรู้ AI & ข้อมูล</div>
            <button class="btn btn-sm btn-outline-success w-100 mb-2 d-flex align-items-center justify-content-center gap-2" onclick="extractCurrentSessionKnowledge()" title="ให้ AI สรุปสาระสำคัญจากแชทนี้แล้วบันทึกลงคลังความรู้">
                <i class="fa-solid fa-brain"></i>
                <span>สกัดความรู้เข้า AI</span>
            </button>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary flex-fill" onclick="toggleCurrentStatus()">
                    <i class="fa-solid fa-circle-check me-1"></i> <span id="drawerStatusText">ปิดการสนทนา</span>
                </button>
                <button class="btn btn-sm btn-outline-danger flex-fill" onclick="deleteCurrentSession()">
                    <i class="fa-solid fa-trash-can me-1"></i> <span>ลบแชทนี้</span>
                </button>
            </div>
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

<!-- AI Knowledge Extraction Preview & Edit Modal -->
<div class="modal fade" id="aiKnowledgeModal" tabindex="-1" aria-labelledby="aiKnowledgeModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, var(--skj-pink) 0%, #c2185b 100%);">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-white text-danger rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px;">
                        <i class="fa-solid fa-brain fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="aiKnowledgeModalLabel">
                            สกัดสาระสำคัญเข้าคลังความรู้ AI
                        </h5>
                        <small class="text-white-50">น้องกุหลาบ AI ประมวลผลสรุปบทสนทนาให้แล้ว เจ้าหน้าที่สามารถตรวจสอบ/แก้ไขก่อนบันทึก</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <input type="hidden" id="aiKnowledgeSessionId">
                <div class="alert alert-info py-2 px-3 small d-flex align-items-center justify-content-between mb-3 border-0" style="background: #e3f2fd; color: #0d47a1; border-radius: 12px;">
                    <div>
                        <i class="fa-solid fa-circle-info me-1"></i>
                        สนทนากับ: <strong id="aiKnowledgeUserName">-</strong>
                    </div>
                    <div class="text-muted" style="font-size: 0.78rem;">
                        <i class="fa-solid fa-wand-magic-sparkles me-1 text-primary"></i> ถอดบทเรียนจากแชทจริง
                    </div>
                </div>

                <div class="card border-0 shadow-sm p-3 mb-2 bg-white" style="border-radius: 14px;">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold small text-dark mb-1">
                                <i class="fa-solid fa-heading text-primary me-1"></i> หัวข้อความรู้ (Title) <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="aiKnowledgeTitle" placeholder="เช่น กำหนดการมอบตัว ม.1 และ ม.4 ประจำปีการศึกษา 2568" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-dark mb-1">
                                <i class="fa-solid fa-folder text-warning me-1"></i> หมวดหมู่ (AI ระบุอัตโนมัติ)
                            </label>
                            <input type="text" class="form-control" id="aiKnowledgeCategory" list="aiCategoryDatalist" placeholder="AI ระบุอัตโนมัติ หรือพิมพ์ใหม่" autocomplete="off">
                            <datalist id="aiCategoryDatalist">
                                <option value="การรับสมัครนักเรียน">
                                <option value="งานวิชาการและตารางสอบ">
                                <option value="งานทะเบียนและเอกสาร ปพ.">
                                <option value="ทุนการศึกษาและสวัสดิการ">
                                <option value="กิจกรรมและวินัยนักเรียน">
                                <option value="การเงินและค่าบำรุงการศึกษา">
                                <option value="การแนะแนวและศึกษาต่อ">
                                <option value="ข้อมูลทั่วไปและการติดต่อ">
                                <option value="การใช้งานระบบและบริการ">
                            </datalist>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold small text-dark mb-1">
                                <i class="fa-solid fa-file-lines text-success me-1"></i> สาระสำคัญ / คำถาม-คำตอบ (Content Summary) <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="aiKnowledgeSummary" rows="6" placeholder="เนื้อหาความรู้ รายละเอียด หรือถาม-ตอบที่สกัดได้จากบทสนทนา..." style="font-size: 0.9rem; line-height: 1.6;" required></textarea>
                            <div class="form-text small text-muted">ท่านสามารถแก้ไข เพิ่มเติม หรือตัดทอนเนื้อหาเพื่อให้ AI นำไปตอบคำถามได้อย่างถูกต้องและกระชับที่สุด</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold small text-dark mb-1">
                                <i class="fa-solid fa-tags text-danger me-1"></i> คำสำคัญสำหรับการค้นหา (Keywords / Tags)
                            </label>
                            <input type="text" class="form-control" id="aiKnowledgeKeywords" placeholder="เช่น มอบตัว, ม.1, เอกสาร, รายงานตัว, ค่าบำรุงการศึกษา">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex align-items-center justify-content-between p-3 bg-white border-top">
                <div>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">
                        ยกเลิก
                    </button>
                    <button type="button" class="btn btn-outline-warning btn-sm rounded-pill px-3 ms-1 d-none" id="btnSkipAndCloseSession" onclick="closeCurrentSessionDirectly()">
                        <i class="fa-solid fa-forward me-1"></i> ปิดเคสโดยไม่บันทึกความรู้
                    </button>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" id="btnSaveOnlyKnowledge" onclick="submitSaveExtractedKnowledge(false)">
                        <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกเข้าคลังความรู้
                    </button>
                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 text-white" id="btnSaveAndCloseKnowledge" onclick="submitSaveExtractedKnowledge(true)" style="background: linear-gradient(135deg, var(--skj-pink) 0%, #c2185b 100%); border: none;">
                        <i class="fa-solid fa-check-double me-1"></i> บันทึก + ปิดการสนทนา
                    </button>
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

        // Clipboard Paste Image (Ctrl+V) handler
        const handleClipboardPaste = (e) => {
            if (!currentSessionId) return;
            const clipboardData = e.clipboardData || window.clipboardData;
            if (!clipboardData || !clipboardData.items) return;

            const items = clipboardData.items;
            for (let i = 0; i < items.length; i++) {
                const item = items[i];
                if (item.type && item.type.indexOf('image') !== -1) {
                    e.preventDefault();
                    const blob = item.getAsFile();
                    if (blob) {
                        const now = new Date();
                        const timestamp = now.getFullYear() +
                            String(now.getMonth() + 1).padStart(2, '0') +
                            String(now.getDate()).padStart(2, '0') + '_' +
                            String(now.getHours()).padStart(2, '0') +
                            String(now.getMinutes()).padStart(2, '0') +
                            String(now.getSeconds()).padStart(2, '0');
                        const ext = (item.type.split('/')[1] || 'png').replace('jpeg', 'jpg');
                        const file = new File([blob], `screenshot_${timestamp}.${ext}`, { type: blob.type });
                        uploadFileObject(file);
                        break;
                    }
                }
            }
        };

        textarea.addEventListener('paste', handleClipboardPaste);
        document.addEventListener('paste', (e) => {
            if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                handleClipboardPaste(e);
            }
        });

        // Drag & Drop File Upload on Chat Area
        const deskArea = document.querySelector('.desk-chat');
        if (deskArea) {
            ['dragenter', 'dragover'].forEach(eventName => {
                deskArea.addEventListener(eventName, (e) => {
                    if (!currentSessionId) return;
                    e.preventDefault();
                    e.stopPropagation();
                    deskArea.classList.add('drag-over');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                deskArea.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    deskArea.classList.remove('drag-over');
                }, false);
            });

            deskArea.addEventListener('drop', (e) => {
                if (!currentSessionId) return;
                const dt = e.dataTransfer;
                if (dt && dt.files && dt.files.length > 0) {
                    uploadFileObject(dt.files[0]);
                }
            }, false);
        }
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

        if (typeof window.syncSidebarBadges === 'function') {
            window.syncSidebarBadges(stats);
        } else {
            const sidebarBadge = document.getElementById('sidebarUnreadBadge');
            if (sidebarBadge) {
                const unread = stats.unassigned || 0;
                sidebarBadge.innerText = unread > 99 ? '99+' : unread;
                sidebarBadge.classList.toggle('d-none', unread === 0);
            }
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
                            <div class="d-flex align-items-center gap-1">
                                ${s.status === 'closed'
                                    ? '<span class="badge bg-secondary-subtle text-secondary border" style="font-size: 0.65rem;"><i class="fa-solid fa-lock me-1"></i>ปิดแล้ว</span>'
                                    : (s.is_bot_paused == 1 
                                        ? '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="font-size: 0.65rem;"><i class="fa-solid fa-user-shield me-1"></i>จนท.ดูแล</span>' 
                                        : '<span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 0.65rem;"><i class="fa-solid fa-robot me-1"></i>น้องกุหลาบ AI</span>')
                                }
                            </div>
                            <span class="text-truncate text-muted" style="max-width: 130px;">
                                ${s.assigned_agent_name 
                                    ? `👤 ${escapeHtml(s.assigned_agent_name)}` 
                                    : '<span class="text-warning-emphasis fw-medium">ยังไม่มอบหมาย</span>'}
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

                        if (data.session && data.session.status === 'closed') {
                            const closedNotice = document.createElement('div');
                            closedNotice.className = 'alert alert-secondary py-2 px-3 mb-3 text-center small border-0 shadow-sm rounded-3';
                            closedNotice.style.background = '#f1f5f9';
                            closedNotice.style.color = '#475569';
                            closedNotice.innerHTML = '<i class="fa-solid fa-lock me-1 text-secondary"></i> <strong>การสนทนานี้สิ้นสุดแล้ว (ปิดแล้ว)</strong> — เจ้าหน้าที่สามารถคลิกปุ่ม <strong>"เปิดสนทนาใหม่"</strong> ที่แถบด้านบนเพื่อส่งข้อความต่อ';
                            container.appendChild(closedNotice);
                        }

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
            if (s.status === 'active') {
                statusBadge.innerHTML = '<span class="badge-live-dot me-1"></span> กำลังสนทนา';
                statusBadge.className = 'badge bg-success-subtle text-success border border-success-subtle small d-inline-flex align-items-center';
            } else {
                statusBadge.innerHTML = '<i class="fa-solid fa-lock me-1"></i> ปิดแล้ว';
                statusBadge.className = 'badge bg-secondary-subtle text-secondary border border-secondary-subtle small d-inline-flex align-items-center';
            }
        }

        const botModeBadge = document.getElementById('activeBotModeBadge');
        if (botModeBadge) {
            if (parseInt(s.is_bot_paused, 10) === 1) {
                botModeBadge.innerHTML = '<i class="fa-solid fa-user-shield me-1 text-warning"></i> จนท. ดูแลสด';
                botModeBadge.className = 'badge bg-warning-subtle text-warning-emphasis border border-warning-subtle flex-shrink-0 small d-none d-sm-inline-flex align-items-center';
                botModeBadge.title = 'บอทถูกพักไว้ เจ้าหน้าที่กำลังดูแลตอบแชทโดยตรง';
            } else {
                botModeBadge.innerHTML = '<i class="fa-solid fa-robot me-1 text-primary"></i> น้องกุหลาบ AI';
                botModeBadge.className = 'badge bg-info-subtle text-info border border-info-subtle flex-shrink-0 small d-none d-sm-inline-flex align-items-center';
                botModeBadge.title = 'น้องกุหลาบ AI กำลังตอบข้อความอัตโนมัติ';
            }
        }

        const assignedEl = document.getElementById('activeAssignedAgent');
        if (assignedEl) {
            assignedEl.innerHTML = s.assigned_agent_name 
                ? `<i class="fa-solid fa-user-check me-1 text-primary"></i> จนท: <strong>${escapeHtml(s.assigned_agent_name)}</strong>`
                : '<span class="text-warning-emphasis"><i class="fa-solid fa-triangle-exclamation me-1"></i> ยังไม่มอบหมาย</span>';
        }

        // Bot toggle button
        const toggleBotBtn = document.getElementById('toggleBotBtn');
        if (toggleBotBtn) {
            const isPaused = (parseInt(s.is_bot_paused, 10) === 1);
            if (isPaused) {
                toggleBotBtn.className = 'btn btn-sm btn-outline-success fw-medium rounded-pill px-2 px-xl-3';
                toggleBotBtn.innerHTML = '<i class="fa-solid fa-robot text-success"></i> <span id="toggleBotText" class="d-none d-xl-inline ms-1">เปิด AI ตอบ</span>';
                toggleBotBtn.title = 'สถานะปัจจุบัน: จนท. ดูแลอยู่ (คลิกเพื่อให้ AI น้องกุหลาบเริ่มตอบแทนอัตโนมัติ)';
            } else {
                toggleBotBtn.className = 'btn btn-sm btn-light border fw-medium rounded-pill px-2 px-xl-3';
                toggleBotBtn.innerHTML = '<i class="fa-solid fa-pause text-warning"></i> <span id="toggleBotText" class="d-none d-xl-inline ms-1">พักบอท</span>';
                toggleBotBtn.title = 'สถานะปัจจุบัน: AI กำลังตอบอัตโนมัติ (คลิกเพื่อพัก AI ให้ จนท. ดูแล)';
            }
        }

        // Close/reopen button
        const toggleStatusBtn = document.getElementById('toggleStatusBtn');
        const drawerStatusText = document.getElementById('drawerStatusText');
        if (toggleStatusBtn) {
            const isActive = (s.status === 'active');
            if (isActive) {
                toggleStatusBtn.className = 'btn btn-sm btn-light border text-secondary rounded-circle p-0';
                toggleStatusBtn.title = 'ปิดการสนทนา / สิ้นสุดเคส';
                toggleStatusBtn.innerHTML = '<i class="fa-solid fa-circle-check" style="font-size:0.9rem;"></i>';
            } else {
                toggleStatusBtn.className = 'btn btn-sm btn-light border text-primary rounded-circle p-0';
                toggleStatusBtn.title = 'เปิดการสนทนาใหม่';
                toggleStatusBtn.innerHTML = '<i class="fa-solid fa-rotate-left" style="font-size:0.9rem;"></i>';
            }
        }
        if (drawerStatusText) {
            drawerStatusText.innerText = (s.status === 'active') ? 'ปิดการสนทนา' : 'เปิดสนทนาใหม่';
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

        let readStatusHtml = '';
        if (m.sender_type === 'admin' || isBot) {
            if (parseInt(m.is_read, 10) === 1) {
                readStatusHtml = `<span class="msg-read-status read" title="ผู้ใช้งานอ่านข้อความนี้แล้ว"><i class="fa-solid fa-check-double"></i> อ่านแล้ว</span>`;
            } else {
                readStatusHtml = `<span class="msg-read-status sent" title="ส่งถึงอุปกรณ์ผู้ใช้แล้ว (รอเปิดอ่าน)"><i class="fa-solid fa-check"></i> ส่งแล้ว</span>`;
            }
        } else if (m.sender_type === 'user') {
            readStatusHtml = `<span class="msg-read-status sent text-muted"><i class="fa-solid fa-arrow-down-left"></i> ผู้ติดต่อส่ง</span>`;
        }

        const div = document.createElement('div');
        if (m.sender_type === 'system' && !m.is_bot) {
            div.className = 'chat-bubble-row system';
            div.innerHTML = `<div class="bubble-content"><i class="fa-solid fa-circle-info me-1 text-primary"></i> ${escapeHtml(m.message)}</div>`;
        } else {
            div.className = `chat-bubble-row ${rowClass} ${botClass}`;
            
            let headerTag = '';
            if (isBot) {
                headerTag = `
                    <div class="bubble-sender-name">
                        <span class="badge" style="background: linear-gradient(135deg, #e91e63 0%, #1976d2 100%); color: #fff; font-size: 0.68rem; padding: 2px 8px; border-radius: 8px;">
                            <i class="fa-solid fa-wand-magic-sparkles me-1"></i> น้องกุหลาบ AI (ระบบตอบอัตโนมัติ)
                        </span>
                    </div>`;
            } else if (m.sender_type === 'admin') {
                headerTag = `
                    <div class="bubble-sender-name">
                        <span class="fw-semibold text-dark">${escapeHtml(senderName)}</span>
                        <span class="badge bg-danger-subtle text-danger" style="font-size: 0.65rem; padding: 2px 6px; border-radius: 6px;"><i class="fa-solid fa-user-tie me-1"></i>เจ้าหน้าที่</span>
                    </div>`;
            } else {
                headerTag = `
                    <div class="bubble-sender-name">
                        <span class="fw-semibold text-dark">${escapeHtml(senderName)}</span>
                        <span class="badge bg-primary-subtle text-primary" style="font-size: 0.65rem; padding: 2px 6px; border-radius: 6px;"><i class="fa-solid fa-user me-1"></i>ผู้ติดต่อ</span>
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
                    <div class="bubble-meta">
                        <span><i class="fa-regular fa-clock me-1" style="font-size: 0.68rem;"></i>${formatTime(m.created_at)}</span>
                        ${readStatusHtml}
                    </div>
                </div>
            `;
        }

        container.appendChild(div);
        if (autoScroll) {
            container.scrollTop = container.scrollHeight;
        }
    }

    // Markdown Parser for Chat Desk (Natural reading typography, high contrast links & numbers)
    function parseDeskMarkdown(text) {
        if (!text) return '';
        let escaped = escapeHtml(text.trim());

        // 1. Bold & Italic
        escaped = escaped.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        escaped = escaped.replace(/\*(.*?)\*/g, '<em>$1</em>');

        // 2. Markdown Links [label](url or tel:...)
        escaped = escaped.replace(/\[(.*?)\]\((https?:\/\/[^\s\)]+|tel:[0-9]+)\)/g, function(match, label, href) {
            if (href.startsWith('tel:')) {
                const cleanTel = href.replace(/[^0-9]/g, '');
                return '<a href="tel:' + cleanTel + '" class="msg-tel-badge"><i class="fa-solid fa-phone me-1"></i>' + label.replace(/^[📞\s]+/, '') + '</a>';
            }
            return '<a href="' + href + '" target="_blank" rel="noopener noreferrer" class="msg-link-badge">' + label + ' <i class="fa-solid fa-arrow-up-right-from-square ms-1" style="font-size:0.7em;"></i></a>';
        });

        // 3. Raw URLs (not inside quotes/href)
        escaped = escaped.replace(/(^|[^"'>])(https?:\/\/[^\s<]+)/g, function(match, prefix, url) {
            let label = url;
            try {
                const u = new URL(url);
                label = u.hostname + (u.pathname.length > 1 ? u.pathname.substring(0, 15) + '...' : '');
            } catch(e) {}
            return prefix + '<a href="' + url + '" target="_blank" rel="noopener noreferrer" class="msg-link-badge">' + label + ' <i class="fa-solid fa-arrow-up-right-from-square ms-1" style="font-size:0.7em;"></i></a>';
        });

        // 4. Standalone Phone numbers (Only outside existing HTML tags)
        const parts = escaped.split(/(<a\b[^>]*>.*?<\/a>|<[^>]+>)/gis);
        for (let i = 0; i < parts.length; i++) {
            if (parts[i] && !parts[i].startsWith('<')) {
                parts[i] = parts[i].replace(/(^|[^0-9])((?:0[2-9]\d{1}-\d{3}-\d{3,4})|(?:0[2-9]\d{7,8})|(?:0[689]\d{1}-\d{3}-\d{4}))(?=$|[^0-9])/g, function(match, prefix, phone) {
                    const clean = phone.replace(/[^0-9]/g, '');
                    return prefix + '<a href="tel:' + clean + '" class="msg-tel-badge"><i class="fa-solid fa-phone me-1"></i>' + phone + '</a>';
                });
            }
        }
        escaped = parts.join('');

        // 5. Bullet lists
        escaped = escaped.replace(/^[ \t]*[-*•][ \t]+(.*)$/gm, '<div class="msg-list-item"><span class="msg-bullet">•</span><div>$1</div></div>');

        // 6. Clean newlines & normalize spacing
        escaped = escaped.replace(/\n{2,}/g, '\n');
        escaped = escaped.replace(/<\/div>\n+/g, '</div>');
        escaped = escaped.replace(/\n+<div class="msg-list-item"/g, '<div class="msg-list-item"');
        escaped = escaped.replace(/\n/g, '<br>');
        escaped = escaped.replace(/(<\/div>)\s*(<br\s*\/?>)+/gi, '$1');
        escaped = escaped.replace(/(<br\s*\/?>)+\s*(<div class="msg-list-item")/gi, '$2');

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

        const sendBtn = document.querySelector('.chat-send-btn');
        if (sendBtn) {
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
        }

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
            if (sendBtn) {
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<i class="fa-solid fa-paper-plane fs-6"></i>';
            }
            if (data.status === 'success') {
                if (data.data) {
                    appendSingleDeskMessage(data.data, true);
                }
                refreshCurrentSession(false);
                loadQueue();
            }
        })
        .catch(() => {
            if (sendBtn) {
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<i class="fa-solid fa-paper-plane fs-6"></i>';
            }
        });
    }

    function uploadFileObject(file) {
        if (!file) return;
        const formData = new FormData();
        formData.append('file', file);

        Swal.fire({
            title: 'กำลังอัปโหลดรูปภาพ / ไฟล์...',
            html: `<div class="small text-muted mt-1">${escapeHtml(file.name || 'image.png')}</div>`,
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

                const thumbImg = document.getElementById('attachmentThumb');
                const defaultIcon = document.getElementById('attachmentDefaultIcon');
                if (data.attachment_type === 'image') {
                    if (thumbImg) {
                        thumbImg.src = data.attachment_url;
                        thumbImg.classList.remove('d-none');
                    }
                    if (defaultIcon) defaultIcon.classList.add('d-none');
                } else {
                    if (thumbImg) thumbImg.classList.add('d-none');
                    if (defaultIcon) defaultIcon.classList.remove('d-none');
                }

                document.getElementById('attachmentFileName').innerText = data.file_name;
                document.getElementById('attachmentPreviewChip').classList.remove('d-none');
                document.getElementById('chatInputMessage')?.focus();
            } else {
                Swal.fire('อัปโหลดไม่สำเร็จ', data.message || 'ไฟล์ไม่ถูกต้อง', 'error');
            }
        })
        .catch(err => {
            Swal.close();
            Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถอัปโหลดไฟล์ได้', 'error');
        });
    }

    function handleFileUpload(input) {
        if (!input.files || input.files.length === 0) return;
        uploadFileObject(input.files[0]);
        input.value = '';
    }

    function removeAttachment() {
        pendingAttachment = null;
        const previewChip = document.getElementById('attachmentPreviewChip');
        if (previewChip) previewChip.classList.add('d-none');
        const thumb = document.getElementById('attachmentThumb');
        if (thumb) {
            thumb.src = '';
            thumb.classList.add('d-none');
        }
        const defaultIcon = document.getElementById('attachmentDefaultIcon');
        if (defaultIcon) defaultIcon.classList.remove('d-none');
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

    function extractCurrentSessionKnowledge(triggerSource = 'manual') {
        if (!currentSessionId) return;

        const userName = (currentSessionData && currentSessionData.user_name) ? currentSessionData.user_name : 'ผู้ใช้งาน';

        Swal.fire({
            title: 'น้องกุหลาบ AI กำลังประมวลผลบทสนทนา...',
            html: `
                <div class="text-center py-2">
                    <div class="spinner-border text-primary mb-2" role="status"></div>
                    <div class="small text-muted">กำลังอ่านข้อความที่คุยกัน สรุปสาระสำคัญ และจัดหมวดหมู่อัตโนมัติ...</div>
                </div>
            `,
            allowOutsideClick: false,
            showConfirmButton: false
        });

        fetch(`${BASE_URL}/chat/preview-knowledge/${currentSessionId}`, { method: 'POST' })
            .then(res => res.json())
            .then(data => {
                Swal.close();
                if (data.status === 'success') {
                    document.getElementById('aiKnowledgeSessionId').value = currentSessionId;
                    document.getElementById('aiKnowledgeUserName').innerText = userName;
                    document.getElementById('aiKnowledgeTitle').value = data.title || '';
                    const catVal = data.category || 'ข้อมูลทั่วไปและการติดต่อ';
                    document.getElementById('aiKnowledgeCategory').value = catVal;

                    // Automatically add new AI category into datalist if not already present
                    const datalist = document.getElementById('aiCategoryDatalist');
                    if (datalist && catVal) {
                        const exists = Array.from(datalist.options).some(opt => opt.value.trim().toLowerCase() === catVal.trim().toLowerCase());
                        if (!exists) {
                            const newOpt = document.createElement('option');
                            newOpt.value = catVal;
                            datalist.prepend(newOpt);
                        }
                    }

                    document.getElementById('aiKnowledgeSummary').value = data.summary || '';
                    document.getElementById('aiKnowledgeKeywords').value = data.keywords || '';

                    const skipBtn = document.getElementById('btnSkipAndCloseSession');
                    if (skipBtn) {
                        if (triggerSource === 'close') {
                            skipBtn.classList.remove('d-none');
                        } else {
                            skipBtn.classList.add('d-none');
                        }
                    }

                    const modalEl = document.getElementById('aiKnowledgeModal');
                    if (modalEl) {
                        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        modal.show();
                    }
                } else {
                    Swal.fire('ไม่สามารถสกัดความรู้ได้', data.message || 'เกิดข้อผิดพลาดในการประมวลผล', 'warning');
                }
            })
            .catch(err => {
                Swal.close();
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
            });
    }

    function submitSaveExtractedKnowledge(autoClose = false) {
        const sessionId = document.getElementById('aiKnowledgeSessionId').value || currentSessionId;
        const title     = document.getElementById('aiKnowledgeTitle').value.trim();
        const category  = document.getElementById('aiKnowledgeCategory').value;
        const summary   = document.getElementById('aiKnowledgeSummary').value.trim();
        const keywords  = document.getElementById('aiKnowledgeKeywords').value.trim();

        if (!title || !summary) {
            Swal.fire('ข้อมูลไม่ครบถ้วน', 'กรุณาระบุหัวข้อความรู้และเนื้อหาที่สกัดได้', 'warning');
            return;
        }

        const targetBtn = autoClose 
            ? document.getElementById('btnSaveAndCloseKnowledge') 
            : document.getElementById('btnSaveOnlyKnowledge');
        
        if (window.setButtonLoading) {
            window.setButtonLoading(targetBtn, true, 'กำลังบันทึก...');
        }

        const formData = new FormData();
        formData.append('session_id', sessionId);
        formData.append('title', title);
        formData.append('category', category);
        formData.append('summary', summary);
        formData.append('keywords', keywords);
        formData.append('auto_close', autoClose ? '1' : '0');

        fetch(`${BASE_URL}/chat/save-extracted-knowledge`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (window.setButtonLoading) window.setButtonLoading(targetBtn, false);

            if (data.status === 'success') {
                const modalEl = document.getElementById('aiKnowledgeModal');
                if (modalEl) {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }

                Swal.fire({
                    icon: 'success',
                    title: 'บันทึกเข้าคลังความรู้เรียบร้อย!',
                    html: `
                        <div class="text-start p-3 bg-light rounded-3 small mt-2">
                            <div class="fw-bold text-primary mb-1">📌 ${escapeHtml(data.title)}</div>
                            <div class="text-muted small">บันทึกเป็นคลังความรู้ AI รหัส #${data.knowledge_id} เรียบร้อยแล้ว น้องกุหลาบจะนำข้อมูลนี้ไปใช้ตอบคำถามผู้ใช้ทันที</div>
                        </div>
                    `,
                    confirmButtonText: 'รับทราบ',
                    confirmButtonColor: '#e91e63',
                    showCancelButton: true,
                    cancelButtonText: 'เปิดดูคลังความรู้',
                    cancelButtonColor: '#1976d2'
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.cancel) {
                        window.open(`${BASE_URL}/knowledge`, '_blank');
                    }
                });

                refreshCurrentSession(false);
                loadQueue();
            } else {
                Swal.fire('บันทึกไม่สำเร็จ', data.message || 'เกิดข้อผิดพลาดในการบันทึก', 'error');
            }
        })
        .catch(err => {
            if (window.setButtonLoading) window.setButtonLoading(targetBtn, false);
            Swal.fire('ข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
        });
    }

    function closeCurrentSessionDirectly() {
        if (!currentSessionId) return;

        const modalEl = document.getElementById('aiKnowledgeModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        }

        fetch(`${BASE_URL}/chat/toggle-status/${currentSessionId}`, { method: 'POST' })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    refreshCurrentSession(false);
                    loadQueue();
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'info',
                        title: 'ปิดการสนทนาเรียบร้อยแล้ว',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            });
    }

    function deleteCurrentSession() {
        if (!currentSessionId) return;

        Swal.fire({
            title: 'ยืนยันการลบประวัติการสนทนานี้?',
            html: '<div class="text-muted small">ข้อความสนทนาและไฟล์แนบทั้งหมดของแชทนี้จะถูกลบถาวรออกจากระบบ เพื่อความเป็นส่วนตัว (PDPA) และไม่สามารถกู้คืนได้</div>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fa-solid fa-trash me-1"></i> ยืนยันลบถาวร',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'กำลังลบข้อมูล...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch(`${BASE_URL}/chat/delete-session/${currentSessionId}`, { method: 'POST' })
                    .then(res => res.json())
                    .then(data => {
                        Swal.close();
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'ลบเรียบร้อย',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            currentSessionId = null;
                            currentSessionData = null;
                            document.getElementById('emptyDeskView')?.classList.remove('d-none');
                            document.getElementById('activeChatView')?.classList.add('d-none');
                            document.getElementById('activeChatView')?.classList.remove('d-flex');
                            loadQueue();
                        } else {
                            Swal.fire('ลบไม่สำเร็จ', data.message, 'error');
                        }
                    })
                    .catch(() => {
                        Swal.close();
                        Swal.fire('ข้อผิดพลาด', 'ไม่สามารถลบข้อมูลได้', 'error');
                    });
            }
        });
    }

    function toggleCurrentStatus() {
        if (!currentSessionId) return;

        const isCurrentlyActive = currentSessionData && currentSessionData.status === 'active';

        if (isCurrentlyActive) {
            Swal.fire({
                title: 'ต้องการปิดการสนทนานี้?',
                html: `
                    <div class="text-muted small mb-3">เมื่อปิดการสนทนา แชทจะย้ายไปแท็บ 'ปิดแล้ว' และลดจำนวนคิวรอตอบ</div>
                    <div class="p-3 bg-light rounded-3 border text-start">
                        <div class="fw-semibold text-dark mb-1"><i class="fa-solid fa-brain text-danger me-1"></i> ประมวลผลเข้าคลังความรู้ AI ก่อนปิด:</div>
                        <div class="text-muted small">ระบบจะให้ AI วิเคราะห์ข้อความคำถาม-คำตอบ แล้วเปิดหน้าต่างให้ตรวจสอบ/แก้ไขก่อนบันทึก</div>
                    </div>
                `,
                icon: 'question',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonColor: '#e91e63',
                denyButtonColor: '#64748b',
                cancelButtonColor: '#cbd5e1',
                confirmButtonText: '<i class="fa-solid fa-brain me-1"></i> ให้ AI ประมวลผลก่อนปิด',
                denyButtonText: '<i class="fa-solid fa-circle-check me-1"></i> ปิดทันที (ไม่บันทึกความรู้)',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    extractCurrentSessionKnowledge('close');
                } else if (result.isDenied) {
                    closeCurrentSessionDirectly();
                }
            });
        } else {
            // Reopen chat
            fetch(`${BASE_URL}/chat/toggle-status/${currentSessionId}`, { method: 'POST' })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        refreshCurrentSession(false);
                        loadQueue();
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: data.message,
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                });
        }
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
        const btn = document.getElementById('saveNotesBtn');
        if (window.setButtonLoading) window.setButtonLoading(btn, true, 'บันทึก...');

        const formData = new FormData();
        formData.append('session_id', currentSessionId);
        formData.append('notes', notes);

        fetch(`${BASE_URL}/chat/notes`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (window.setButtonLoading) window.setButtonLoading(btn, false);
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'บันทึกสำเร็จ',
                    timer: 1000,
                    showConfirmButton: false
                });
            }
        })
        .catch(() => {
            if (window.setButtonLoading) window.setButtonLoading(btn, false);
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
