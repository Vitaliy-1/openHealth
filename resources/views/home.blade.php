<!-- resources/views/home.blade.php -->
@extends('layouts.base')

@section('title', trans('oh.title'))
@section('description', trans('oh.description'))

@section('content')
<section class="bg-meta-10 sm:bg-image-1 bg-right-bottom bg-cover bg-no-repeat flex flex-col items-center justify-center lg:h-90vh h-auto sm:flex-row sm:justify-between md:p-12 p-6 pt-20 pb-20">
    <div class="container mx-auto max-w-custom flex flex-col sm:flex-row items-center justify-between">
        <div class="text-left w-full">
            <h2 class="text-white text-xl sm:text-1xl font-semibold mb-4">{{ trans('Медична інформаційна система') }}</h2>
            <h1 class="text-white text-4xl sm:text-6xl font-bold mb-10">{{ trans('OPEN HEALTH') }}</h1>
            <p class="text-white text-3lg sm:text-2xl font-semibold mb-9">{{ trans('Перша МІС з відкритим вихідним кодом') }}</p>
            <a href="#consultation-form" class="bg-orange text-white text-lg font-bold py-3 px-6 rounded-full hover:bg-blue">{{ trans('Зворотній зв\'язок') }}</a>
        </div>
    </div>
</section>

<!-- services-->
<section id="services" class="bg-gray-3 pt-20 sm:pt-30 pb-20 sm:pb-30 pl-5 pr-5">
    <div class="container mx-auto">
        <h2 class="text-black lg:text-4xl text-3xl font-semibold mb-10">{{ trans('Переваги') }}</h2>

        <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-y-4 gap-y-10">
            <!-- card 1 -->
            <div class="bg-white p-5 rounded-lg shadow-card hover:shadow-lg transition-all">
                <div class="wrapper-icon flex justify-center items-center bg-gray h-17 w-17 rounded-full mb-3">
                    <img src="{{ Vite::asset('resources/images/code.svg') }}" alt="{{ trans('Відкритий вихідний код') }}" class="icon w-10 h-10">
                </div>

                <h3 class="text-black text-lg font-semibold mb-2">
                    {{ trans('Відкритий вихідний код') }}
                    <span class="text-meta-10">→</span>
                </h3>
                <hr class="w-1/5 h-1 text-orange bg-orange mb-5">
                <p class="text-link text-md font-normal">{{ trans('Перша в Україні та світі медична інформаційна система із відкритим вихідним кодом та відкритою ліцензією GPL version 3') }}</p>
            </div>

            <!-- card 2 -->
            <div class="bg-white p-5 rounded-lg shadow-card hover:shadow-lg transition-all">
                <div class="wrapper-icon flex justify-center items-center bg-gray h-17 w-17 rounded-full mb-3">
                    <img src="{{ Vite::asset('resources/images/cloud.svg') }}" alt="{{ trans('Хмарні технології') }}" class="icon w-10 h-10">
                </div>

                <h3 class="text-black text-lg font-semibold mb-2">
                    {{ trans('Хмарні технології') }}
                    <span class="text-meta-10">→</span>
                </h3>
                <hr class="w-1/5 h-1 text-orange bg-orange mb-5">
                <p class="text-link text-md font-normal">{{ trans('Цілодобовий доступ з будь якого пристрою з максимальною безпекою') }}</p>
            </div>

            <!-- card 3 -->
            <div class="bg-white p-5 rounded-lg shadow-card hover:shadow-lg transition-all">
                <div class="wrapper-icon flex justify-center items-center bg-gray h-17 w-17 rounded-full mb-3">
                    <img src="{{ Vite::asset('resources/images/puzzle.svg') }}" alt="{{ trans('Інтеграція з ЕСОЗ') }}" class="icon w-10 h-10">
                </div>

                <h3 class="text-black text-lg font-semibold mb-2">
                    {{ trans('Інтеграція з ЕСОЗ') }}
                    <span class="text-meta-10">→</span>
                </h3>
                <hr class="w-1/5 h-1 text-orange bg-orange mb-5">
                <p class="text-link text-md font-normal">{{ trans('Максимально зручна робота з електронною системою охорони здоровʼя') }}</p>
            </div>

            <!-- card 4 -->
            <div class="bg-white p-5 rounded-lg shadow-card hover:shadow-lg transition-all">
                <div class="wrapper-icon flex justify-center items-center bg-gray h-17 w-17 rounded-full mb-3">
                    <img src="{{ Vite::asset('resources/images/touch.svg') }}" alt="{{ trans('Інтуїтивний інтерфейс') }}" class="icon w-10 h-10">
                </div>

                <h3 class="text-black text-lg font-semibold mb-2">
                    {{ trans('Інтуїтивний інтерфейс') }}
                    <span class="text-meta-10">→</span>
                </h3>
                <hr class="w-1/5 h-1 text-orange bg-orange mb-5">
                <p class="text-link text-md font-normal">{{ trans('Швидке навчання роботі у системи: тиждень на опанування всіх технічних можливостей') }}</p>
            </div>

            <!-- card 5 -->
            <div class="bg-white p-5 rounded-lg shadow-card hover:shadow-lg transition-all">
                <div class="wrapper-icon flex justify-center items-center bg-gray h-17 w-17 rounded-full mb-3">
                    <img src="{{ Vite::asset('resources/images/consultant.svg') }}" alt="{{ trans('Людяна служба підтримки') }}" class="icon w-10 h-10">
                </div>

                <h3 class="text-black text-lg font-semibold mb-2">
                    {{ trans('Людяна служба підтримки') }}
                    <span class="text-meta-10">→</span>
                </h3>
                <hr class="w-1/5 h-1 text-orange bg-orange mb-5">
                <p class="text-link text-md font-normal">{{ trans('Завжди з Вами на звʼязку, у чаті або за телефоном, готові вирішити проблеми') }}</p>
            </div>

            <!-- card 6 -->
            <div class="bg-white p-5 rounded-lg shadow-card hover:shadow-lg transition-all">
                <div class="wrapper-icon flex justify-center items-center bg-gray h-17 w-17 rounded-full mb-3">
                    <img src="{{ Vite::asset('resources/images/cart.svg') }}" alt="{{ trans('Телемедицина') }}" class="icon w-10 h-10">
                </div>

                <h3 class="text-black text-lg font-semibold mb-2">
                    {{ trans('Телемедицина') }}
                    <span class="text-meta-10">→</span>
                </h3>
                <hr class="w-1/5 h-1 text-orange bg-orange mb-5">
                <p class="text-link text-md font-normal">{{ trans('Використання сучасних технологій для комунікації між пацієнтом та лікарем') }}</p>
            </div>

            <!-- card 7 -->
            <div class="bg-white p-5 rounded-lg shadow-card hover:shadow-lg transition-all">
                <div class="wrapper-icon flex justify-center items-center bg-gray h-17 w-17 rounded-full mb-3">
                    <img src="{{ Vite::asset('resources/images/gpt.svg') }}" alt="{{ trans('Використання ШІ') }}" class="icon w-10 h-10">
                </div>

                <h3 class="text-black text-lg font-semibold mb-2">
                    {{ trans('Використання ШІ') }}
                    <span class="text-meta-10">→</span>
                </h3>
                <hr class="w-1/5 h-1 text-orange bg-orange mb-5">
                <p class="text-link text-md font-normal">{{ trans('Застосування технологій Deep Learning для комфортної роботи лікаря') }}</p>
            </div>

            <!-- card 8 -->
            <div class="bg-white p-5 rounded-lg shadow-card hover:shadow-lg transition-all">
                <div class="wrapper-icon flex justify-center items-center bg-gray h-17 w-17 rounded-full mb-3">
                    <img src="{{ Vite::asset('resources/images/safe.svg') }}" alt="{{ trans('Безпека та прозорість') }}" class="icon w-10 h-10">
                </div>

                <h3 class="text-black text-lg font-semibold mb-2">
                    {{ trans('Безпека та прозорість') }}
                    <span class="text-meta-10">→</span>
                </h3>
                <hr class="w-1/5 h-1 text-orange bg-orange mb-5">
                <p class="text-link text-md font-normal">{{ trans('Відкритий код перевіряється багатьма розробниками, які виправляють можливі помилки') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Автоматизація медичного бізнесу -->
<section class="lg:h-90vh h-auto bg-meta-10 sm:bg-image-2 bg-right-bottom bg-cover bg-no-repeat flex items-center pt-20 sm:pt-30 pb-20 sm:pb-20 pl-5 pr-5">
    <div class="md:container mx-auto">
        <h2 class="text-white text-3xl sm:text-4xl font-semibold mb-20">{{ trans('Автоматизація медичного бізнесу') }}</h2>

        <div class="w-full lg:w-3/5 grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 gap-2 md:gap-y-10">
            <!-- card 1 -->
            <div class="bg-transporant flex items-center text-white lg:text-2xl text-2xl">
                <div class="wrapper-icon bg-white flex justify-center items-center bg-gray lg:h-17 lg:w-17 md:h-11 md:w-11 h-11 w-13 rounded-full">
                    <img src="{{ Vite::asset('resources/images/cart.svg') }}" alt="{{ trans('Онлайн запис на прийом') }}" class="icon lg:w-10 lg:h-10 w-5 h-5">
                </div>

                <h3 class="w-full md:w-3/5 font-semibold pl-3">
                    {{ trans('Онлайн запис на прийом') }}
                </h3>
            </div>

            <!-- card 2 -->
            <div class="bg-transporant flex items-center text-white lg:text-2xl text-2xl">
                <div class="wrapper-icon bg-white flex justify-center items-center bg-gray lg:h-17 lg:w-17 md:h-11 md:w-11 h-11 w-13 rounded-full">
                    <img src="{{ Vite::asset('resources/images/health.svg') }}" alt="{{ trans('Управління бізнес-процесами') }}" class="icon lg:w-10 lg:h-10 w-5 h-5">
                </div>

                <h3 class="w-full md:w-3/5 font-semibold pl-3">
                    {{ trans('Управління бізнес-процесами') }}
                </h3>
            </div>

            <!-- card 3 -->
            <div class="bg-transporant flex items-center text-white lg:text-2xl text-2xl">
                <div class="wrapper-icon bg-white flex justify-center items-center bg-gray lg:h-17 lg:w-17 md:h-11 md:w-11 h-11 w-13 rounded-full">
                    <img src="{{ Vite::asset('resources/images/cv.svg') }}" alt="{{ trans('Електронна медична картка') }}" class="icon lg:w-10 lg:h-10 w-5 h-5">
                </div>

                <h3 class="w-full md:w-3/5 font-semibold pl-3">
                    {{ trans('Електронна медична картка') }}
                </h3>
            </div>

            <!-- card 4 -->
            <div class="bg-transporant flex items-center text-white lg:text-2xl text-2xl">
                <div class="wrapper-icon bg-white flex justify-center items-center bg-gray lg:h-17 lg:w-17 md:h-11 md:w-11 h-11 w-13 rounded-full">
                    <img src="{{ Vite::asset('resources/images/puzzle.svg') }}" alt="{{ trans('Інтеграція зі сторонніми сервісами') }}" class="icon lg:w-10 lg:h-10 w-5 h-5">
                </div>

                <h3 class="w-full md:w-3/5 font-semibold pl-3">
                    {{ trans('Інтеграція зі сторонніми сервісами') }}
                </h3>
            </div>
        </div>
    </div>
</section>

{{--<!-- our team --><section class="bg-gray-3 flex items-center justify-center md:py-30 py-15"></section>--}}

<!-- offers -->
<section id="offers" class="lg:h-90vh lg:h-auto h-auto bg-white flex pt-20 sm:pt-30 pb-20 sm:pb-20 pl-5 pr-5">
    <div class="md:container mx-auto">
        <h2 class="text-black text-center text-3xl sm:text-4xl font-bold mb-10">{{ trans('Індивідуальна розробка') }}</h2>
        <p class="text-link text-center text-3lg sm:text-xl font-semibold mb-15">
            {{ trans('Створимо інформаційне середовище під ваші потреби') }}
        </p>

        <div class="w-full grid grid-cols-1 sm:grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-10">
            <!-- card 1 -->
            <div class="bg-transporant mb-10">
                <div class="image bg-cover bg-image-3 bg-no-repeat w-full md:h-60 sm:h-70 h-50 rounded mb-4"></div>

                <h3 class="text-black lg:text-2xl text-xl font-semibold mb-4">
                    {{ trans('Інтеграція зі сторонніми сервісами') }}
                </h3>
                <p class="text-link text-lg font-normal">
                    {{ trans('Потрібно налаштувати специфічний бізнес-процес чи інтеграцію зі стороннім додатком? Легко! Наша команда розробників готова до складних викликів!') }}
                </p>
            </div>

            <!-- card 2 -->
            <div class="bg-transporant mb-10">
                <div class="image bg-cover bg-image-4 bg-no-repeat w-full md:h-60 sm:h-70 h-50 rounded mb-4"></div>

                <h3 class="text-black lg:text-2xl text-xl font-semibold mb-4">
                    {{ trans('Гнучка система модулів') }}
                </h3>
                <p class="text-link text-lg font-normal">
                    {{ trans('Велика бібліотека готових додатків, які дозволяють налаштувати бізнес-процес.') }}
                </p>
            </div>

            <!-- card 3 -->
            <div class="bg-meta-10 text-white rounded mb-10 p-10">
                <h3 class="lg:text-3xl text-2xl font-semibold mb-4">
                    {{ trans('Потрібно більше?') }}
                </h3>
                <hr class="w-1/5 h-1 text-icon bg-icon mb-5">

                <p class="text-sm2 font-bold pb-4">
                    <span class="text-icon">● </span> {{ trans('Персоналізований підхід') }}
                </p>
                <p class="text-sm2 font-bold border-t border-icon pt-4 pb-4">
                    <span class="text-icon">● </span> {{ trans('Досвідчена команда розробників') }}
                </p>
                <p class="text-sm2 font-bold border-t border-icon pt-4 pb-4">
                    <span class="text-icon">● </span> {{ trans('Потужна команда бізнес-аналітиків') }}
                </p>
                <p class="text-sm2 font-bold border-t border-icon pt-4 pb-4">
                    <span class="text-icon">● </span> {{ trans('Допомога із впровадженням та тренуванням персоналу') }}
                </p>
            </div>
        </div>
    </div>
</section>

<!-- action block -->
<section class="bg-meta-10 flex flex-col justify-center lg:h-50vh h-60vh p-6 sm:flex-row sm:justify-between sm:p-12">
    <div class="container mx-auto max-w-custom flex flex-col sm:flex-row items-center justify-center">
        <div class="flex flex-col justify-center w-full">
            <img class="text-center w-20 h-20 mx-auto" src="{{ Vite::asset('resources/images/phone.svg') }}" alt="phone">
            <h2 class="text-white text-center text-3xl sm:text-4xl font-bold mb-10">{{ trans('Цікавить наш продукт?') }}</h2>
            <p class="text-white text-center text-3lg sm:text-xl font-semibold">
                {!! Lang::get('Напишіть нам на :email або дзвоніть :phone', ['email' => '<a class="underline hover:text-orange" href="mailto:' . $email . '">' . $email . '</a>', 'phone' => '<a class="underline hover:text-orange" href="tel:' . $phone . '">' . $phone . '</a>']) !!}
            </p>
        </div>
    </div>
</section>

<!-- contact form -->
<section class="bg-gray-3 flex items-center justify-center md:py-30 py-15">
    <div class="container mx-auto w-full lg:w-3/5 flex flex-col sm:flex-row items-center justify-between md:pl-0 pl-5 md:pr-0 pr-5">
        <div class="bg-white rounded-lg shadow-lg shadow border-link md:p-15 p-5 w-full mx-auto">
            <h2 class="md:text-4xl text-2xl font-bold text-center mb-4">{{ trans('Зворотній зв\'язок') }}</h2>
            <p class="text-link font-bold text-center mb-8">{{ trans('Заповніть форму і ми зв\'яжемось із Вами якнайшвидше') }}</p>
            <form id="consultation-form" method="POST" action="{{ route('send.email') }}">
                @csrf
                <div class="mb-4">
                    <input type="text" name="name" placeholder="{{ trans('Your Name*') }}" class="w-full bg-gray px-4 py-2 border border-transparent rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500" title="{{ trans('Введіть ваше ім\'я') }}">
                </div>
                <div class="mb-10">
                    <input type="text" name="phone" placeholder="{{ trans('Phone number*') }}" class="w-full bg-gray px-4 py-2 border border-transparent rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500"
                    title="{{ trans('Введіть дійсний номер телефону щонайменше з 10 цифр') }}">
                </div>
                <button type="submit" class="w-full bg-orange text-white font-semibold py-3 rounded-lg hover:bg-blue focus:outline-none focus:ring-2 focus:ring-orange">
                    {{ trans('Request a Callback') }}
                </button>
            </form>
        </div>
    </div>
</section>

@endsection

@push('modals')
    <!-- Modal Contact Form successModal -->
    <div id="successModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden ml-3 mr-3">
        <div class="bg-white border-black rounded-lg overflow-hidden shadow-card hover:shadow-lg transform transition-all max-w-lg w-full p-6">
            <div class="text-center">
                <h3 class="text-lg font-medium text-gray-900">
                    {{ trans('Повідомлення відправлено успішно')}}
                </h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-500">
                        {{ trans('Дякуємо, що звернулися до нас. Ми отримали ваш електронний лист і незабаром зв’яжемося з вами')}}
                    </p>
                </div>
            </div>
            <div class="mt-4">
                <button id="closeModal" type="button" class="bg-orange inline-flex justify-center w-full rounded-md border border-transparent shadow-sm hover:shadow-lg px-4 py-2 text-base font-medium text-white hover:bg-blue focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange sm:text-sm">
                    {{ trans('Close')}}
                </button>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    @vite('resources/js/home.js')
@endpush
