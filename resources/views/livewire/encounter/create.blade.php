<div>
    <x-section-navigation>
        <x-slot name="title">{{ __('patients.encounter_create') }}</x-slot>
    </x-section-navigation>

    <section class="bg-white dark:bg-gray-800">
        <div class="py-8 px-4 mx-auto md:max-w-6xl lg:py-16">
            <form action="#">
                <div class="p-4 sm:p-8 border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                    <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">
                        Загальна інформація
                    </h2>
                    <div class="grid gap-4 md:gap-6 lg:grid-cols-4 md:grid-cols-2">
                        <!-- Column -->
                        <div class="relative z-0 w-full mb-5 mt-5 group">
                            <input type="text"
                                   name="name"
                                   id="name"
                                   class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                   placeholder=" "
                                   value="Безшейко Віталій Григорович"
                                   required
                            />
                            <label
                                for="name"
                                class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6"
                            >
                                Email address
                            </label>
                        </div>
                        <div>
                            <label for="last-name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Last Name</label>
                            <input type="text" name="last-name" id="last-name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Last Name" required="">
                        </div>
                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                            <input type="email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="name@company.com" required="">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
    <div class="inline-block min-w-full align-middle">
        <x-forms.forms-section submit="store">
            <x-slot name='form'>
            </x-slot>
        </x-forms.forms-section>
    </div>



    <x-forms.loading/>
</div>
