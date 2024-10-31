define(function () {
  "use strict";

  // This function is kept for debugging reasons.
  function getCurrentTime() {
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, "0");
    const minutes = now.getMinutes().toString().padStart(2, "0");
    const seconds = now.getSeconds().toString().padStart(2, "0");
    return `${hours}:${minutes}:${seconds}`;
  }

  async function fetchDataFromApi() {
    const responseRaw = await fetch("/rest/V1/weatherwidget/data");
    if (responseRaw.ok) {
      const response = await responseRaw.json();
      return response;
    } else {
      console.error(
        "weather-widget-fetchDataFromApi() failed: ",
        responseRaw.statusText
      );
    }
  }

  function saveDataOnLocalStorage(response) {
    localStorage.setItem("weather-widget-api-response", response);
  }

  function getDataFromLocalStorage() {
    return localStorage.getItem("weather-widget-api-response");
  }

  function saveLastConfigChangeOnLocalStorage(lastConfigChange) {
    localStorage.setItem("weather-widget-last-config-change", lastConfigChange);
  }

  function getLastConfigChangeFromLocalStorage() {
    return localStorage.getItem("weather-widget-last-config-change");
  }

  function saveLastApiCallTimestampOnLocalStorage() {
    localStorage.setItem("weather-widget-last-api-call-timestamp", Date.now());
  }

  function getLastApiCallTimestampFromLocalStorage() {
    return localStorage.getItem("weather-widget-last-api-call-timestamp");
  }

  function getTimeDifferenceFromLastApiCall() {
    const lastApiCallTimestamp = getLastApiCallTimestampFromLocalStorage();
    const currentTime = Date.now();
    return currentTime - lastApiCallTimestamp;
  }

  return {
    fetchDataFromApi: fetchDataFromApi,
    saveDataOnLocalStorage: saveDataOnLocalStorage,
    getDataFromLocalStorage: getDataFromLocalStorage,
    saveLastConfigChangeOnLocalStorage: saveLastConfigChangeOnLocalStorage,
    getLastConfigChangeFromLocalStorage: getLastConfigChangeFromLocalStorage,
    saveLastApiCallTimestampOnLocalStorage:
      saveLastApiCallTimestampOnLocalStorage,
    getTimeDifferenceFromLastApiCall: getTimeDifferenceFromLastApiCall,
  };
});
