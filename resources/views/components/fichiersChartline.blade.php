<!-- Card -->
<a href="{{ route('documents.index') }}"
    class="p-4 md:p-5 min-h-[200px] flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-900 dark:hover:bg-neutral-800 dark:border-neutral-700">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-sm text-gray-500 dark:text-neutral-400">
                Documents
            </h2>
            <p class="text-xl sm:text-2xl font-medium text-gray-800 dark:text-neutral-200">
                {{ $nbrDocuments }}
            </p>
        </div>

        <div>
            <span
                class="py-[5px] px-1.5 inline-flex items-center gap-x-1 text-xs font-medium rounded-md bg-teal-100 text-teal-800 dark:bg-teal-500/10 dark:text-teal-500">
                <svg class="inline-block size-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v13m0-13 4 4m-4-4-4 4" />
                </svg>
                {{ $nbrDocuments }} documents
            </span>
        </div>
    </div>
    <!-- End Header -->

    <div data-preset="fan" class="ldBar label-center" id="myItem1" data-value="{{ $nbrDocuments }}"></div>
</a>
<!-- End Card -->
