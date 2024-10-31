define([
  "CristianScheid_WeatherWidget/js/api",
  "CristianScheid_WeatherWidget/js/dom",
], function (api, dom) {
  return function (config) {
    let errorOccurred = false;

    async function fetchAndInitialize() {
      try {
        const response = await api.fetchDataFromApi();
        dom.createElements(response);
        dom.positionElements();
        api.saveDataOnLocalStorage(response);
        api.saveLastApiCallTimestampOnLocalStorage();
      } catch (error) {
        console.error("weather-widget-fetchAndInitialize() failed:", error);
        errorOccurred = true;
      }
    }

    async function fetchAndUpdate() {
      try {
        const response = await api.fetchDataFromApi();
        dom.updateElements(response);
        api.saveDataOnLocalStorage(response);
        api.saveLastApiCallTimestampOnLocalStorage();
      } catch (error) {
        console.error("weather-widget-fetchAndUpdate() failed:", error);
        errorOccurred = true;
      }
    }

    function initializeFromLocalStorage() {
      try {
        const response = api.getDataFromLocalStorage();
        dom.createElements(response);
        dom.positionElements();
      } catch (error) {
        console.error(
          "weather-widget-initializeFromLocalStorage() failed:",
          error
        );
        errorOccurred = true;
      }
    }

    async function scheduleNextApiCall() {
      const waitTimeApiCall = 15 * 60 * 1000; // 15 minutes
      let interval = waitTimeApiCall - api.getTimeDifferenceFromLastApiCall();
      if (interval <= 0) {
        interval = 0;
      }
      setTimeout(async () => {
        await fetchAndUpdate();
        if (!errorOccurred) {
          scheduleNextApiCall();
        }
      }, interval);
    }

    async function init() {
      if (config.isModuleEnabled) {
        const storedLastConfigChange =
          api.getLastConfigChangeFromLocalStorage();
        switch (true) {
          case config.lastConfigChange !== storedLastConfigChange:
            await fetchAndInitialize();
            api.saveLastConfigChangeOnLocalStorage(config.lastConfigChange);
            break;

          case config.lastConfigChange === storedLastConfigChange:
            if (api.getDataFromLocalStorage()) {
              initializeFromLocalStorage();
            } else {
              await fetchAndInitialize();
            }
            break;
        }
        dom.addEventListeners();
        await scheduleNextApiCall();
      }
    }

    init();
  };
});
