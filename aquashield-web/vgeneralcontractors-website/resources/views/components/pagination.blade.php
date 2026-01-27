@props(['paginator'])

<div class="mt-4 flex flex-col sm:flex-row justify-between items-center space-y-3 sm:space-y-0">
    <div class="text-sm text-white/80 w-full sm:w-auto text-center sm:text-left backdrop-blur-sm bg-black/30 px-4 py-2 rounded-lg border border-white/10 shadow-sm shadow-purple-500/10">
        Showing {{ $paginator->firstItem() ?? 0 }} to {{ $paginator->lastItem() ?? 0 }} of
        {{ $paginator->total() }} entries
    </div>
    <div class="w-full sm:w-auto flex justify-center sm:justify-end">
        <div class="backdrop-blur-sm bg-black/30 px-2 py-1 rounded-lg border border-white/10 shadow-sm shadow-purple-500/10">
            {{ $paginator->links() }}
        </div>
    </div>
</div>
