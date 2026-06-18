<nav x-data="{ open: false }" class="bg-red-600 h-screen w-full md:w-60 border-b border-red-700 md:border-b-0 md:border-r md:border-red-700 fixed md:sticky top-0 z-50 overflow-visible shadow-xl">
    <div class="flex h-full flex-col px-4 py-6 sm:px-6 justify-between">

        <div x-data="{ activeDropdown: null }">
            <div class="flex items-center justify-between md:justify-start pb-4 mb-4 border-b border-red-500/30">
                <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('subcon.dashboard') }}" class="inline-flex items-center">
                    <img src="https://aito.com.my/wp-content/uploads/2022/08/aitonewlogowhite.png" class="h-12 w-auto" alt="AITO Logo" />
                </a>
            </div>

            <div class="space-y-2">
                @if(Auth::user()->role === 'admin')
                    <div class="flex items-center text-white/60 px-3 py-1 text-[10px] font-black uppercase tracking-[0.2em]">
                        Admin Panel
                    </div>
                @elseif(Auth::user()->role === 'subcon')
                    <div class="flex items-center text-white/60 px-3 py-1 text-[10px] font-black uppercase tracking-[0.2em]">
                        Subcontractor Portal
                    </div>
                @endif

                <div class="space-y-1">
                    @php
                        $isManagement = Auth::user()->role === 'admin';
                        $dropId1 = $isManagement ? 'management' : 'subcon_menu';
                        $label1 = $isManagement ? 'Tenders Menu' : 'My Workspace';
                    @endphp

                        <button @click="activeDropdown === '{{ $dropId1 }}' ? activeDropdown = null : activeDropdown = '{{ $dropId1 }}'"
                            class="flex items-center justify-between w-full text-white hover:bg-red-700/60 rounded-xl px-3 py-3 font-bold uppercase tracking-[0.18em] text-xs border-l-4 border-transparent hover:border-white transition-all duration-150 group focus:outline-none">
                        <span class="opacity-80 group-hover:opacity-100 transition-opacity">{{ $label1 }}</span>
                        <svg class="h-3.5 w-3.5 transform transition-transform duration-200 opacity-70 group-hover:opacity-100"
                             :class="{ 'rotate-180': activeDropdown === '{{ $dropId1 }}' }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="activeDropdown === '{{ $dropId1 }}'" x-collapse class="pl-4 space-y-1 overflow-hidden" style="display: none;">
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.tenders.index') }}" class="block text-red-100 hover:text-white hover:bg-red-700/30 px-3 py-2 rounded-md text-xs font-medium tracking-wide transition-colors {{ request()->routeIs('admin.tenders.index') ? 'bg-red-700/40 text-white' : '' }}">
                                {{ ('Tenders List') }}
                            </a>
                            <a href="{{ route('admin.tenders.create') }}" class="block text-red-100 hover:text-white hover:bg-red-700/30 px-3 py-2 rounded-md text-xs font-medium tracking-wide transition-colors {{ request()->routeIs('admin.tenders.create') ? 'bg-red-700/40 text-white' : '' }}">
                                {{ ('Create New Tender') }}
                            </a>
                            <a href="{{ route('admin.pending-approvals') }}" class="block text-red-100 hover:text-white hover:bg-red-700/30 px-3 py-2 rounded-md text-xs font-medium tracking-wide transition-colors {{ request()->routeIs('admin.pending-approvals') ? 'bg-red-700/40 text-white' : '' }}">
                                {{ ('Pending Approvals') }}
                            </a>
                            @php
                                try {
                                    $notifSvc = app(\App\Services\AdminNotificationService::class);
                                    $activityCount = $notifSvc->unreadCount();
                                } catch (\Throwable $e) {
                                    $activityCount = 0;
                                }
                            @endphp

                            <a href="{{ route('admin.activity') }}" class="flex items-center justify-between gap-2 w-full text-red-100 hover:text-white hover:bg-red-700/30 px-3 py-2 rounded-md text-xs font-medium tracking-wide transition-colors {{ request()->routeIs('admin.activity') ? 'bg-red-700/40 text-white' : '' }}">
                                <span>{{ ('Activity') }}</span>
                                @if(!empty($activityCount) && $activityCount > 0)
                                    <span class="inline-flex items-center justify-center h-6 min-w-[22px] px-2 rounded-full bg-white text-red-700 text-xs font-black">{{ $activityCount }}</span>
                                @endif
                            </a>
                        @else
                            <a href="#" class="block text-red-100 hover:text-white hover:bg-red-700/30 px-3 py-2 rounded-md text-xs font-medium tracking-wide transition-colors">
                                {{ ('My Assigned Tasks') }}
                            </a>
                            <a href="{{ route('profile.edit') }}" class="block text-red-100 hover:text-white hover:bg-red-700/30 px-3 py-2 rounded-md text-xs font-medium tracking-wide transition-colors">
                                {{ ('Company Profile') }}
                            </a>
                            <a href="{{ route('subcon.documents.index') }}" class="block text-red-100 hover:text-white hover:bg-red-700/30 px-3 py-2 rounded-md text-xs font-medium tracking-wide transition-colors">
                                {{ ('Upload Documents') }}
                            </a>
                        @endif
                    </div>
                </div>

                <div class="space-y-1">
                    @php
                        $label2 = Auth::user()->role === 'admin' ? 'Subcontractors' : 'My Projects';
                    @endphp

                        <button @click="activeDropdown === 'settings' ? activeDropdown = null : activeDropdown = 'settings'"
                            class="flex items-center justify-between w-full text-white hover:bg-red-700/60 rounded-xl px-3 py-3 font-bold uppercase tracking-[0.18em] text-xs border-l-4 border-transparent hover:border-white transition-all duration-150 group focus:outline-none">
                        <span class="opacity-80 group-hover:opacity-100 transition-opacity">{{ $label2 }}</span>
                        <svg class="h-3.5 w-3.5 transform transition-transform duration-200 opacity-70 group-hover:opacity-100"
                             :class="{ 'rotate-180': activeDropdown === 'settings' }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="activeDropdown === 'settings'" x-collapse class="pl-4 space-y-1 overflow-hidden" style="display: none;">
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="block text-red-100 hover:text-white hover:bg-red-700/30 px-3 py-2 rounded-md text-xs font-medium tracking-wide transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-red-700/40 text-white' : '' }}">
                                {{ ('Subcontractors List') }}
                            </a>
                            <a href="#" class="block text-red-100 hover:text-white hover:bg-red-700/30 px-3 py-2 rounded-md text-xs font-medium tracking-wide transition-colors">
                                {{ ('Assign New Task') }}
                            </a>
                        @else
                            <a href="#" class="block text-red-100 hover:text-white hover:bg-red-700/30 px-3 py-2 rounded-md text-xs font-medium tracking-wide transition-colors">
                                {{ ('Active Projects') }}
                            </a>
                            <a href="#" class="block text-red-100 hover:text-white hover:bg-red-700/30 px-3 py-2 rounded-md text-xs font-medium tracking-wide transition-colors">
                                {{ ('Project History') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-red-500/30 relative" x-data="{ open: false }">
            <button @click="open = !open" @click.away="open = false" class="flex items-center justify-between w-full px-3 py-2.5 text-left text-white bg-red-700/40 hover:bg-red-700/80 rounded-xl border border-red-500/20 focus:outline-none transition ease-in-out duration-150 uppercase tracking-widest text-xs font-bold group">
                <span class="truncate max-w-[140px] opacity-90 group-hover:opacity-100">{{ Auth::user()->name }}</span>
                <svg class="fill-current h-4 w-4 text-white opacity-70 group-hover:opacity-100 transition-transform duration-200" :class="{'rotate-180': open}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>

            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute bottom-full left-0 mb-3 w-full bg-white rounded-xl shadow-2xl py-1.5 ring-1 ring-black ring-opacity-5 z-50 border border-gray-100"
                 style="display: none;">

                <x-dropdown-link :href="route('profile.edit')" class="text-gray-700 hover:text-red-600 hover:bg-red-50 font-semibold text-xs tracking-wider uppercase px-4 py-2.5 block transition-colors">
                    {{ ('Profile') }}
                </x-dropdown-link>

                <div class="h-px bg-gray-100 my-1"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')" class="text-gray-700 hover:text-red-600 hover:bg-red-50 font-semibold text-xs tracking-wider uppercase px-4 py-2.5 block transition-colors"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ ('Log Out') }}
                    </x-dropdown-link>
                </form>
            </div>
        </div>

    </div>
</nav>
