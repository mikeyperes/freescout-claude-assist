@extends('layouts.app')

@section('title', 'Claude AI — Settings')

@section('content')
<div class="container" style="max-width:800px;padding:20px;">

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <h2 style="margin:0;font-size:22px;color:#333;">
            <i class="glyphicon glyphicon-flash" style="margin-right:8px;color:#9b59b6;"></i>
            Claude AI
            <small style="font-size:13px;color:#999;margin-left:8px;">Anthropic API Settings</small>
        </h2>
        <a href="{{ url('/hexaweb/ai/templates') }}" class="btn btn-default btn-sm">
            <i class="glyphicon glyphicon-list-alt"></i> AI Templates
        </a>
    </div>

    @if(session('flash_success_floating'))
        <div class="alert alert-success">{{ session('flash_success_floating') }}</div>
    @endif
    @if(session('flash_error_floating'))
        <div class="alert alert-danger">{{ session('flash_error_floating') }}</div>
    @endif

    {{-- Status banner --}}
    <div class="panel panel-default" style="border-left:4px solid {{ $api_key_set ? '#5cb85c' : '#d9534f' }};">
        <div class="panel-body" style="padding:12px 16px;">
            @if($api_key_set)
                <i class="glyphicon glyphicon-ok-circle" style="color:#5cb85c;font-size:16px;margin-right:8px;"></i>
                <strong style="color:#3c763d;">Claude API key is configured.</strong>
                <span style="color:#888;margin-left:8px;">The AI panel will appear in the reply form.</span>
            @else
                <i class="glyphicon glyphicon-exclamation-sign" style="color:#d9534f;font-size:16px;margin-right:8px;"></i>
                <strong style="color:#a94442;">No API key configured.</strong>
                <span style="color:#888;margin-left:8px;">Follow the setup instructions below to get started.</span>
            @endif
        </div>
    </div>

    {{-- Balance --}}
    @if($api_key_set)
    <div class="panel panel-default">
        <div class="panel-heading" style="display:flex;align-items:center;justify-content:space-between;">
            <strong><i class="glyphicon glyphicon-usd" style="margin-right:6px;"></i> API Credit Balance</strong>
            <a href="https://console.anthropic.com/settings/billing" target="_blank" class="btn btn-default btn-xs">
                <i class="glyphicon glyphicon-new-window"></i> Open Console Billing
            </a>
        </div>
        <div class="panel-body">
            <div class="alert alert-info" style="margin-bottom:14px;padding:10px 14px;font-size:13px;">
                <i class="glyphicon glyphicon-info-sign" style="margin-right:6px;"></i>
                Anthropic does not provide a credit balance API. Check your balance in the Console, then enter it below so it shows on conversation pages.
            </div>

            @if($manual_balance)
            <div style="margin-bottom:14px;padding:10px 14px;background:#f6fff8;border:1px solid #c3e6cb;border-radius:4px;font-size:13px;">
                <strong style="color:#27ae60;">Current balance:</strong>
                <span style="font-size:20px;font-weight:700;color:#27ae60;margin:0 8px;">${{ $manual_balance }}</span>
                @if($manual_balance_date)
                <span style="color:#aaa;font-size:12px;">last updated {{ \Carbon\Carbon::parse($manual_balance_date)->diffForHumans() }}</span>
                @endif
            </div>
            @endif

            <form method="POST" action="{{ url('/hexaweb/claude-assist/settings') }}" class="form-inline" style="margin-bottom:14px;">
                {{ csrf_field() }}
                <input type="hidden" name="model" value="{{ $model }}">
                <input type="hidden" name="max_tokens" value="{{ $max_tokens }}">
                <input type="hidden" name="system_prompt" value="{{ $system_prompt }}">
                <div class="input-group" style="width:200px;margin-right:8px;">
                    <span class="input-group-addon">$</span>
                    <input type="text" name="manual_balance" class="form-control" placeholder="e.g. 4.85"
                           value="{{ $manual_balance }}" style="width:140px;">
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Update Balance</button>
                <span class="help-block" style="display:inline;margin-left:10px;font-size:12px;color:#888;">
                    Enter your current balance from <a href="https://console.anthropic.com/settings/billing" target="_blank">Anthropic Console</a>
                </span>
            </form>

            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <a href="https://console.anthropic.com/settings/billing" target="_blank" class="btn btn-default btn-sm">
                    <i class="glyphicon glyphicon-usd"></i> View Balance <i class="glyphicon glyphicon-new-window" style="font-size:10px;"></i>
                </a>
                <a href="https://console.anthropic.com/settings/billing" target="_blank" class="btn btn-success btn-sm">
                    <i class="glyphicon glyphicon-plus"></i> Add Credits <i class="glyphicon glyphicon-new-window" style="font-size:10px;"></i>
                </a>
                <a href="https://console.anthropic.com/settings/billing" target="_blank" class="btn btn-default btn-sm">
                    <i class="glyphicon glyphicon-list-alt"></i> Payment History <i class="glyphicon glyphicon-new-window" style="font-size:10px;"></i>
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- Configuration form --}}
    <div class="panel panel-default">
        <div class="panel-heading"><strong>Configuration</strong></div>
        <div class="panel-body">
            <form method="POST" action="{{ url('/hexaweb/claude-assist/settings') }}" class="form-horizontal">
                {{ csrf_field() }}

                {{-- API Key --}}
                <div class="form-group">
                    <label class="col-sm-3 control-label">Anthropic API Key</label>
                    <div class="col-sm-9">
                        @if($api_key_set)
                            <div style="margin-bottom:8px;">
                                <span class="label label-success"><i class="glyphicon glyphicon-lock"></i> Key configured</span>
                            </div>
                        @endif
                        <input type="password" name="api_key" class="form-control"
                               placeholder="{{ $api_key_set ? '•••••••••••••  (leave blank to keep current key)' : 'sk-ant-api03-...' }}"
                               autocomplete="new-password">
                        <span class="help-block" style="font-size:12px;">
                            Starts with <code>sk-ant-api03-</code> &nbsp;·&nbsp;
                            <a href="https://console.anthropic.com/settings/keys" target="_blank" rel="noopener">Get a key at console.anthropic.com</a>
                        </span>
                    </div>
                </div>

                {{-- Model --}}
                <div class="form-group">
                    <label class="col-sm-3 control-label">Default Model</label>
                    <div class="col-sm-9">
                        <select name="model" class="form-control" style="width:auto;min-width:300px;">
                            @foreach($models as $value => $label)
                                <option value="{{ $value }}" {{ $model === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <span class="help-block" style="font-size:12px;">Can be overridden per-request in the reply panel.</span>
                    </div>
                </div>

                {{-- Max Tokens --}}
                <div class="form-group">
                    <label class="col-sm-3 control-label">Max Tokens</label>
                    <div class="col-sm-9">
                        <input type="number" name="max_tokens" class="form-control" style="width:120px;"
                               value="{{ $max_tokens }}" min="64" max="8192">
                        <span class="help-block" style="font-size:12px;">Max length of AI response. 1024 is a good default.</span>
                    </div>
                </div>

                {{-- System Prompt --}}
                <div class="form-group">
                    <label class="col-sm-3 control-label">System Prompt</label>
                    <div class="col-sm-9">
                        <textarea name="system_prompt" class="form-control" rows="3"
                                  placeholder="e.g. You are a helpful support agent for Hexa Web Systems. Always be concise and professional.">{{ $system_prompt }}</textarea>
                        <span class="help-block" style="font-size:12px;">Optional. Prepended to every request to set Claude's behaviour.</span>
                    </div>
                </div>

                {{-- Save + Test --}}
                <div class="form-group" style="margin-bottom:0;">
                    <div class="col-sm-offset-3 col-sm-9" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                        <button type="submit" class="btn btn-primary">
                            <i class="glyphicon glyphicon-floppy-disk"></i> Save Settings
                        </button>
                        @if($api_key_set)
                            <button type="button" id="hbt-claude-test-btn" class="btn btn-default">
                                <i class="glyphicon glyphicon-flash"></i> Test Connection
                            </button>
                            <span id="hbt-claude-test-result" style="display:none;font-size:13px;"></span>
                        @endif
                    </div>
                </div>

            </form>
        </div>
    </div>

    {{-- Setup Instructions --}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong><i class="glyphicon glyphicon-book" style="margin-right:6px;"></i> How to Get Your Anthropic API Key</strong>
        </div>
        <div class="panel-body">

            <div class="alert alert-info" style="margin-bottom:20px;padding:10px 14px;">
                <i class="glyphicon glyphicon-info-sign" style="margin-right:6px;"></i>
                You need an <strong>Anthropic account</strong> with a <strong>paid API plan</strong> (separate from Claude.ai subscriptions).
                The API has pay-as-you-go pricing — there is no monthly fee.
            </div>

            <h4 style="margin-top:0;margin-bottom:16px;font-size:15px;border-bottom:1px solid #eee;padding-bottom:8px;">
                Step-by-step
            </h4>

            <table class="table" style="margin-bottom:0;font-size:13px;">
                <tbody>
                    <tr>
                        <td style="width:36px;text-align:center;vertical-align:top;padding-top:12px;">
                            <span class="label label-default">1</span>
                        </td>
                        <td style="padding-top:10px;">
                            <strong>Go to the Anthropic Console</strong><br>
                            <a href="https://console.anthropic.com/" target="_blank" rel="noopener">console.anthropic.com</a>
                            — click <strong>Sign up</strong> if you don't have an account, or <strong>Log in</strong> if you do.
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center;vertical-align:top;padding-top:12px;">
                            <span class="label label-default">2</span>
                        </td>
                        <td style="padding-top:10px;">
                            <strong>Add billing (required before API works)</strong><br>
                            In the left sidebar, click <strong>Settings</strong> → <strong>Billing</strong>.<br>
                            Click <strong>Add payment method</strong>, enter your card, then click <strong>Add credits</strong>.<br>
                            Enter <strong>$5</strong> — this is enough for thousands of AI replies at Haiku pricing.
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center;vertical-align:top;padding-top:12px;">
                            <span class="label label-default">3</span>
                        </td>
                        <td style="padding-top:10px;">
                            <strong>Create an API key</strong><br>
                            In the left sidebar, click <strong>Settings</strong> → <strong>API Keys</strong>.<br>
                            Click the <strong>Create Key</strong> button (top right of the page).
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center;vertical-align:top;padding-top:12px;">
                            <span class="label label-default">4</span>
                        </td>
                        <td style="padding-top:10px;">
                            <strong>Name the key</strong><br>
                            Enter a name like <em>FreeScout</em>, then click <strong>Create Key</strong>.
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center;vertical-align:top;padding-top:12px;">
                            <span class="label label-default" style="background:#d9534f;">5</span>
                        </td>
                        <td style="padding-top:10px;">
                            <strong style="color:#d9534f;">Copy the key immediately — it will never be shown again</strong><br>
                            The key starts with <code>sk-ant-api03-</code>.<br>
                            Click the copy icon or select and copy the full key.
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center;vertical-align:top;padding-top:12px;">
                            <span class="label label-default">6</span>
                        </td>
                        <td style="padding-top:10px;">
                            <strong>Paste the key above and save</strong><br>
                            Paste into the <strong>Anthropic API Key</strong> field above, then click <strong>Save Settings</strong>.<br>
                            Use the <strong>Test Connection</strong> button to confirm it works.
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="alert alert-warning" style="margin-top:16px;margin-bottom:0;padding:10px 14px;font-size:13px;">
                <strong>Billing note:</strong> The API requires prepaid credits — you won't be charged automatically beyond what you add.
                Claude Haiku 4.5 costs ~$0.001 per reply. $5 = ~5,000 AI replies.
            </div>

        </div>
    </div>

    {{-- Troubleshooting --}}
    <div class="panel panel-default">
        <div class="panel-heading"><strong><i class="glyphicon glyphicon-wrench" style="margin-right:6px;"></i> Troubleshooting</strong></div>
        <div class="panel-body" style="padding:0;">
            <table class="table table-condensed" style="margin:0;font-size:13px;">
                <tr>
                    <td style="width:200px;color:#d9534f;font-weight:600;padding-left:16px;">401 Unauthorized</td>
                    <td>API key is wrong or expired. Re-enter it.</td>
                </tr>
                <tr>
                    <td style="color:#d9534f;font-weight:600;padding-left:16px;">429 Rate limited</td>
                    <td>No billing credits on the account. Add credits at console.anthropic.com → Billing.</td>
                </tr>
                <tr>
                    <td style="color:#d9534f;font-weight:600;padding-left:16px;">500 / no response</td>
                    <td>Server cannot reach api.anthropic.com. Check outbound HTTPS firewall rules.</td>
                </tr>
                <tr>
                    <td style="color:#f0ad4e;font-weight:600;padding-left:16px;">Key set but not working</td>
                    <td>Save a blank key to clear it, then re-enter the key and save again.</td>
                </tr>
            </table>
        </div>
    </div>

</div>
@endsection
