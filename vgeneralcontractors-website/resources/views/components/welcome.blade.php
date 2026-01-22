@props(['title' => 'Your Sales Insights', 'subtitle' => 'Track your performance and grow your business'])

<div style="background-color: #141414;" class="text-white min-h-screen">
    <!-- Dashboard Content -->
    <div class="p-6">
        <!-- Dashboard Header -->
        <div class="mb-8 text-center sm:text-center md:text-left lg:text-left">
            <h2 class="text-base sm:text-base md:text-2xl lg:text-2xl font-bold text-white mb-2">{{ $title }}</h2>
            <p class="text-base sm:text-base md:text-base lg:text-base text-gray-400">{{ $subtitle }}</p>
        </div>

        <!-- Quick Action Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Create Appointment Card -->
            <a href="{{ route('appointments.create') }}"
                class="group relative overflow-hidden rounded-xl p-6 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-green-500/25 shadow-lg shadow-gray-900/50"
                style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(34, 197, 94, 0.05) 100%); border: 1px solid rgba(34, 197, 94, 0.2);">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div
                            class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center group-hover:bg-green-500/30 transition-colors duration-300">
                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3
                            class="text-sm font-semibold text-white group-hover:text-green-300 transition-colors duration-300">
                            {{ __('create_appointment') }}</h3>
                        <p class="text-xs text-gray-400 group-hover:text-gray-300 transition-colors duration-300">
                            {{ __('schedule_new_client_meeting') }}</p>
                    </div>
                </div>
                <div
                    class="absolute inset-0 bg-gradient-to-r from-green-500/0 via-green-500/5 to-green-500/0 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                </div>
            </a>

            <!-- Calendar View Card -->
            <a href="{{ route('appointment-calendar') }}"
                class="group relative overflow-hidden rounded-xl p-6 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-blue-500/25 shadow-lg shadow-gray-900/50"
                style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%); border: 1px solid rgba(59, 130, 246, 0.2);">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div
                            class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center group-hover:bg-blue-500/30 transition-colors duration-300">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3
                            class="text-sm font-semibold text-white group-hover:text-blue-300 transition-colors duration-300">
                            {{ __('calendar_view_dashboard') }}</h3>
                        <p class="text-xs text-gray-400 group-hover:text-gray-300 transition-colors duration-300">
                            {{ __('view_all_appointments') }}</p>
                    </div>
                </div>
                <div
                    class="absolute inset-0 bg-gradient-to-r from-blue-500/0 via-blue-500/5 to-blue-500/0 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                </div>
            </a>

            <!-- Create Post Card -->
            <a href="{{ route('posts-crud.index') }}"
                class="group relative overflow-hidden rounded-xl p-6 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-yellow-500/25 shadow-lg shadow-gray-900/50"
                style="background: linear-gradient(135deg, rgba(234, 179, 8, 0.1) 0%, rgba(234, 179, 8, 0.05) 100%); border: 1px solid rgba(234, 179, 8, 0.2);">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div
                            class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center group-hover:bg-yellow-500/30 transition-colors duration-300">
                            <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3
                            class="text-sm font-semibold text-white group-hover:text-yellow-300 transition-colors duration-300">
                            {{ __('manage_posts') }}</h3>
                        <p class="text-xs text-gray-400 group-hover:text-gray-300 transition-colors duration-300">
                            {{ __('create_edit_blog_posts') }}</p>
                    </div>
                </div>
                <div
                    class="absolute inset-0 bg-gradient-to-r from-yellow-500/0 via-yellow-500/5 to-yellow-500/0 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                </div>
            </a>

            <!-- View Portfolio Card -->
            <a href="{{ route('portfolios-crud.index') }}"
                class="group relative overflow-hidden rounded-xl p-6 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-purple-500/25 shadow-lg shadow-gray-900/50"
                style="background: linear-gradient(135deg, rgba(147, 51, 234, 0.1) 0%, rgba(147, 51, 234, 0.05) 100%); border: 1px solid rgba(147, 51, 234, 0.2);">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div
                            class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center group-hover:bg-purple-500/30 transition-colors duration-300">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3
                            class="text-sm font-semibold text-white group-hover:text-purple-300 transition-colors duration-300">
                            {{ __('portfolio_dashboard') }}</h3>
                        <p class="text-xs text-gray-400 group-hover:text-gray-300 transition-colors duration-300">
                            {{ __('manage_project_gallery') }}</p>
                    </div>
                </div>
                <div
                    class="absolute inset-0 bg-gradient-to-r from-purple-500/0 via-purple-500/5 to-purple-500/0 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                </div>
            </a>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Pending Appointments Card -->
            <div style="background-color: #2C2E36;"
                class="rounded-lg p-6 border border-gray-900 shadow-lg shadow-gray-900/50">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-gray-300 text-sm font-medium">{{ __('pending_appointments') }}</h3>
                    <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mb-4">
                    @php
                        $pendingAppointments = \App\Models\Appointment::where('inspection_status', 'Pending')->count();
                    @endphp
                    <div class="text-3xl font-bold text-white mb-1">{{ $pendingAppointments }}</div>
                    <div class="flex items-center text-sm">
                        <span class="text-gray-400">{{ __('awaiting_confirmation') }}</span>
                    </div>
                </div>
                <div class="flex items-center justify-center h-16">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-orange-500/20 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Leads Card -->
            <div style="background-color: #2C2E36;"
                class="rounded-lg p-6 border border-gray-900 shadow-lg shadow-gray-900/50">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-gray-300 text-sm font-medium">{{ __('new_leads') }}</h3>
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
                <div class="mb-4">
                    @php
                        $newLeads = \App\Models\Appointment::where('status_lead', 'New')->count();
                    @endphp
                    <div class="text-3xl font-bold text-white mb-1">{{ $newLeads }}</div>
                    <div class="flex items-center text-sm">
                        <span class="text-gray-400">{{ __('require_follow_up') }}</span>
                    </div>
                </div>
                <div class="flex items-center justify-center h-16">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-green-500/20 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Published Posts Card -->
            <div style="background-color: #2C2E36;"
                class="rounded-lg p-6 border border-gray-900 shadow-lg shadow-gray-900/50">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-gray-300 text-sm font-medium">{{ __('published_posts') }}</h3>
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                </div>
                <div class="mb-4">
                    @php
                        $publishedPosts = \App\Models\Post::where('post_status', 'published')->count();
                    @endphp
                    <div class="text-3xl font-bold text-white mb-1">{{ $publishedPosts }}</div>
                    <div class="flex items-center text-sm">
                        <span class="text-gray-400">{{ __('blog_articles_live') }}</span>
                    </div>
                </div>
                <div class="flex items-center justify-center h-16">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Monthly Leads Chart -->
            <div style="background-color: #2C2E36;"
                class="rounded-lg p-6 border border-gray-900 shadow-lg shadow-gray-900/50">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-white">{{ __('monthly_leads') }}</h3>
                    <div class="text-xs text-gray-400">{{ date('Y') }}</div>
                </div>

                @php
                    $currentYear = date('Y');
                    $monthlyData = [];
                    $maxLeads = 0;

                    // Get leads count for each month of current year
                    for ($month = 1; $month <= 12; $month++) {
                        $leadsCount = \App\Models\Appointment::whereYear('created_at', $currentYear)
                            ->whereMonth('created_at', $month)
                            ->count();

                        $monthlyData[] = [
                            'month' => $month,
                            'count' => $leadsCount,
                            'name' => date('M', mktime(0, 0, 0, $month, 1)),
                        ];

                        if ($leadsCount > $maxLeads) {
                            $maxLeads = $leadsCount;
                        }
                    }

                    // Calculate heights as percentages
                    foreach ($monthlyData as &$data) {
                        $data['height'] = $maxLeads > 0 ? ($data['count'] / $maxLeads) * 100 : 0;
                    }
                @endphp

                <!-- Chart Area -->
                <div class="h-64 bg-gray-900 rounded-lg flex items-end justify-between p-4 relative">
                    <!-- Y-axis labels -->
                    <div class="absolute left-2 top-4 bottom-4 flex flex-col justify-between text-xs text-gray-500">
                        <span>{{ $maxLeads }}</span>
                        <span>{{ intval($maxLeads * 0.75) }}</span>
                        <span>{{ intval($maxLeads * 0.5) }}</span>
                        <span>{{ intval($maxLeads * 0.25) }}</span>
                        <span>0</span>
                    </div>

                    <!-- Bar Chart with Real Data -->
                    <div class="flex items-end justify-between w-full ml-8 space-x-1">
                        @foreach ($monthlyData as $data)
                            <div class="flex flex-col items-center group relative">
                                <div class="bg-yellow-500 rounded-t transition-all duration-500 hover:bg-yellow-400 cursor-pointer"
                                    style="height: {{ $data['height'] }}%; width: 20px; min-height: 2px;"
                                    title="{{ $data['name'] }}: {{ $data['count'] }} {{ __('leads') }}">
                                </div>

                                <!-- Tooltip on hover -->
                                <div
                                    class="absolute bottom-full mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap z-10">
                                    {{ $data['count'] }} {{ __('leads') }}
                                    <div
                                        class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-800">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Month labels -->
                <div class="flex justify-between text-xs text-gray-400 mt-4 ml-8">
                    @foreach ($monthlyData as $data)
                        <span class="text-center" style="width: 20px;">{{ $data['name'] }}</span>
                    @endforeach
                </div>

                <!-- Summary stats -->
                <div class="mt-4 pt-4 border-t border-gray-700">
                    <div class="flex justify-between text-sm">
                        <div class="text-gray-400">
                            {{ __('total_leads') }}: <span
                                class="text-white font-semibold">{{ array_sum(array_column($monthlyData, 'count')) }}</span>
                        </div>
                        <div class="text-gray-400">
                            {{ __('peak_month') }}: <span class="text-yellow-400 font-semibold">
                                @php
                                    $peakMonth = collect($monthlyData)->sortByDesc('count')->first();
                                    echo $peakMonth['name'] . ' (' . $peakMonth['count'] . ')';
                                @endphp
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lead Sources Analytics -->
            <div style="background-color: #2C2E36;"
                class="rounded-lg p-6 border border-gray-900 shadow-lg shadow-gray-900/50">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-white">{{ __('lead_sources') }}</h3>
                    <div class="text-xs text-gray-400">{{ __('current_year') }}</div>
                </div>

                @php
                    $leadSources = \App\Models\Appointment::selectRaw('lead_source, COUNT(*) as count')
                        ->whereYear('created_at', date('Y'))
                        ->whereNotNull('lead_source')
                        ->groupBy('lead_source')
                        ->get();

                    $totalLeads = $leadSources->sum('count');
                    $sourceData = [];
                    $colors = [
                        'Website' => ['bg' => 'bg-blue-500', 'text' => 'text-blue-400'],
                        'Facebook Ads' => ['bg' => 'bg-green-500', 'text' => 'text-green-400'],
                        'Retell AI' => ['bg' => 'bg-purple-500', 'text' => 'text-purple-400'],
                        'Reference' => ['bg' => 'bg-yellow-500', 'text' => 'text-yellow-400'],
                    ];

                    foreach ($leadSources as $source) {
                        $percentage = $totalLeads > 0 ? round(($source->count / $totalLeads) * 100, 1) : 0;
                        $sourceData[] = [
                            'name' => $source->lead_source,
                            'count' => $source->count,
                            'percentage' => $percentage,
                            'color' => $colors[$source->lead_source] ?? [
                                'bg' => 'bg-gray-500',
                                'text' => 'text-gray-400',
                            ],
                        ];
                    }

                    // Calculate stroke-dasharray values for donut chart
                    $circumference = 2 * pi() * 15.9155;
                    $currentOffset = 0;
                @endphp

                <!-- Donut Chart with Real Data -->
                <div class="flex items-center justify-center mb-6">
                    <div class="relative w-56 h-56">
                        <svg class="w-56 h-56 transform -rotate-90" viewBox="0 0 36 36">
                            <!-- Background circle -->
                            <path class="text-gray-900" stroke="currentColor" stroke-width="4" fill="none"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />

                            @foreach ($sourceData as $index => $data)
                                @php
                                    $strokeLength = ($data['percentage'] / 100) * $circumference;
                                    $strokeDasharray = $strokeLength . ', ' . $circumference;
                                    $strokeDashoffset = -$currentOffset;
                                    $currentOffset += $strokeLength;

                                    $colorClass = match ($data['name']) {
                                        'Website' => 'text-blue-500',
                                        'Facebook Ads' => 'text-green-500',
                                        'Retell AI' => 'text-purple-500',
                                        'Reference' => 'text-yellow-500',
                                        default => 'text-gray-500',
                                    };
                                @endphp

                                <path class="{{ $colorClass }}" stroke="currentColor" stroke-width="4"
                                    fill="none" stroke-dasharray="{{ $strokeDasharray }}"
                                    stroke-dashoffset="{{ $strokeDashoffset }}"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                    style="transition: stroke-dasharray 1s ease-in-out;" />
                            @endforeach
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-white">{{ $totalLeads }}</div>
                                <div class="text-sm text-gray-400">{{ __('total') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Legend with Real Data -->
                <div class="space-y-3">
                    @forelse($sourceData as $data)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 {{ $data['color']['bg'] }} rounded-full mr-3"></div>
                                <span class="text-sm text-gray-300">{{ $data['name'] }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-semibold text-white">{{ $data['percentage'] }}%</span>
                                <div class="text-xs {{ $data['color']['text'] }}">{{ $data['count'] }}
                                    {{ __('leads') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-400 text-sm">{{ __('no_lead_source_data') }}</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Activity - Live Leads Marquee -->
        <div class="w-full rounded-lg border border-gray-900 shadow-lg shadow-gray-900/50"
            style="background-color: #2C2E36;">
            <div class="px-6 py-4 border-b border-gray-900">
                <h3 class="text-lg font-semibold text-white">{{ __('recent_leads_activity') }}</h3>
                <p class="text-sm text-gray-400">{{ __('live_updates_latest_appointments') }}</p>
            </div>
            <div class="relative h-80 overflow-hidden">
                @php
                    $recentLeads = \App\Models\Appointment::orderBy('created_at', 'desc')->take(20)->get();
                @endphp

                <!-- Scrolling Container -->
                <div class="absolute inset-0 animate-marquee-vertical">
                    <div class="space-y-4 p-6">
                        @foreach ($recentLeads as $lead)
                            <div
                                class="flex items-center space-x-4 bg-gray-800/50 rounded-lg p-4 border border-gray-700/50">
                                <!-- Status Icon -->
                                <div
                                    class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                                    @if ($lead->status_lead === 'New') bg-blue-500
                                    @elseif($lead->status_lead === 'Called') bg-green-500
                                    @elseif($lead->status_lead === 'Pending') bg-yellow-500
                                    @elseif($lead->status_lead === 'Declined') bg-red-500
                                    @else bg-gray-500 @endif">
                                    @if ($lead->status_lead === 'New')
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    @elseif($lead->status_lead === 'Called')
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    @elseif($lead->status_lead === 'Pending')
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @elseif($lead->status_lead === 'Declined')
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @endif
                                </div>

                                <!-- Lead Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <p class="text-sm font-medium text-white truncate">
                                            {{ $lead->full_name }}
                                        </p>
                                        <span
                                            class="text-xs px-2 py-1 rounded-full
                                            @if ($lead->status_lead === 'New') bg-blue-500/20 text-blue-400
                                            @elseif($lead->status_lead === 'Called') bg-green-500/20 text-green-400
                                            @elseif($lead->status_lead === 'Pending') bg-yellow-500/20 text-yellow-400
                                            @elseif($lead->status_lead === 'Declined') bg-red-500/20 text-red-400
                                            @else bg-gray-500/20 text-gray-400 @endif">
                                            {{ $lead->status_lead ?? 'Unknown' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs text-gray-400 truncate">
                                            {{ $lead->city }}, {{ $lead->state }} • {{ $lead->phone }}
                                        </p>
                                        <p class="text-xs text-gray-500 flex-shrink-0 ml-2">
                                            {{ $lead->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    @if ($lead->lead_source)
                                        <div class="mt-1">
                                            <span
                                                class="text-xs px-2 py-0.5 rounded font-medium
                                                @if ($lead->lead_source === 'Website') bg-blue-500/20 text-blue-400 border border-blue-500/30
                                                @elseif($lead->lead_source === 'Facebook Ads') bg-indigo-500/20 text-indigo-400 border border-indigo-500/30
                                                @elseif($lead->lead_source === 'Retell AI') bg-purple-500/20 text-purple-400 border border-purple-500/30
                                                @elseif($lead->lead_source === 'Reference') bg-emerald-500/20 text-emerald-400 border border-emerald-500/30
                                                @else bg-gray-700 text-gray-300 border border-gray-600 @endif">
                                                @if ($lead->lead_source === 'Website')
                                                    🌐
                                                @elseif($lead->lead_source === 'Facebook Ads')
                                                    📱
                                                @elseif($lead->lead_source === 'Retell AI')
                                                    🤖
                                                @elseif($lead->lead_source === 'Reference')
                                                    👥
                                                @endif
                                                {{ $lead->lead_source }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <!-- Duplicate content for seamless loop -->
                        @foreach ($recentLeads as $lead)
                            <div
                                class="flex items-center space-x-4 bg-gray-800/50 rounded-lg p-4 border border-gray-700/50">
                                <!-- Status Icon -->
                                <div
                                    class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                                    @if ($lead->status_lead === 'New') bg-blue-500
                                    @elseif($lead->status_lead === 'Called') bg-green-500
                                    @elseif($lead->status_lead === 'Pending') bg-yellow-500
                                    @elseif($lead->status_lead === 'Declined') bg-red-500
                                    @else bg-gray-500 @endif">
                                    @if ($lead->status_lead === 'New')
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    @elseif($lead->status_lead === 'Called')
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    @elseif($lead->status_lead === 'Pending')
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @elseif($lead->status_lead === 'Declined')
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @endif
                                </div>

                                <!-- Lead Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <p class="text-sm font-medium text-white truncate">
                                            {{ $lead->full_name }}
                                        </p>
                                        <span
                                            class="text-xs px-2 py-1 rounded-full
                                            @if ($lead->status_lead === 'New') bg-blue-500/20 text-blue-400
                                            @elseif($lead->status_lead === 'Called') bg-green-500/20 text-green-400
                                            @elseif($lead->status_lead === 'Pending') bg-yellow-500/20 text-yellow-400
                                            @elseif($lead->status_lead === 'Declined') bg-red-500/20 text-red-400
                                            @else bg-gray-500/20 text-gray-400 @endif">
                                            {{ $lead->status_lead ?? 'Unknown' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs text-gray-400 truncate">
                                            {{ $lead->city }}, {{ $lead->state }} • {{ $lead->phone }}
                                        </p>
                                        <p class="text-xs text-gray-500 flex-shrink-0 ml-2">
                                            {{ $lead->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    @if ($lead->lead_source)
                                        <div class="mt-1">
                                            <span
                                                class="text-xs px-2 py-0.5 rounded font-medium
                                                @if ($lead->lead_source === 'Website') bg-blue-500/20 text-blue-400 border border-blue-500/30
                                                @elseif($lead->lead_source === 'Facebook Ads') bg-indigo-500/20 text-indigo-400 border border-indigo-500/30
                                                @elseif($lead->lead_source === 'Retell AI') bg-purple-500/20 text-purple-400 border border-purple-500/30
                                                @elseif($lead->lead_source === 'Reference') bg-emerald-500/20 text-emerald-400 border border-emerald-500/30
                                                @else bg-gray-700 text-gray-300 border border-gray-600 @endif">
                                                @if ($lead->lead_source === 'Website')
                                                    🌐
                                                @elseif($lead->lead_source === 'Facebook Ads')
                                                    📱
                                                @elseif($lead->lead_source === 'Retell AI')
                                                    🤖
                                                @elseif($lead->lead_source === 'Reference')
                                                    👥
                                                @endif
                                                {{ $lead->lead_source }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Gradient Overlays for fade effect -->
                <div
                    class="absolute top-0 left-0 right-0 h-8 bg-gradient-to-b from-gray-800 to-transparent pointer-events-none z-10">
                </div>
                <div
                    class="absolute bottom-0 left-0 right-0 h-8 bg-gradient-to-t from-gray-800 to-transparent pointer-events-none z-10">
                </div>
            </div>
        </div>

        <!-- Custom CSS for vertical marquee animation -->
        <style>
            @keyframes marquee-vertical {
                0% {
                    transform: translateY(100%);
                }

                100% {
                    transform: translateY(-100%);
                }
            }

            .animate-marquee-vertical {
                animation: marquee-vertical 60s linear infinite;
            }

            .animate-marquee-vertical:hover {
                animation-play-state: paused;
            }
        </style>
    </div>
</div>
