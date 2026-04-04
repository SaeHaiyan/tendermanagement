<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Subcon Workspace
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Welcome back, {{ Auth::user()->name }}!
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-green-50 border-l-4 border-green-400 p-4">
                            <p class="text-sm text-green-700 uppercase font-bold">Current Tasks</p>
                            <p class="text-3xl font-bold text-gray-800">0</p>
                        </div>

                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                            <p class="text-sm text-blue-700 uppercase font-bold">Messages</p>
                            <p class="text-gray-600 mt-1 italic text-sm">No new updates from Admin.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
