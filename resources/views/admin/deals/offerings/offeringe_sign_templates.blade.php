@push('style')
    <link rel="stylesheet" href="https://unpkg.com/vue-multiselect/dist/vue-multiselect.min.css">
    @vite(['resources/js/documenso.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .deal-modal.right .modal-dialog {
            position: fixed;
            margin: auto;
            width: 50rem;
            max-width: 50%;
            height: 100%;
            right: 0;
            top: 0;
            bottom: 0;
        }

        .deal-modal.right .modal-content {
            height: 100%;
            overflow-y: auto;
        }

        .deal-modal.right .modal-body {
            padding: 15px 15px 80px;
        }

        .step-progress {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .step-progress .step {
            flex: 1;
            text-align: center;
            position: relative;
        }

        .step-progress .step::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #e9ecef;
            z-index: 1;
        }

        .step-progress .step.active::before {
            background-color: #007bff;
        }

        .step-progress .step .step-number {
            position: relative;
            z-index: 2;
            background-color: #fff;
            border: 2px solid #e9ecef;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            line-height: 26px;
            display: inline-block;
        }

        .step-progress .step.active .step-number {
            border-color: #007bff;
            color: #007bff;
        }

        .file-uploader .drop-zone {
            border: 2px dashed #007bff;
            padding: 20px;
            text-align: center;
            cursor: pointer;
        }

        .file-uploader .drop-zone.drag-over {
            background-color: #e9ecef;
        }

        .file-uploader .file-list .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
        }
    </style>
@endpush
<div x-data="ESignTemplateHandler()">
    <template x-if="loading">
        <div class="custom-loader-overlay">
            <div class="custom-loader"></div>
        </div>
    </template>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <p class="mb-0">Note: There can only be one template for each investor type</p>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTemplateModal">+ Create
                template</button>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Template name</th>
                        <th>Profile type</th>
                        <th>Status</th>
                        <th>Date added</th>
                        <th>Date edited</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($offering->esignTemplates as $eSignTemplate)
                        <tr>
                            <td>{{ $eSignTemplate->template_name }}</td>
                            <td>{{ $eSignTemplate->template_type }}</td>
                            <td>{{ $eSignTemplate->status }}</td>
                            <td>{{ $eSignTemplate->created_at }}</td>
                            <td>{{ $eSignTemplate->updated_at }}</td>
                            <td class="text-center">
                                <button id="editTemplate" class="btn" data-template-id="{{ $eSignTemplate->id }}"
                                    data-file-path="{{ asset($eSignTemplate->file_path) }}"
                                    data-template-name="{{ $eSignTemplate->template_name }}"
                                    data-document-id="{{ $eSignTemplate->documenso_document_id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                {{--  add a button in which icon is pencil and by open that we see the popup in which we can edit the name of template and show the template type but not edit able  --}}
                                {{--  <span role="button" title="edit" data-bs-toggle="modal"
                                    data-bs-target="#editTemplateModal"
                                    @click="editTemplate('{{ $eSignTemplate->id }}', '{{ $eSignTemplate->template_name }}', '{{ $eSignTemplate->template_type }}')">

                                    <i class="fas fa-pencil-alt text-primary ms-2"></i>
                                </span>  --}}
                                <span role="button" title="View Document"
                                    onclick="window.open('{{ route($prefix . '.ESignTemplates.viewTemplate', $eSignTemplate->id) }}', '_blank')">
                                    <i class="fas fa-eye text-success ms-2"></i>
                                </span>


                                <span role="button" title="delete"
                                    onclick="confirmTemplateDelete('{{ route($prefix . '.ESignTemplates.deleteTemplate', [$eSignTemplate->id]) }}', this)">
                                    <i class="fas fa-trash-alt text-danger ms-2"></i>
                                </span>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div id="v-documenso">
        <div>
            {{-- <counter /> --}}
            <documenso-template />
            {{--  <hurdle-component :classes="{{$classes}}" />  --}}

        </div>
    </div>
    <div class="deal-modal modal right fade" id="editTemplateModal" tabindex="-1"
        aria-labelledby="editTemplateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content px-2">
                <div class="modal-header row bg-primary text-white" style="padding: 30px 20px; height: 90px;">
                    <h5 class="modal-title col text-white">Edit Template</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @csrf
                    <div class="container mt-3">
                        <div class="mb-3">
                            <label for="editTemplateName" class="form-label">Template name <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="editTemplateName" class="form-control"
                                x-model="editTemplateData.name" placeholder="Enter name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProfileType" class="form-label">Profile type</label>
                            <input type="text" id="editProfileType" class="form-control"
                                x-model="editTemplateData.type" readonly>
                        </div>
                        <div class="mt-4">
                            <button class="btn btn-primary me-2"
                                @click="saveEditedTemplate(editTemplateData.id)">Save</button>
                            <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="deal-modal modal right fade" id="addTemplateModal" tabindex="-1"
        aria-labelledby="addTemplateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content px-2">
                <!-- Modal Header -->
                <div class="modal-header row bg-primary text-white" style="padding: 30px 20px; height: 90px;">
                    <h5 class="modal-title col text-white">Add Template</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    @csrf
                    <!-- File Upload -->
                    <div class="file-upload mt-3 mb-3">
                        <input type="file" id="template_section_file" class="form-control"
                            @change="handleFiles($event)" hidden>
                        <div class="drop-zone border border-primary rounded p-3 text-center"
                            @click="document.getElementById('template_section_file').click()"
                            @drop.prevent="handleDrop($event)" @dragover.prevent="dragOver = true"
                            @dragleave.prevent="dragOver = false" :class="{ 'bg-light': dragOver }">
                            <template x-if="!fileName">
                                <p class="drop-zone-text mb-0">Click or drag a file to this area to upload</p>
                            </template>
                            <template x-if="fileName">
                                <div class="uploaded-file d-flex align-items-center justify-content-between">
                                    <div>
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/8/87/PDF_file_icon.svg"
                                            alt="PDF Icon" width="24" height="24" class="me-2">
                                        <span x-text="fileName"></span> (<span x-text="fileSize"></span> kB)
                                    </div>
                                    <button class="btn btn-sm btn-link text-decoration-none"
                                        @click="replaceInitiated = true; document.getElementById('template_section_file').click()">Replace</button>
                                </div>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar" role="progressbar" :style="{ width: progress + '%' }">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>




                    <!-- Template Details -->
                    <div class="container mt-5">
                        <div class="mb-3">
                            <label for="templateName" class="form-label">Template name <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="templateName" class="form-control"
                                x-model="ESignTemplate.template_name" placeholder="Enter name" required>
                        </div>
                        <div class="mb-3">
                            <label for="profileType" class="form-label">
                                Profile type <span class="text-danger">*</span>
                            </label>
                            <select id="profileType" class="form-select" x-model="ESignTemplate.template_type"
                                required>
                                <option value="" disabled selected>Select profile type</option>

                                @if (!in_array('individual', $existingTemplates))
                                    <option value="individual">Individual</option>
                                @endif

                                @if (!in_array('custodian', $existingTemplates))
                                    <option value="custodian">Custodian IRA or Custodian based 401(k)</option>
                                @endif

                                @if (!in_array('join_tenancy', $existingTemplates))
                                    <option value="join_tenancy">Joint Tenancy with Right of Survivorship</option>
                                @endif

                                @if (!in_array('lcps_property', $existingTemplates))
                                    <option value="lcps_property">LLC, Corp, Partnership, Solo 401(K), or checkbook IRA
                                    </option>
                                @endif
                            </select>
                        </div>


                        <!-- Offering Settings -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Offering settings</h5>
                            <button class="btn btn-outline-primary"
                                onclick="window.location.href='{{ route($prefix . '.deals.offerings.offering_manage', [$deal->id, $offering->id]) }}'">Manage
                                offering</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Setting</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="setting in settings" :key="setting.name">
                                        <tr>
                                            <td>
                                                <span x-text="setting.name"></span>
                                                <button class="btn btn-sm btn-link text-decoration-none"
                                                    title="Additional info">
                                                    <i class="bi bi-question-circle"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <span x-text="setting.status"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- Save and Cancel Buttons -->
                        <div class="mt-4">
                            <button class="btn btn-primary me-2" @click="submitESignTemplate">Save</button>
                            <button class="btn btn-outline-secondary" @click="cancelChanges">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('script')
    <script>
        var csrf = '{{ csrf_token() }}';

        window.baseUrl = "{{ url('/') }}";

        function ESignTemplateHandler() {
            return {
                dragOver: false,
                fileName: '',
                settings: [],
                fileSize: 0,
                progress: 0,
                replaceInitiated: false,
                loading: false,
                ESignTemplate: {
                    _token: csrf,
                    template_name: '',
                    template_type: '',
                    files: [],
                },
                editTemplateData: {
                    id: '',
                    name: '',
                    type: ''
                },
                editTemplate(id, name, type) {
                    this.editTemplateData = {
                        id: id,
                        name: name,
                        type: type
                    };
                },
                handleFiles(event) {
                    const file = event.target.files[0] || event.dataTransfer.files[0];
                    if (file) {
                        this.fileName = file.name;
                        this.fileSize = (file.size / 1024).toFixed(1);
                        this.ESignTemplate.files = [file];
                    }
                },
                handleDrop(event) {
                    this.handleFiles(event);
                },
                replaceFile(file) {
                    if (file) {
                        this.fileName = file.name;
                        this.fileSize = (file.size / 1024).toFixed(1);
                        this.ESignTemplate.files = [file];
                        this.replaceInitiated = false;
                    }
                },
                async submitESignTemplate() {
                    let formData = new FormData();
                    formData.append('template_name', this.ESignTemplate.template_name);
                    formData.append('template_type', this.ESignTemplate.template_type);
                    formData.append('file', this.ESignTemplate.files[0]);
                    formData.append('deal_id', "{{ $deal->id }}");
                    formData.append('offering_id', "{{ $offering->id }}");

                    this.loading = true;
                    let url = "{{ route($prefix . '.ESignTemplates.uploadTemplate') }}";

                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf
                            },
                            body: formData
                        });

                        this.loading = false;
                        const responseData = await response.json();

                        if (response.status === 200) {
                            // ✅ Success - show success alert and emit event
                            const modalElement = document.querySelector('#addTemplateModal');
                            const modalInstance = bootstrap.Modal.getInstance(modalElement);
                            cosyAlert('<strong>Success</strong><br />E Sign Template Added Successfully!', 'success');
                            modalInstance.hide();


                            console.log('File Path:', responseData.file_path);

                            window.dispatchEvent(new CustomEvent('open-documenso-popup', {
                                detail: {
                                    filePath: responseData.file_path,
                                    templateName: this.ESignTemplate.template_name,
                                    documentId: responseData.documenso_document_id,
                                    templateId: responseData.template_id,
                                    
                                }
                            }));

                            this.cancelChanges();
                        } else if (response.status === 409) {
                            cosyAlert(
                                '<strong>Duplicate Template</strong><br />A template of this type already exists for this offering.',
                                'error');
                        } else {
                            console.error('Error:', responseData);
                            cosyAlert('<strong>Error</strong><br />Something went wrong. Please try again.', 'error');
                        }

                    } catch (error) {
                        this.loading = false;
                        console.error('Error:', error);
                        cosyAlert('<strong>Error</strong><br />An unexpected error occurred.', 'error');
                    }
                },

                async saveEditedTemplate(id) {
                    if (!id) {
                        cosyAlert('<strong>Error</strong><br />Template ID is missing', 'error');
                        return;
                    }

                    this.loading = true;
                    let url = `{{ url($prefix . '/esigntemplate') }}/${id}`;

                    try {
                        const response = await fetch(url, {
                            method: 'POST', // Using POST as per your route
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                template_name: this.editTemplateData.name,
                                _token: csrf
                            })
                        });

                        this.loading = false;
                        const responseData = await response.json();

                        if (response.ok) {
                            const modalElement = document.querySelector('#editTemplateModal');
                            const modalInstance = bootstrap.Modal.getInstance(modalElement);
                            cosyAlert('<strong>Success</strong><br />Template Updated Successfully!', 'success');

                            // Update the table row without reloading
                            const row = document.querySelector(`tr td[data-template-id="${id}"]`);
                            if (row) {
                                row.textContent = this.editTemplateData.name;
                            } else {}
                        } else {
                            console.error('Error:', responseData);
                            cosyAlert('<strong>Error</strong><br />Failed to update template: ' + (responseData.error ||
                                'Unknown error'), 'error');
                        }
                    } catch (error) {
                        this.loading = false;
                        console.error('Error:', error);
                        cosyAlert('<strong>Error</strong><br />An error occurred: ' + error.message, 'error');
                    }
                },
                cancelChanges() {
                    this.ESignTemplate = {
                        template_name: '',
                        template_type: '',
                        files: []
                    };
                    this.fileName = '';
                },
            }
        }

        function confirmTemplateDelete(url, element) {
            Swal.fire({
                title: 'Delete Template',
                text: "Are you sure you want to delete this Template? This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                allowOutsideClick: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteTemplate(url, element);
                }
            });
        }

        function deleteTemplate(url, element) {
            fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message || data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: data.message || 'Template has been deleted successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Remove the row from the table
                        const row = element.closest('tr');
                        if (row) row.remove();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.error || 'An error occurred while deleting the Template.'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred while deleting the Template.'
                    });
                });
        }
    </script>
    <script>
        window.documensoRoutes = {
            save_fields: "{{ route($prefix . '.ESignTemplates.saveFields') }}",
        };
    </script>
@endpush
