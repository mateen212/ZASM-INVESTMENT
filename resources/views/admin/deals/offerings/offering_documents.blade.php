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
            <p>Add documents to be shown (only) on the offering page. Typically GPs upload their subscription docs here for LPs to preview, as well as their webinar deck and recording. Please use PDF format or provide a link to your content.</p>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addDocumentModal">+ Add Document Link</button>
        </div>

        <div class="modal fade" id="addDocumentModal" tabindex="-1" aria-labelledby="addDocumentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary ">
                        <h5 class="modal-title text-white" id="addDocumentModalLabel">Add Document Link</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="documentForm">
                            <div class="mb-3">
                                <label for="documentName" class="form-label">Document name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="documentName" placeholder="Enter document name" required>
                            </div>
                            <div class="mb-3">
                                <label for="documentLink" class="form-label">Document link <span class="text-danger">*</span></label>
                                <input type="url" class="form-control" id="documentLink" placeholder="Enter document link" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="saveButton" @click="addDocument()">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3 mb-3">
            <div class="file-upload">
                <input type="file" id="document_section_file" name="document_section_file" class="form-control"
                    multiple @change="handleFiles($event)" hidden>
                <div class="drop-zone" @click="document.getElementById('document_section_file').click()"
                    @drop.prevent="handleDrop($event)" @dragover.prevent="dragOver = true"
                    @dragleave.prevent="dragOver = false" :class="{ 'drag-over': dragOver }">
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
                    <!-- Row 1 -->

                    @foreach ($offering->documents as $document)
                        <tr>
                            <td>{{ $document->name }}
                                <span class="view-icon ml-4"
                                    @click="viewDocument('{{ route('admin.document.view', $document->id) }}')"
                                    title="View Document">
                                    <i class="fas fa-eye"></i>
                                </span>
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
                                <span class="edit-icon"
                                    @click="openEditModal('{{ $document->offering_id }}', '{{ $document->name }}')">
                                    <i class="fas fa-pencil"></i>
                                </span>
                                <span class="delete-icon ml-4"
                                    @click="confirmDel('{{ route('admin.document.destroy', $document->id) }}')">
                                    <i class="fas fa-trash"></i>
                                </span>
                            </td>
                        </tr>

                        <div class="modal" id="editModal" tabindex="-1" aria-labelledby="editModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content p-5">
                                    <div class="modal-header border-0">
                                        <h5 class="modal-title text-center fw-bold" id="editModalLabel">Enter a new name
                                            for this document:</h5>
                                    </div>
                                    <div class="modal-body text-center">
                                        <input type="text" class="form-control text-center" id="documentName" />
                                    </div>
                                    <div class="modal-footer justify-content-center border-0">
                                        <button type="button" class="btn btn-outline-primary px-4"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-primary px-4"
                                            @click="renameDocument()">Rename</button>
                                    </div>
                                </div>
                            </div>
                        </div>                      
                    @endforeach

                </tbody>
            </table>
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
                    documentForm: {
                        _token: "{{ csrf_token() }}",
                    },

                    async handleFiles(event) {
                        const files = event.target.files || [];
                        for (const file of files) {
                            await this.uploadFile(file);
                        }
                    },

                    async handleDrop(event) {
                        const files = event.dataTransfer.files || [];
                        for (const file of files) {
                            await this.uploadFile(file);
                        }
                    },

                    async uploadFile(file) {
                        const url = "{{ route('admin.document.store') }}"; // Replace with your API route
                        const formData = new FormData();

                        // Add the file and any required form data
                        formData.append('file', file);
                        formData.append('_token', "{{ csrf_token() }}");
                        formData.append('deal_id', "{{ $deal->id }}");
                        formData.append('offering_id', "{{ $offering->id }}");

                        try {
                            const response = await fetch(url, {
                                method: 'POST',
                                body: formData,
                            });

                            if (response.ok) {
                                const result = await response.json();
                                cosyAlert('<strong>Success</strong><br />File uploaded successfully!', 'success');
                                console.log('Upload Result:', result);
                                window.location.hash = "#offeringDocumentsTab";
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
                                const documentNameElement = document.querySelector(
                                    `#documentName-${this.currentDocumentId}`);
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
            }

            function viewDocument(url) {
                window.open(url, '_blank');
            };

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
            };

            function addDocument() {
                const documentName = document.getElementById('documentName').value;
                const documentLink = document.getElementById('documentLink').value;

                if (!documentName || !documentLink) {
                    cosyAlert('Please fill all the fields', 'error');
                    return;
                }
                const urlPattern = /^(https?:\/\/)?([\w\-]+\.)+[a-z]{2,6}(:\d{1,5})?(\/.*)?$/i;
                if (!urlPattern.test(documentLink)) {
                    cosyAlert('Invalid link format. Please enter a valid URL.', 'error');
                    return;
                }

                const url = "{{ route('admin.document.storeLink') }}";
                const formData = new FormData();
                formData.append('name', documentName);
                formData.append('link', documentLink);
                formData.append('_token', "{{ csrf_token() }}");
                formData.append('deal_id', "{{ $deal->id }}");
                formData.append('offering_id', "{{ $offering->id }}");

                    const response = fetch(url, {
                        method: 'POST',
                        body: formData,
                    });

                    if (response.ok) {
                        const result =  response.json();
                        cosyAlert('<strong>Success</strong><br />Document added successfully!', 'success');

                        var modal = bootstrap.Modal.getInstance(document.getElementById('addDocumentModal'));
                        if (modal) modal.hide();
                        setTimeout(() => location.reload(), 500);
                    } else {
                        const errorData =  response.json();
                        console.error('Error adding document:', errorData);
                    }
                
            }
        </script>
    <script>
        $(document).ready(function() {
            var urlString = window.location.href;
            var hash = urlString.split("#")[1];
            if (hash == 'offeringDocumentsTab') {
                $('a[href="#offering_documents"]').tab('show');
            }
        });
    </script>
    @endpush
