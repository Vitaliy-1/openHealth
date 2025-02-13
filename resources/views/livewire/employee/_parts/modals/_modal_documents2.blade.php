<div id="documentModal"
     tabindex="-1"
     aria-hidden="true"
     class="hidden modal"
>
    <div class="modal-wrapper">
        <div class="modal-content">
            <div class="modal-header">
                <h3>{{__('forms.addDocument')}}</h3>
                <button type="button" class="modal-close" data-modal-toggle="documentModal">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    <span class="sr-only">{{__('forms.modalClose')}}</span>
                </button>
            </div>

            <form action="#">
                <div class="form-row-modal">
                    <div>
                        <label for="documentType" class="label-modal">{{__('forms.documentType')}}</label>
                        <input type="text" name="documentType" id="documentType" class="input-modal" required>
                    </div>
                    <div>
                        <label for="documentNumber" class="label-modal">{{__('forms.documentNumber')}}</label>
                        <input type="text" name="documentNumber" id="documentNumber" class="input-modal" required>
                    </div>
                    <div>
                        <label for="documentIssuedBy" class="label-modal">{{__('forms.documentIssuedBy')}}</label>
                        <input type="text" name="documentIssuedBy" id="documentIssuedBy" class="input-modal">
                    </div>
                    <div>
                        <label for="documentIssuedAt" class="label-modal">{{__('forms.documentIssuedAt')}}</label>
                        <input type="text" name="documentIssuedAt" id="documentIssuedAt" class="input-modal">
                    </div>
                </div>
                <button type="submit" class="button-primary mt-4">
                    {{__('forms.save')}}
                </button>
            </form>
        </div>
    </div>

</div>
