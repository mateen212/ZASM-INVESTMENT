@extends('admin.layouts.app')

@php
    if(auth('admin')->user()->hasRole('partner')){
        $prefix = 'partner';
    }else{
        $prefix = 'admin';
    }
@endphp

@section('panel')
    <div class="card">
        <div class="card-body">
            <div class="edit-deal" x-data="entityEdit()" x-cloak>
                <template x-if="loading">
                    <div class="custom-loader-overlay">
                        <div class="custom-loader"></div>
                    </div>
                </template>
                <nav aria-label="breadcrumbs">
                    <ol class="breadcrumbs align-items-center">
                        <li class="breadcrumbs-item">
                            <a href="{{ route($prefix . '.dashboard') }}" class="home-icon"><i class="fas fa-home"
                                    title="Dashboard"></i></a>
                        </li>
                        <li class="breadcrumbs-item" onclick="window.location.href='{{ route($prefix . '.deals.index') }}'">Deals
                        </li>
                        <li class="breadcrumbs-items">></li>
                        <li class="breadcrumbs-item"
                            onclick="window.location.href='{{ route($prefix . '.deals.summary', $deal->id) }}'">
                            {{ $deal->name }}</li>
                        <li class="breadcrumbs-items">></li>
                        <li class="breadcrumbs-item" >Edit Entity</li>
                    </ol>
                </nav>
                <hr>
                <div class="d-flex justify-content-between mt-4 align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" class="btn btn-outline-primary rounded-lg" onclick="window.history.back();">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </button>
                        <h2 class="mb-0 fw-semibold" style="font-size: 24px;">Edit entity details</h2>
                    </div>
                </div>

                <!-- Tab Contents -->
                <div class="mt-4">
                    <div>
                        <div class="container mt-4">
                            <h4 class="mb-3">Basic Information</h4>

                            <div class="mb-3">
                                <label class="col-md-3">Organization name <span class="text-danger">*</span></label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control border-1 border-bottom"
                                        x-model="owningEntityForm.owning_entity_name" placeholder="Enter organization name">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Executive Name</label>
                                    <input type="text" class="form-control border-1 border-bottom"
                                        x-model="owningEntityForm.executive_name" placeholder="Enter name">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Executive Title</label>
                                    <input type="text" class="form-control border-1 border-bottom"
                                        x-model="owningEntityForm.executive_title" placeholder="Enter title">
                                </div>
                            </div>


                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Jurisdiction</label>
                                    <input type="text" class="form-control border-1 border-bottom"
                                        x-model="owningEntityForm.jurisdiction" placeholder="Enter jurisdiction">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Taxpayer ID number</label>
                                    <input type="text" class="form-control border-1 border-bottom"
                                        x-model="owningEntityForm.taxpayer_id" placeholder="Enter ID number">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Email address</label>
                                    <input type="email" class="form-control border-1 border-bottom"
                                        x-model="owningEntityForm.email" placeholder="Enter email">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Date formed</label>
                                    <input type="date" class="form-control border-1 border-bottom"
                                        x-model="owningEntityForm.date_formed">
                                </div>
                            </div>

                        </div>
                        <div class="container mt-4">
                            <h4 class="mb-3">Address</h4>

                            <div class="row mb-3">
                                <div class="col-md-3 text-start d-flex align-items-center">
                                    <label class="form-label">Street address line 1</label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" class="form-control border-1 border-bottom"
                                        x-model="owningEntityForm.address_1" placeholder="Enter Street address line 1">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3 text-start d-flex align-items-center">
                                    <label class="form-label">Street address line 2</label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" class="form-control border-1 border-bottom"
                                        x-model="owningEntityForm.address_2" placeholder="Enter Street address line 2">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">City</label>
                                    <input type="text" class="form-control border-1 border-bottom"
                                        x-model="owningEntityForm.city" placeholder="Enter City">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Province</label>
                                    <input type="text" class="form-control border-1 border-bottom"
                                        x-model="owningEntityForm.province" placeholder="Enter Province">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Postal code</label>
                                    <input type="email" class="form-control border-1 border-bottom"
                                        x-model="owningEntityForm.postal_code" placeholder="Enter Postal code">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Country</label>
                                    <input type="text" class="form-control border-1 border-bottom"
                                        x-model="owningEntityForm.country">
                                </div>
                            </div>
                        </div>

                    </div>
                    <button class="btn btn-primary" @click="submitOwningEntityForm()">Save </button>

                </div>
            </div>
        @endsection
        @push('script')
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
            <script>
                var csrf = '{{ csrf_token() }}';

                function entityEdit() {
                    return {
                        owningEntityForm: {
                            deal_id: {{ $deal->id }},
                            owning_entity_name: "{{ $deal->owningEntityDetails->owning_entity_name ?? $deal->owning_entity_name }}",
                            executive_name: '',
                            executive_title: '',
                            jurisdiction: '',
                            taxpayer_id: '',
                            email: '',
                            date_formed: '',
                            address_1: '',
                            address_2: '',
                            city: '',
                            province: '',
                            postal_code: '',
                            country: '',
                        },
                        loading: false,
                        errors: {},

                        async submitOwningEntityForm() {
                            this.loading = true;
                            let url = "{{ route($prefix . '.deals.entityDetailStore', $deal->id) }}";

                            try {

                                let formData = new FormData();
                                for (const key in this.owningEntityForm) {
                                    if (this.owningEntityForm.hasOwnProperty(key)) {
                                        formData.append(key, this.owningEntityForm[key]);
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
                                    this.errors = responseData.errors;
                                    return;
                                }

                                const responseData = await response.json();
                                if (response.status === 200) {
                                    window.location.reload();

                                } else {
                                    console.log(responseData);
                                }

                            } catch (error) {
                                console.error('Error:', error);
                            }
                        },

                    }
                }
            </script>
        @endpush
