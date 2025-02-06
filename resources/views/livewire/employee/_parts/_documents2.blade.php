<fieldset class="fieldset-table">
    <legend class="legend">
        {{__('forms.documents')}}
    </legend>

    <table class="table-input">
        <thead class="thead-input">
            <tr>
                <th scope="col" class="th-input">{{ __('forms.documentType') }}</th>
                <th scope="col" class="th-input">{{ __('forms.number') }} </th>
                <th scope="col" class="th-input">{{ __('forms.issuedBy') }}</th>
                <th scope="col" class="th-input">{{ __('forms.issuedAt') }}</th>
                <th scope="col" class="th-input">{{ __('forms.actions') }}</th>
            </tr>
        </thead>
        <tbody x-data="{ documents: $wire.entangle('employeeRequest.documents') }">
            <template x-for="(document, index) in documents">
                <tr>
                    <td class="td-input" x-data="{ document.type }"></td>
                    <td class="td-input" x-data=""></td>
                    <td class="td-input" x-data=""></td>
                    <td class="td-input" x-data=""></td>
                    <td class="td-input"></td>
                </tr>
            </template>
        </tbody>
    </table>

    <button class="item-add mt-5">
        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/>
        </svg>

        {{__('forms.addDocument')}}
    </button>
</fieldset>
