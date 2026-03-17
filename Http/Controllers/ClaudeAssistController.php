<?php

namespace Modules\HexawebClaudeAssist\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ClaudeAssistController extends Controller
{
    const MODELS = [
        'claude-opus-4-6'            => 'Claude Opus 4.6',
        'claude-sonnet-4-6'          => 'Claude Sonnet 4.6 (Recommended)',
        'claude-haiku-4-5-20251001'  => 'Claude Haiku 4.5',
        'claude-3-7-sonnet-20250219' => 'Claude 3.7 Sonnet',
        'claude-3-5-sonnet-20241022' => 'Claude 3.5 Sonnet (Oct 2024)',
        'claude-3-5-haiku-20241022'  => 'Claude 3.5 Haiku (Oct 2024)',
        'claude-3-opus-20240229'     => 'Claude 3 Opus',
    ];

    public function settings()
    {
        $user = auth()->user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        $apiKeySet = false;
        $encrypted = \Option::get('claude_assist.api_key_encrypted', '');
        if ($encrypted) {
            try {
                $apiKeySet = !empty(\Crypt::decryptString($encrypted));
            } catch (\Exception $e) {
                $apiKeySet = false;
            }
        }

        return view('hexawebclaudeassist::settings', [
            'model'                   => \Option::get('claude_assist.model', 'claude-sonnet-4-6'),
            'max_tokens'              => \Option::get('claude_assist.max_tokens', 1024),
            'system_prompt'           => \Option::get('claude_assist.system_prompt', ''),
            'api_key_set'             => $apiKeySet,
            'models'                  => self::MODELS,
            'manual_balance'          => \Option::get('claude_assist.manual_balance', ''),
            'manual_balance_date'     => \Option::get('claude_assist.manual_balance_date', ''),
        ]);
    }

    public function settingsSave(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        \Option::set('claude_assist.model',         $request->input('model', 'claude-sonnet-4-6'));
        \Option::set('claude_assist.max_tokens',    (int)$request->input('max_tokens', 1024));
        \Option::set('claude_assist.system_prompt', $request->input('system_prompt', ''));

        $manualBalance = trim($request->input('manual_balance') ?? '');
        if ($manualBalance !== '') {
            \Option::set('claude_assist.manual_balance',      $manualBalance);
            \Option::set('claude_assist.manual_balance_date', now()->toDateTimeString());
        }

        $apiKey = trim($request->input('api_key', ''));
        if (!empty($apiKey)) {
            \Option::set('claude_assist.api_key_encrypted', \Crypt::encryptString($apiKey));
        }

        \Session::flash('flash_success_floating', 'Claude AI settings saved.');
        return redirect()->route('hexaweb.claude_assist.settings');
    }

    public function testConnection()
    {
        $user = auth()->user();
        if (!$user || !$user->isAdmin()) {
            return response()->json(['status' => 'error', 'msg' => 'Unauthorized'], 403);
        }

        $encrypted = \Option::get('claude_assist.api_key_encrypted', '');
        if (!$encrypted) {
            return response()->json(['status' => 'error', 'msg' => 'No API key set.']);
        }

        try {
            $apiKey = \Crypt::decryptString($encrypted);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'msg' => 'Could not decrypt API key.']);
        }

        $model   = \Option::get('claude_assist.model', 'claude-sonnet-4-6');
        $payload = json_encode(['model' => $model, 'max_tokens' => 10, 'messages' => [['role' => 'user', 'content' => 'Say hi']]]);

        $ctx = stream_context_create([
            'http' => [
                'method'        => 'POST',
                'header'        => "Content-Type: application/json\r\nx-api-key: {$apiKey}\r\nanthropic-version: 2023-06-01",
                'content'       => $payload,
                'timeout'       => 15,
                'ignore_errors' => true,
            ],
        ]);

        $raw  = @file_get_contents('https://api.anthropic.com/v1/messages', false, $ctx);
        $data = json_decode($raw, true);

        if (!empty($data['content'][0]['text'])) {
            return response()->json(['status' => 'success', 'msg' => 'Connection successful. Model: ' . $model]);
        }
        return response()->json(['status' => 'error', 'msg' => 'Failed: ' . ($data['error']['message'] ?? ($raw ?: 'No response'))]);
    }

    public function balance()
    {
        $user = auth()->user();
        if (!$user || !$user->isAdmin()) {
            return response()->json(['status' => 'error', 'msg' => 'Unauthorized'], 403);
        }

        $encrypted = \Option::get('claude_assist.api_key_encrypted', '');
        if (!$encrypted) {
            return response()->json(['status' => 'error', 'msg' => 'No API key set.']);
        }

        // Anthropic does not expose a credit balance endpoint via API key.
        // Return any previously cached value plus links to the console.
        $cached = \Option::get('claude_assist.balance_cache', null);
        return response()->json([
            'status'      => 'console_only',
            'cached'      => $cached ? json_decode($cached, true) : null,
            'billing_url' => 'https://console.anthropic.com/settings/billing',
            'topup_url'   => 'https://console.anthropic.com/settings/billing',
        ]);
    }
}
