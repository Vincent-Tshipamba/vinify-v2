<x-layouts::app :title="__('Dashboard')">
    <div class="flex flex-col flex-1 gap-4 rounded-xl w-full h-full">
        <div class="">
        
            <h1 class="mb-2 font-light text-gray-800 dark:text-gray-200 text-3xl sm:text-4xl transition-all duration-300">
                Bienvenue, <span class="text-[#ff0]">{{ auth()->user()->name }}</span> 👋
            </h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm">
                Heureux de vous revoir sur <span class="font-semibold text-[#ff0]">Vinify</span>. N'oubliez pas de vérifier
                les derniers rapports ou documents.
            </p>
            {{-- <time>Mar 10, 2020</time> --}}
        </div>
        
        <div class="flex justify-between items-center mt-6">
            <div>
                <div class="ldBar" style="width:100%;height:60px" ,
                    data-stroke="data:ldbar/res,gradient(0,1,#9df,#9fd,#df9,#fd9)" ,
                    data-path="M10 20Q20 15 30 20Q40 25 50 20Q60 15 70 20Q80 25 90 20"></div>
            </div>
            <div class="flex flex-wrap items-start gap-6">
                <!-- Stat block -->
                @foreach ([['route' => 'users.index', 'permission' => 'manage users', 'title' => 'Users', 'value' => $users, 'icon' => 'users'], ['route' => 'analyses.index', 'permission' => 'view analyses', 'title' => 'Analyse', 'value' => $nbrAnalyses, 'icon' => 'analyse'], ['route' => 'documents.index', 'permission' => 'manage documents', 'title' => 'Fichiers', 'value' => $nbrDocuments, 'icon' => 'file']] as $stat)
                    <div class="flex flex-col items-start space-y-1 min-w-[120px]">

                        @can($stat['permission'])
                            <div class="flex items-center gap-2">
                                <a href="{{ route($stat['route']) }}"
                                    class="bg-white/10 dark:bg-neutral-800/50 backdrop-blur-md p-2 rounded-full text-gray-600 hover:text-yellow-500 dark:text-gray-300 transition"
                                    title="{{ $stat['title'] }}">

                                    @if ($stat['icon'] === 'users')
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                                d="M16 19h4a1 1 0 0 0 1-1v-1a3 3 0 0 0-3-3h-2m-2.236-4a3 3 0 1 0 0-4M3 18v-1a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1Zm8-10a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    @elseif($stat['icon'] === 'analyse')
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 3v4a1 1 0 0 1-1 1H5m8 7.5 2.5 2.5M19 4v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1Zm-5 9.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                                        </svg>
                                    @elseif($stat['icon'] === 'file')
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                                                d="M10 3v4a1 1 0 0 1-1 1H5m14-4v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1Z" />
                                        </svg>
                                    @endif
                                </a>
                                <span
                                    class="font-light text-gray-600 dark:text-gray-300 text-3xl sm:text-4xl">{{ $stat['value'] }}</span>
                            </div>
                            <span class="font-light text-gray-600 dark:text-gray-300 text-sm">{{ $stat['title'] }}</span>
                        @endcan
                    </div>
                @endforeach
            </div>
        </div>
        
        
        <div class="gap-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 mt-6">
        
            <!--
                <article
                    class="isolate relative flex flex-col justify-end rounded-2xl min-h-[410px] overflow-hidden">
        
                    {{-- <!-- Image --> --}}
            <img src="https://img.freepik.com/photos-gratuite/homme-noir-posant_23-2148171684.jpg" alt="User photo"
                class="absolute inset-0 w-full h-full object-cover">
        
            {{-- <!-- Gradient overlay pour améliorer lisibilité du bas --> --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>
        
            {{-- <!-- Conteneur avec blur en bas --> --}}
            <div class="z-10 relative bg-black/20 bg-opacity-40 backdrop-blur-sm p-4">
                <h3 class="font-light text-white text-3xl sm:text-4xl">
                    {{ auth()->user()->name }}
                </h3>
                <div class="text-gray-300 text-sm leading-6">Super Admin</div>
            </div>
            </article>
            -->
            @can('manage users')
                <x-userchartline :users="$users" />
            @endcan
            @can('view analyses')
                <x-analyseschartline :nbrAnalyses="$nbrAnalyses" :percentageCriticalPlagiarism="$percentageCriticalPlagiarism" />
            @endcan
            @can('manage documents')
                <x-fichiersChartline :nbrDocuments="$nbrDocuments" />
            @endcan
        </div>
    </div>
</x-layouts::app>
