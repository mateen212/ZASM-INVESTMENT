@php
    if (auth('admin')->user()->hasRole('partner')) {
        $prefix = 'partner';
    } else {
        $prefix = 'admin';
    }
@endphp
<div x-data="membersForm()" x-clock>
    <h3>Partners</h3>
    <div class="d-flex justify-content-between">
        <div class="search-bar position-relative">
            <input type="text" name="search" id="search-members" class="form-control form-control-sm"
                placeholder="Search members..." style="padding-right: 2.5rem;" />
            <i class="la la-search position-absolute"></i>
        </div>
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">Add Members
        </button>

    </div>
    <div class="table-responsive mt-3">
        <table class="table table-bordered mt-3" id="members-table">
            <thead>
                <tr>
                    <th class="sortable" data-sort="offering_name">@lang('Name')
                        <span class="sort-icons">
                            <i class="fas fa-sort-up"></i>
                            <i class="fas fa-sort-down"></i>
                        </span>
                    </th>
                    <th class="sortable" data-sort="internal_name">@lang('Email Address')
                        <span class="sort-icons">
                            <i class="fas fa-sort-up"></i>
                            <i class="fas fa-sort-down"></i>
                        </span>
                    </th>
                    <th class="sortable" data-sort="offering_size">@lang('Role')
                        <span class="sort-icons">
                            <i class="fas fa-sort-up"></i>
                            <i class="fas fa-sort-down"></i>
                        </span>
                    </th>
                    <th class="sortable" data-sort="status">@lang('Total capital balance')
                        <span class="sort-icons">
                            <i class="fas fa-sort-up"></i>
                            <i class="fas fa-sort-down"></i>
                        </span>
                    </th>
                    <th class="sortable" data-sort="visiblity">@lang('Email interception')
                        <span class="sort-icons">
                            <i class="fas fa-sort-up"></i>
                            <i class="fas fa-sort-down"></i>
                        </span>
                    </th>
                    <th class="sortable" data-sort="type">@lang('Status')
                        <span class="sort-icons">
                            <i class="fas fa-sort-up"></i>
                            <i class="fas fa-sort-down"></i>
                        </span>
                    </th>
                    <!-- No sort icons for the Actions column -->
                    <th>@lang('Actions')</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loop over the members array dynamically -->
                <template x-if="members.length > 0">
                    <template x-for="member in members" :key="member.id">
                        <tr>
                            <td x-text="member.name"></td>
                            <td x-text="member.email || '-'"></td>
                            <td x-text="`${member.pivot.role ? titleCase(member.pivot.role) : 'N/A'}`"></td>
                            <td x-text="member.capital_balance || 'N/A'"></td>
                            <td x-text="member.email_interception || 'N/A'"></td>
                            <td x-text="member.pivot.status === 1 ? 'Active' : 'Inactive'"></td>
                            <td>
                                <!-- Show Edit button only if member is Active and NOT lead-sponsor -->
                                <template x-if="member.pivot.status !== 0 && member.pivot.role !== 'lead-sponsor'">
                                    <button class="text-primary" @click="openEditMemberModal(member)">
                                        <i class="fas fa-edit" title="Edit"></i>
                                    </button>
                                </template>

                                <!-- Show Delete button only if role is NOT 'lead-sponsor' -->
                                <template x-if="member.pivot.role !== 'lead-sponsor'">
                                    <button class="text-danger delete-icon" @click="confirmMemberDelete(member.id)">
                                        <i class="fas fa-trash" title="Delete"></i>
                                    </button>
                                </template>
                            </td>
                        </tr>
                    </template>
                </template>

                </template>
                <template x-if="members.length === 0">
                    <tr>
                        <td colspan="7" class="text-center">No members available</td>
                    </tr>
                </template>
            </tbody>

        </table>
    </div>
    <div class="deal-modal modal right fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content px-2">
                <div class="modal-header row bg-primary text-white">
                    <h5 class="modal-title col text-white">Add Partner</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Deal Form Body --}}
                    <div>
                        @csrf
                        <div x-show="offerings.length !== 0">
                            <div class="mb-3">
                                <label for="contact" class="form-label">Select contact</label>
                                <select id="contact" name="contact" class="form-select"
                                    x-on:input="memberErrors.contact = ''" x-model="memberForm.contact" required>
                                    <option>select contact</option>
                                    <option value="add">Add a person by email</option>
                                </select>
                                {{--  <span x-show="memberErrors.contact"
                                                x-text="memberErrors.contact" class="text-danger"></span>  --}}
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name*</label>
                            <input type="text" id="first_name" name="first_name" class="form-control"
                                x-on:input="memberErrors.first_name = ''" x-model="memberForm.first_name" required>
                            <span x-show="memberErrors.first_name" x-text="memberErrors.first_name"
                                class="text-danger"></span>
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name*</label>
                            <input type="text" id="last_name" name="last_name" class="form-control"
                                x-on:input="memberErrors.last_name = ''" x-model="memberForm.last_name" required>
                            <span x-show="memberErrors.last_name" x-text="memberErrors.last_name"
                                class="text-danger"></span>
                        </div>
                        <div class="mb-3">
                            <label for="email_address" class="form-label">Email Address*</label>
                            <input type="email" id="email_address" name="email_address" class="form-control"
                                x-on:input="memberErrors.email_address = ''" x-model="memberForm.email_address"
                                required>
                            <span x-show="memberErrors.email_address" x-text="memberErrors.email_address"
                                class="text-danger"></span>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Select Role*</label>
                            <select id="role" name="role" class="form-select"
                                x-on:input="memberErrors.role = ''" x-model="memberForm.role" required>
                                <option value="">Select role</option>
                                <option value="admin_sponsor">Admin Sponsor</option>
                                <option value="co_sponsor">Co-Sponsor</option>
                                <option value="cpa_accountant">CPA/Accountant</option>
                                <option value="registered_investment_advisor">Registered Investment advisor</option>
                            </select>
                            <span x-show="memberErrors.role" x-text="memberErrors.role" class="text-danger"></span>
                        </div>

                        <div class="mb-3">
                            <label for="invitation_email" class="form-label">Send invitation email*</label>
                            <div class=" align-items-center">
                                <label class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" name="invitation_email"
                                        :value="true" x-on:input="memberErrors.invitation_email = ''"
                                        x-model="memberForm.invitation_email" selected>
                                    Yes (most common)
                                </label>
                                <label class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" name="invitation_email"
                                        :value="false" x-on:input="memberErrors.invitation_email = ''"
                                        x-model="memberForm.invitation_email">
                                    No
                                </label>
                            </div>
                            <span x-show="memberErrors.invitation_email" x-text="memberErrors.invitation_email"
                                class="text-danger"></span>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2"
                                data-bs-dismiss="modal">Cancel</button>
                            <span class="btn btn-primary deal-save" @click="submitMemberForm(memberForm)">
                                Save
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--  <div class="modal fade" id="editPartnerModal" tabindex="-1" aria-labelledby="editPartnerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content px-2">
                <div class="modal-header row bg-primary text-white">
                    <h5 class="modal-title col text-white" id="editPartnerModalLabel">Edit Partner</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div>
                        @csrf
                        <div class="mb-3">
                            <label for="edit_first_name" class="form-label">First Name*</label>
                            <input type="text" id="edit_first_name" name="first_name" class="form-control"
                                x-on:input="memberErrors.first_name = ''" x-model="editMemberForm.first_name"
                                required>
                            <span x-show="memberErrors.first_name" x-text="memberErrors.first_name"
                                class="text-danger"></span><label for="edit_first_name" class="form-label">First Name*</label>
                            <input type="text" id="edit_first_name" name="first_name" class="form-control"
                                x-on:input="memberErrors.first_name = ''" x-model="editMemberForm.first_name"
                                required>
                            <span x-show="memberErrors.first_name" x-text="memberErrors.first_name"
                                class="text-danger"></span>
                        </div>
                        <div class="mb-3">
                            <label for="edit_last_name" class="form-label">Last Name*</label>
                            <input type="text" id="edit_last_name" name="last_name" class="form-control"
                                x-on:input="memberErrors.last_name = ''" x-model="editMemberForm.last_name" required>
                            <span x-show="memberErrors.last_name" x-text="memberErrors.last_name"
                                class="text-danger"></span>
                        </div>
                        <div class="mb-3">
                            <label for="edit_role" class="form-label">Select Role*</label>
                            <select id="edit_role" name="role" class="form-select"
                                x-on:input="memberErrors.role = ''" x-model="editMemberForm.role" required>
                                <option value="">Select role</option>
                                <option value="admin_sponsor">Admin Sponsor</option>
                                <option value="co_sponsor">Co-Sponsor</option>
                                <option value="cpa_accountant">CPA/Accountant</option>
                                <option value="registered_investment_advisor">Registered Investment advisor</option>
                            </select>
                            <span x-show="memberErrors.role" x-text="memberErrors.role" class="text-danger"></span>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2"
                                data-bs-dismiss="modal">Cancel</button>
                            <span class="btn btn-primary deal-save"
                                @click="submitEditMemberForm(editMemberForm)">Save</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  --}}
    <div class="modal fade" id="editPartnerModal" tabindex="-1" aria-labelledby="editPartnerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary ">
                    <h5 class="modal-title text-white" id="editPartnerModalLabel">Edit Partner</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label"> Name*</label>
                        <input type="text" id="name" name="first_name" class="form-control"
                            x-on:input="memberErrors.name = ''" x-model="editMemberForm.name" required readonly>
                        <span x-show="memberErrors.name" x-text="memberErrors.name" class="text-danger"></span>
                    </div>
                    <div class="mb-3">
                        <label for="edit_role" class="form-label">Select Role*</label>
                        <select id="edit_role" name="role" class="form-select"
                            x-on:input="memberErrors.role = ''" x-model="editMemberForm.role" required>
                            <option value="">Select role</option>
                            <option value="admin_sponsor">Admin Sponsor</option>
                            <option value="co_sponsor">Co-Sponsor</option>
                            <option value="cpa_accountant">CPA/Accountant</option>
                            <option value="registered_investment_advisor">Registered Investment advisor</option>
                        </select>
                        <span x-show="memberErrors.role" x-text="memberErrors.role" class="text-danger"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                    <span class="btn btn-primary deal-save" @click="submitEditMemberForm(editMemberForm)">Save</span>
                </div>
            </div>
        </div>
    </div>
</div>
@push('script')
    <script>
        function membersForm() {
            return {
                loading: false,
                members: @json($partners),
                title_case: (str) => str.replace(/\b\w/g, l => l.toUpperCase()),
                memberForm: {
                    _token: csrf,
                    deal_id: '{{ $deal->id }}',
                    contact: '',
                    first_name: '',
                    last_name: '',
                    email_address: '',
                    role: '',
                    invitation_email: 'true',
                },
                memberErrors: {},
                async submitMemberForm(data) {
                    this.loading = true;
                    let url = "{{ route('admin.deals.storeMember', $deal->id) }}";
                    if ("{{ $prefix }}" == 'partner') {
                        let url = "{{ route('partner.deals.storeMember', $deal->id) }}";
                    }

                    try {

                        let formData = new FormData();
                        for (const key in data) {
                            if (data.hasOwnProperty(key)) {
                                formData.append(key, data[key]);
                            }
                        }

                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf
                            },
                            body: formData
                        });

                        this.loading = false;

                        if (response.status === 422) {
                            const responseData = await response.json();
                            // update errors in alpine data
                            this.memberErrors = responseData.errors;
                            return;
                        }

                        const responseData = await response.json();
                        if (response.status === 200) {
                            // Hide the modal (using Bootstrap's modal API)
                            const modalElement = document.querySelector('.modal.show');
                            const modalInstance = bootstrap.Modal.getInstance(modalElement);
                            modalInstance.hide();

                            // Clear the member form
                            this.memberForm = {
                                _token: csrf,
                                deal_id: '{{ $deal->id }}',
                                first_name: '',
                                last_name: '',
                                email_address: '',
                                role: '',
                                invitation_email: '1',
                            };

                            this.members.push(responseData.member);
                        } else {
                            // alert(responseData.message);
                            console.log(responseData);
                        }

                    } catch (error) {
                        console.error('Error:', error)
                    }
                },
                confirmMemberDelete(memberId) {
                    Swal.fire({
                        title: 'Delete Member',
                        text: "Are you sure you want to delete this Member? This action cannot be undone!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Build URL dynamically using the memberId
                            let url = `{{ url('admin/deals/' . $deal->id . '/member') }}/${memberId}`;
                            if ("{{ $prefix }}" == 'partner') {
                                url = `{{ url('partner/deals/' . $deal->id . '/member') }}/${memberId}`;
                            }
                            // Call the deleteMember method
                            this.deleteMember(url, memberId, () => {
                                Swal.fire(
                                    'Deleted!',
                                    'Member has been deleted successfully.',
                                    'success'
                                );
                                // Remove member from the Alpine array
                                this.members = this.members.filter(m => m.id !== memberId);
                            });
                        }
                    });
                },

                // Function to delete member via fetch API
                async deleteMember(url, memberId, onSuccess) {
                    try {
                        const response = await fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                            },
                        });
                        const data = await response.json();
                        if (data.success) {
                            if (onSuccess) onSuccess();
                        } else {
                            Swal.fire(
                                'Error!',
                                data.message || 'An error occurred while deleting the Member.',
                                'error'
                            );
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire(
                            'Error!',
                            'An error occurred while deleting the Member.',
                            'error'
                        );
                    }
                },

                titleCase(str) {
                    return str
                        .replace(/[-_]/g, ' ')
                        .replace(/\b\w/g, l => l.toUpperCase());
                },
                editMemberForm: {
                    _token: csrf,
                    deal_id: '{{ $deal->id }}',
                    contact: '',
                    first_name: '',
                    last_name: '',
                    email_address: '',
                    role: '',
                    invitation_email: 'true',
                },
                openEditMemberModal(member) {
                    this.editMemberForm = {
                        ...member,
                        contact: member.contact || '',
                        role: member.pivot.role || '',
                        first_name: member.name ? member.name.split(' ')[0] : '',
                        last_name: member.name ? member.name.split(' ')[1] : '',
                        email_address: member.email || '',
                        invitation_email: member.email_interception || 'true',
                    };
                    const modalElement = document.getElementById('editPartnerModal'); // Updated ID
                    const modalInstance = new bootstrap.Modal(modalElement);
                    modalInstance.show();
                },
                async submitEditMemberForm(data) {
                    this.loading = true;
                    let url = "/admin/deals/{{ $deal->id }}/members/" + data.id + "/update";
                    if ("{{ $prefix }}" === 'partner') {
                        url = "/partner/deals/{{ $deal->id }}/members/" + data.id + "/update";
                    }



                    try {
                        let formData = new FormData();
                        formData.append('_token', csrf);
                        formData.append('first_name', data.first_name);
                        formData.append('last_name', data.last_name);
                        formData.append('role', data.role);

                        const response = await fetch(url, {
                            method: 'POST', // Using POST since we're reusing storeMember
                            headers: {
                                'X-CSRF-TOKEN': csrf
                            },
                            body: formData
                        });

                        this.loading = false;

                        if (response.status === 422) {
                            const responseData = await response.json();
                            this.memberErrors = responseData.errors;
                            return;
                        }

                        const responseData = await response.json();
                        if (response.status === 200) {
                            const modalElement = document.querySelector('.modal.show');
                            const modalInstance = bootstrap.Modal.getInstance(modalElement);
                            modalInstance.hide();

                            // Update the member in the list
                            this.members = this.members.map(member =>
                                member.id === responseData.member.id ? {
                                    ...member,
                                    name: `${data.first_name} ${data.last_name}`,
                                    pivot: {
                                        ...member.pivot,
                                        role: data.role
                                    }
                                } : member
                            );
                        } else {
                            console.log(responseData);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                }
            }
        }
    </script>
@endpush
@push('style')
    <style>
        #editPartnerModal .modal-dialog {
            max-width: 500px;
            /* Adjust width as needed */
            margin: 1.75rem auto;
            /* Default Bootstrap centering */
        }

        #editPartnerModal .modal-content {
            border-radius: 8px;
            /* Optional: rounded corners */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            /* Optional: shadow for depth */
        }
    </style>
@endpush
