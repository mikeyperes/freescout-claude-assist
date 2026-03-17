<?php

namespace Modules\HexawebClaudeAssist\Providers;

use Illuminate\Support\ServiceProvider;

class ClaudeAssistServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'hexawebclaudeassist');

        $this->registerHooks();
    }

    public function register()
    {
        //
    }

    private function registerHooks()
    {
        // Settings link in Manage menu
        \Eventy::addAction('menu.manage.append', function () {
            $user = auth()->user();
            if (!$user || !$user->isAdmin()) {
                return;
            }
            $active = request()->is('hexaweb/claude-assist*') ? ' class="active"' : '';
            echo '<li' . $active . '><a href="' . url('/hexaweb/claude-assist/settings') . '"><i class="glyphicon glyphicon-flash"></i> Claude AI</a></li>';
        }, 20);

        // Test connection button JS
        \Eventy::addAction('javascript', function () {
            ?>
if (!window._hbtClaudeBound) {
    window._hbtClaudeBound = true;
    jQuery(document).on('click', '#hbt-claude-test-btn', function() {
        var $btn = jQuery(this);
        var $result = jQuery('#hbt-claude-test-result');
        $btn.prop('disabled', true).text('Testing...');
        $result.html('').hide();
        jQuery.ajax({
            url: '<?php echo url("/hexaweb/claude-assist/test"); ?>',
            type: 'POST',
            data: { _token: jQuery('meta[name="csrf-token"]').attr('content') },
            success: function(r) {
                $btn.prop('disabled', false).text('Test Connection');
                var ok = r.status === 'success';
                $result.html('<span style="color:' + (ok ? '#3c763d' : '#a94442') + ';">'
                    + '<i class="glyphicon glyphicon-' + (ok ? 'ok' : 'remove') + '"></i> ' + r.msg + '</span>').show();
            },
            error: function() {
                $btn.prop('disabled', false).text('Test Connection');
                $result.html('<span style="color:#a94442;">Request failed.</span>').show();
            }
        });
    });
}
            <?php
        }, 20);
    }
}
