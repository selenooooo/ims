@stack('modals')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMS Dashboard</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

</head>

<body class="bg-gray-100 min-h-screen flex flex-col font-inter">

    <!-- HEADER -->
    <header class="bg-white shadow px-6 py-4 flex justify-between items-center">
        <a href="{{ auth()->user()->isSupervisor() ? route('supervisor.dashboard') : route('dashboard') }}"
           class="flex items-center space-x-3">
            <img src="{{ asset('images/ims_logo.png') }}" class="h-10">
            <h1 class="text-xl text-gray-800">
                Intern Management System
            </h1>
        </a>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                <span>Logout</span>
                <i class="fas fa-sign-out-alt"></i>
            </button>
        </form>
    </header>

    <!-- BODY -->
    <div class="flex flex-1">

        <!-- SIDEBAR -->
        <aside class="w-64 bg-white shadow-md p-6 mt-4">

            <!-- Profile -->
            <div class="flex items-center space-x-3 mb-8">
                <i class="fas fa-user-circle text-4xl text-gray-500"></i>
                <div>
                    <p class="font-semibold text-gray-800">
                        {{ auth()->user()->name }}
                    </p>
                    <p class="text-sm text-gray-500">
                        {{ auth()->user()->isSupervisor() ? 'Supervisor' : 'Intern' }}
                    </p>
                </div>
            </div>

            <!-- Menu -->
            <nav class="space-y-2">
                @if(auth()->user()->isSupervisor())

                    @if(request()->routeIs('supervisor.leave.create'))
                        <div class="ml-2 space-y-1">
                                <a href="{{ route('supervisor.leave.index') }}"
                                class="block px-4 py-2 rounded text-sm text-gray-500 hover:text-gray-900 hover:bg-gray-100 flex items-center gap-2">
                                    <i class="fas fa-arrow-left w-4 text-center"></i>
                                    Back
                                </a>
                        </div>
                    @elseif(request()->routeIs('supervisor.leave.leaveType'))
                        <div class="ml-2 space-y-1">
                                <a href="{{ route('supervisor.leave.index') }}"
                                class="block px-4 py-2 rounded text-sm text-gray-500 hover:text-gray-900 hover:bg-gray-100 flex items-center gap-2">
                                    <i class="fas fa-arrow-left w-4 text-center"></i>
                                    Back
                                </a>
                        </div>
                    @elseif(request()->routeIs('supervisor.leave.*'))
                        <!-- Only show Leave Sub-menu -->
                        <div class="ml-2 space-y-1">
                            <a href="{{ route('supervisor.dashboard') }}"
                            class="block px-4 py-2 rounded text-sm text-gray-500 hover:text-gray-900 hover:bg-gray-100 flex items-center gap-2">
                                <i class="fas fa-arrow-left w-4 text-center"></i>
                                Back
                            </a>
                            <a href="{{ route('supervisor.leave.index') }}"
                            class="block px-4 py-2 rounded text-sm flex items-center gap-2
                            {{ !request('filter') ? 'bg-gray-200 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                                <i class="fas fa-list w-4 text-center text-gray-500"></i>
                                All Records
                            </a>
                            <a href="{{ route('supervisor.leave.index', ['filter' => 'pending']) }}"
                                class="block px-4 py-2 rounded text-sm
                                {{ request('filter') === 'pending' ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100' }}">
                                <i class="fas fa-clock w-4 text-center text-yellow-500"></i>
                                Pending
                            </a>

                            <a href="{{ route('supervisor.leave.index', ['filter' => 'approved']) }}"
                                class="block px-4 py-2 rounded text-sm
                                {{ request('filter') === 'approved' ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100' }}">
                                 <i class="fas fa-check-circle w-4 text-center text-green-500"></i>
                                Approved
                            </a>

                            <a href="{{ route('supervisor.leave.index', ['filter' => 'rejected']) }}"
                                class="block px-4 py-2 rounded text-sm
                                {{ request('filter') === 'rejected' ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100' }}">
                                 <i class="fas fa-times-circle w-4 text-center text-red-500"></i>
                                Rejected
                            </a>
                        </div>
                    @else 
                        <!-- Show Normal Supervisor Menu -->
                        <a href="{{ route('supervisor.dashboard') }}" class="flex items-center gap-3 px-4 py-2 rounded
                        {{ request()->routeIs('supervisor.dashboard') 
                                ? 'bg-gray-200 text-gray-900 font-semibold' 
                                : 'text-gray-700 hover:bg-gray-100' }}">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('supervisor.interns.index') }}" class="flex items-center gap-3 px-4 py-2 rounded
                        {{ request()->routeIs('supervisor.interns.*') 
                                ? 'bg-gray-200 text-gray-900 font-semibold' 
                                : 'text-gray-700 hover:bg-gray-100' }}">
                            <i class="fas fa-users"></i>
                            <span>Interns</span>
                        </a>

                        <a href="{{ route('supervisor.attendance.index') }}" class="flex items-center gap-3 px-4 py-2 rounded
                        {{ request()->routeIs('supervisor.attendance.*') 
                                ? 'bg-gray-200 text-gray-900 font-semibold' 
                                : 'text-gray-700 hover:bg-gray-100' }}">
                            <i class="fas fa-calendar-check"></i>
                            <span>Attendance</span>
                        </a>

                        <a href="{{ route('supervisor.leave.index') }}" class="flex items-center gap-3 px-4 py-2 rounded
                        {{ request()->routeIs('supervisor.leave.*') 
                                ? 'bg-gray-200 text-gray-900 font-semibold' 
                                : 'text-gray-700 hover:bg-gray-100' }}">
                            <i class="fas fa-calendar-check"></i>
                            <span>Leaves</span>
                        </a>

                        <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-4 py-2 rounded
                        {{ request()->routeIs('profile.show') 
                                ? 'bg-gray-200 text-gray-900 font-semibold' 
                                : 'text-gray-700 hover:bg-gray-100' }}">
                            <i class="fas fa-gear"></i>
                            <span>Settings</span>
                        </a>
                    @endif

                @else

                    @if(request()->routeIs('intern.leave.create'))
                        <div class="ml-2 space-y-1">
                                <a href="{{ route('intern.leave.index') }}"
                                class="block px-4 py-2 rounded text-sm text-gray-500 hover:text-gray-900 hover:bg-gray-100 flex items-center gap-2">
                                    <i class="fas fa-arrow-left w-4 text-center"></i>
                                    Back
                                </a>
                        </div>
                    @elseif(request()->routeIs('intern.leave.*'))
                        <!-- Only show Leave Sub-menu -->
                        <div class="ml-2 space-y-1">
                            <a href="{{ route('dashboard') }}"
                            class="block px-4 py-2 rounded text-sm text-gray-500 hover:text-gray-900 hover:bg-gray-100 flex items-center gap-2">
                                <i class="fas fa-arrow-left w-4 text-center"></i>
                                Back
                            </a>
                            <a href="{{ route('intern.leave.index') }}"
                            class="block px-4 py-2 rounded text-sm flex items-center gap-2
                            {{ !request('filter') ? 'bg-gray-200 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                                <i class="fas fa-list w-4 text-center text-gray-500"></i>
                                All Records
                            </a>
                            <a href="{{ route('intern.leave.index', ['filter' => 'pending']) }}"
                            class="block px-4 py-2 rounded text-sm flex items-center gap-2
                            {{ request('filter') === 'pending' ? 'bg-gray-200 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                                <i class="fas fa-clock w-4 text-center text-yellow-500"></i>
                                Pending
                            </a>
                            <a href="{{ route('intern.leave.index', ['filter' => 'approved']) }}"
                            class="block px-4 py-2 rounded text-sm flex items-center gap-2
                            {{ request('filter') === 'approved' ? 'bg-gray-200 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                                <i class="fas fa-check-circle w-4 text-center text-green-500"></i>
                                Approved
                            </a>
                            <a href="{{ route('intern.leave.index', ['filter' => 'rejected']) }}"
                            class="block px-4 py-2 rounded text-sm flex items-center gap-2
                            {{ request('filter') === 'rejected' ? 'bg-gray-200 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                                <i class="fas fa-times-circle w-4 text-center text-red-500"></i>
                                Rejected
                            </a>
                        </div>
                    @else
                    <!-- Interns Menu  -->
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2 rounded
                    {{ request()->routeIs('dashboard') 
                            ? 'bg-gray-200 text-gray-900 font-semibold' 
                            : 'text-gray-700 hover:bg-gray-100' }}">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('attendance.history') }}" class="flex items-center gap-3 px-4 py-2 rounded
                    {{ request()->routeIs('attendance.*') 
                            ? 'bg-gray-200 text-gray-900 font-semibold' 
                            : 'text-gray-700 hover:bg-gray-100' }}">
                        <i class="fas fa-calendar-check"></i>
                        <span>Attendance</span>
                    </a>

                    <a href="{{ route('intern.leave.index') }}" class="flex items-center gap-3 px-4 py-2 rounded
                    {{ request()->routeIs('intern.leave.*') 
                            ? 'bg-gray-200 text-gray-900 font-semibold' 
                            : 'text-gray-700 hover:bg-gray-100' }}">
                        <i class="fas fa-calendar-check"></i>
                        <span>Leaves</span>
                    </a>

                    <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-4 py-2 rounded
                    {{ request()->routeIs('profile.*') 
                            ? 'bg-gray-200 text-gray-900 font-semibold' 
                            : 'text-gray-700 hover:bg-gray-100' }}">
                        <i class="fas fa-gear"></i>
                        <span>Settings</span>
                    </a>
                    @endif

                @endif
            </nav>

        </aside>

        <!-- MAIN CONTENT -->
        <main class="flex-1 p-6">
            {{ $slot }}
        </main>

    </div>

    <!-- FOOTER -->
    <footer class="bg-white shadow py-4 text-center text-gray-500">
        &copy; {{ date('Y') }} IMS
    </footer>

    @stack('scripts')
</body>
</html>
