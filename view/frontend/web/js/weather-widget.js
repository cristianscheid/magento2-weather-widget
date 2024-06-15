require([
    'CristianScheid_WeatherWidget/js/api',
    'CristianScheid_WeatherWidget/js/dom'
], function(api, dom) {

    async function fetchAndInitialize() {
        try {
            const response = await api.fetchDataFromApi();
            dom.createElements(response);
            dom.positionWeatherIcon();
            dom.positionWeatherInfo();
            api.saveDataOnLocalStorage(response);
            api.saveLastApiCallTimestampOnLocalStorage();
        } catch (error) {
            console.error('fetchAndInitialize() failed:', error);
        }
    }

    async function fetchAndUpdate() {
        try {
            const response = await api.fetchDataFromApi();
            dom.updateElements(response);
            api.saveDataOnLocalStorage(response);
            api.saveLastApiCallTimestampOnLocalStorage();
        } catch (error) {
            console.error('fetchAndUpdate() failed:', error);
        }
    }

    function initializeFromLocalStorage() {
        try {
            const response = api.getDataFromLocalStorage();
            dom.createElements(response);
            dom.positionWeatherIcon();
            dom.positionWeatherInfo();
        } catch (error) {
            console.error('initializeFromLocalStorage() failed:', error);
        }
    }

    async function scheduleNextApiCall() {
        const waitTimeApiCall = 10 * 1000; // X seconds
        let interval = waitTimeApiCall - api.getTimeDifferenceFromLastApiCall();
        if (interval <= 0) {
            interval = 0;
        }
        setTimeout(async () => {
            await fetchAndUpdate();
            scheduleNextApiCall();
        }, interval);
    }

    async function init() {
        const storedLastConfigChange = api.getLastConfigChangeFromLocalStorage();
        // The 'lastConfigChange' variable is injected from a PHTML file
        // Source: CristianScheid_WeatherWidget::weather_widget.phtml
        switch (true) {
            
            case (lastConfigChange !== storedLastConfigChange):
                await fetchAndInitialize();
                api.saveLastConfigChangeOnLocalStorage(lastConfigChange);
                break;
            
            case (lastConfigChange === storedLastConfigChange):
                if (api.getDataFromLocalStorage()) {
                    initializeFromLocalStorage();
                } else {
                    await fetchAndInitialize();
                }
                break;
        }
        await scheduleNextApiCall();
        dom.addEventListeners();
    }

    init();

});