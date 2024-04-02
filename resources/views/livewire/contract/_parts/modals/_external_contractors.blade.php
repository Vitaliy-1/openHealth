<x-dialog-modal  maxWidth="3xl" class="w-3 h-full" wire:model.live="showModal">
    <x-slot name="title">
        {{__('forms.external_contractors')}}
    </x-slot>
    <x-slot name="content">
        <x-forms.forms-section-modal submit="">
            <x-slot name="form">
                <div class="mb-4.5 flex flex-col gap-6   xl:flex-row">
                    <x-forms.form-group x-data="{ open: false }" class="xl:w-1/2">
                        <x-slot name="label">
                            <x-forms.label for="documents_issued_by" class="default-label">
                                {{__('forms.edrpou')}}
                            </x-forms.label>
                        </x-slot>
                        <x-slot  name="input">
                            <div x-on:mouseleave="timeout = setTimeout(() => { open = false }, 300)">


                                <x-forms.input  class="default-input" wire:model.live="legalEntity_search.text" type="text"
                                                wire:keyup="getLegalEntityApi; open = true"
                                                id=""/>


                                <div x-show="open" x-ref="dropdown" wire:target="getLegalEntityApi">
                                <div
                                    class="z-10 max-h-96 overflow-auto w-full	 absolute  bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                                        aria-labelledby="dropdownHoverButton">
                                        @if($legalEntityApi)
                                            @foreach($legalEntityApi as $legalEntity)
                                                <li>
                                                    <a x-on:click.prevent="
                                            $wire.set('legalEntity_search.text', '{{$legalEntity['edr']['name']}}');
                                            $wire.set('legalEntity_search.edrpou', '{{$legalEntity['edr']['name']}}');
                                            open = false;"
                                                       href="#"
                                                       class="pointer
                                   block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                                        {{$legalEntity['edr']['name']}}
                                                    </a>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            </div>
                        </x-slot>
                        @error('legalEntity_search')
                        <x-slot name="error">
                            <x-forms.error>
                                {{$message}}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-forms.form-group>
                </div>
                <div wire:loading role="status"
                     class="absolute -translate-x-1/2 -translate-y-1/2 top-2/4 left-1/2">
                    <svg aria-hidden="true"
                         class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                         viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                            fill="currentColor"/>
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentFill"/>
                    </svg>
                </div>
            </x-slot>

        </x-forms.forms-section-modal>

    </x-slot>
</x-dialog-modal>




