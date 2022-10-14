<?php
// https://icon-sets.iconify.design/
$iconset = [
    // entidades
    'app' => 'heroicons-solid:puzzle',
    'user' => 'fa-solid:user-friends',
    'agent' => 'fa-solid:user-friends',
    'agent-1' => 'fa-solid:user',
    'agent-2' => 'fa-solid:user-friends',
    'space' => 'clarity:building-solid',
    'event' => 'bxs:calendar-event',
    'project' => 'ri:file-list-2-fill',
    'opportunity' => 'mdi:lightbulb-on',

    // redes sociais
    'facebook' => 'brandico:facebook',
    'github' => 'la:github-alt',
    'instagram' => 'fa6-brands:instagram',
    'linkedin' => 'akar-icons:linkedin-box-fill',
    'pinterest' => 'fa6-brands:pinterest-p',
    'spotify' => 'akar-icons:spotify-fill',
    'telegram' => 'cib:telegram-plane',
    'twitter' => 'akar-icons:twitter-fill',
    'whatsapp' => 'akar-icons:whatsapp-fill',
    'youtube' => 'brandico:vimeo',
    'vimeo' => 'akar-icons:youtube-fill',


    // IMPORTANTE: manter ordem alfabética
    'access' => 'ooui:previous-rtl',
    'add' => 'ps:plus',
    'app' => 'heroicons-solid:puzzle',
    'archive' => 'mi:archive',
    'arrow-down' => 'akar-icons:arrow-down',
    'arrow-left' => 'akar-icons:arrow-left',
    'arrow-right' => 'akar-icons:arrow-right',
    'arrow-top' => 'akar-icons:arrow-top',
    'arrowPoint-down' => 'fe:arrow-down',
    'arrowPoint-left' => 'fe:arrow-left',
    'arrowPoint-right' => 'fe:arrow-right',
    'arrowPoint-top' => 'fe:arrow-top',
    'exchange' => 'material-symbols:change-circle-outline',
    'code' => 'fa-solid:code',
    'copy' => 'ic:baseline-content-copy',
    'close' => 'gg:close',
    'down' => 'mdi:chevron-down',
    'dashboard' => 'ic:round-dashboard',
    'date'=> 'material-symbols:date-range-rounded',
    'delete' => 'gg:close',
    'download' => 'el:download-alt',
    'edit' => 'zondicons:edit-pencil',
    'exclamation' => 'bi:exclamation-circle-fill',
    'favorite' => 'mdi:star-outline',
    'filter' => 'ic:baseline-filter-alt',
    'home' => 'ci:home-fill',
    'image' => 'bi:image-fill',
    'link' => 'cil:link-alt',
    'list' => 'ci:list-ul',
    'loading' => 'eos-icons:three-dots-loading',
    'login' => 'icon-park-outline:login',
    'logout' => 'ri:logout-box-line',
    'map' => 'bxs:map-alt',
    'map-pin' => 'charm:map-pin',
    'menu-mobile' => 'icon-park-outline:hamburger-button',
    'network' => 'grommet-icons:connect',
    'next' => 'ooui:previous-rtl',
    'notification' => 'eva:bell-outline',
    'pin' => 'ph:map-pin-fill',
    'previous' => 'ooui:previous-ltr',
    'role'  => 'ri:profile-line',
    'search' => 'ant-design:search-outlined',
    'settings' => 'bxs:cog',
    'sort' => 'mdi:sort',
    'trash' => 'ooui:trash',
    'ticket' => 'mdi:ticket-confirmation-outline',  
    'triangle-down' => 'entypo:triangle-down',
    'triangle-up' => 'entypo:triangle-up',
    'up' => 'mdi:chevron-up',
    'user-config' => 'fa-solid:users-cog',
    'upload' => 'ic:baseline-file-upload',
];

$app->applyHook('component(mc-icon).iconset', [&$iconset]);

$this->jsObject['config']['iconset'] = $iconset;