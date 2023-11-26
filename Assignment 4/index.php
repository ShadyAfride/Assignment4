<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Weather Forecast</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="container">
    <h1>Weather Forecast</h1>
    <form method="POST">
      <input type="text" name="city" placeholder="Enter city name">
      <button type="submit" name="getWeather">Get Forecast</button>
    </form>
    <div id="weatherInfo" class="grid-container">

    <h1></h1>
      <?php
      if (isset($_POST['getWeather'])) {
        getWeather();
      }
      ?>
    </div>
  </div>
</body>
</html>

<?php
function getWeather() {
  $city = $_POST['city'];
  $apiUrl = "https://api.openweathermap.org/data/2.5/forecast?q=$city&appid=ea8b04a9f72807c95ce660120d58ae07&units=metric";

  $response = file_get_contents($apiUrl);
  $data = json_decode($response, true);

  echo '<div class="city-info">';
  echo '<h2>Weather forecast for ' . $city . ', ' . $data['city']['country'] . '</h2>';
  echo '</div>';
  

  if ($data['cod'] === '200') {
    $groupedForecast = groupForecastByDay($data);
    foreach ($groupedForecast as $day => $dayForecast) {
      $dayName = date('l', strtotime($day));
      echo '<div class="forecast-box">';
      echo '<h3 class="day-title">' . $dayName . '</h3>';
      echo '<div class="forecast-items">';
      foreach ($dayForecast as $forecast) {
        $time = date('H:i', strtotime($forecast['dt_txt']));
        $temperature = $forecast['main']['temp'];
        $description = $forecast['weather'][0]['description'];
        echo '
          <div class="forecast-item">
            <p>' . $time . '</p>
            <p>' . $description . '</p>
            <p>Temperature: ' . $temperature . ' °C</p>
            <p>---------------------</p>
          </div>';
      }
      echo '</div></div>';
    }
  } else {
    echo '<p>Error fetching weather data</p>';
  }
}

function groupForecastByDay($data) {
  $groupedForecast = array();
  foreach ($data['list'] as $forecast) {
    $day = date('Y-m-d', strtotime($forecast['dt_txt']));
    $groupedForecast[$day][] = $forecast;
  }
  return $groupedForecast;
}
?>
