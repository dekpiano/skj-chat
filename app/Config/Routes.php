<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('ChatDesk');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// Root
$routes->get('/', 'ChatDesk::index');

// Authentication (Google OAuth 2.0)
$routes->group('auth', function ($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('verify-google', 'Auth::verifyGoogle');
    $routes->get('dev-login', 'Auth::devLogin');
    $routes->get('logout', 'Auth::logout');
});

// Live Chat Desk
$routes->group('chat', function ($routes) {
    $routes->get('desk', 'ChatDesk::index');
    $routes->get('queue', 'ChatDesk::getQueue');
    $routes->get('session/(:num)', 'ChatDesk::getSessionDetail/$1');
    $routes->post('send', 'ChatDesk::sendMessage');
    $routes->post('upload', 'ChatDesk::uploadAttachment');
    $routes->post('assign', 'ChatDesk::assignAgent');
    $routes->post('toggle-bot/(:num)', 'ChatDesk::toggleBot/$1');
    $routes->post('trigger-ai/(:num)', 'ChatDesk::triggerAi/$1');
    $routes->post('notes', 'ChatDesk::saveNotes');
    $routes->post('toggle-status/(:num)', 'ChatDesk::toggleStatus/$1');
    $routes->post('online-status', 'ChatDesk::updateOnlineStatus');
    $routes->post('extract-knowledge/(:num)', 'ChatDesk::extractKnowledge/$1');
    $routes->post('preview-knowledge/(:num)', 'ChatDesk::previewKnowledge/$1');
    $routes->post('save-extracted-knowledge', 'ChatDesk::saveExtractedKnowledge');
    $routes->post('delete-session/(:num)', 'ChatDesk::deleteSession/$1');
    $routes->post('cleanup-history', 'ChatDesk::cleanupHistory');
});

// AI Knowledge Base
$routes->group('knowledge', function ($routes) {
    $routes->get('/', 'Knowledge::index');
    $routes->post('save-url', 'Knowledge::saveUrl');
    $routes->post('upload-file', 'Knowledge::uploadFile');
    $routes->post('save-text', 'Knowledge::saveText');
    $routes->post('toggle/(:num)', 'Knowledge::toggle/$1');
    $routes->post('delete/(:num)', 'Knowledge::delete/$1');
});

// Settings
$routes->group('settings', function ($routes) {
    $routes->get('ai', 'AiSettings::index');
    $routes->post('ai/save', 'AiSettings::save');
    $routes->post('ai/test', 'AiSettings::testAi');

    $routes->get('telegram', 'TelegramSettings::index');
    $routes->post('telegram/save', 'TelegramSettings::save');
    $routes->post('telegram/test', 'TelegramSettings::testNotification');

    $routes->get('oauth', 'Settings::index');
    $routes->post('oauth/save', 'Settings::saveOauth');

    $routes->get('embed', 'Embed::index');
});

// Agents & Team
$routes->group('agents', function ($routes) {
    $routes->get('/', 'Agents::index');
    $routes->post('save', 'Agents::saveAgent');
    $routes->post('delete/(:num)', 'Agents::deleteAgent/$1');
});

// Canned Replies
$routes->group('canned', function ($routes) {
    $routes->get('/', 'CannedReplies::index');
    $routes->post('save', 'CannedReplies::saveReply');
    $routes->post('delete/(:num)', 'CannedReplies::deleteReply/$1');
});

// Analytics
$routes->get('analytics', 'Analytics::index');

// Widget API (CORS for client website embeds)
$routes->group('api/widget', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    $routes->options('(:any)', 'WidgetApi::initSession');
    $routes->post('init', 'WidgetApi::initSession');
    $routes->get('poll', 'WidgetApi::pollMessages');
    $routes->post('send', 'WidgetApi::sendMessage');
    $routes->post('upload', 'WidgetApi::uploadAttachment');
});
