<div x-data="valuationFormHandler()" >
    <div class="deal-modal modal right fade" id="addValuationModal" tabindex="-1" aria-labelledby="addValuationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content px-2">
                <div class="modal-header row bg-primary text-white" style="height:80px;">
                    <h5 class="modal-title col text-white">Add Valuation</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Deal Form Body --}}
                    <div>
                        @csrf
                        <div class="mb-3">
                            <label for="valuation_date" class="form-label">Valuation date</label>
                            <input type="date" onclick="this.showPicker()"  id="valuation_date" name="valuation_date" class="form-select "
                                x-model="valuationForm.valuation_date" required>
                        </div>
                        
                        <div class="mb-3"> 
                            <label class="form-label" for="duplicate_valuation" >Duplicate most recent valuation*</label>
                            <div>
                                <div class="form-check form-check-inline"> 
                                    <input class="form-check-input"type="radio" id="duplicate" name="duplicate" value="duplicate"x-model="valuationForm.duplicate_valuation"> 
                                    <label class="form-check-label" for="yes">Duplicate</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input"type="radio" id="no_duplicate" name="no_duplicate" value="no_duplicate" x-model="valuationForm.duplicate_valuation" > 
                                    <label class="form-check-label"for="no">Don't Duplicate</label>
                                </div>
                            </div>
                        </div>

                       <template x-if="valuationForm.duplicate_valuation == 'no_duplicate'">
                            <div class="mb-3"> 
                                <label class="form-label" for="include_nav" >Include NAV statement?*</label>
                                <div>
                                    <div class="form-check form-check-inline"> 
                                        <input class="form-check-input"type="radio" id="yes_include" name="includeNav" value="yes" x-model="valuationForm.include_nav"> 
                                        <label class="form-check-label" for="yes">Yes</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input"type="radio" id="no" name="includeNav" value="no"  x-model="valuationForm.include_nav"> 
                                        <label class="form-check-label"for="no_include">No</label>
                                    </div>
                                </div>
                            </div>
                       </template>


                        <div class="d-flex mt-3 justify-content-end">
                            <button type="button" class="btn btn-secondary me-2"
                                data-bs-dismiss="modal">Cancel</button>
                            <span class="btn btn-primary deal-save" @click="submitValuationForm(valuationForm)">
                                Save
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--  snapshot model  --}}
    <div class="deal-modal modal right fade" id="addSnapshotModal" tabindex="-1" aria-labelledby="addSnapshotModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content px-2">
                <div class="modal-header row bg-primary text-white" style="height:80px;">
                    <h5 class="modal-title col text-white">Add Snapshot</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Deal Form Body --}}
                    <div>
                        @csrf
                        <div class="mb-3">
                            <label for="snapshot_date" class="form-label">Snapshot date</label>
                            <input type="date" onclick="this.showPicker()"  id="snapshot_date" name="snapshot_date" class="form-select "
                                x-model="snapshotForm.snapshot_date" required>
                        </div>
                        
                        <div class="mb-3"> 
                            <label class="form-label" for="duplicate_snapshot" >Duplicate most recent valuation*</label>
                            <div>
                                <div class="form-check form-check-inline"> 
                                    <input class="form-check-input"type="radio" id="duplicate" name="duplicate" value="duplicate"x-model="snapshotForm.duplicate_snapshot"> 
                                    <label class="form-check-label" for="yes">Duplicate</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input"type="radio" id="no_duplicate" name="no_duplicate" value="no_duplicate" x-model="snapshotForm.duplicate_snapshot" > 
                                    <label class="form-check-label"for="no">Don't Duplicate</label>
                                </div>
                            </div>
                        </div>

                       {{--  <template x-if="snapshot.duplicate_snapshot == 'no_duplicate'">
                            <div class="mb-3"> 
                                <label class="form-label" for="include_nav" >Include NAV statement?*</label>
                                <div>
                                    <div class="form-check form-check-inline"> 
                                        <input class="form-check-input"type="radio" id="yes_include" name="includeNav" value="yes" x-model="snapshot.include_nav"> 
                                        <label class="form-check-label" for="yes">Yes</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input"type="radio" id="no" name="includeNav" value="no"  x-model="snapshot.include_nav"> 
                                        <label class="form-check-label"for="no_include">No</label>
                                    </div>
                                </div>
                            </div>
                       </template>  --}}


                        <div class="d-flex mt-3 justify-content-end">
                            <button type="button" class="btn btn-secondary me-2"
                                data-bs-dismiss="modal">Cancel</button>
                            <span class="btn btn-primary deal-save" @click="submitSnapshotForm(snapshotForm)">
                                Save
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--  Fmv Form  --}}
    <div class="deal-modal modal right fade" id="addFMVFormModal" tabindex="-1" aria-labelledby="addFMVFormModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content px-2">
                <div class="modal-header row bg-primary text-white" style="height:80px;">
                    <h5 class="modal-title col text-white">Add Snapshot</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Deal Form Body --}}
                    <div>
                        @csrf
                        <div class="mb-3">
                            <label for="fmv_date" class="form-label">Snapshot date</label>
                            <input type="date" onclick="this.showPicker()"  id="fmv_date" name="fmv_date" class="form-select "
                                x-model="fmvForm.fmv_date" required>
                        </div>
                        
                        <div class="mb-3"> 
                            <label class="form-label" for="duplicate_snapshot" >Asset Name*</label>
                           <input type="text" id="fmv_name" name="fmv_name" class="form-select" x-modal="fmvForm.fmv_name">
                        </div>

                       {{--  <template x-if="snapshot.duplicate_snapshot == 'no_duplicate'">
                            <div class="mb-3"> 
                                <label class="form-label" for="include_nav" >Include NAV statement?*</label>
                                <div>
                                    <div class="form-check form-check-inline"> 
                                        <input class="form-check-input"type="radio" id="yes_include" name="includeNav" value="yes" x-model="snapshot.include_nav"> 
                                        <label class="form-check-label" for="yes">Yes</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input"type="radio" id="no" name="includeNav" value="no"  x-model="snapshot.include_nav"> 
                                        <label class="form-check-label"for="no_include">No</label>
                                    </div>
                                </div>
                            </div>
                       </template>  --}}


                        <div class="d-flex mt-3 justify-content-end">
                            <button type="button" class="btn btn-secondary me-2"
                                data-bs-dismiss="modal">Cancel</button>
                            <span class="btn btn-primary deal-save" @click="submitSnapshotForm(snapshotForm)">
                                Save
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('style')
  <script>
        function valuationFormHandler() {
            return {
                valuationForm:{
                    _token : csrf,
                   valuation_date:'',
                   duplicate_valuation:'',
                   include_nav:'',

                }, 
                snapshotForm:{
                    _token : csrf,
                   snapshot_date:'',
                   duplicate_snapshot:'',
                   
                },
                fmvForm:{
                    _token : csrf,
                   fmv_date:'',
                   fmv_name:'',
                },
                
               
            }
        }
        
        


    </script>       
    

@endpush