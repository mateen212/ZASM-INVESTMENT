    <style>
        .file-upload .drop-zone {
            border: 2px dashed gray;
            padding: 60px;
            text-align: center;
            cursor: pointer;
        }

        .file-upload .drop-zone.drag-over {
            background-color: #e9ecef;
        }

        .file-upload .file-list .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
        }

        .edit-icon,
        .delete-icon {
            display: inline-flex;
            align-items: center;
        }

        .delete-icon {
            margin-left: 10px;
            /* Adjust the space as needed */
        }
    </style>

    <div x-data="DocumentFormHandler()">
        <div class="d-flex justify-content-between">
            <input type="text" class="form-control w-25" placeholder="Search documents..." id="search-documents">
            <button class="btn btn-outline-primary">Manage Label</button>
        </div>

        <div class="mt-3 mb-3">
            <div class="file-upload">
                <input type="file" id="document_section_file" name="document_section_file" class="form-control" multiple @change="handleFiles($event)" hidden>
                <div class="drop-zone" @click="document.getElementById('document_section_file').click()" @drop.prevent="handleDrop($event)" @dragover.prevent="dragOver = true" @dragleave.prevent="dragOver = false" :class="{ 'drag-over': dragOver }">
                    <p class="drop-zone-text">Click or drag a file to this area to upload</p>
                </div>
            </div>
        </div>

        <div class="table-responsive mt-3">
            <table class="table table-bordered mt-3" id="documents-table">
                <thead>
                    <tr>
                        <th>Document</th>
                        <th>Date added</th>
                        <th>Shared with</th>
                        <th>Label (Visible to LPs)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="accordionTable">

                    @foreach ($deal->document_sections as $index => $document_section)
                    <tr>
                        <td colspan="5">
                            <p class="w-100 text-start d-flex justify-content-between align-items-center"
                                x-data="{ isOpen: false }"
                                @click="isOpen = !isOpen"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapseSection{{ $index }}"
                                aria-expanded="false"
                                aria-controls="collapseSection{{ $index }}">
                                <span class="d-flex align-items-center">
                                    <i :class="isOpen ? 'fas fa-caret-down text-primary' : 'fas fa-caret-right text-primary'"></i>
                                    <span class="ms-2">{{ $document_section->name }}</span>
                                </span>
                                <span class="d-flex align-items-center ms-auto">
                                    @if ($document_section->can_edit)
                                    <span class="me-2">
                                        <i class="fas fa-plus-circle " role="button" title="Add Document" @click="setCurrentSection('{{ $document_section->id }}'); document.getElementById('document_section_file').click()"></i>
                                    </span>
                                    <span class="me-2">
                                        <i class="fas fa-pencil" role="button" title="Edit Name" @click="openEditSectionModal('{{ $document_section->id }}', '{{ $document_section->name }}')"></i>
                                    </span>
                                    <span class="me-2">
                                        <i class="fas fa-trash" role="button" title="Delete Section" @click="confirmDelSection('{{ route('admin.document.destroySection', $document_section->id) }}')"></i>
                                    </span>
                                    @endif
                                    {{ count($document_section->documents) }} document{{ count($document_section->documents) !== 1 ? 's' : '' }}
                                </span>
                            </p>
                        </td>
                <tbody>
                    @foreach ($document_section->documents as $document)

                    <tr id="collapseSection{{ $index }}" class="collapse accordion-collapse" data-bs-parent="#accordionTable">
                        <td>
                            <div class="d-flex justify-content-between">
                                <span> {{ $document->name }}</span>
                                <span class="view-icon" @click="viewDocument('{{ route('admin.document.view', $document->id) }}')" title="View Document">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </td>
                        <td>{{ $document->date_added }}</td>
                        <td>
                            <select class="form-control share-dropdown" name="share_with_{{ $document->id }}">
                                <option value="">All</option>
                                <option value="">All IRA investors</option>
                                <option value="">My investors</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-control label-dropdown" name="label_{{ $document->id }}">
                                <option value="">Select label...</option>
                                <option value="label1">Label 1</option>
                                <option value="label2">Label 2</option>
                                <option value="label3">Label 3</option>
                            </select>
                        </td>
                        <td>
                            <span class="edit-icon" @click="openEditModal('{{ $document->id }}', '{{ $document->name }}')">
                                <i class="fas fa-pencil"></i>
                            </span>
                            <span class="delete-icon ml-4" @click="confirmDel('{{ route('admin.document.destroy', $document->id) }}')">
                                <i class="fas fa-trash"></i>
                            </span>
                        </td>
                    </tr>
                    <div class="modal" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content p-5">
                                <div class="modal-header border-0">
                                    <h5 class="modal-title text-center fw-bold" id="editModalLabel">Enter a new name for this document:</h5>
                                </div>
                                <div class="modal-body text-center">
                                    <input type="text" class="form-control text-center" id="documentName" />
                                </div>
                                <div class="modal-footer justify-content-center border-0">
                                    <button type="button" class="btn btn-outline-primary px-4" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary px-4" @click="renameDocument()">Rename</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal" id="editSectionModal" tabindex="-1" aria-labelledby="editSectionModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content p-5">
                                <div class="modal-header border-0">
                                    <h5 class="modal-title text-center fw-bold" id="editSectionModalLabel">Enter a new name for this section:</h5>
                                </div>
                                <div class="modal-body text-center">
                                    <input type="text" class="form-control text-center" id="sectionName" />
                                </div>
                                <div class="modal-footer justify-content-center border-0">
                                    <button type="button" class="btn btn-outline-primary px-4" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary px-4" @click="renameSection()">Rename</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="table-responsive mt-3">
            <button class="btn pt-6 btn-outline-primary" @click="addSection()">Add section</button>
        </div>
    </div>

    @push('script')
    <script>
        // function DocumentFormHandler() {
        //     return {
        //         documentForm :{
        //             _token : csrf,
        //         },
        //     }
        // }
        function DocumentFormHandler() {
            return {
                dragOver: false,
                currentDocumentId: null,
                currentDocumentName: '',
                currentSectionId: null,
                sectionId: null,
                sectionName: '',
                documentForm: {
                    _token: "{{ csrf_token() }}",
                },

                setCurrentSection(sectionId) {
                    this.currentSectionId = sectionId;
                },

                async handleFiles(event) {
                    const files = event.target.files || [];
                    for (const file of files) {
                        await this.uploadFile(file, this.currentSectionId);
                    }
                },

                async handleDrop(event) {
                    const files = event.dataTransfer.files || [];
                    for (const file of files) {
                        await this.uploadFile(file);
                    }
                },

                async uploadFile(file, sectionId = null) {
                    const url = "{{ route('admin.document.store') }}"; // Replace with your API route
                    const formData = new FormData();

                    // Add the file and any required form data
                    formData.append('file', file);
                    formData.append('_token', "{{ csrf_token() }}");
                    formData.append('deal_id', "{{ $deal->id }}");
                    formData.append('section_id', sectionId);


                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            body: formData,
                        });

                        if (response.ok) {
                            const result = await response.json();
                            cosyAlert('<strong>Success</strong><br />File uploaded successfully!', 'success');
                            console.log('Upload Result:', result);
                            window.location.hash = "#documentsTab";
                            setTimeout(() => {
                                location.reload();
                            }, 500);
                        } else {
                            const errorData = await response.json();
                            console.error('Error uploading file:', errorData);
                        }
                    } catch (error) {
                        console.error('Network error:', error);
                    }
                },

                openEditModal(id, name) {
                    this.currentDocumentId = id;
                    this.currentDocumentName = name;
                    document.getElementById('documentName').value = name;
                    // Open modal
                    var modal = new bootstrap.Modal(document.getElementById('editModal'));
                    modal.show();
                },

                async renameDocument() {
                    const newName = document.getElementById('documentName').value;
                    const url = `{{ route('admin.document.rename', ':id') }}`.replace(':id', this.currentDocumentId);
                    const formData = new FormData();
                    formData.append('new_name', newName);
                    formData.append('_token', "{{ csrf_token() }}");

                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            body: formData,
                        });
                        const result = await response.json();

                        if (result.success) {
                            // Update document name in the table
                            const documentNameElement = document.querySelector(`#documentName-${this.currentDocumentId}`);
                            if (documentNameElement) {
                                documentNameElement.textContent = newName;
                            }
                            // Close modal
                            const modalElement = document.getElementById('editModal');
                            const modalInstance = bootstrap.Modal.getInstance(modalElement);
                            if (modalInstance) {
                                modalInstance.hide();
                            }
                        } else {
                            alert('Error renaming document');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                },

                openEditSectionModal(id, name) {
                    this.sectionId = id;
                    this.sectionName = name;
                    document.getElementById('sectionName').value = name;
                    // Open modal
                    var modal = new bootstrap.Modal(document.getElementById('editSectionModal'));
                    modal.show();
                },

                async renameSection() {
                    const newName = document.getElementById('sectionName').value;
                    const url = `{{ route('admin.document.renameSection', ':id') }}`.replace(':id', this.sectionId);
                    const formData = new FormData();
                    formData.append('new_name', newName);
                    formData.append('_token', "{{ csrf_token() }}");

                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            body: formData,
                        });
                        const result = await response.json();

                        if (result.success) {
                            // Update document name in the table
                            const documentNameElement = document.querySelector(`#sectionName-${this.sectionId}`);
                            if (documentNameElement) {
                                documentNameElement.textContent = newName;
                            }
                            // Close modal
                            const modalElement = document.getElementById('editSectionModal');
                            const modalInstance = bootstrap.Modal.getInstance(modalElement);
                            if (modalInstance) {
                                modalInstance.hide();
                            }
                        } else {
                            alert('Error renaming section');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                },
            };
        }

        function confirmDel(url) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Call the delete function
                    deleteDocument(url, () => {
                        Swal.fire(
                            'Deleted!',
                            'Your document has been deleted.',
                            'success'
                        )
                    });
                }
            });
        };

        function confirmDelSection(url) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to delete this section?<br /> All documents will be deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Call the delete function
                    deleteDocument(url, () => {
                        Swal.fire(
                            'Deleted!',
                            'Section has been deleted.',
                            'success'
                        )
                    });
                }
            });
        };

        function viewDocument(url) {
            window.open(url, '_blank');
        };

        function addSection() {
            const url = "{{ route('admin.document.storeSection') }}";
            const data = {
                _token: "{{ csrf_token() }}",
                deal_id: "{{ $deal->id }}",
                name: "New Section"
            };

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data),
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        cosyAlert('<strong>Success</strong><br />Section added successfully!', 'success');                       
                        window.location.reload();
                    } else {
                        alert('Failed to add section: ' + (result.message || 'Unknown error.'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function deleteDocument(url, onSuccess) {
            fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (onSuccess) onSuccess();

                    } else {
                        Swal.fire(
                            'Error!',
                            data.message || 'An error occurred while deleting the document.',
                            'error'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Error!',
                        'An error occurred while deleting the document.',
                        'error'
                    );
                });
        }
    </script>

    <script>
        $(document).ready(function() {
            var urlString = window.location.href;
            var hash = urlString.split("#")[1];
            if (hash == 'documentsTab') {
                $('a[href="#documents"]').tab('show');
            }
        });
    </script>
    @endpush