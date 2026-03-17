<?php

Route::group(['middleware' => ['web', 'auth']], function () {
    Route::get('/hexaweb/claude-assist/settings',  'Modules\HexawebClaudeAssist\Http\Controllers\ClaudeAssistController@settings')
        ->name('hexaweb.claude_assist.settings');
    Route::post('/hexaweb/claude-assist/settings', 'Modules\HexawebClaudeAssist\Http\Controllers\ClaudeAssistController@settingsSave');
    Route::post('/hexaweb/claude-assist/test',     'Modules\HexawebClaudeAssist\Http\Controllers\ClaudeAssistController@testConnection')
        ->name('hexaweb.claude_assist.test');
    Route::get('/hexaweb/claude-assist/balance',   'Modules\HexawebClaudeAssist\Http\Controllers\ClaudeAssistController@balance')
        ->name('hexaweb.claude_assist.balance');
});
