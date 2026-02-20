<x-app-layout>
    <div class="max-w-8xl mx-auto space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">My Leaves</h1>
            <a href="{{ route('intern.leave.create') }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
               Add Leave
            </a>
        </div>

        @php
            $alPercent = $alTotal > 0 ? ($alRemaining/ $alTotal) * 100 : 0;
        @endphp

        <!-- Annual Leave Balance -->
        <div class="w-full md:w-1/2">
            <div class=" bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-m font-semibold text-gray-700 uppercase tracking-wide">
                        Annual Leave Balance
                    </h2>
                </div>

                <div class="flex items-end justify-between mb-4">
                    <div>
                        <p class="text-3xl font-semibold text-gray-900">
                            {{ $alRemaining }}
                            <span class="text-sm font-medium text-gray-700">days remaining</span>
                        </p>
                        <p class="text-xs text-gray-700 mt-1">
                            Total entitlement: {{ $alTotal }} days
                        </p>
                    </div>
                </div>

                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                    <div
                        class="bg-gray-600 h-2 transition-all duration-500"
                        style="width: {{ $alPercent }}%">
                    </div>
                </div>
            </div>
        </div>

        <!-- Leave Table -->
        <div class="bg-white rounded-xl shadow p-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Leave Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Half Day</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Reason</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Submitted on</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700"></th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Export PDF</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($leaves as $leave)
                        <tr>
                            <td class="px-6 py-4">{{ \Carbon\Carbon::parse($leave->leave_date)->format('d-M-Y') }}</td>
                            <td class="px-6 py-4">{{ $leave->leaveType->name }} ({{ $leave->leaveType->code }})</td>
                            <td class="px-6 py-4">{{ strtoupper($leave->half_day) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    @if($leave->status === 'pending')
                                        bg-yellow-100 text-yellow-800
                                    @elseif($leave->status === 'approved')
                                        bg-green-100 text-green-800
                                    @elseif($leave->status === 'rejected')
                                        bg-red-100 text-red-800
                                    @endif
                                ">
                                    {{ ucfirst($leave->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ $leave->reason ?? '-' }}</td>
                            <td class="px-6 py-4 text-xs">{{ $leave->created_at }}</td>
                            
                            <!-- Action Column -->
                            <td class="px-6 py-4 text-center">
                                    <form action="{{ route('intern.leave.destroy', $leave) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this leave?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 text-xs font-semibold rounded bg-red-600 hover:bg-red-700 text-white transition">
                                            Cancel
                                        </button>
                                    </form>
                                    <span class="text-gray-400 italic text-xs">-</span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if($leave->status === 'approved')
                                    <a href="{{ route('intern.leave.pdf', $leave->id) }}" 
                                    target="_blank"
                                    class="text-green-600 hover:text-green-800 transition text-lg">
                                        
                                        <i class="fas fa-download"></i>
                                        <span class="sr-only">Download PDF</span>
                                    </a>
                                @else
                                    <span class="text-gray-400 italic text-xs">-</span>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-400">
                                No leave records yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
