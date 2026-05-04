<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif; /* Use a widely supported font */
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            color: #333333;
        }

        .invoice-container {
            width: 700px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #ddd;
        }

        .header {
            overflow: hidden; /* Clear floats */
        }

        .logo-container {
            float: left;
            width: 120px;
        }

        .logo {
            width: 100%;
            height: auto;
        }

        .company-info {
            float: right;
            text-align: right;
            font-size: 14px;
            color: #333333;
        }

        .title {
            clear: both; /* Clear floats */
            text-align: center;
            font-size: 24px;
            color: #0d6efd;
            margin: 30px 0 20px;
        }

        .details {
            margin-top: 20px;
        }

        .details p {
            margin: 10px 0;
            font-size: 14px;
            border-bottom: 1px solid #eee;
            padding-bottom: 6px;
        }

        .details strong {
            width: 180px;
            display: inline-block;
        }

        .footer {
            clear: both; /* Clear floats */
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #888;
        }
        .logo-container {
            width: 50%;
        }
        .logo-container > img{
            max-width: 150px; /* Adjust the size as needed */
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <div class="logo-container">
                <img src="https://www.xochitlsnacks.com/cdn/shop/files/Xochitl_Chips_and_Salsa_Logo_200x.png" width="200" height="70" />
            </div>
            <div class="company-info">
                <h4>Cre-Cloud</h4><br>
                <p>Real Estate Management System</p>
            </div>
        </div>

        <div class="title">Invoice</div>

        <div class="details">
            <p><strong>Investment ID:</strong> {{ $investment_id }}</p>
            <p><strong>Payment Method:</strong> {{ $payment_method }}</p>
            <p><strong>Bank Name:</strong> {{ $receiving_bank }}</p>
            <p><strong>Bank Address:</strong> {{ $bank_address }}</p>
            <p><strong>Routing Number:</strong> {{ $routing_no }}</p>
            <p><strong>Account Number:</strong> {{ $account_no }}</p>
            <p><strong>Account Type:</strong> {{ $account_type }}</p>
            <p><strong>Beneficiary:</strong> {{ $beneficiary_account_name }}</p>
            <p><strong>Beneficiary Address:</strong> {{ $beneficiary_address }}</p>
        </div>

        <div class="footer">
            <p>Generated on {{ \Carbon\Carbon::now()->format('F d, Y') }}</p>
            <p>© {{ date('Y') }} Cre-Cloud Ltd. All rights reserved.</p>
        </div>
    </div>
</body>
</html>