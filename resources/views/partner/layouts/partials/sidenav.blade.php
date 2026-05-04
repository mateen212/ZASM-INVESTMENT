@php
    $sidebar = json_decode(file_get_contents(resource_path('views/partner/layouts/sidenav.json')), true);
@endphp

<div class="sidebar bg--dark">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a href="{{route('partner.dashboard')}}" class="sidebar__main-logo"><img src="{{siteLogo('dark')}}" alt="@lang('image')"></a>
            <button type="button" class="navbar__expand"></button>
        </div>

        <div class="sidebar__menu-wrapper" id="sidebar__menuWrapper">
            <ul class="sidebar__menu">
                @foreach($sidebar as $item)
                    @if(isset($item['submenu']))
                        <li class="sidebar-menu-item sidebar-dropdown">
                            <a href="javascript:void(0)" class="{{ menuActive($item['url'] ?? '', 2) }}">
                                <i class="menu-icon la la-{{ $item['icon'] }}"></i>
                                <span class="menu-title">{{ __($item['name']) }}</span>
                                @if(isset($item['badge']))
                                    <span class="menu-badge pill bg--{{ $item['badge']['color'] ?? 'primary' }}">
                                        {{ __($item['badge']['value']) }}
                                    </span>
                                @endif
                            </a>
                            <div class="sidebar-submenu {{menuActive($item['url'] ?? '', 2) ? 'active' : '' }}">
                                <ul>
                                    @foreach($item['submenu'] as $submenu)
                                        <li class="sidebar-menu-item {{menuActive(route($submenu['url']))}}">
                                            <a href="{{route($submenu['url'])}}" class="nav-link">
                                                <i class="menu-icon las la-dot-circle"></i>
                                                <span class="menu-title">{{__($submenu['name'])}}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                    @else
                        <li class="sidebar-menu-item {{menuActive(route($item['url']))}}">
                            <a href="{{route($item['url'])}}" class="nav-link">
                                <i class="menu-icon la la-{{ $item['icon'] }}"></i>
                                <span class="menu-title">{{__($item['name'])}}</span>
                                @if(isset($item['badge']))
                                    <span class="menu-badge pill bg--{{ $item['badge']['color'] ?? 'primary' }}">
                                        {{ __($item['badge']['value']) }}
                                    </span>
                                @endif
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
            <!-- Removed version text as requested -->
        </div>
    </div>
</div>
<!-- sidebar end -->
