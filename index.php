<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crypto Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Cryptocurrency Dashboard</h1>
        <form method="GET" action="">
            <label for="crypto">Choose a cryptocurrency:</label>
            <input type="text" id="crypto" name="crypto" placeholder="e.g. bitcoin">
            <button type="submit">Get Data</button>
        </form>

        <?php
        if (isset($_GET['crypto'])) {
            $crypto = htmlspecialchars($_GET['crypto']);
            $apiUrl = "https://api.coingecko.com/api/v3/coins/$crypto?localization=false&market_data=true";

            $response = file_get_contents($apiUrl);
            $data = json_decode($response, true);

            if ($data) {
                echo '<div class="crypto-data">';
                echo '<h2>' . ucfirst($crypto) . ' (' . strtoupper($data['symbol']) . ')</h2>';
                echo '<p>Current Price: $' . $data['market_data']['current_price']['usd'] . '</p>';
                echo '<p>Market Cap: $' . number_format($data['market_data']['market_cap']['usd']) . '</p>';
                echo '<h3 style="color:white">Price Chart (Last 30 days)</h3>';
                echo '<canvas id="priceChart"></canvas>';
                echo '</div>';

                $chartUrl = "https://api.coingecko.com/api/v3/coins/$crypto/market_chart?vs_currency=usd&days=30";
$chartResponse = file_get_contents($chartUrl);
$chartData = json_decode($chartResponse, true);

if (isset($chartData['prices'])) {
    $pricePoints = array_map(function($point) {
        return $point[1]; // [timestamp, price]
    }, $chartData['prices']);
    $jsonChartData = json_encode($pricePoints);
}

            } else {
                echo '<p>Cryptocurrency not found. Please try another.</p>';
            }
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartData = <?php echo $jsonChartData ?? '[]'; ?>;
        const ctx = document.getElementById('priceChart').getContext('2d');

        const priceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: Array.from({ length: chartData.length }, (_, i) => i + 1),
                datasets: [{
                    label: 'Price (USD)',
                    data: chartData,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    fill: false
                }]
            },
            options: {
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Time (Last 7 Days)'
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Price (USD)'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
