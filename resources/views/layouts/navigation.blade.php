<nav x-data="{ open: false }" class="bg-red-600 shadow-lg border-b border-red-700"> <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20"> <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('subcon.dashboard') }}">
                        <img src="https://aito.com.my/wp-content/uploads/2022/08/aitonewlogowhite.png" class="block h-12 w-auto" alt="AITO Logo" /> </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if(Auth::user()->role === 'admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="text-white hover:text-gray-100 font-bold uppercase tracking-wider text-sm border-t-4 border-transparent hover:border-gray-100 transition-all">
                            {{ ('Admin Panel') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.tenders.index')" :active="request()->routeIs('admin.tenders.*')" class="text-white hover:text-gray-100 font-bold uppercase tracking-wider text-sm border-t-4 border-transparent hover:border-gray-100 transition-all">
                            {{ ('Tender Projects') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('subcon.dashboard')" :active="request()->routeIs('dashboard')" class="text-white hover:text-gray-100 font-bold uppercase tracking-wider text-sm border-t-4 border-transparent hover:border-gray-100 transition-all">
                            {{ ('Dashboard') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-bold rounded-md text-white bg-red-600 hover:text-gray-100 focus:outline-none transition ease-in-out duration-150 uppercase tracking-widest">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="text-gray-700 hover:text-red-600 font-medium">
                            {{ ('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" class="text-gray-700 hover:text-red-600 font-medium"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ ('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-100 hover:text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 transition duration-150">
                    <svg class="h-6 w-6 text-white" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</nav>
