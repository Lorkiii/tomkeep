@props([
    'active' => '',
    'items' => null,
    'user' => null,
])

@php
    // Backend can pass :items from Livewire/controller, or use config
    $menuItems = $items ?? config('sidebar.items', []);
@endphp

<aside
    {{ $attributes->merge([
        'class' => 'fixed left-0 top-9 z-20 flex h-[calc(100vh-2.25rem)] w-64 flex-col border-r border-[#2f56b0] bg-[#2f56b0] text-white',
    ]) }}
>
    <div class="flex flex-1 flex-col overflow-y-auto p-4">
        {{-- Profile block (optional) --}}
        @if($user)
            <div class="flex flex-col items-center pb-6">
                <div class="h-20 w-20 rounded-full border-2 border-white bg-white/10"></div>
                <p class="mt-3 text-center text-sm font-medium text-white">
                    {{ trim(($user['first_name'] ?? '') . ' ' . ($user['middle_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: 'User' }}
                </p>
                <p class="mt-0.5 max-w-full truncate text-center text-xs text-white/90">
                    {{ $user['email'] ?? '' }}
                </p>
            </div>
        @endif

        {{-- Nav: one root list, backend-driven --}}
        <nav class="flex-1" aria-label="Sidebar navigation">
            <ul class="space-y-0">
                @foreach($menuItems as $item)
                    @php
                        $itemKey = $item['key'] ?? null;
                        $label = $item['label'] ?? '';
                        $routeName = $item['route'] ?? null;
                        $url = $routeName ? route($routeName) : ($item['url'] ?? '#');
                        $isActive = $active && $itemKey && $active === $itemKey;
                        $children = $item['children'] ?? [];
                    @endphp
                    @if($label && ($routeName || ($item['url'] ?? null)))
                        <li>
                            <a
                                href="{{ $url }}"
                                wire:navigate
                                class="block border-b border-gray-400/40 px-3 py-3 text-sm font-medium text-white transition {{ $isActive ? 'bg-amber-400/90 text-slate-900' : 'hover:bg-white/10' }}"
                            >
                                {{ $label }}
                            </a>
                            @if(count($children) > 0)
                                <ul class="border-b border-gray-400/20 bg-black/10">
                                    @foreach($children as $child)
                                        @php
                                            $childKey = $child['key'] ?? null;
                                            $childLabel = $child['label'] ?? '';
                                            $childRoute = $child['route'] ?? null;
                                            $childUrl = $childRoute ? route($childRoute) : ($child['url'] ?? '#');
                                            $isChildActive = $active && $childKey && $active === $childKey;
                                        @endphp
                                        @if($childLabel && ($childRoute || ($child['url'] ?? null)))
                                            <li>
                                                <a
                                                    href="{{ $childUrl }}"
                                                    wire:navigate
                                                    class="block px-3 py-2 pl-6 text-sm text-white/90 transition {{ $isChildActive ? 'bg-amber-400/80 text-slate-900' : 'hover:bg-white/10' }}"
                                                >
                                                    {{ $childLabel }}
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endif
                @endforeach
            </ul>
        </nav>

        {{-- Footer slot: Log Out button or custom actions --}}
        @if(isset($footer))
            <div class="pt-4">
                {{ $footer }}
            </div>
        @endif
    </div>
</aside>
