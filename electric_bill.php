<?php
$name = $customerType = "";
$prevReading = $currReading = 0;
$error = $result = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name         = htmlspecialchars(trim($_POST["consumer_name"] ?? ""));
    $customerType = htmlspecialchars(trim($_POST["customer_type"] ?? ""));
    $prevReading  = floatval($_POST["prev_reading"] ?? 0);
    $currReading  = floatval($_POST["curr_reading"] ?? 0);

    if ($currReading < $prevReading) {
        $error = "Invalid Reading: Current reading cannot be lower than previous.";
    } else {
        $usage      = $currReading - $prevReading;
        $rate       = ($usage <= 200) ? 10.00 : 15.00;
        $billAmount = $usage * $rate;
        $surcharge  = ($customerType === "Commercial") ? 500.00 : 0.00;
        $totalBill  = $billAmount + $surcharge;

        $result = [
            "name"         => $name,
            "customerType" => $customerType,
            "usage"        => $usage,
            "rate"         => $rate,
            "surcharge"    => $surcharge,
            "total"        => $totalBill,
        ];
    }
}

$getPreFill = htmlspecialchars(trim($_GET["name"] ?? ""));
if ($getPreFill && !$name) $name = $getPreFill;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Eco-Friendly Electric Bill App</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f2f2f2;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
        }

        .container {
            background: #ffffff;
            width: 100%;
            max-width: 480px;
            padding: 40px;
        }

        h1 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1a73e8;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-size: 0.85rem;
            color: #333;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 0.9rem;
            color: #333;
            background: #fff;
            outline: none;
        }

        input:focus, select:focus {
            border-color: #1a73e8;
        }

        .btn-submit {
            width: 100%;
            padding: 10px;
            background: #1a73e8;
            color: #fff;
            font-size: 0.95rem;
            font-weight: 600;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 8px;
        }

        .btn-submit:hover {
            background: #1558b0;
        }

        /* Error */
        .alert-error {
            background: #fff0f0;
            border-left: 4px solid #e74c3c;
            padding: 10px 14px;
            color: #c0392b;
            font-size: 0.88rem;
            margin-bottom: 20px;
        }

        /* Result */
        .result-box {
            background: #dff0d8;
            border-radius: 4px;
            padding: 16px 18px;
            margin-top: 24px;
            font-size: 0.88rem;
            color: #333;
            line-height: 1.9;
        }

        .result-box p span {
            font-weight: 600;
        }

        .divider {
            border: none;
            border-top: 1px solid #e5e5e5;
            margin: 24px 0;
        }
    </style>
</head>
<body>
<div class="container">

    <h1>Eco-Friendly Electric Bill App</h1>

    <?php if ($error): ?>
        <div class="alert-error">
            ⚠️ <?= $error ?>
        </div>
    <?php endif; ?>

    <!-- POST Form -->
    <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">

        <div class="form-group">
            <label for="consumer_name">Consumer Name:</label>
            <input
                type="text"
                id="consumer_name"
                name="consumer_name"
                value="<?= htmlspecialchars($name) ?>"
                required
            />
        </div>

        <div class="form-group">
            <label for="prev_reading">Previous Reading (kWh):</label>
            <input
                type="number"
                id="prev_reading"
                name="prev_reading"
                value="<?= htmlspecialchars($prevReading ?: '') ?>"
                min="0"
                step="any"
                required
            />
        </div>

        <div class="form-group">
            <label for="curr_reading">Current Reading (kWh):</label>
            <input
                type="number"
                id="curr_reading"
                name="curr_reading"
                value="<?= htmlspecialchars($currReading ?: '') ?>"
                min="0"
                step="any"
                required
            />
        </div>

        <div class="form-group">
            <label for="customer_type">Customer Type:</label>
            <select id="customer_type" name="customer_type" required>
                <option value="" disabled <?= !$customerType ? 'selected' : '' ?>>Select Type</option>
                <option value="Residential" <?= $customerType === 'Residential' ? 'selected' : '' ?>>Residential</option>
                <option value="Commercial"  <?= $customerType === 'Commercial'  ? 'selected' : '' ?>>Commercial</option>
            </select>
        </div>

        <button type="submit" class="btn-submit">Calculate Bill</button>
    </form>

    <?php if ($result): ?>
        <div class="result-box">
            <p><span>Consumer Name:</span> <?= $result['name'] ?></p>
            <p><span>Customer Type:</span> <?= $result['customerType'] ?></p>
            <p><span>Usage:</span> <?= number_format($result['usage'], 2) ?> kWh</p>
            <p><span>Rate per kWh:</span> P<?= number_format($result['rate'], 2) ?></p>
            <?php if ($result['surcharge'] > 0): ?>
            <p><span>Service Charge:</span> P<?= number_format($result['surcharge'], 2) ?></p>
            <?php endif; ?>
            <p><span>Total Bill:</span> P<?= number_format($result['total'], 2) ?></p>
        </div>
    <?php endif; ?>

</div>
</body>
</html>
