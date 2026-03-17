# FreeScout Claude Assist

Anthropic Claude API integration for FreeScout.

## Features

- Secure API key storage (encrypted in database)
- Model selection (Claude Opus 4.6, Sonnet 4.6, Haiku 4.5, and legacy models)
- Configurable max tokens and system prompt
- Test connection button
- Manual balance entry (Anthropic has no public balance API)

## Requirements

- FreeScout v1.8+
- PHP 8.0+
- Anthropic API key (console.anthropic.com)

## Installation

1. Upload the `HexawebClaudeAssist` folder to `Modules/`
2. Go to Manage → Modules and activate it
3. Go to Manage → Claude AI and enter your API key
