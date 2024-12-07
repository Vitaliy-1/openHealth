<div {{ $attributes->merge(['class' => 'flex flex-col h-screen']) }}>
    <div class="overflow-x-auto">
        <div class="inline-block min-w-full align-middle">
            <div class="overflow-hidden shadow pb-[50px]">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
<x-forms.loading/>
