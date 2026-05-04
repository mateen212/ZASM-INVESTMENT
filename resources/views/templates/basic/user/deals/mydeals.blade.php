@extends($activeTemplate . 'layouts.master')
@section('content')
    <div x-data="uploadModal()" x-cloak>
        <div class="mr-auto mb-4 breadcrumb-dashboard">
            <form class="flex-start">
                <h5>Joined deals</h5>
                <div class="position-relative">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search Deals..."
                        style="padding-right: 2.5rem;" />
                    <i class="la la-search position-absolute"
                        style="right: 10px; top: 50%; transform: translateY(-50%) scaleX(-1); pointer-events: none;"></i>
                </div>

            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>
                            <div class="sortable">
                                <p class="column-header">Deal Name</p>
                                <div class="column-body">
                                    <span class="sort-icons">▲</span>
                                    <span class="sort-icons">▼</span>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="sortable">
                                <p class="column-header">Deal stage</p>
                                <div class="column-body">
                                    <span class="sort-icons">▲</span>
                                    <span class="sort-icons">▼</span>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="sortable">
                                <p class="column-header">Close date</p>
                                <div class="column-body">
                                    <span class="sort-icons">▲</span>
                                    <span class="sort-icons">▼</span>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="sortable">
                                <p class="column-header">Includes investments by</p>
                                <div class="column-body">
                                    <span class="sort-icons">▲</span>
                                    <span class="sort-icons">▼</span>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="sortable">
                                <p class="column-header">Sponsors</p>
                                <div class="column-body">
                                    <span class="sort-icons">▲</span>
                                    <span class="sort-icons">▼</span>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="sortable">
                                <p class="column-header">Investment total</p>
                                <div class="column-body">
                                    <span class="sort-icons">▲</span>
                                    <span class="sort-icons">▼</span>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="sortable">
                                <p class="column-header">Distribution total</p>
                                <div class="column-body">
                                    <span class="sort-icons">▲</span>
                                    <span class="sort-icons">▼</span>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="sortable">
                                <p class="column-header">Action required</p>
                                <div class="column-body">
                                    <span class="sort-icons">▲</span>
                                    <span class="sort-icons">▼</span>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($deals as $deal)
                        <tr>
                            <td>{{ $deal->name }}</td>
                            <td>{{ $deal->deal_stage }}</td>
                            <td>{{ $deal->close_date }}</td>
                            <td></td>
                            <td>{{ $deal->primary_sponsor }}</td>
                            <td>
                                @php
                                    $totalInvestmentAmount = $deal->total_investment_amount;
                                    $formattedInvestment =
                                        intval($totalInvestmentAmount) == $totalInvestmentAmount
                                            ? '$' . number_format($totalInvestmentAmount, 0)
                                            : '$' . number_format($totalInvestmentAmount, 2);
                                @endphp
                                {{ $formattedInvestment }}
                            </td>
                            <td>
                                @php
                                    $totalDistributionAmount = $deal->total_distribution_amount;
                                    $formattedDistribution =
                                        intval($totalDistributionAmount) == $totalDistributionAmount
                                            ? '$' . number_format($totalDistributionAmount, 0)
                                            : '$' . number_format($totalDistributionAmount, 2);
                                @endphp
                                {{ $formattedDistribution }}
                            </td>
                            <td class="text-center align-middle">
                                <span role="button" title="View"
                                    class="d-inline-flex justify-content-center align-items-center">
                                    <a href="#" class="text-dark"><i class="far fa-eye"></i></a>
                                </span>
                            </td>


                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


        {{--  In-progress investments  --}}
        <div class="mb-4 mt-8 breadcrumb-dashboard">
            <form>
                <h5>In-progress investments</h5>
                <div class="position-relative">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Search investments..." style="padding-right: 2.5rem;" />
                    <i class="la la-search position-absolute"
                        style="right: 10px; top: 50%; transform: translateY(-50%) scaleX(-1); pointer-events: none;"></i>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>
                            <div class="sortable">
                                <p class="column-header">Offering Name</p>
                                <div class="column-body">
                                    <span class="sort-icons">▲</span>
                                    <span class="sort-icons">▼</span>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="sortable">
                                <p class="column-header">Sponsors</p>
                                <div class="column-body">
                                    <span class="sort-icons">▲</span>
                                    <span class="sort-icons">▼</span>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="sortable">
                                <p class="column-header">Amount</p>
                                <div class="column-body">
                                    <span class="sort-icons">▲</span>
                                    <span class="sort-icons">▼</span>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="sortable">
                                <p class="column-header">Class</p>
                                <div class="column-body">
                                    <span class="sort-icons">▲</span>
                                    <span class="sort-icons">▼</span>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="sortable">
                                <p class="column-header">Profile</p>
                                <div class="column-body">
                                    <span class="sort-icons">▲</span>
                                    <span class="sort-icons">▼</span>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="sortable">
                                <p class="column-header">Status</p>
                                <div class="column-body">
                                    <span class="sort-icons">▲</span>
                                    <span class="sort-icons">▼</span>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="sortable">
                                <p class="column-header">Action</p>
                                <div class="column-body">
                                    <span class="sort-icons">▲</span>
                                    <span class="sort-icons">▼</span>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>

                <tbody class="table">
                    @foreach ($investments as $investment)
                        <tr>
                            @if ($investment->offering)
                                <td>
                                    <a href="{{ route('user.offerings.offering', $investment->offering->uuid) }}">
                                        {{ $investment->offering->name }}
                                    </a>


                                </td>
                                <td>{{ $investment->primary_sponsor }}</td>
                                <td>{{ $investment->investment_amount }}</td>
                                <td>{{ $investment?->class?->equity_class_name }}</td>
                                <td>{{ $investment->profile?->name }}</td>
                                <td>{{ $investment->investment_status }}</td>
                                <td>
                                    @if ($investment->wire_transfer_status == 'Pending')
                                        <span role="button " title="View"
                                            class="d-inline-flex justify-content-center align-items-center">
                                            <a href=""
                                                class="text-dark"><i class="far fa-eye"></i></a>
                                        </span>
                                    @else
                                        <span class="btn_primary" data-bs-toggle="modal"
                                            class="d-inline-flex justify-content-center align-items-center"
                                            data-bs-target="#addBankDetailModal"
                                            @click="setInvestmentId({{ $investment->id }})">
                                            Add Invoice
                                        </span>
                                    @endif

                                </td>
                            @endif
                            <template x-if="offerings.length === 0">
                        <tr>
                            <td colspan="7" class="text-center">No offering available</td>
                        </tr>
                        </template>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="modal fade" id="addBankDetailModal" tabindex="-1" aria-labelledby="addBankDetailModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addBankDetailModalLabel">Add Wire Transfer Detail</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <input type="hidden" x-model="investmentId" name="investment_id">
                        <div class="modal-body">
                            @csrf

                            <!-- Multiple Image Upload -->
                            <div class="mb-3">
                                <label for="invoice_images" class="form-label">Upload Invoice Images</label>
                                <input type="file" class="form-control" id="invoice_images" name="invoice_images[]"
                                    accept="image/*" multiple required @change="handleFileChange($event)">
                                <div class="mt-2">
                                    <template x-for="url in previewUrls" :key="url">
                                        <img :src="url" alt="Preview" class="img-thumbnail"
                                            style="max-width: 100px; margin: 5px;">
                                    </template>
                                </div>
                                @error('invoice_images.*')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Transaction Details -->
                            <div class="mb-3">
                                <label for="transaction_details" class="form-label">Transaction Details</label>
                                <textarea class="form-control" id="transaction_details" x-model="transactionDetails" name="transaction_details"
                                    rows="4" placeholder="Enter transaction details (e.g., bank name, date, reference number)" required></textarea>
                                @error('transaction_details')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="closeModal">Cancel</button>
                            <span class="btn btn-primary deal-save" @click="submitBankDetail()">
                                Save
                            </span>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
@push('style')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <style>
        .btn_primary {
            background-color: blue !important;
            border-radius: 4px;
            /* padding: 10px !important; */
            height: 40px;
            width: 110px;
            display: flex;
            justify-content: center;
            float: right;
            color: white !important;
            font-weight: bolder;
            border: none;
            padding: 10px 16px !important;
            cursor: pointer;
        }

        .btn_primary:hover {
            color: white;
            border-radius: 4px;
            height: 40px;
            width: 110px;
            display: flex;
            float: right;
            background-color: #69A2FF !important;
            font-weight: bold;
            border: none;
            padding: 10px 16px;
            cursor: pointer;
            border-radius: 4px;
            transition: 0.3sec;
        }

        .custom-button .icon {
            width: 1rem;
            height: 1rem;
        }

        .breadcrumb-dashboard {
            margin-bottom: 50px;
            margin-top: 20px;
        }

        .breadcrumb-dashboard form {
            margin-right: auto;
            margin-left: 0;
        }

        .table-light {
            font-size: 8px;
            white-space: nowrap;
        }

        .table-responsive .table .table-light tr th {
            padding: 23px 11px;
        }

        .sortable {
            display: flex;
            align-items: center;
            gap: 6px;
            /* Space between icons and text */
            position: relative;
            cursor: pointer;
            font-weight: bold;
            color: #0d0d0d;
            white-space: nowrap;
            justify-content: space-between;

        }

        .sort-icons {
            font-size: 10px;
            color: #6c757d;
            line-height: 10px;
            display: inline-block;

        }

        .column-header {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 0;
        }

        .column-body {
            text-align: right;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
    </style>
@endpush
@push('script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>

    <script>
        csrf_token = '{{ csrf_token() }}';

        function uploadModal() {
            return {
                showModal: false,
                investmentId: null,
                images: [],
                previewUrls: [],
                transactionDetails: '',
                investmentId: null,
                openModal(investmentId) {
                    this.investmentId = investmentId;
                    this.showModal = true;
                    // Initialize Bootstrap modal
                    const modal = new bootstrap.Modal(document.getElementById('addBankDetailModal'));
                    modal.show();
                },

                setInvestmentId(id) {
                    this.investmentId = id;
                },

                closeModal() {
                    this.showModal = false;
                    this.images = [];
                    this.previewUrls = [];
                    this.transactionDetails = '';
                    this.investmentId = null;

                    // Get the modal instance and hide it
                    const modalElement = document.getElementById('addBankDetailModal');
                    const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                    modal.hide();

                    // Explicitly remove the backdrop and reset modal state
                    modalElement.classList.remove('show');
                    modalElement.style.display = 'none';
                    document.body.classList.remove('modal-open');
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }
                },

                handleFileChange(event) {
                    this.images = Array.from(event.target.files);
                    this.previewUrls = this.images.map(file => URL.createObjectURL(file));
                },
                async submitBankDetail() {
                    if (this.images.length === 0) {
                        alert('Please select at least one image.');
                        return;
                    }

                    const formData = new FormData();
                    this.images.forEach((image, index) => {
                        formData.append(`invoice_images[${index}]`, image);
                    });
                    formData.append('transaction_details', this.transactionDetails);
                    formData.append('investment_id', this.investmentId);
                    formData.append('_token', '{{ csrf_token() }}');

                    try {
                        const url = `/user/investment/${this.investmentId}/upload-invoice`;
                        const response = await fetch(url, {
                            method: 'POST',
                            body: formData,
                        });

                        if (response.ok) {
                            alert('Wire transfer details uploaded successfully!');
                            window.location.reload();
                            this.closeModal();
                        } else {
                            const res = await response.json();
                            console.error(res);
                            alert('Error: ' + (res.message || 'Failed to submit details'));
                        }
                    } catch (error) {
                        console.error('Upload error:', error);
                        alert('An error occurred while uploading the details');
                    }
                }
            };
        }
    </script>
@endpush
