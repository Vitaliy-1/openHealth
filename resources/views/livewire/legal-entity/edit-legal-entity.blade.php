<div>


    <x-section-navigation x-data="{ showFilter: false }" class="">
        <x-slot name="title">{{ __('Редагувати заклад ') }}</x-slot>
    </x-section-navigation>
    <div
        class="p-4 mb-4 bg-white border border-gray-200  shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">
        <x-forms.forms-section submit="" x-data="{
                    init() {
                        Livewire.hook('commit', ({ succeed }) => {
                                    succeed(() => {
                                        this.$nextTick(() => {
                                            const firstErrorMessage = document.querySelector('.error-message')
                                            if (firstErrorMessage !== null) {
                                                firstErrorMessage.scrollIntoView({ block: 'center', inline: 'center' })
                                            }
                                        })
                                    })
                                })
                            }
                }">
            <x-slot name="form">
                <div class="grid-cols-1">
                    <div class="p-6.5">
                        @foreach($steps as $key=>$view)
                            <div
                                class="w-full mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{$view['title']}}</h5>
                                @include('livewire.legal-entity.step.'.$view['view'])
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6 flex flex-col gap-6 xl:flex-row justify-end	items-center ">
                        <div class="xl:w-1/4  flex justify-end">
                            <x-button type="click" class="default-button w-full" wire:click="updateLegalEntity">
                                {{__('forms.send_request')}}
                            </x-button>
                        </div>
                    </div>

                </div>
            </x-slot>
        </x-forms.forms-section>
    </div>

    <x-forms.loading/>

</div>
