define(function () {
  "use strict";

  const imagesBaseUrl = require.toUrl("CristianScheid_WeatherWidget/images");

  function createElements(response) {
    const responseObject = JSON.parse(response);
    const selectedParameters = responseObject.data.selectedParameters;
    const weatherData = responseObject.data.weatherData;

    const weatherWidget = document.createElement("div");
    weatherWidget.id = "weather-widget";
    document.body.appendChild(weatherWidget);

    const weatherIcon = document.createElement("div");
    weatherIcon.id = "weather-icon";
    weatherWidget.appendChild(weatherIcon);

    const weatherIconImg = document.createElement("img");
    weatherIconImg.id = "weather-icon-img";
    weatherIconImg.src =
      imagesBaseUrl + "/weather-icon/" + weatherData["weather_icon"];
    weatherIcon.appendChild(weatherIconImg);

    const weatherInfo = document.createElement("div");
    weatherInfo.id = "weather-info";
    weatherWidget.appendChild(weatherInfo);

    let lastWeatherInfoElement;
    for (const parameter of selectedParameters) {
      const weatherInfoItem = document.createElement("div");
      weatherInfoItem.id = "weather-info-item-" + parameter;
      weatherInfoItem.className = "weather-info-item";

      const weatherInfoItemImg = document.createElement("img");
      weatherInfoItemImg.id = "weather-info-item-img-" + parameter;
      weatherInfoItemImg.className = "weather-info-item-img";
      weatherInfoItemImg.src =
        imagesBaseUrl + "/weather-info/" + parameter + ".png";

      const weatherInfoItemText = document.createElement("span");
      weatherInfoItemText.id = "weather-info-item-text-" + parameter;
      weatherInfoItemText.className = "weather-info-item-text";
      weatherInfoItemText.textContent = weatherData[parameter];

      weatherInfoItem.appendChild(weatherInfoItemImg);
      weatherInfoItem.appendChild(weatherInfoItemText);

      if (parameter === "apparent_temperature") {
        const weatherInfoItemAsterisk = document.createElement("span");
        weatherInfoItemAsterisk.className = "weather-info-item-asterisk";
        weatherInfoItemAsterisk.textContent = "*";
        weatherInfoItem.appendChild(weatherInfoItemAsterisk);
      }

      weatherInfo.appendChild(weatherInfoItem);
      lastWeatherInfoElement = weatherInfoItem;
    }
    if (lastWeatherInfoElement) {
      lastWeatherInfoElement.classList.add("last-weather-info-item");
    }
    if (weatherData.hasOwnProperty("apparent_temperature")) {
      const weatherInfoItemNote = document.createElement("div");
      weatherInfoItemNote.textContent = "* Apparent Temperature";
      weatherInfoItemNote.className = "weather-info-item-note";
      weatherInfo.appendChild(weatherInfoItemNote);
    }
  }

  function updateElements(response) {
    const responseObject = JSON.parse(response);
    const selectedParameters = responseObject.data.selectedParameters;
    const weatherData = responseObject.data.weatherData;

    const weatherIconImg = document.getElementById("weather-icon-img");
    weatherIconImg.src =
      imagesBaseUrl + "/weather-icon/" + weatherData["weather_icon"];

    for (const parameter of selectedParameters) {
      const weatherInfoItemText = document.getElementById(
        "weather-info-item-text-" + parameter
      );
      weatherInfoItemText.textContent = weatherData[parameter];
    }
  }

  function positionElements() {
    const pageHeader = document.querySelector(".page-header");
    const weatherIcon = document.getElementById("weather-icon");
    const headerHeight = pageHeader.offsetHeight;
    weatherIcon.style.top = `${headerHeight + 5}px`;
    const iconRect = weatherIcon.getBoundingClientRect();
    const weatherInfo = document.getElementById("weather-info");
    weatherInfo.style.top = `${iconRect.bottom + 5}px`;
  }

  function addEventListeners() {
    window.addEventListener("resize", () => {
      positionElements();
    });

    const weatherIcon = document.getElementById("weather-icon");
    weatherIcon.addEventListener("click", function () {
      const weatherIcon = document.getElementById("weather-icon");
      const weatherInfo = document.getElementById("weather-info");
      if (
        weatherInfo.style.display === "none" ||
        weatherInfo.style.display === ""
      ) {
        positionElements();
        weatherInfo.style.display = "block";
      } else {
        weatherInfo.style.display = "none";
      }
    });
  }

  return {
    createElements: createElements,
    updateElements: updateElements,
    positionElements: positionElements,
    addEventListeners: addEventListeners,
  };
});
