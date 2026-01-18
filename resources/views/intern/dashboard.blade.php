<x-app-layout>
    <div class="max-w-full mx-auto px-4 space-y-6 grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- LEFT: Attendance Card -->
        <div class="md:col-span-2 bg-white border border-gray-200 shadow-sm rounded-md p-8 space-y-8">

            <!-- Header -->
            <div class="flex items-center justify-between border-b pb-4">
                <h2 class="text-xl text-gray-900">
                    Welcome, {{ auth()->user()->name }}
                </h2>
            </div>

            <!-- Check In -->
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">
                    Check In
                </h3>

                @if($todayAttendance && $todayAttendance->check_in)
                    <!-- Already Checked In -->
                    <div class="bg-green-50 border border-green-200 rounded-md p-4">
                        <p class="text-sm text-green-700 font-medium">
                        You have checked in
                        </p>
                        <p class="text-sm text-gray-700 mt-1">
                            Time:
                            <span class="font-semibold">
                                {{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') }}
                            </span>
                        </p>
                        <p class="text-sm text-gray-700">
                            Date:
                            <span class="font-semibold">
                                {{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('d M Y') }}
                            </span>
                        </p>
                    </div>
                @elseif($onLeaveToday)
                        <!-- On Leave -->
                        <div class="bg-purple-50 border border-purple-200 rounded-md p-4">
                            <p class="text-sm text-purple-700 font-semibold">
                                ON LEAVE
                            </p>
                            <p class="text-sm text-gray-700 mt-1">
                                You are on approved leave today.
                            </p>
                        </div>
                    @else
                        <!-- Not Checked In Yet -->
                        <form method="POST" action="{{ route('attendance.checkin') }}" class="space-y-3">

                        @csrf
                        <label class="block text-sm font-medium text-gray-700">
                            Check In Time
                        </label>

                        <input type="hidden" name="check_in" id="check_in_time">

                        <button type="submit"
                            onclick="return confirmCheckIn()"
                            class="bg-green-800 hover:bg-green-900 text-white px-6 py-2 rounded-md text-sm font-medium transition">
                            Submit Check In
                        </button>
                    </form>
                @endif
            </div>

            <!-- Check Out -->
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">
                    Check Out
                </h3>

                @if(!$todayAttendance || !$todayAttendance->check_in)
                    <!-- User has not checked in yet -->
                    <div class="text-sm text-gray-500">
                        Please check in first to be able to check out.
                    </div>

                @elseif($todayAttendance && $todayAttendance->check_out)
                    <!-- Already Checked Out -->
                    <div class="bg-red-50 border border-red-200 rounded-md p-4">
                        <p class="text-sm text-red-700 font-medium">
                        You have checked out
                        </p>
                        <p class="text-sm text-gray-700 mt-1">
                            Time:
                            <span class="font-semibold">
                                {{ \Carbon\Carbon::parse($todayAttendance->check_out)->format('h:i A') }}
                            </span>
                        </p>
                        <p class="text-sm text-gray-700">
                            Total Hours:
                            <span class="font-semibold">
                                {{ $todayAttendance->total_hours ?? '0' }} hrs
                            </span>
                        </p>
                    </div>

                @else
                    <!-- Not Checked Out Yet -->
                    <form method="POST" action="{{ route('attendance.checkout') }}" class="space-y-3">
                        @csrf

                        <label class="block text-sm font-medium text-gray-700">
                            Check Out Time
                        </label>

                        <input type="hidden" name="check_out" id="check_out_time">

                        <button type="submit"
                            onclick="return confirmCheckOut()"
                            class="bg-red-700 hover:bg-red-800 text-white px-6 py-2 rounded-md text-sm font-medium transition">
                            Submit Check Out
                        </button>

                    </form>
                @endif
            </div>


            <!-- Footer -->
            <div class="pt-4 border-t">
                <a href="{{ route('attendance.history') }}"
                   class="text-sm font-medium text-gray-700 hover:text-gray-900 underline">
                    View Attendance History
                </a>
            </div>
        </div>

        <!-- RIGHT: Calendar & Clock -->
        <div class="bg-white border border-gray-200 shadow-sm rounded-md p-6 space-y-6 text-center">

            <!-- Live Clock -->
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">Current Time</p>
                <p id="clock" class="text-3xl font-semibold text-gray-900"></p>
            </div>

            <!-- Date -->
            <div class="border-t pt-4">
                <p class="text-sm text-gray-500 uppercase tracking-wide">Today</p>
                <p id="date" class="text-lg font-medium text-gray-800"></p>
            </div>

        </div>
    </div>

    <!-- Real-time Clock Script -->
    <script>
        function updateClock() {
            const now = new Date();

            document.getElementById('clock').innerText =
                now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });

            document.getElementById('date').innerText =
                now.toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }

        function getCurrentTime() {
        const now = new Date();
        return now.toTimeString().slice(0, 5); // HH:mm
        }

        function confirmCheckIn() {
            document.getElementById('check_in_time').value = getCurrentTime();
            return confirm("Confirm check-in at current time?");
        }

        function confirmCheckOut() {
            document.getElementById('check_out_time').value = getCurrentTime();
            return confirm("Confirm check-out at current time?");
        }

        setInterval(updateClock, 1000);
        updateClock();
    </script>
</x-app-layout>
