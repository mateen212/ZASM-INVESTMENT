@extends('admin.layouts.app') <!-- Assuming you have a main app layout -->

@push('style')
    <style>
        .secondary-assets {
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
            background-color: #f6f7f7;
        }

        .text-sm {
            font-size: .75rem;
        }
    </style>

    <style>
        .btn {
            white-space: nowrap;
        }

        .square {
            position: relative;
            width: 100%;
            padding-top: 100%;
            /* 1:1 Aspect Ratio */
        }

        .square img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
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

        .img-thumbnail {
            padding: 0 !important;
        }
    </style>
@endpush

@php
    if(auth('admin')->user()->hasRole('partner')){
        $prefix = 'partner';
    } else { 
        $prefix = 'admin';
    }
@endphp

@section('panel')
    <div class="card" x-data="assetDetail()">
        <div class="card-body">
            <template x-if="loading">
                <div class="custom-loader-overlay">
                    <div class="custom-loader"></div>
                </div>
            </template>
            <nav aria-label="breadcrumbs">
                <ol class="breadcrumbs align-items-center">
                    <li class="breadcrumbs-item">
                        <a href="{{route ('admin.dashboard')}}" class="home-icon"><i class="fas fa-home" title="Dashboard"></i></a>
                    </li>
                    <li class="breadcrumbs-item" onclick="window.location.href='{{ route($prefix . '.deals.index') }}'">Deals</li>
                    <li class="breadcrumbs-items">></li>
                    <li class="breadcrumbs-item" onclick="window.location.href='{{ route($prefix . '.deals.summary', $deal->id ) }}'">{{$deal->name}}</li>
                    <li class="breadcrumbs-items">></li>
                    <li class="breadcrumbs-item" onclick="window.location.href='{{ route($prefix . '.deals.assets.asset_detail', [$deal->id, $asset->id]) }}'">{{$asset->name}}</li>
                </ol>
            </nav>
            <hr>
            <div class="d-flex justify-content-between mt-4 align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-outline-primary rounded-lg" onclick="window.history.back();">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </button>
                    <h2 class="mb-0 fw-semibold" style="font-size: 24px;">{{$asset->name}}</h2>
                </div>
            </div>
            <div class="mt-5">
                <div class="row p-2 text-sm">
                    <!-- Left Column (70%) -->
                    <div class="col-md-8 asset1">
                        <div class="row mb-3">
                            <label for="propertyName" class="col-sm-4 col-form-label">Name of property <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="{{ $asset->name }}" x-model="asset.name">
                            </div>
                        </div>

                        <h4 class="mb-3">ADDRESS</h4>
                        <div class="mb-4">
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Street address line 1</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" value="{{ $asset->address }}"
                                        x-model="asset.address">
                                </div>
                            </div>
                            {{-- <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Street address line 2</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" placeholder="">
                                </div>
                            </div> --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-sm-4 col-form-label">City</label>
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" value="{{ $asset->city }}"
                                                x-model="asset.city">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-sm-4 col-form-label">State</label>
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" value="{{ $asset->state }}"
                                                x-model="asset.state">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class=" mb-3">
                                        <label class="col-sm-4 col-form-label">Zipcode</label>
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" value="{{ $asset->zip }}"
                                                x-model="asset.zip">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-sm-4 col-form-label">Country</label>
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" value="{{ $asset->country }}"
                                                x-model="asset.country">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h4 class="mb-3">Additional Information</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-sm-4 col-form-label">Property Type</label>
                                        <div class="col-sm-12">
                                            <select class="form-select" value="{{ $asset->property_type }}"
                                                x-model="asset.property_type">
                                                <option selected disabled>Select type</option>
                                                @foreach (config('cre.property_types') as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-sm-4 col-form-label">Property Class</label>
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" value="{{ $asset->property_class }}"
                                                x-model="asset.property_class">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-sm-4 col-form-label">Number of Units</label>
                                        <div class="col-sm-12">
                                            <input type="number" class="form-control" x-model="asset.number_of_units">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-sm-4 col-form-label">Type of Units</label>
                                        <div class="col-sm-12">
                                            <select class="form-select" x-model="asset.type_of_units">
                                                <option selected>Units</option>
                                                @foreach (config('cre.type_of_units') as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-sm-4 col-form-label">Net Asset Value ($)</label>
                                        <div class="col-sm-12">
                                            <input type="text" x-on:input="moneyFormat($el)" class="form-control"
                                                placeholder="$10,000,000" x-model="asset.net_asset_value">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-sm-4 col-form-label">Acquisition Price ($)</label>
                                        <div class="col-sm-12">
                                            <input type="text" x-on:input="moneyFormat($el)" class="form-control"
                                                x-model="asset.acquisition_price">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-sm-4 col-form-label">Acquisition Date</label>
                                        <div class="col-sm-12">
                                            <input type="date" onclick="this.showPicker()"  class="form-control" x-model="asset.acquisition_date">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-sm-4 col-form-label">Exit Price ($)</label>
                                        <div class="col-sm-12">
                                            <input type="text" x-on:input="moneyFormat($el)" class="form-control"
                                                x-model="asset.exit_price">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-sm-4 col-form-label">Exit Date</label>
                                        <div class="col-sm-12">
                                            <input type="date" onclick="this.showPicker()"  class="form-control" x-model="asset.exit_date">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-sm-4 col-form-label">Year Built</label>
                                        <div class="col-sm-12">
                                            <input type="number" x-mask="9999" placeholder="YYYY" class="form-control"
                                                x-model="asset.year_built">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-sm-4 col-form-label">Year Renovated</label>
                                        <div class="col-sm-12">
                                            <input type="number" x-mask="9999" placeholder="YYYY" class="form-control"
                                                x-model="asset.year_renovated">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column (30%) - Image Upload -->
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="property_images" class="form-label">Upload Photos</label>
                            <div class="file-uploader">
                                <input type="file" id="property_images" name="property_images[]" class="form-control"
                                    multiple @change="handleFiles($event)" hidden>
                                <div class="drop-zone" @drop.prevent="handleDrop($event)"
                                    @dragover.prevent="dragOver = true" @dragleave.prevent="dragOver = false"
                                    :class="{ 'drag-over': dragOver }">
                                    <p class="drop-zone-text">Drag & drop files here or click to upload</p>
                                    <button type="button" class="btn btn-primary"
                                        @click="document.getElementById('property_images').click()">Select
                                        Files</button>
                                </div>
                                <div class="file-list mt-3">
                                    <div class="row">
                                        {{-- <template x-for="file in files" :key="file.name">
                                                <div class="col-4 position-relative p-2">
                                                    <div class="square">
                                                        <img :src="URL.createObjectURL(file)" alt=""
                                                            class="img-thumbnail w-100 h-100">
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm position-absolute top-0 end-0"
                                                            @click="removeFile(file)">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="asset_media" class="form-label">Asset Media</label>
                            <div class="row">
                                <template x-for="media in asset.asset_media" :key="media.id">
                                    <div class="col-4 position-relative p-2">
                                        <div class="square">
                                            <img :src="baseurl + media.media_url" alt=""
                                                class="img-thumbnail w-100 h-100">
                                            <button type="button"
                                                class="btn btn-danger btn-sm position-absolute top-0 end-0"
                                                @click="removeMedia(media.id)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-2">
                    <button @click="updateAssets()" class="btn btn-primary ">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
     <script>
        function alpineHelpers() {
            return {
                moneyFormat(el) {
                    let value = el.value;
                    // Remove non-numeric characters except for the decimal point
                    value = value.replace(/[^\d.]/g, '');

                    // Remove leading zeros
                    value = value.replace(/^0+(?=\d)/, '');
                    // If there's more than one decimal point, keep only the first one
                    const parts = value.split('.');
                    if (parts.length > 2) {
                        value = parts[0] + '.' + parts.slice(1).join('');
                    }
                    // Limit the decimal part to two digits
                    if (parts[1]) {
                        parts[1] = parts[1].slice(0, 2);
                        value = parts.join('.');
                    }
                    // Add commas for thousands separator
                    value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    // Ensure that the value starts with $ and no other non-numeric characters
                    if (value !== '') {
                        el.value = '$' + value;
                    } else {
                        el.value = '$0';
                    }
                },
                percentFormat(el) {
                    let value = el.value;
                    // Remove non-numeric characters except for the decimal point
                    value = value.replace(/[^\d.]/g, '');

                    // Remove leading zeros
                    value = value.replace(/^0+(?=\d)/, '');
                    // If there's more than one decimal point, keep only the first one
                    const parts = value.split('.');
                    if (parts.length > 2) {
                        value = parts[0] + '.' + parts.slice(1).join('');
                    }
                    // Limit the decimal part to two digits
                    if (parts[1]) {
                        parts[1] = parts[1].slice(0, 2);
                        value = parts.join('.');
                    }
                    // Ensure that the value ends with % and no other non-numeric characters
                    if (value !== '') {
                        el.value = value + '%';
                    } else {
                        el.value = '0%';
                    }
                    el.setSelectionRange(el.value.length - 1, el.value.length - 1);
                }
            }
        }
    </script>
    <script>
        var csrf = '{{ csrf_token() }}';

        function assetDetail() {
            return {
                ...alpineHelpers(),
                assets: @json($deal->assets),
                asset: @json($asset),
                baseurl: "{{ asset('') }}",
                files: [],
                loading: false,
                dragOver: false,

                async updateAssets() {
                    this.loading = true;
                    let url = "{{ route($prefix . '.offering.assets.update', $asset->id) }}";
                    await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf // Assuming csrf is defined elsewhere in your script
                            },
                            body: JSON.stringify(this.asset)
                        }).then(response => response.json())
                        .then(data => {
                            this.loading = false;
                            if (data.success) {
                                cosyAlert('Assets updated successfully', 'success');
                            } else {
                                cosyAlert('Failed to update assets', 'error');
                            }
                        }).catch(error => {
                            this.loading = false;
                            console.error('Error:', error);
                        });
                },
                handleFiles() {
                    const files = document.getElementById('property_images').files;
                    for (let i = 0; i < files.length; i++) {
                        this.files.push(files[i]);
                    }
                    // Upload images to server
                    this.uploadFiles(files);
                },
                uploadFiles(files) {
                    let formData = new FormData();
                    for (let i = 0; i < files.length; i++) {
                        formData.append('media[]', files[i]);
                    }
                    this.loading = true;
                    fetch("{{ route($prefix . '.offering.assets.upload', $asset->id) }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            this.loading = false;
                            if (data.success) {
                                this.asset.asset_media = data.asset_media;
                            }
                        })
                        .catch(error => {
                            this.loading = false;
                            console.error('Error:', error);
                        });
                },
                async removeMedia(mediaId) {
                    this.loading = true;
                    let url = "{{ route($prefix . '.offering.assets.remove-media', $asset->id) }}";
                    await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf
                            },
                            body: JSON.stringify({
                                media_id: mediaId
                            })
                        }).then(response => response.json())
                        .then(data => {
                            this.loading = false;
                            if (data.success) {
                                this.asset.asset_media = data.asset_media;
                            }
                        }).catch(error => {
                            this.loading = false;
                            console.error('Error:', error);
                        });
                },
                async handleDrop(event) {
                    const droppedFiles = Array.from(event.dataTransfer.files);
                    this.files.push(...droppedFiles);
                    this.dragOver = false;
                    this.uploadFiles(this.files);
                },
                init() {
                    //debugger;

                },
                
            };
        }
    </script>
@endpush
