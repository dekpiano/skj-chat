(function() {
    'use strict';

    if (window.__SKJ_CHAT_WIDGET_LOADED__) return;
    window.__SKJ_CHAT_WIDGET_LOADED__ = true;

    // Find script tag configuration
    const scriptTag = document.currentScript || (function() {
        const scripts = document.getElementsByTagName('script');
        return scripts[scripts.length - 1];
    })();

    const DEFAULT_SERVER = (window.location.origin && window.location.origin.startsWith('http')) 
        ? window.location.origin 
        : 'https://localhost:8071';
    const CHAT_SERVER   = (scriptTag && scriptTag.getAttribute('data-chat-server')) || DEFAULT_SERVER;
    const SCHOOL_NAME   = (scriptTag && scriptTag.getAttribute('data-school-name')) || 'โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์';
    const PRIMARY_COLOR = (scriptTag && scriptTag.getAttribute('data-primary-color')) || '#e91e63';
    const SECONDARY_COLOR = '#1976d2';
    const HIDE_MOBILE_BTN = scriptTag && (scriptTag.getAttribute('data-hide-mobile-btn') === 'true' || scriptTag.getAttribute('data-hide-floating-mobile') === 'true');
    const HIDE_FLOATING_BTN = scriptTag && (scriptTag.getAttribute('data-hide-floating') === 'true' || scriptTag.getAttribute('data-hide-launcher') === 'true' || scriptTag.getAttribute('data-hide-button') === 'true');

    const STORAGE_KEY   = 'skj_chat_session_token';
    const USER_NAME_KEY = 'skj_chat_user_name';
    const USER_TEL_KEY  = 'skj_chat_user_tel';

    let currentToken    = localStorage.getItem(STORAGE_KEY) || '';
    let currentUserName = localStorage.getItem(USER_NAME_KEY) || '';
    let currentUserTel  = localStorage.getItem(USER_TEL_KEY) || '';
    let pollTimer       = null;
    let isOpen          = false;
    let lastMsgId       = 0;
    let renderedMsgIds  = new Set();
    let isWaitingReply  = false;
    let audioCtx        = null;

    // Quick Question Suggestions
    const QUICK_CHIPS = [
        { label: '📌 ข้อมูลรับสมัครนักเรียน', text: 'ขอข้อมูลการรับสมัครนักเรียนใหม่ และกำหนดการรับสมัครหน่อยครับ/ค่ะ' },
        { label: '📅 ปฏิทินการศึกษา', text: 'ขอทราบปฏิทินการศึกษา และกำหนดการเปิด-ปิดภาคเรียนครับ/ค่ะ' },
        { label: '💰 แผนการเรียน & ค่าเทอม', text: 'อยากสอบถามเกี่ยวกับแผนการเรียนที่เปิดสอน และค่าบำรุงการศึกษาครับ/ค่ะ' },
        { label: '📞 ติดต่อฝ่ายธุรการ', text: 'ขอเบอร์ติดต่อฝ่ายธุรการ และช่องทางการติดต่อโรงเรียนครับ/ค่ะ' }
    ];

    // Web Audio Synthesizer for gentle notification chime
    function playChime() {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            if (!audioCtx) audioCtx = new AudioContext();
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }

            const now = audioCtx.currentTime;
            
            // Note 1: E5 (659.25 Hz)
            const osc1 = audioCtx.createOscillator();
            const gain1 = audioCtx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(659.25, now);
            gain1.gain.setValueAtTime(0.08, now);
            gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
            osc1.connect(gain1);
            gain1.connect(audioCtx.destination);
            osc1.start(now);
            osc1.stop(now + 0.35);

            // Note 2: A5 (880.00 Hz)
            const osc2 = audioCtx.createOscillator();
            const gain2 = audioCtx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(880.00, now + 0.12);
            gain2.gain.setValueAtTime(0.12, now + 0.12);
            gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.55);
            osc2.connect(gain2);
            gain2.connect(audioCtx.destination);
            osc2.start(now + 0.12);
            osc2.stop(now + 0.55);
        } catch (e) {
            // Audio context silently ignored if user hasn't interacted yet
        }
    }

    // Inject Styles
    const style = document.createElement('style');
    style.innerHTML = `
        @import url('https://fonts.googleapis.com/css2?family=K2D:wght@300;400;500;600;700&display=swap');

        .skj-widget-btn {
            position: fixed;
            bottom: 24px;
            right: 24px;
            height: 56px;
            padding: 0 20px 0 16px;
            border-radius: 28px;
            background: linear-gradient(135deg, ${PRIMARY_COLOR} 0%, #c2185b 55%, ${SECONDARY_COLOR} 100%);
            box-shadow: 0 8px 26px rgba(233, 30, 99, 0.42);
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            z-index: 999999;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: 2px solid #ffffff;
            user-select: none;
            color: #ffffff;
            font-family: 'K2D', sans-serif;
            font-weight: 600;
            font-size: 14.5px;
        }
        .skj-widget-btn:hover {
            transform: scale(1.05) translateY(-2px);
            box-shadow: 0 12px 30px rgba(233, 30, 99, 0.52);
        }
        .skj-widget-btn:active {
            transform: scale(0.96);
        }
        .skj-widget-btn.open {
            padding: 0;
            width: 56px;
            height: 56px;
            justify-content: center;
            background: #334155;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
        }
        .skj-widget-btn.open .skj-widget-btn-text {
            display: none !important;
        }
        .skj-widget-btn.open .skj-icon-chat {
            display: none !important;
        }
        .skj-widget-btn.open .skj-icon-close {
            display: block !important;
        }
        .skj-widget-btn svg {
            width: 24px;
            height: 24px;
            fill: #ffffff;
            flex-shrink: 0;
            transition: transform 0.3s ease;
        }
        .skj-widget-btn-text {
            display: inline-block;
            white-space: nowrap;
            letter-spacing: 0.02em;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }
        .skj-widget-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #ef4444;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            border-radius: 20px;
            padding: 2px 7px;
            border: 2px solid #ffffff;
            display: none;
            animation: skjPulse 1.8s infinite;
        }
        @keyframes skjPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.15); }
            100% { transform: scale(1); }
        }

        /* Floating Teaser Prompt Balloon */
        .skj-widget-teaser {
            position: fixed;
            bottom: 90px;
            right: 24px;
            background: #ffffff;
            border: 1.5px solid #fce4ec;
            border-radius: 18px;
            padding: 10px 14px 10px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12), 0 4px 14px rgba(233, 30, 99, 0.16);
            z-index: 999997;
            cursor: pointer;
            font-family: 'K2D', sans-serif;
            animation: skjTeaserFloat 3.8s ease-in-out infinite;
            transition: opacity 0.3s, transform 0.3s;
            user-select: none;
            max-width: 280px;
        }
        .skj-widget-teaser:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 34px rgba(233, 30, 99, 0.24);
        }
        .skj-widget-teaser::after {
            content: '';
            position: absolute;
            bottom: -7px;
            right: 28px;
            width: 12px;
            height: 12px;
            background: #ffffff;
            border-right: 1.5px solid #fce4ec;
            border-bottom: 1.5px solid #fce4ec;
            transform: rotate(45deg);
        }
        @keyframes skjTeaserFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        .skj-teaser-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #fff0f5;
            border: 1.5px solid #fce4ec;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(233, 30, 99, 0.12);
        }
        .skj-teaser-title {
            font-size: 13px;
            font-weight: 700;
            color: #c2185b;
            line-height: 1.25;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .skj-teaser-sub {
            font-size: 11px;
            color: #64748b;
            line-height: 1.25;
            margin-top: 2px;
        }
        .skj-teaser-close {
            position: absolute;
            top: -6px;
            left: -6px;
            width: 19px;
            height: 19px;
            border-radius: 50%;
            background: #94a3b8;
            color: #ffffff;
            border: 1.5px solid #ffffff;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            line-height: 1;
            transition: all 0.2s;
        }
        .skj-teaser-close:hover {
            background: #ef4444;
            transform: scale(1.1);
        }

        .skj-widget-window {
            position: fixed;
            bottom: 100px;
            right: 24px;
            width: 390px;
            max-width: calc(100vw - 32px);
            height: 590px;
            max-height: calc(100vh - 120px);
            background: #ffffff;
            border-radius: 22px;
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.18), 0 2px 8px rgba(0, 0, 0, 0.06);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            z-index: 999998;
            opacity: 0;
            pointer-events: none;
            transform: translateY(24px) scale(0.94);
            transition: all 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
            font-family: 'K2D', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        .skj-widget-window.open {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0) scale(1);
        }

        /* Mobile-First Fullscreen & Ergonomic Viewport for Smartphones */
        @media (max-width: 640px) {
            .skj-widget-window {
                top: 0 !important;
                bottom: 0 !important;
                left: 0 !important;
                right: 0 !important;
                width: 100vw !important;
                width: 100dvw !important;
                height: 100% !important;
                height: 100dvh !important;
                max-width: 100% !important;
                max-height: 100% !important;
                border-radius: 0 !important;
                border: none !important;
                box-shadow: none !important;
                z-index: 2147483647 !important;
                transform: translateY(100%) !important;
                transition: transform 0.32s cubic-bezier(0.32, 1, 0.23, 1), opacity 0.2s ease !important;
            }
            .skj-widget-window.open {
                transform: translateY(0) !important;
                opacity: 1 !important;
            }
            .skj-widget-btn {
                bottom: calc(76px + env(safe-area-inset-bottom, 0px)) !important;
                right: 16px !important;
                width: 48px !important;
                height: 48px !important;
                padding: 0 !important;
                border-radius: 50% !important;
                justify-content: center !important;
                z-index: 2147483646 !important;
                box-shadow: 0 6px 18px rgba(233, 30, 99, 0.45) !important;
                border: 2px solid #ffffff !important;
            }
            .skj-widget-btn .skj-widget-btn-text {
                display: none !important;
            }
            .skj-widget-btn svg {
                width: 22px !important;
                height: 22px !important;
            }
            .skj-widget-teaser {
                bottom: calc(134px + env(safe-area-inset-bottom, 0px)) !important;
                right: 16px !important;
                max-width: calc(100vw - 36px) !important;
                padding: 8px 12px !important;
                z-index: 2147483645 !important;
                border-radius: 14px !important;
            }
            .skj-widget-teaser::after {
                right: 20px !important;
            }
            .skj-teaser-avatar {
                width: 28px !important;
                height: 28px !important;
                font-size: 14px !important;
            }
            .skj-teaser-title {
                font-size: 12px !important;
            }
            .skj-teaser-sub {
                font-size: 10.5px !important;
            }
            .skj-header {
                padding-top: max(12px, env(safe-area-inset-top, 12px)) !important;
                padding-bottom: 12px !important;
                padding-left: max(14px, env(safe-area-inset-left, 14px)) !important;
                padding-right: max(14px, env(safe-area-inset-right, 14px)) !important;
                min-height: calc(58px + env(safe-area-inset-top, 0px)) !important;
                box-sizing: border-box !important;
                box-shadow: 0 4px 18px rgba(0, 0, 0, 0.16) !important;
            }
            .skj-header-avatar-wrap {
                width: 44px !important;
                height: 44px !important;
            }
            .skj-header-logo {
                width: 44px !important;
                height: 44px !important;
                padding: 2px !important;
                box-shadow: 0 3px 8px rgba(0,0,0,0.2) !important;
            }
            .skj-header-status-dot {
                width: 12px !important;
                height: 12px !important;
                border-width: 2.5px !important;
            }
            .skj-header-title {
                font-size: 15.5px !important;
                font-weight: 700 !important;
                gap: 6px !important;
            }
            .skj-header-badge {
                font-size: 11px !important;
                padding: 2px 8px !important;
            }
            .skj-header-subtitle {
                font-size: 12px !important;
                margin-top: 2px !important;
                color: rgba(255, 255, 255, 0.95) !important;
            }
            .skj-close-btn {
                width: 40px !important;
                height: 40px !important;
                min-width: 40px !important;
                min-height: 40px !important;
                border-radius: 50% !important;
                background: rgba(255, 255, 255, 0.22) !important;
                border: 1px solid rgba(255, 255, 255, 0.35) !important;
            }
            .skj-chips-bar {
                padding: 10px 14px !important;
                gap: 8px !important;
                -webkit-overflow-scrolling: touch !important;
            }
            .skj-chip {
                padding: 7px 14px !important;
                font-size: 13px !important;
                border-radius: 18px !important;
            }
            .skj-body {
                padding: 16px 14px !important;
                gap: 12px !important;
                -webkit-overflow-scrolling: touch !important;
            }
            .skj-msg-row {
                max-width: 90% !important;
            }
            .skj-msg-bubble {
                font-size: 14.5px !important;
                line-height: 1.65 !important;
                padding: 11px 15px !important;
                border-radius: 18px !important;
            }
            .skj-footer {
                padding-top: 10px !important;
                padding-bottom: max(14px, env(safe-area-inset-bottom)) !important;
                padding-left: max(12px, env(safe-area-inset-left)) !important;
                padding-right: max(12px, env(safe-area-inset-right)) !important;
                gap: 8px !important;
                background: #ffffff !important;
                box-shadow: 0 -3px 12px rgba(0, 0, 0, 0.05) !important;
            }
            .skj-input {
                font-size: 16px !important; /* Prevents iOS Safari auto-zoom */
                padding: 12px 16px !important;
                min-height: 46px !important;
                border-radius: 23px !important;
            }
            .skj-icon-btn, .skj-send-btn {
                width: 46px !important;
                height: 46px !important;
                min-width: 46px !important;
                min-height: 46px !important;
                border-radius: 50% !important;
            }
            .skj-start-screen {
                padding: 20px 16px max(24px, env(safe-area-inset-bottom)) !important;
            }
            .skj-quick-card {
                padding: 13px 15px !important;
                min-height: 50px !important;
                border-radius: 14px !important;
            }
            .skj-quick-card-text {
                font-size: 13.5px !important;
            }
            .skj-start-input {
                font-size: 16px !important;
                padding: 13px 16px !important;
                min-height: 48px !important;
                border-radius: 14px !important;
            }
            .skj-start-btn {
                font-size: 15.5px !important;
                padding: 14px !important;
                min-height: 48px !important;
                border-radius: 14px !important;
            }
            .skj-phone-pill, .skj-web-link {
                font-size: 13.5px !important;
                padding: 6px 12px !important;
            }
            ${HIDE_MOBILE_BTN ? `
            .skj-widget-btn, .skj-widget-teaser {
                display: none !important;
            }
            ` : ''}
        }

        ${HIDE_FLOATING_BTN ? `
        .skj-widget-btn, .skj-widget-teaser {
            display: none !important;
        }
        ` : ''}

        .skj-header {
            background: linear-gradient(135deg, ${PRIMARY_COLOR} 0%, #c2185b 52%, ${SECONDARY_COLOR} 100%);
            color: #ffffff;
            padding: 13px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
            user-select: none;
            position: relative;
            z-index: 10;
            border-bottom: 1px solid rgba(255, 255, 255, 0.18);
        }
        .skj-header-info {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            min-width: 0;
        }
        .skj-header-avatar-wrap {
            position: relative;
            width: 40px;
            height: 40px;
            flex-shrink: 0;
        }
        .skj-header-logo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #ffffff;
            padding: 2.5px;
            object-fit: contain;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.9);
            display: block;
        }
        .skj-header-status-dot {
            position: absolute;
            bottom: 0px;
            right: 0px;
            width: 11px;
            height: 11px;
            border-radius: 50%;
            background: #10b981;
            border: 2px solid #ffffff;
            box-shadow: 0 0 8px #10b981;
            animation: skjStatusGlow 2s infinite ease-in-out;
        }
        @keyframes skjStatusGlow {
            0%, 100% { box-shadow: 0 0 6px #10b981; transform: scale(1); }
            50% { box-shadow: 0 0 12px #34d399; transform: scale(1.12); }
        }
        .skj-header-text {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .skj-header-title {
            font-weight: 700;
            font-size: 14.5px;
            line-height: 1.25;
            display: flex;
            align-items: center;
            gap: 6px;
            color: #ffffff;
            letter-spacing: 0.01em;
        }
        .skj-header-badge {
            font-size: 10.5px;
            font-weight: 500;
            background: rgba(255, 255, 255, 0.22);
            backdrop-filter: blur(4px);
            padding: 1.5px 7px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #ffffff;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .skj-header-subtitle {
            font-size: 11.5px;
            opacity: 0.94;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: rgba(255, 255, 255, 0.92);
        }
        .skj-close-btn {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #ffffff;
            cursor: pointer;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
            touch-action: manipulation;
            flex-shrink: 0;
            margin-left: 8px;
        }
        .skj-close-btn:hover {
            background: rgba(255, 255, 255, 0.35);
            transform: scale(1.08);
        }
        .skj-close-btn:active {
            transform: scale(0.92);
            background: rgba(255, 255, 255, 0.45);
        }

        /* Chips Bar */
        .skj-chips-bar {
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 8px 12px;
            display: flex;
            gap: 6px;
            overflow-x: auto;
            white-space: nowrap;
            scrollbar-width: none;
        }
        .skj-chips-bar::-webkit-scrollbar { display: none; }
        .skj-chip {
            background: #fff1f5;
            color: #c2185b;
            border: 1px solid #fce4ec;
            border-radius: 14px;
            padding: 4px 10px;
            font-size: 11.5px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
            user-select: none;
        }
        .skj-chip:hover {
            background: ${PRIMARY_COLOR};
            color: #ffffff;
            border-color: ${PRIMARY_COLOR};
            transform: translateY(-1px);
        }

        .skj-body {
            flex: 1;
            background: #f8fafc;
            overflow-y: auto;
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            scroll-behavior: smooth;
        }

        .skj-msg-row {
            display: flex;
            gap: 8px;
            max-width: 86%;
            animation: skjFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes skjFadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .skj-msg-row.user {
            align-self: flex-end;
            flex-direction: row-reverse;
        }
        .skj-msg-row.agent, .skj-msg-row.system {
            align-self: flex-start;
        }

        .skj-msg-bubble {
            padding: 10px 14px;
            border-radius: 18px;
            font-size: 13.5px;
            line-height: 1.6;
            word-break: break-word;
            overflow-wrap: anywhere;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }
        .skj-msg-row.user .skj-msg-bubble {
            background: linear-gradient(135deg, ${PRIMARY_COLOR} 0%, #d81b60 100%);
            color: #ffffff;
            border-top-right-radius: 4px;
        }
        .skj-msg-row.agent .skj-msg-bubble, .skj-msg-row.system .skj-msg-bubble {
            background: #ffffff;
            color: #1e293b;
            border: 1px solid #e2e8f0;
            border-top-left-radius: 4px;
        }

        /* Markdown styling inside bubble */
        .skj-msg-bubble strong { font-weight: 700; color: inherit; }
        .skj-msg-bubble a { color: ${SECONDARY_COLOR}; text-decoration: underline; font-weight: 500; }
        .skj-msg-row.user .skj-msg-bubble a { color: #ffffff; text-decoration: underline; }
        .skj-msg-bubble ul { margin: 6px 0; padding-left: 20px; }
        .skj-msg-bubble li { margin-bottom: 3px; }

        /* Mobile-First Tap Pills & Links */
        .skj-phone-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #059669;
            color: #ffffff !important;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 700;
            text-decoration: none !important;
            margin: 3px 0;
            border: 1px solid #047857;
            font-size: 13px;
            box-shadow: 0 2px 6px rgba(5, 150, 105, 0.25);
            transition: all 0.2s;
            touch-action: manipulation;
        }
        .skj-phone-pill:hover, .skj-phone-pill:active {
            background: #047857;
            color: #ffffff !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(5, 150, 105, 0.35);
        }
        .skj-web-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #2563eb;
            color: #ffffff !important;
            padding: 4px 12px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none !important;
            margin: 3px 0;
            border: 1px solid #1d4ed8;
            font-size: 13px;
            word-break: break-word;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
            transition: all 0.2s;
            touch-action: manipulation;
        }
        .skj-web-link:hover, .skj-web-link:active {
            background: #1d4ed8;
            color: #ffffff !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.35);
        }
        .skj-msg-row.user .skj-web-link, .skj-msg-row.user .skj-phone-pill {
            background: rgba(255, 255, 255, 0.28);
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.45);
        }
        .skj-list-item {
            margin: 2px 0;
            padding-left: 2px;
            display: flex;
            align-items: flex-start;
            gap: 6px;
            line-height: 1.45;
        }
        .skj-list-bullet {
            color: ${PRIMARY_COLOR};
            font-size: 12px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .skj-msg-sender {
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 3px;
            opacity: 0.85;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .skj-msg-time {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .skj-msg-row.user .skj-msg-time {
            justify-content: flex-end;
            color: rgba(255, 255, 255, 0.85);
        }
        .skj-read-receipt {
            display: inline-flex;
            align-items: center;
            gap: 2px;
            font-size: 9.5px;
            padding: 1px 5px;
            border-radius: 8px;
        }
        .skj-read-receipt.read {
            color: #ffffff;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.22);
        }
        .skj-read-receipt.sent {
            color: rgba(255, 255, 255, 0.75);
        }

        /* Typing Dots Animation */
        .skj-typing-bubble {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 10px 14px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            border-top-left-radius: 4px;
        }
        .skj-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: ${PRIMARY_COLOR};
            display: inline-block;
            animation: skjBounce 1.4s infinite ease-in-out both;
        }
        .skj-dot:nth-child(1) { animation-delay: -0.32s; }
        .skj-dot:nth-child(2) { animation-delay: -0.16s; }
        @keyframes skjBounce {
            0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
            40% { transform: scale(1.1); opacity: 1; }
        }
        @keyframes skjSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .skj-spin {
            animation: skjSpin 0.75s linear infinite;
        }

        .skj-footer {
            padding: 10px 12px;
            background: #ffffff;
            border-top: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .skj-input {
            flex: 1;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            border-radius: 20px;
            padding: 10px 14px;
            font-size: 14px;
            outline: none;
            font-family: inherit;
            transition: all 0.2s;
        }
        .skj-input:focus {
            border-color: ${PRIMARY_COLOR};
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(233, 30, 99, 0.12);
        }

        .skj-icon-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f1f5f9;
            border: none;
            color: #64748b;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
            touch-action: manipulation;
        }
        .skj-icon-btn:hover {
            background: #e2e8f0;
            color: #1e293b;
        }
        .skj-send-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, ${PRIMARY_COLOR} 0%, #d81b60 100%);
            border: none;
            color: #ffffff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(233, 30, 99, 0.3);
            transition: all 0.2s;
            flex-shrink: 0;
            touch-action: manipulation;
        }
        .skj-send-btn:hover {
            transform: scale(1.06);
            box-shadow: 0 6px 16px rgba(233, 30, 99, 0.4);
        }
        .skj-send-btn:active {
            transform: scale(0.94);
        }

        .skj-start-screen {
            flex: 1;
            padding: 20px 18px;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            text-align: center;
            background: #ffffff;
        }
        .skj-start-logo {
            width: 58px;
            height: 58px;
            margin: 0 auto 10px;
            border-radius: 16px;
            padding: 5px;
            background: linear-gradient(135deg, #fff1f5 0%, #e3f2fd 100%);
            box-shadow: 0 6px 16px rgba(233, 30, 99, 0.14);
        }
        .skj-start-screen h5 {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 4px;
        }
        .skj-start-screen p {
            font-size: 12px;
            color: #64748b;
            margin: 0 0 14px;
            line-height: 1.5;
        }
        .skj-quick-menu-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #94a3b8;
            margin: 12px 0 8px;
            text-align: left;
        }
        .skj-quick-cards-grid {
            display: flex;
            flex-direction: column;
            gap: 7px;
            margin-bottom: 14px;
            text-align: left;
        }
        .skj-quick-card {
            background: #fff5f8;
            border: 1px solid #fce4ec;
            border-radius: 12px;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.2s;
            user-select: none;
            touch-action: manipulation;
        }
        .skj-quick-card:hover, .skj-quick-card:active {
            background: #fce4ec;
            border-color: #f8bbd0;
            transform: scale(0.99);
        }
        .skj-quick-card-text {
            font-size: 12.5px;
            font-weight: 500;
            color: #880e4f;
            line-height: 1.35;
        }
        .skj-quick-card-arrow {
            font-size: 13px;
            color: ${PRIMARY_COLOR};
            flex-shrink: 0;
            margin-left: 8px;
        }
        .skj-start-input {
            width: 100%;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            border-radius: 12px;
            padding: 11px 14px;
            font-size: 13.5px;
            margin-bottom: 10px;
            box-sizing: border-box;
            font-family: inherit;
            outline: none;
            transition: all 0.2s;
        }
        .skj-start-input:focus {
            border-color: ${PRIMARY_COLOR};
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(233, 30, 99, 0.12);
        }
        .skj-start-btn {
            background: linear-gradient(135deg, ${PRIMARY_COLOR} 0%, ${SECONDARY_COLOR} 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            font-size: 14.5px;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(233, 30, 99, 0.28);
            transition: all 0.2s;
            touch-action: manipulation;
        }
        .skj-start-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 22px rgba(233, 30, 99, 0.38);
        }
    `;
    document.head.appendChild(style);

    // Create DOM elements
    const widgetTeaser = document.createElement('div');
    widgetTeaser.className = 'skj-widget-teaser';
    widgetTeaser.id = 'skjWidgetTeaser';
    widgetTeaser.innerHTML = `
        <button class="skj-teaser-close" id="skjTeaserClose" title="ปิดข้อความแนะนำ">&times;</button>
        <div class="skj-teaser-avatar">🌸</div>
        <div class="skj-teaser-body">
            <div class="skj-teaser-title">สอบถามข้อมูลโรงเรียน</div>
            <div class="skj-teaser-sub">ปรึกษาน้องกุหลาบ AI & เจ้าหน้าที่</div>
        </div>
    `;

    const widgetBtn = document.createElement('div');
    widgetBtn.className = 'skj-widget-btn';
    widgetBtn.title = 'สอบถามข้อมูลโรงเรียน / SKJ Live Chat';
    widgetBtn.innerHTML = `
        <svg class="skj-icon-chat" viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 12h-2v-2h2v2zm1.07-4.75l-.9.92C12.45 10.9 12 11.5 12 13h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H7c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.04-.42 1.99-1.07 2.75z"/></svg>
        <svg class="skj-icon-close" viewBox="0 0 24 24" style="display:none;"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
        <span class="skj-widget-btn-text">สอบถามข้อมูล</span>
        <span class="skj-widget-badge" id="skjUnreadBadge">0</span>
    `;

    const widgetWindow = document.createElement('div');
    widgetWindow.className = 'skj-widget-window';
    widgetWindow.innerHTML = `
        <div class="skj-header">
            <div class="skj-header-info">
                <div class="skj-header-avatar-wrap">
                    <img src="${CHAT_SERVER}/public/assets/images/logo-skj.png" class="skj-header-logo" alt="SKJ Logo" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=SKJ&background=ffffff&color=e91e63';">
                    <span class="skj-header-status-dot" title="ระบบออนไลน์พร้อมให้บริการ"></span>
                </div>
                <div class="skj-header-text">
                    <div class="skj-header-title">
                        <span>SKJ Live Chat</span>
                        <span class="skj-header-badge">AI & เจ้าหน้าที่</span>
                    </div>
                    <div class="skj-header-subtitle">
                        <span>🌸 พร้อมให้บริการ</span>
                        <span>•</span>
                        <span style="opacity:0.9;">สวนกุหลาบฯ (จิรประวัติ)</span>
                    </div>
                </div>
            </div>
            <button type="button" class="skj-close-btn" id="skjCloseBtn" title="ปิดหน้าต่างสนทนา" aria-label="Close Chat">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                </svg>
            </button>
        </div>

        <!-- Chips Row -->
        <div class="skj-chips-bar" id="skjChipsBar">
            ${QUICK_CHIPS.map((c, i) => `<span class="skj-chip" data-index="${i}">${c.label}</span>`).join('')}
        </div>

        <!-- Start Screen -->
        <div id="skjStartScreen" class="skj-start-screen" style="display: ${currentToken ? 'none' : 'flex'};">
            <img src="${CHAT_SERVER}/public/assets/images/logo-skj.png" class="skj-start-logo" alt="Logo" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=SKJ&background=fce4ec&color=c2185b';">
            <h5>ยินดีต้อนรับสู่ SKJ Live Chat</h5>
            <p>โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์<br>น้องกุหลาบ AI และคุณครูเจ้าหน้าที่ยินดีให้บริการค่ะ 🌸</p>

            <div class="skj-quick-menu-title">⚡ แตะเลือกคำถามด่วน (ตอบทันที)</div>
            <div class="skj-quick-cards-grid">
                ${QUICK_CHIPS.map((c, i) => `
                    <div class="skj-quick-card" data-index="${i}">
                        <div class="skj-quick-card-text">${c.label}</div>
                        <span class="skj-quick-card-arrow">➔</span>
                    </div>
                `).join('')}
            </div>

            <div class="skj-quick-menu-title" style="text-align: center;">หรือระบุข้อมูลเพื่อเริ่มแชทสด</div>
            <input type="text" id="skjRegName" class="skj-start-input" placeholder="ชื่อของคุณ (เช่น สมชาย หรือ ผู้ปกครอง)" value="${currentUserName}">
            <input type="tel" id="skjRegTel" class="skj-start-input" placeholder="เบอร์โทรศัพท์ติดต่อ (ไม่ระบุก็ได้)" value="${currentUserTel}">
            <button class="skj-start-btn" id="skjStartBtn">เริ่มการสนทนา</button>
        </div>

        <!-- Chat Screen -->
        <div id="skjChatScreen" style="display: ${currentToken ? 'flex' : 'none'}; flex-direction: column; flex: 1; overflow: hidden;">
            <div class="skj-body" id="skjMessageContainer"></div>
            
            <!-- Hidden File Input for Attachment -->
            <input type="file" id="skjWidgetFileInput" style="display:none;" accept="image/*,.pdf,.doc,.docx,.zip">

            <div class="skj-footer">
                <button type="button" class="skj-icon-btn" id="skjAttachBtn" title="แนบรูปภาพหรือเอกสาร">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M16.5 6v11.5c0 2.21-1.79 4-4 4s-4-1.79-4-4V5c0-1.38 1.12-2.5 2.5-2.5s2.5 1.12 2.5 2.5v10.5c0 .55-.45 1-1 1s-1-.45-1-1V6H10v9.5c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5V5c0-2.21-1.79-4-4-4S7 2.79 7 5v12.5c0 3.04 2.46 5.5 5.5 5.5s5.5-2.46 5.5-5.5V6h-1.5z"/></svg>
                </button>
                <input type="text" class="skj-input" id="skjMessageInput" placeholder="พิมพ์คำถามหรือข้อความที่นี่...">
                <button class="skj-send-btn" id="skjSendBtn" title="ส่งข้อความ">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(widgetTeaser);
    document.body.appendChild(widgetBtn);
    document.body.appendChild(widgetWindow);

    // Event Handlers
    widgetBtn.addEventListener('click', toggleWidget);
    widgetTeaser.addEventListener('click', (e) => {
        if (e.target.closest('#skjTeaserClose')) {
            e.stopPropagation();
            widgetTeaser.style.display = 'none';
            return;
        }
        if (!isOpen) toggleWidget();
    });
    document.getElementById('skjCloseBtn').addEventListener('click', toggleWidget);

    function toggleWidget() {
        isOpen = !isOpen;
        widgetWindow.classList.toggle('open', isOpen);
        widgetBtn.classList.toggle('open', isOpen);

        // Mobile background scroll locking
        if (window.innerWidth <= 640) {
            if (isOpen) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }

        if (widgetTeaser) {
            widgetTeaser.style.display = isOpen ? 'none' : 'flex';
        }
        if (isOpen) {
            const badge = document.getElementById('skjUnreadBadge');
            if (badge) {
                badge.style.display = 'none';
                badge.innerText = '0';
            }
            if (currentToken) {
                pollChat();
                setTimeout(() => {
                    const input = document.getElementById('skjMessageInput');
                    if (input && window.innerWidth > 640) input.focus();
                }, 100);
            }
        }
    }

    // Expose Global Public API for Navbar / Custom Buttons
    window.SKJChat = {
        open: function() {
            if (!isOpen) toggleWidget();
            // Automatically dismiss Bootstrap Offcanvas if open
            try {
                const offcanvas = document.querySelector('.offcanvas.show');
                if (offcanvas && window.bootstrap && bootstrap.Offcanvas) {
                    const inst = bootstrap.Offcanvas.getInstance(offcanvas);
                    if (inst) inst.hide();
                }
            } catch(e) {}
        },
        close: function() {
            if (isOpen) toggleWidget();
        },
        toggle: function() {
            toggleWidget();
        },
        isOpen: function() {
            return isOpen;
        }
    };
    window.openSKJChat = window.SKJChat.open;

    // Delegate Click for any custom trigger in Navbar, Links, Buttons or Page
    document.addEventListener('click', function(e) {
        const trigger = e.target.closest('[data-skj-chat="open"], [data-skj-chat-open], [data-skj-chat-toggle], [data-skj-chat], .skj-chat-trigger, .btn-skj-chat, a[href="#skj-chat"], a[href="#chat"], a[href="#skjchat"]');
        if (trigger) {
            e.preventDefault();
            if (trigger.getAttribute('data-skj-chat-toggle') !== null || trigger.getAttribute('data-skj-chat') === 'toggle') {
                window.SKJChat.toggle();
            } else {
                window.SKJChat.open();
            }
        }
    });

    const skjMsgInput = document.getElementById('skjMessageInput');
    if (skjMsgInput) {
        skjMsgInput.addEventListener('focus', () => {
            setTimeout(() => {
                const container = document.getElementById('skjMessageContainer');
                if (container) container.scrollTop = container.scrollHeight;
            }, 300);
        });
    }

    // Quick Chips & Cards Click Handler
    function handleQuestionClick(item) {
        if (!item) return;
        const name = document.getElementById('skjRegName')?.value.trim() || 'ผู้เยี่ยมชม';
        const tel  = document.getElementById('skjRegTel')?.value.trim() || '';

        if (!currentToken) {
            startSession(name, tel, () => {
                sendMessageDirect(item.text);
            });
        } else {
            sendMessageDirect(item.text);
        }
    }

    document.querySelectorAll('.skj-chip').forEach(chip => {
        chip.addEventListener('click', () => {
            const idx = parseInt(chip.getAttribute('data-index'), 10);
            handleQuestionClick(QUICK_CHIPS[idx]);
        });
    });

    document.querySelectorAll('.skj-quick-card').forEach(card => {
        card.addEventListener('click', () => {
            const idx = parseInt(card.getAttribute('data-index'), 10);
            handleQuestionClick(QUICK_CHIPS[idx]);
        });
    });

    // Start Session Button
    document.getElementById('skjStartBtn').addEventListener('click', () => {
        const name = document.getElementById('skjRegName').value.trim() || 'ผู้เยี่ยมชม';
        const tel  = document.getElementById('skjRegTel').value.trim();
        startSession(name, tel);
    });

    function startSession(name, tel, callback) {
        currentUserName = name;
        currentUserTel = tel;
        localStorage.setItem(USER_NAME_KEY, name);
        localStorage.setItem(USER_TEL_KEY, tel);

        const formData = new FormData();
        formData.append('user_name', name);
        formData.append('user_tel', tel);

        fetch(`${CHAT_SERVER}/api/widget/init`, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    currentToken = data.session.session_token;
                    localStorage.setItem(STORAGE_KEY, currentToken);

                    document.getElementById('skjStartScreen').style.display = 'none';
                    document.getElementById('skjChatScreen').style.display = 'flex';

                    if (data.messages && data.messages.length > 0) {
                        data.messages.forEach(m => appendBubble(m, false));
                        const container = document.getElementById('skjMessageContainer');
                        container.scrollTop = container.scrollHeight;
                    }

                    startPolling();
                    if (typeof callback === 'function') callback();
                }
            })
            .catch(err => console.error('SKJ Init error:', err));
    }

    // Attach File Helper
    function uploadWidgetFile(file) {
        if (!file) return;
        if (!currentToken) {
            alert('กรุณารอระบบเริ่มต้นการสนทนาสักครู่...');
            return;
        }

        const formData = new FormData();
        formData.append('file', file);

        showTypingIndicator('กำลังอัปโหลดไฟล์/รูปภาพ...');

        fetch(`${CHAT_SERVER}/api/widget/upload`, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                hideTypingIndicator();
                if (data.status === 'success') {
                    sendMessageDirect('', data.attachment_url, data.attachment_type);
                } else {
                    alert(data.message || 'ไม่สามารถอัปโหลดไฟล์ได้');
                }
            })
            .catch(() => {
                hideTypingIndicator();
                alert('เกิดข้อผิดพลาดในการอัปโหลดไฟล์');
            });
    }

    // Attach File Button
    const attachBtn = document.getElementById('skjAttachBtn');
    const fileInput = document.getElementById('skjWidgetFileInput');
    if (attachBtn && fileInput) {
        attachBtn.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', () => {
            if (!fileInput.files || fileInput.files.length === 0) return;
            uploadWidgetFile(fileInput.files[0]);
            fileInput.value = '';
        });
    }

    // Clipboard Paste Image (Ctrl+V) on Widget Input
    const widgetMsgInput = document.getElementById('skjMessageInput');
    const widgetChatWindow = document.getElementById('skjChatWindow');

    const handleWidgetPaste = (e) => {
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
                    uploadWidgetFile(file);
                    break;
                }
            }
        }
    };

    if (widgetMsgInput) {
        widgetMsgInput.addEventListener('paste', handleWidgetPaste);
    }

    // Drag & Drop File Upload on Widget
    if (widgetChatWindow) {
        ['dragenter', 'dragover'].forEach(eventName => {
            widgetChatWindow.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            widgetChatWindow.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });

        widgetChatWindow.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            if (dt && dt.files && dt.files.length > 0) {
                uploadWidgetFile(dt.files[0]);
            }
        }, false);
    }

    // Send Message
    let isSending = false;

    function sendMessage() {
        const input = document.getElementById('skjMessageInput');
        const text = input.value.trim();
        if (!text) return;
        input.value = '';
        sendMessageDirect(text);
    }

    function sendMessageDirect(text, attachmentUrl = null, attachmentType = null) {
        if (!currentToken || isSending) return;
        isSending = true;

        const tempClientId = 'temp_' + Date.now();

        // Render optimistic user bubble
        appendBubble({
            client_id: tempClientId,
            sender_type: 'user',
            sender_name: currentUserName || 'คุณ',
            message: text,
            attachment_url: attachmentUrl,
            attachment_type: attachmentType,
            created_at: new Date()
        }, true);

        showTypingIndicator('น้องกุหลาบกำลังพิมพ์...');

        const formData = new FormData();
        formData.append('token', currentToken);
        formData.append('message', text);
        if (attachmentUrl) {
            formData.append('attachment_url', attachmentUrl);
            formData.append('attachment_type', attachmentType);
        }

        const sendBtn = document.getElementById('skjSendBtn');
        if (sendBtn) {
            sendBtn.disabled = true;
            sendBtn.style.opacity = '0.75';
            sendBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="skj-spin"><circle cx="12" cy="12" r="9" stroke-dasharray="32" stroke-dashoffset="12"/></svg>';
        }

        fetch(`${CHAT_SERVER}/api/widget/send`, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                isSending = false;
                hideTypingIndicator();
                if (sendBtn) {
                    sendBtn.disabled = false;
                    sendBtn.style.opacity = '1';
                    sendBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>';
                }
                if (data.status === 'success') {
                    // Update renderedMsgIds & lastMsgId with the saved user message
                    if (data.message && data.message.message_id) {
                        const userMid = parseInt(data.message.message_id, 10);
                        renderedMsgIds.add(userMid);
                        if (userMid > lastMsgId) lastMsgId = userMid;
                        const tempEl = document.querySelector(`[data-client-id="${tempClientId}"]`);
                        if (tempEl) tempEl.setAttribute('data-message-id', userMid);
                    }

                    // Render bot reply if present and register its ID
                    if (data.bot_msg && data.bot_msg.message) {
                        appendBubble(data.bot_msg, true);
                        playChime();
                    } else if (data.bot_reply) {
                        appendBubble({
                            sender_type: 'system',
                            sender_name: 'น้องกุหลาบ (SKJ AI Assistant)',
                            message: data.bot_reply,
                            created_at: new Date()
                        }, true);
                        playChime();
                    }

                    // Poll to catch any other updates from staff
                    pollChat();
                }
            })
            .catch(() => {
                isSending = false;
                hideTypingIndicator();
                if (sendBtn) {
                    sendBtn.disabled = false;
                    sendBtn.style.opacity = '1';
                    sendBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>';
                }
            });
    }

    document.getElementById('skjSendBtn').addEventListener('click', sendMessage);
    document.getElementById('skjMessageInput').addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            sendMessage();
        }
    });

    // Incremental Polling
    function pollChat() {
        if (!currentToken || isSending) return;

        fetch(`${CHAT_SERVER}/api/widget/poll?token=${currentToken}&after_id=${lastMsgId}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' && data.messages && data.messages.length > 0) {
                    let hasNewAdminMsg = false;
                    data.messages.forEach(m => {
                        const mid = parseInt(m.message_id, 10);
                        if (mid && !renderedMsgIds.has(mid)) {
                            appendBubble(m, true);
                            if (m.sender_type !== 'user') {
                                hasNewAdminMsg = true;
                            }
                        }
                    });

                    if (hasNewAdminMsg) {
                        hideTypingIndicator();
                        playChime();
                        if (!isOpen) {
                            const badge = document.getElementById('skjUnreadBadge');
                            if (badge) {
                                const count = (parseInt(badge.innerText, 10) || 0) + 1;
                                badge.innerText = count;
                                badge.style.display = 'block';
                            }
                        }
                    }
                }
            })
            .catch(() => {});
    }

    function appendBubble(m, autoScroll = true) {
        const mid = m.message_id ? parseInt(m.message_id, 10) : null;
        if (mid && renderedMsgIds.has(mid)) return null;

        // If this is a user message from server (with mid), check if there is an unconfirmed optimistic user bubble to match
        if (m.sender_type === 'user' && mid) {
            const pendingBubble = document.querySelector('.skj-msg-row.user[data-client-id]:not([data-message-id])');
            if (pendingBubble) {
                pendingBubble.setAttribute('data-message-id', mid);
                renderedMsgIds.add(mid);
                if (mid > lastMsgId) lastMsgId = mid;
                const statusEl = pendingBubble.querySelector('.skj-read-receipt');
                if (statusEl && m.is_read == 1) {
                    statusEl.className = 'skj-read-receipt read';
                    statusEl.innerHTML = '<svg style="width:11px;height:11px;display:inline-block;vertical-align:-1px;margin-right:2px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline><polyline points="20 12 12 20"></polyline></svg> อ่านแล้ว';
                }
                return pendingBubble;
            }
        }

        if (mid) {
            renderedMsgIds.add(mid);
            if (mid > lastMsgId) lastMsgId = mid;
        }

        const container = document.getElementById('skjMessageContainer');
        if (!container) return null;

        const row = document.createElement('div');
        const isUser = (m.sender_type === 'user');
        const isBot = (m.is_bot == 1 || (m.sender_type === 'system' && m.sender_name && m.sender_name.includes('AI')));
        
        row.className = `skj-msg-row ${isUser ? 'user' : 'agent'}`;
        if (m.client_id) {
            row.setAttribute('data-client-id', m.client_id);
        }
        if (mid) {
            row.setAttribute('data-message-id', mid);
        }

        let senderTitle = '';
        if (isUser) {
            senderTitle = '👤 คุณ';
        } else if (isBot) {
            senderTitle = '🌸 น้องกุหลาบ (SKJ AI)';
        } else {
            senderTitle = `👤 ${escapeHtml(m.sender_name || 'เจ้าหน้าที่โรงเรียน')}`;
        }

        let attachmentHtml = '';
        if (m.attachment_url) {
            if (m.attachment_type === 'image' || m.attachment_url.match(/\.(jpg|jpeg|png|gif|webp)$/i)) {
                attachmentHtml = `<div style="margin-top:6px;"><a href="${m.attachment_url}" target="_blank"><img src="${m.attachment_url}" style="max-width:100%; max-height:200px; object-fit:cover; border-radius:12px; display:block; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" alt="รูปแนบ" onerror="this.onerror=null;this.parentNode.innerHTML='<span style=\\'color:#ef4444;font-size:12px;\\'>[รูปภาพไม่สามารถแสดงได้]</span>';"></a></div>`;
            } else {
                attachmentHtml = `<div style="margin-top:6px;"><a href="${m.attachment_url}" target="_blank" style="color:inherit; text-decoration:underline; font-size:12px;">📎 ดาวน์โหลดไฟล์แนบ</a></div>`;
            }
        }

        const parsedContent = parseMarkdown(m.message || '');

        let readStatusHtml = '';
        if (isUser) {
            if (parseInt(m.is_read, 10) === 1) {
                readStatusHtml = `<span class="skj-read-receipt read" title="เจ้าหน้าที่/AI อ่านแล้ว"><svg style="width:11px;height:11px;display:inline-block;vertical-align:-1px;margin-right:2px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline><polyline points="20 12 12 20"></polyline></svg> อ่านแล้ว</span>`;
            } else {
                readStatusHtml = `<span class="skj-read-receipt sent" title="ส่งถึงระบบแล้ว"><svg style="width:10px;height:10px;display:inline-block;vertical-align:-1px;margin-right:2px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg> ส่งแล้ว</span>`;
            }
        }

        row.innerHTML = `
            <div class="skj-msg-bubble">
                <div class="skj-msg-sender">${senderTitle}</div>
                <div>${parsedContent}</div>
                ${attachmentHtml}
                <div class="skj-msg-time">
                    <span>${formatTime(m.created_at)}</span>
                    ${readStatusHtml}
                </div>
            </div>
        `;

        // If typing indicator is visible, insert before it
        const indicator = document.getElementById('skjTypingRow');
        if (indicator && indicator.parentNode === container) {
            container.insertBefore(row, indicator);
        } else {
            container.appendChild(row);
        }

        if (autoScroll) {
            container.scrollTop = container.scrollHeight;
        }

        return row;
    }

    function showTypingIndicator(label = 'กำลังพิมพ์...') {
        hideTypingIndicator();
        const container = document.getElementById('skjMessageContainer');
        const row = document.createElement('div');
        row.className = 'skj-msg-row agent';
        row.id = 'skjTypingRow';
        row.innerHTML = `
            <div class="skj-typing-bubble">
                <span class="skj-dot"></span>
                <span class="skj-dot"></span>
                <span class="skj-dot"></span>
                <span style="font-size:11px; color:#64748b; margin-left:6px;">${escapeHtml(label)}</span>
            </div>
        `;
        container.appendChild(row);
        container.scrollTop = container.scrollHeight;
    }

    function hideTypingIndicator() {
        const row = document.getElementById('skjTypingRow');
        if (row && row.parentNode) {
            row.parentNode.removeChild(row);
        }
    }

    // Mobile-First Markdown Parser
    function parseMarkdown(text) {
        if (!text) return '';
        let escaped = escapeHtml(text);

        // 1. Bold & Italic
        escaped = escaped.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        escaped = escaped.replace(/\*(.*?)\*/g, '<em>$1</em>');

        // 2. Markdown Links [label](url or tel:...)
        escaped = escaped.replace(/\[(.*?)\]\((https?:\/\/[^\s\)]+|tel:[0-9]+)\)/g, function(match, label, href) {
            if (href.startsWith('tel:')) {
                const cleanTel = href.replace(/[^0-9]/g, '');
                return '<a href="tel:' + cleanTel + '" class="skj-phone-pill">📞 ' + label + '</a>';
            }
            return '<a href="' + href + '" target="_blank" rel="noopener noreferrer" class="skj-web-link">' + label + ' <svg style="width:11px;height:11px;fill:currentColor;margin-left:3px;display:inline-block;" viewBox="0 0 24 24"><path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"/></svg></a>';
        });

        // 3. Raw URLs (not inside href)
        escaped = escaped.replace(/(^|[^"'>])(https?:\/\/[^\s<]+)/g, function(match, prefix, url) {
            let label = url;
            try {
                const u = new URL(url);
                label = u.hostname + (u.pathname.length > 1 ? u.pathname.substring(0, 15) + '...' : '');
            } catch(e) {}
            return prefix + '<a href="' + url + '" target="_blank" rel="noopener noreferrer" class="skj-web-link">🌐 ' + label + ' <svg style="width:11px;height:11px;fill:currentColor;margin-left:3px;display:inline-block;" viewBox="0 0 24 24"><path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"/></svg></a>';
        });

        // 4. Standalone Phone numbers (Only outside existing HTML tags)
        const parts = escaped.split(/(<a\b[^>]*>.*?<\/a>|<[^>]+>)/gis);
        for (let i = 0; i < parts.length; i++) {
            if (parts[i] && !parts[i].startsWith('<')) {
                parts[i] = parts[i].replace(/(^|[^0-9])((?:0[2-9]\d{1}-\d{3}-\d{3,4})|(?:0[2-9]\d{7,8})|(?:0[689]\d{1}-\d{3}-\d{4}))(?=$|[^0-9])/g, function(match, prefix, phone) {
                    const clean = phone.replace(/[^0-9]/g, '');
                    return prefix + '<a href="tel:' + clean + '" class="skj-phone-pill">📞 ' + phone + '</a>';
                });
            }
        }
        escaped = parts.join('');

        // 5. Bullet lists (- or * or •)
        escaped = escaped.replace(/^[ \t]*[-*•][ \t]+(.*)$/gm, '<div class="skj-list-item"><span class="skj-list-bullet">🌸</span><div>$1</div></div>');

        // 6. Normalize excessive blank lines (collapse multiple blank lines into single line break)
        escaped = escaped.replace(/\n{2,}/g, '\n');

        // 7. Strip newlines touching block elements
        escaped = escaped.replace(/<\/div>\n+/g, '</div>');
        escaped = escaped.replace(/\n+<div class="skj-list-item"/g, '<div class="skj-list-item"');

        // 8. Convert remaining newlines to <br>
        escaped = escaped.replace(/\n/g, '<br>');

        // 9. Clean up redundant <br> next to list items
        escaped = escaped.replace(/(<\/div>)\s*(<br\s*\/?>)+/gi, '$1');
        escaped = escaped.replace(/(<br\s*\/?>)+\s*(<div class="skj-list-item")/gi, '$2');

        return escaped;
    }

    function startPolling() {
        if (pollTimer) clearInterval(pollTimer);
        pollTimer = setInterval(pollChat, 3000);
    }

    if (currentToken) {
        startPolling();
    }

    function escapeHtml(s) {
        return (s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function formatTime(d) {
        const date = new Date(d);
        return isNaN(date.getTime()) ? '' : date.toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit' });
    }
})();
