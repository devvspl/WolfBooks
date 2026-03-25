<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — WolfBooks</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
</head>

<body class="h-full font-sans antialiased bg-stone-100 text-stone-900"
      x-data="{
          sidebarOpen: false,
          mobileSearch: false,
          collapsed: window.innerWidth >= 1024 && localStorage.getItem('sidebarCollapsed') === 'true',
          notifOpen: false,
          toggleCollapse() {
              this.collapsed = !this.collapsed;
              localStorage.setItem('sidebarCollapsed', this.collapsed);
          },
          init() {
              window.addEventListener('resize', () => {
                  if (window.innerWidth < 1024) {
                      this.sidebarOpen = false;
                      this.collapsed = false;
                  }
              });
          }
      }">

{{-- ── Mobile overlay ── --}}
<div x-show="sidebarOpen"
     x-transition:enter="transition-opacity duration-200"
     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity duration-200"
     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="sidebar-overlay" @click="sidebarOpen = false"></div>

{{-- ══════════════════════════════════════════════════════
     SIDEBAR
══════════════════════════════════════════════════════ --}}
<aside :class="[
           sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
           collapsed ? 'lg:w-[68px]' : 'lg:w-[260px]'
       ]"
       class="fixed top-0 left-0 z-30 h-full w-[260px] flex flex-col
              bg-gradient-to-b from-stone-900 via-red-950 to-stone-950
              border-r border-white/5 shadow-2xl
              transition-all duration-300 ease-in-out">

    {{-- Brand --}}
    <div class="flex items-center border-b border-white/10 shrink-0 h-16 px-3 gap-2 overflow-hidden">

        {{-- Logo icon — click to re-expand when collapsed --}}
        <div @click="if(collapsed) toggleCollapse()"
             class="flex items-center justify-center w-9 h-9 shrink-0 bg-white/10 rounded-xl border border-white/10 shadow"
             :class="collapsed ? 'cursor-pointer hover:bg-white/20 transition' : ''">
            <svg class="w-5 h-5 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
        </div>

        {{-- Brand text — hidden when collapsed --}}
        <div class="leading-tight min-w-0 flex-1 transition-all duration-300 overflow-hidden"
             :class="collapsed ? 'w-0 max-w-0 opacity-0 pointer-events-none' : 'opacity-100'">
            <span class="text-white font-bold text-base tracking-tight whitespace-nowrap">WolfBooks</span>
            <p class="text-stone-400 text-[10px] font-medium uppercase tracking-widest whitespace-nowrap">Accounting</p>
        </div>

        {{-- Mobile close (only visible on mobile, no collapse state needed) --}}
        <button @click="sidebarOpen = false"
                class="lg:hidden flex items-center justify-center w-8 h-8 rounded-lg ml-auto
                       text-stone-400 hover:bg-white/10 hover:text-white transition shrink-0"
                aria-label="Close sidebar">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        {{-- Desktop collapse toggle — always in flow, never absolute --}}
        <button @click="toggleCollapse()"
                class="hidden lg:flex shrink-0 items-center justify-center w-7 h-7 rounded-lg
                       text-stone-400 hover:bg-white/10 hover:text-white transition ml-auto"
                aria-label="Toggle sidebar">
            <svg class="w-4 h-4 transition-transform duration-300" :class="collapsed ? 'rotate-180' : ''"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
        </button>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto overflow-x-hidden sidebar-scroll py-3 px-2 space-y-0.5"
         role="navigation" aria-label="Main navigation">

        @php
        $navSections = [
            'Main' => [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6'],
                ['label' => 'Master',    'route' => 'master',    'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16'],
            ],
        ];
        @endphp

        @foreach($navSections as $section => $items)
            @if(count($items) > 0)
            {{-- Section label --}}
            <div class="overflow-hidden transition-all duration-300"
                 :class="collapsed ? 'max-h-0 opacity-0 my-0 py-0' : 'max-h-8 opacity-100 mt-4 mb-1'">
                <p class="px-3 text-[10px] font-semibold uppercase tracking-widest text-stone-500 whitespace-nowrap">
                    {{ $section }}
                </p>
            </div>
            <div x-show="collapsed" class="my-1 mx-2 border-t border-white/10"></div>

            @foreach($items as $item)
            @php $isActive = Request::routeIs($item['route']); @endphp
            <div class="relative group/tip">
                <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                   class="nav-item {{ $isActive ? 'active' : '' }} relative"
                   :class="collapsed ? 'justify-center !px-0' : ''"
                   @click="if(window.innerWidth < 1024) sidebarOpen = false">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $item['icon'] }}"/>
                    </svg>
                    <span class="transition-all duration-300 whitespace-nowrap overflow-hidden flex-1"
                          :class="collapsed ? 'w-0 opacity-0 max-w-0' : 'opacity-100'">
                        {{ $item['label'] }}
                    </span>
                    @if(!empty($item['badge']))
                    <span x-show="!collapsed"
                          class="ml-auto inline-flex items-center justify-center min-w-[20px] h-5 px-1.5
                                 text-[11px] font-bold bg-red-500 text-white rounded-full leading-none">
                        {{ $item['badge'] }}
                    </span>
                    <span x-show="collapsed"
                          class="absolute top-0.5 right-0.5 w-4 h-4 flex items-center justify-center
                                 bg-red-500 text-white text-[9px] font-bold rounded-full leading-none">
                        {{ $item['badge'] }}
                    </span>
                    @endif
                </a>
                {{-- Tooltip (desktop collapsed only) --}}
                <div x-show="collapsed"
                     class="pointer-events-none absolute left-full top-1/2 -translate-y-1/2 ml-2 z-[60]
                            hidden lg:flex items-center gap-1
                            opacity-0 group-hover/tip:opacity-100 transition-opacity duration-150">
                    <div class="w-2 h-2 bg-stone-700 rotate-45 -mr-1 shrink-0"></div>
                    <div class="bg-stone-700 text-stone-100 text-xs font-medium px-3 py-1.5 rounded-lg shadow-xl whitespace-nowrap">
                        {{ $item['label'] }}
                        @if(!empty($item['badge']))
                            <span class="ml-1.5 bg-red-500 text-white px-1.5 py-0.5 rounded-full text-[10px] font-bold">{{ $item['badge'] }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
            @endif
        @endforeach
    </nav>

    {{-- User card --}}
    <div class="shrink-0 border-t border-white/10 transition-all duration-300"
         :class="collapsed ? 'px-1 py-3' : 'px-2 py-3'">
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                    class="w-full flex items-center gap-3 rounded-xl
                           hover:bg-white/5 transition-all duration-150 text-left min-h-[44px]"
                    :class="collapsed ? 'justify-center px-0 py-2' : 'px-2 py-2'"
                    aria-label="User menu">
                <div class="relative shrink-0">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin User') }}&background=7f1d1d&color=fca5a5&size=80"
                         alt="Avatar" class="w-8 h-8 rounded-full border-2 border-white/20">
                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-400 border-2 border-stone-900 rounded-full"></span>
                </div>
                <div class="min-w-0 transition-all duration-300 overflow-hidden"
                     :class="collapsed ? 'w-0 max-w-0 opacity-0 pointer-events-none' : 'flex-1 opacity-100'">
                    <p class="text-sm font-semibold text-stone-100 truncate leading-tight">{{ auth()->user()->name ?? 'Admin User' }}</p>
                    <p class="text-xs text-stone-500 truncate leading-tight mt-0.5">{{ auth()->user()->email ?? 'admin@wolfbooks.com' }}</p>
                </div>
                <svg class="w-4 h-4 text-stone-500 shrink-0 transition-transform duration-200"
                     :class="[open ? 'rotate-180' : '', collapsed ? '!hidden' : '']"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 @click.outside="open = false"
                 :class="collapsed ? 'left-full bottom-0 ml-3 w-52 origin-bottom-left' : 'bottom-full left-0 right-0 mb-2 origin-bottom'"
                 class="absolute bg-stone-800 border border-white/10 rounded-xl shadow-2xl overflow-hidden z-[100]">
                <div class="px-4 py-3 border-b border-white/10 bg-white/5">
                    <p class="text-sm font-semibold text-stone-100 truncate">{{ auth()->user()->name ?? 'Admin User' }}</p>
                    <p class="text-xs text-stone-400 truncate mt-0.5">{{ auth()->user()->email ?? 'admin@wolfbooks.com' }}</p>
                </div>
                <a href="{{ route('profile') }}" class="flex items-center gap-2.5 px-4 py-3 text-sm text-stone-300 hover:bg-white/5 hover:text-white transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Profile
                </a>
                <a href="{{ route('settings') }}" class="flex items-center gap-2.5 px-4 py-3 text-sm text-stone-300 hover:bg-white/5 hover:text-white transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Settings
                </a>
                <div class="border-t border-white/10"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-3 text-sm text-red-400 hover:bg-red-950/50 hover:text-red-300 transition-colors text-left">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>

{{-- ══════════════════════════════════════════════════════
     MAIN CONTENT
══════════════════════════════════════════════════════ --}}
<div class="flex flex-col min-h-screen transition-all duration-300"
     :class="collapsed ? 'lg:pl-[68px]' : 'lg:pl-[260px]'">

    {{-- ── TOP BAR ── --}}
    <header class="sticky top-0 z-20 h-16 bg-white/90 backdrop-blur-md border-b border-stone-200
                   flex items-center gap-2 sm:gap-3 px-3 sm:px-4 md:px-6 shadow-sm">

        {{-- Hamburger (mobile) --}}
        <button @click="sidebarOpen = true"
                class="lg:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg
                       text-stone-500 hover:bg-stone-100 hover:text-stone-900 transition shrink-0"
                aria-label="Open menu">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- Page title — hidden when mobile search is open --}}
        <div class="flex-1 min-w-0 transition-all duration-200"
             :class="mobileSearch ? 'hidden' : 'block'">
            <h1 class="text-sm sm:text-base font-semibold text-stone-800 truncate">
                @yield('page-title', 'Dashboard')
            </h1>
            @hasSection('breadcrumb')
                <nav class="hidden sm:flex items-center gap-1 text-xs text-stone-400 mt-0.5">
                    @yield('breadcrumb')
                </nav>
            @endif
        </div>

        {{-- Mobile search bar (expands inline) --}}
        <div x-show="mobileSearch"
             x-transition:enter="transition duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="flex-1 flex items-center gap-2 bg-stone-100 border border-stone-200 rounded-lg px-3 py-2 md:hidden">
            <svg class="w-4 h-4 text-stone-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" placeholder="Search…" autofocus
                   class="bg-transparent flex-1 text-sm text-stone-700 placeholder-stone-400 outline-none border-none p-0">
            <button @click="mobileSearch = false" class="text-stone-400 hover:text-stone-600 transition shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Right actions --}}
        <div class="flex items-center gap-1 sm:gap-2 shrink-0">

            {{-- Desktop search --}}
            <div class="hidden md:flex items-center gap-2 bg-stone-100 border border-stone-200
                        rounded-lg px-3 py-1.5 w-48 lg:w-56 xl:w-72">
                <svg class="w-4 h-4 text-stone-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" placeholder="Search invoices, clients…"
                       class="bg-transparent flex-1 text-sm text-stone-700 placeholder-stone-400 outline-none border-none p-0 focus:ring-0">
                <span class="text-[10px] text-stone-400 font-mono bg-stone-200 px-1.5 py-0.5 rounded hidden xl:inline">⌘K</span>
            </div>

            {{-- Mobile search toggle --}}
            <button @click="mobileSearch = true"
                    x-show="!mobileSearch"
                    class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg
                           text-stone-500 hover:bg-stone-100 transition"
                    aria-label="Search">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>

            {{-- Notifications --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                        class="relative inline-flex items-center justify-center w-10 h-10 rounded-lg
                               text-stone-500 hover:bg-stone-100 hover:text-stone-800 transition"
                        aria-label="Notifications">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                </button>

                <div x-show="open"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                     @click.outside="open = false"
                     class="absolute right-0 mt-2 w-[min(320px,calc(100vw-1.5rem))] bg-white border border-stone-200
                            rounded-2xl shadow-2xl overflow-hidden z-50 origin-top-right">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-stone-100">
                        <h3 class="text-sm font-semibold text-stone-800">Notifications</h3>
                        <span class="text-xs text-red-700 font-medium bg-red-50 px-2 py-0.5 rounded-full">3 new</span>
                    </div>
                    <div class="divide-y divide-stone-100 max-h-64 overflow-y-auto">
                        @foreach([
                            ['Invoice #INV-0042 overdue', 'Pending since 3 days ago', 'red'],
                            ['Payment received from Acme Ltd', '₹48,000 credited', 'green'],
                            ['New vendor added', 'TechSupply Co. onboarded', 'blue'],
                        ] as $notif)
                        <div class="flex gap-3 px-4 py-3 hover:bg-stone-50 transition-colors cursor-pointer">
                            <div class="mt-1.5 w-2 h-2 rounded-full bg-{{ $notif[2] }}-500 shrink-0"></div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-stone-800 truncate">{{ $notif[0] }}</p>
                                <p class="text-xs text-stone-400 mt-0.5">{{ $notif[1] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="px-4 py-3 border-t border-stone-100">
                        <a href="#" class="text-xs text-red-700 font-medium hover:underline">View all notifications →</a>
                    </div>
                </div>
            </div>

            {{-- Avatar (mobile) --}}
            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=7f1d1d&color=fca5a5&size=80"
                 alt="Avatar"
                 class="lg:hidden w-8 h-8 rounded-full border-2 border-stone-200 cursor-pointer shrink-0">
        </div>
    </header>

    {{-- ── PAGE CONTENT ── --}}
    <main class="flex-1 p-3 sm:p-4 md:p-6 xl:p-8">

        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition
             class="mb-4 sm:mb-6 flex items-start gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-800">
            <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="flex-1">{{ session('success') }}</p>
            <button @click="show = false" class="text-green-600 hover:text-green-800 shrink-0 p-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        @endif

        @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-transition
             class="mb-4 sm:mb-6 flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-800">
            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="flex-1">{{ session('error') }}</p>
            <button @click="show = false" class="text-red-600 hover:text-red-800 shrink-0 p-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        @endif

        @yield('content')
    </main>

    {{-- ── FOOTER ── --}}
    <footer class="shrink-0 px-4 sm:px-6 py-4 border-t border-stone-200
                   flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-stone-400">
        <span>© {{ date('Y') }} WolfBooks Accounting. All rights reserved.</span>
        <div class="flex items-center gap-4 sm:gap-6">
            <a href="#" class="hover:text-stone-600 transition-colors py-1">Privacy</a>
            <a href="#" class="hover:text-stone-600 transition-colors py-1">Terms</a>
            <a href="#" class="hover:text-stone-600 transition-colors py-1">Help</a>
        </div>
    </footer>
</div>

@stack('scripts')
</body>
</html>
