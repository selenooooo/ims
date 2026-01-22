<x-app-layout>
    <div class="max-w-full mx-auto px-4 space-y-6">

        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Interns</h1>
                    <p class="mt-2 text-sm text-gray-600">Manage all registered interns and track their duration</p>
                </div>
                <a href="{{ route('supervisor.interns.create') }}"
                   class="inline-flex items-center px-4 py-3 bg-blue-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm">
                    <i class="fas fa-user-plus mr-2"></i>
                    Register New Intern
                </a>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg mr-4">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-blue-600">Total Interns</p>
                        <p class="text-2xl font-bold text-blue-900 mt-1">{{ $interns->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-xl p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg mr-4">
                        <i class="fas fa-user-check text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-green-600">Current</p>
                        <p class="text-2xl font-bold text-green-900 mt-1">{{ $activeTodayCount ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-50 to-purple-100 border border-purple-200 rounded-xl p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg mr-4">
                        <i class="fas fa-calendar-alt text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-purple-600">Old Interns</p>
                        <p class="text-2xl font-bold text-purple-900 mt-1">{{ $oldInterns ?? 0 }}</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Interns Table Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h2 class="text-lg font-semibold text-gray-900">Registered Interns</h2>
                    <div class="relative">
                        <input type="text" 
                               placeholder="Search interns..." 
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-64"
                               id="searchInput">
                        <i class="fas fa-search text-gray-400 absolute left-3 top-3"></i>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-user mr-1"></i> Intern
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-envelope mr-1"></i> Contact
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-calendar-day mr-1"></i> Date Added
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-hourglass-half mr-1"></i> Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-cogs mr-1"></i> View
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="internsTable">
                        @forelse ($interns as $intern)
                            @php
                                // Calculate duration from registration date
                                $registeredDate = $intern->created_at;
                                $today = now();
                                $totalDays = $registeredDate->diffInDays($today);
                                $totalMonths = $registeredDate->diffInMonths($today);
                                $remainingDays = $registeredDate->copy()->addMonths($totalMonths)->diffInDays($today);
                                
                                // Format duration display
                                $durationText = '';
                                if ($totalMonths > 0) {
                                    $durationText = $totalMonths . 'm';
                                    if ($remainingDays > 0) {
                                        $durationText .= ' ' . $remainingDays . 'd';
                                    }
                                } else {
                                    $durationText = $totalDays . 'd';
                                }
                                
                                // Determine badge color based on duration
                                if ($totalMonths >= 6) {
                                    $durationClass = 'bg-purple-100 text-purple-800 border-purple-200';
                                    $durationIcon = 'fas fa-user-tie';
                                } elseif ($totalMonths >= 3) {
                                    $durationClass = 'bg-green-100 text-green-800 border-green-200';
                                    $durationIcon = 'fas fa-user-graduate';
                                } elseif ($totalMonths >= 1) {
                                    $durationClass = 'bg-blue-100 text-blue-800 border-blue-200';
                                    $durationIcon = 'fas fa-user-clock';
                                } else {
                                    $durationClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                                    $durationIcon = 'fas fa-user-plus';
                                }
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold">
                                                <i class="fas fa-user text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $intern->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <i class="fas fa-id-badge mr-1"></i> ID: {{ $intern->employee_id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <i class="fas fa-envelope mr-2 text-gray-400"></i>{{ $intern->email }}
                                    </div>
                                    <!-- <div class="text-sm text-gray-500 mt-1">
                                        <i class="fas fa-phone mr-2 text-gray-400"></i>
                                        @if($intern->phone)
                                            {{ $intern->phone }}
                                        @else
                                            <span class="text-gray-400">No phone</span>
                                        @endif
                                    </div> -->
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-medium">
                                        <i class="far fa-calendar mr-2 text-gray-400"></i>{{ $registeredDate->format('d M Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <i class="far fa-clock mr-1 text-gray-400"></i>{{ $registeredDate->diffForHumans() }}
                                    </div>
                                </td>
                                <td>
                                    <span @class([
                                        'px-3 py-1 rounded-full text-sm font-medium',
                                        'bg-green-100 text-green-800' => $intern->status === 'active',
                                        'bg-gray-100 text-gray-800' => $intern->status === 'completed',
                                    ])>
                                        {{ isset($intern->intern) ? ucfirst($intern->intern->status) : '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-4">
                                        <a href="javascript:void(0)"
                                            class="text-blue-600 hover:text-blue-900 transition-colors group view-intern-btn"
                                            data-id="{{ $intern->employee_id }}"
                                            data-name="{{ $intern->name }}"
                                            data-report="{{ $intern->intern ? $intern->intern->report_date : '' }}"
                                            data-duration="{{ $intern->intern ? $intern->intern->intern_duration : '' }}"
                                            data-end="{{ $intern->intern ? $intern->intern->end_date : '' }}"
                                            title="View Profile">
                                            <i class="fas fa-eye"></i>
                                            <span class="text-m ml-1 opacity-0 group-hover:opacity-100 transition-opacity"> View Intern </span>
                                        </a>

                                        <!-- <a href="#" 
                                           class="text-green-600 hover:text-green-900 transition-colors group"
                                           title="View Attendance">
                                            <i class="fas fa-history"></i>
                                            <span class="text-xs ml-1 opacity-0 group-hover:opacity-100 transition-opacity">Attendance</span>
                                        </a> -->
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="text-gray-400">
                                        <i class="fas fa-users text-5xl mb-4"></i>
                                        <h3 class="mt-4 text-lg font-medium text-gray-900">No interns registered</h3>
                                        <p class="mt-1 text-sm text-gray-500">Get started by registering a new intern.</p>
                                        <div class="mt-6">
                                            <a href="{{ route('supervisor.interns.create') }}"
                                               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-medium text-xs text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <i class="fas fa-user-plus mr-2"></i>
                                                Register First Intern
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            

            <!-- Pagination -->
            @if($interns->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $interns->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- Include Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @push('scripts')
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#internsTable tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        document.querySelectorAll('.view-intern-btn').forEach(btn => {
             btn.addEventListener('click', function () {
                openInternTab({
                    id: this.dataset.id,
                    name: this.dataset.name,
                    report: this.dataset.report,
                    duration: this.dataset.duration,
                    end: this.dataset.end,
                });
            });
        });

        function openEditModal() {
            document.getElementById('editInternModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editInternModal').classList.add('hidden');
        }

        function openInternTab(data) {
            // display values
            document.getElementById('tabEmployeeId').innerText = data.id; 
            document.getElementById('tabName').innerText = data.name;
            document.getElementById('tabReportDate').innerText = data.report ?? '-';
            document.getElementById('tabDuration').innerText = data.duration ? data.duration + ' month(s)' : '-';
            document.getElementById('tabEndDate').innerText = data.end ?? '-';

            // modal form values
            document.getElementById('modalReportDate').value = data.report ?? '';
            document.getElementById('modalDuration').value = data.duration ?? '';
            document.getElementById('modalEndDate').value = data.end ?? '';

            document.getElementById('editInternForm').action = `/supervisor/interns/${data.id}`;

            document.getElementById('internTab').classList.remove('translate-x-full');
        }

        function closeInternTab() {
            document.getElementById('internTab')
                .classList.add('translate-x-full');
        }

        function confirmDelete() {
            if (confirm('All data related to this intern will be deleted. Are you sure you want to delete this intern? ')) {
                document.getElementById('deleteInternForm').submit();
            }
        }

    </script>
    @endpush

    <style>
        /* Custom scrollbar styling */
        .overflow-x-auto {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
        }
        
        .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
        }
        
        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f7fafc;
            border-radius: 4px;
        }
        
        .overflow-x-auto::-webkit-scrollbar-thumb {
            background-color: #cbd5e0;
            border-radius: 4px;
        }
        
        /* Smooth transitions */
        tr {
            transition: background-color 0.15s ease-in-out;
        }
        
        /* Focus styles */
        input:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        /* Icon styling */
        .fa-xs {
            font-size: 0.75em;
        }
        
        /* Table header icons */
        th i {
            width: 16px;
            text-align: center;
        }
    </style>
    
        <!-- Intern Detail Tab -->
        <div id="internTab"
            class="fixed top-0 right-0 h-full w-full sm:w-96 bg-white shadow-2xl border-l border-gray-200 transform translate-x-full transition-transform duration-300 z-50">

            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">
                    Intern Details
                </h3>
                <button onclick="closeInternTab()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Content -->
            <div class="p-6 space-y-4 text-sm">
                <div>
                    <p class="text-gray-500">Employee ID</p>
                    <p class="font-semibold text-gray-900" id="tabEmployeeId">-</p>
                </div>

                <div>
                    <p class="text-gray-500">Name</p>
                    <p class="font-semibold text-gray-900" id="tabName">-</p>
                </div>

                <div>
                    <p class="text-gray-500">Report Date</p>
                    <p class="font-semibold text-gray-900" id="tabReportDate">-</p>
                </div>

                <div>
                    <p class="text-gray-500">Intern Duration</p>
                    <p class="font-semibold text-gray-900" id="tabDuration">-</p>
                </div>

                <div>
                    <p class="text-gray-500">End Date</p>
                    <p class="font-semibold text-gray-900" id="tabEndDate">-</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="px-6 py-4 border-t flex justify-between items-center bg-gray-50">
                <button
                    onclick="openEditModal()"
                    class="flex-1 bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600">
                    <i class="fas fa-edit mr-2"></i>Edit
                </button>

                <form id="deleteInternForm" method="POST" action="{{ route('supervisor.interns.destroy', $intern->id) }}" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="button"
                        onclick="confirmDelete()"
                        class="flex-1 bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </form>
            </div>
        </div>

        <!-- Edit Intern Modal -->
        <div id="editInternModal"
            class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center hidden z-50">

            <div class="bg-white rounded-xl shadow-lg w-full max-w-md">
                <!-- Header -->
                <div class="px-6 py-4 border-b flex justify-between items-center">
                    <h2 class="text-lg font-semibold">Edit Intern Details</h2>
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        ✕
                    </button>
                </div>

                <!-- Form -->
                <form id="editInternForm" method="POST" action="{{ route('supervisor.interns.update', $intern->employee_id) }}" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="text-sm text-gray-600">Report Date</label>
                        <input type="date" name="report_date" id="modalReportDate"
                            class="mt-1 w-full border rounded-md px-3 py-2 text-sm">
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Intern Duration (months)</label>
                        <input type="number" name="intern_duration" id="modalDuration"
                            class="mt-1 w-full border rounded-md px-3 py-2 text-sm">
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">End Date</label>
                        <input type="date" name="end_date" id="modalEndDate"
                            class="mt-1 w-full border rounded-md px-3 py-2 text-sm">
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3 pt-4">
                        <button type="submit"
                            class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            Save
                        </button>

                        <button type="button"
                            onclick="closeEditModal()"
                            class="flex-1 bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

</x-app-layout>