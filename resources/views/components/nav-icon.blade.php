@props(['name'])

@php
    // Maps this app's existing semantic icon names (used across
    // Navigation.php and call sites) to Material Symbols glyph names.
    $glyphs = [
        'home' => 'dashboard',
        'bed' => 'bed',
        'users' => 'group',
        'wrench' => 'build',
        'banknotes' => 'payments',
        'settings' => 'settings',
        'user-plus' => 'person_add',
        'support' => 'help',
        'chart-bar' => 'analytics',
        'door' => 'meeting_room',
        'shield' => 'admin_panel_settings',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'material-symbols-outlined text-[20px] leading-none']) }}>{{ $glyphs[$name] ?? $name }}</span>
