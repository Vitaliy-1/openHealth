<div
    x-data="{
        openDropdown: false,
        toggle() {
            this.openDropdown ? this.close() : (this.$refs.button.focus(), this.openDropdown = true)
        },
        close(focusAfter) {
            if (!this.openDropdown) return;
            this.openDropdown = false;
            focusAfter && focusAfter.focus();
        }
    }"
    @keydown.escape.prevent.stop="close($refs.button)"
    @focusin.window="!$refs.panel.contains($event.target) && close()"
    x-id="['dropdown-button']"
    class="relative"
>
    <button
        x-ref="button"
        @click="toggle()"
        :aria-expanded="openDropdown"
        :aria-controls="$id('dropdown-button')"
        type="button"
        class="text-gray-800 dark:text-gray-200"
    >
        <!-- тут можна замінити на будь-яку іконку -->
        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="square" stroke-linejoin="round" stroke-width="2"
                  d="M7 19H5a1 1 0 0 1-1-1v-1a3 3 0 0 1 3-3h1m4-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm7.441 1.559a1.907 1.907 0 0 1 0 2.698l-6.069 6.069L10 19l.674-3.372 6.07-6.07a1.907 1.907 0 0 1 2.697 0Z"/>
        </svg>
    </button>

    <div class="absolute top-0 left-0 right-0 z-10 bg-white shadow-lg">
        <div
            x-ref="panel"
            x-show="openDropdown"
            x-transition:enter="transition transform duration-300 ease-out"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition transform duration-200 ease-in"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2"
            @click.outside="close($refs.button)"
            :id="$id('dropdown-button')"
            x-cloak
            class="dropdown-panel relative"
            style="top: -100%; left: 50%; transform: translateX(-50%);"
        >
            <button @click.prevent="{{ $editAction }}" class="dropdown-button">
                {{ __('forms.edit') }}
            </button>
            <button @click.prevent="{{ $deleteAction }}" class="dropdown-button dropdown-delete">
                {{ __('forms.delete') }}
            </button>
        </div>
    </div>
</div>
