<div x-data="KPIFormHandler()" >

    <div class="d-flex justify-content-between">
        <p>Add KPIs to easily add charts and tables to LP update emails</p>
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addKPIModal">+ Add KPI's Collection</button>
    </div>
    <div class="table-responsive mt-3">
        <table class="table table-bordered mt-3" id="kpis-table">
            <thead>
                <tr>
                    <th> Name</th>
                    <th>Type</th>
                    <th># of KPI's</th>
                    <th># of Dates</th>
                    <th>Latest Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                {{--  @foreach($deal->assets as $asset)  --}}
                    <tr>
                        <td>1</td>
                        <td>2</td>
                        <td>3</td>
                        <td>1</td>
                        <td>1</td>
                        <td>
                            <span role="button" title="delete">
                                <i class="fas fa-trash"></i>
                            </span>
                        </td>
                    </tr>
                {{--  @endforeach  --}}
            </tbody>
        </table>
    </div>  


    <div class="deal-modal modal right fade" id="addKPIModal" tabindex="-1" aria-labelledby="addKPIModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content px-2">
                <div class="modal-header row bg-primary text-white" style="height:80px;">
                    <h5 class="modal-title col text-white">Add KPI collection</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Deal Form Body --}}
                    <div>
                    @csrf
                        <p>Add a KPI collection to contain a set of KPIs</p>
                        {{--  first name  --}}
                        <div class="mb-3">
                            <label for="kpi_collection_name" class="form-label">KPI collection name</label>
                            <input type="text"  id="kpi_collection_name" name="kpi_collection_name" class="form-control" x-model="kpiForm.kpi_collection_name">
                        </div>
                        <div>
                            <label for="kpi_collection_type" class="form-label">KPI collection type</label>
                            <select id="kpi_collection_type" name="kpi_collection_type" class="form-select" x-model="kpiForm.kpi_collection_type">
                                <option value="">Select KPI Type</option>
                                <option value="daily">Daily</option>
                                <option value="yearly">Yearly</option>
                                <option value="monthly">Monthly(Recommended)</option>
                            </select>

                        </div>
                        <div class="d-flex pt-5 justify-content-end">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                            <span class="btn btn-primary deal-save"
                            @click="submitKPIForm(kpiForm)">
                                Save
                            </span> 
                        </div>
                    </div>
                </div>    
            </div>    
        </div>    
    </div>  
</div>


 
@push('script')
    <script>
        function KPIFormHandler() {
            return {
                kpiForm :{
                    _token : csrf,
                   kpi_collection_name:'',
                   kpi_collection_type:'',
                  
                },
               
            }
        }
    </script>
@endpush

