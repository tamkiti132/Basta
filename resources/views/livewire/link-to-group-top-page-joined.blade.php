<div class="relative w-1/2 lg:w-1/3">
    @if ($user_groups->first())
    <div
        class="overflow-hidden text-xs truncate bg-transparent border-none w-28 sm:w-40 sm:text-base whitespace-nowrap">
        <button onclick="window.location.href = '/group/top/' + document.getElementById('group-select').value"
            class="w-full h-full text-left bg-transparent border-none cursor-pointer">
            {{ $user_groups->firstWhere('id', session('group_id'))->name ?? $user_groups->first()->name
            }}
        </button>
    </div>
    <div class="absolute top-0 right-0 w-10 h-full overflow-hidden">
        <select id="group-select" name="group" class="w-full h-full bg-transparent border-none appearance-none"
            onchange="window.location.href = '/group/top/' + this.value">
            @foreach($user_groups as $group)
            <option value="{{ $group->id }}" {{ (session('group_id')==$group->id) ? 'selected' : '' }}>
                {{ $group->name }}
            </option>
            @endforeach
        </select>
    </div>
    @endif
</div>