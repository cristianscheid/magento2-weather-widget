document.addEventListener("DOMContentLoaded", function () {

    const imagesBaseUrl = require.toUrl('CristianScheid_WeatherWidget/images');

    async function fetchDataFromApi() {
        const responseRaw = await fetch('/rest/V1/weatherwidget/data');
        if (responseRaw.ok) {
            const response = await responseRaw.json();
            console.log(getCurrentTime());
            return response;
        } else {
            console.error('fetchDataFromApi() failed: ', responseRaw.statusText);
        }
    }

    function saveDataOnLocalStorage(response) {
        localStorage.setItem('weather-widget-api-response', response);
    }

    function getDataFromLocalStorage() {
        return localStorage.getItem('weather-widget-api-response');
    }

    function saveLastConfigChangeOnLocalStorage(lastConfigChange) {
        localStorage.setItem('weather-widget-last-config-change', lastConfigChange);
    }

    function getLastConfigChangeFromLocalStorage() {
        return localStorage.getItem('weather-widget-last-config-change');
    }

    function saveLastApiCallTimestampOnLocalStorage() {
        localStorage.setItem('weather-widget-last-api-call-timestamp', Date.now());
    }

    function getLastApiCallTimestampFromLocalStorage() {
        return localStorage.getItem('weather-widget-last-api-call-timestamp');
    }

    function createElements(response) {
        const responseObject = JSON.parse(response);
        const selectedParameters = responseObject.data.selectedParameters;
        const weatherData = responseObject.data.weatherData;

        const weatherWidget = document.createElement('div');
        weatherWidget.id = 'weather-widget';
        document.body.appendChild(weatherWidget);

        const weatherIcon = document.createElement('div');
        weatherIcon.id = 'weather-icon';
        weatherWidget.appendChild(weatherIcon);
        
        const weatherIconImg = document.createElement('img');
        weatherIconImg.id = 'weather-icon-img';
        weatherIconImg.src = imagesBaseUrl + '/weather-icon/' + weatherData['weather_icon'];
        weatherIcon.appendChild(weatherIconImg);

        const weatherInfo = document.createElement('div');
        weatherInfo.id = 'weather-info';
        weatherWidget.appendChild(weatherInfo);

        let lastWeatherInfoElement;
        for (const parameter of selectedParameters) {

            const weatherInfoItem = document.createElement('div');
            weatherInfoItem.id = 'weather-info-item-' + parameter;
            weatherInfoItem.className = 'weather-info-item';
            
            const weatherInfoItemImg = document.createElement('img');
            weatherInfoItemImg.id = 'weather-info-item-img-' + parameter;
            weatherInfoItemImg.className = 'weather-info-item-img';
            weatherInfoItemImg.src = imagesBaseUrl + '/weather-info/' + parameter + '.png';
            
            const weatherInfoItemText = document.createElement('span');
            weatherInfoItemText.id = 'weather-info-item-text-' + parameter;
            weatherInfoItemText.className = 'weather-info-item-text';
            weatherInfoItemText.textContent = weatherData[parameter];

            weatherInfoItem.appendChild(weatherInfoItemImg);
            weatherInfoItem.appendChild(weatherInfoItemText);
            
            if (parameter === 'apparent_temperature') {
                const weatherInfoItemAsterisk = document.createElement('span');
                weatherInfoItemAsterisk.className = 'weather-info-item-asterisk';
                weatherInfoItemAsterisk.textContent = '*';
                weatherInfoItem.appendChild(weatherInfoItemAsterisk);
            }

            weatherInfo.appendChild(weatherInfoItem);
            lastWeatherInfoElement = weatherInfoItem;
        }
        if (lastWeatherInfoElement) {
            lastWeatherInfoElement.classList.add('last-weather-info-item');
        }
        if (weatherData.hasOwnProperty('apparent_temperature')) {
            const weatherInfoItemNote = document.createElement('div');
            weatherInfoItemNote.textContent = '* Apparent Temperature';
            weatherInfoItemNote.className = 'weather-info-item-note';
            weatherInfo.appendChild(weatherInfoItemNote);
        }
    }

    function updateElements(response) {

        const responseObject = JSON.parse(response);
        const selectedParameters = responseObject.data.selectedParameters;
        const weatherData = responseObject.data.weatherData;

        const weatherIconImg = document.getElementById('weather-icon-img');
        weatherIconImg.src = imagesBaseUrl + '/weather-icon/' + weatherData['weather_icon'];

        for (const parameter of selectedParameters) {

            const weatherInfoItemText = document.getElementById('weather-info-item-text-' + parameter);
            weatherInfoItemText.textContent = weatherData[parameter];
        }
    }

    function positionWeatherIcon() {
        const pageHeader = document.querySelector('.page-header');
        const headerHeight = pageHeader.offsetHeight;
        const weatherIcon = document.getElementById('weather-icon');
        weatherIcon.style.top = `${headerHeight + 5}px`;
    }

    function positionWeatherInfo() {
        const weatherIcon = document.getElementById('weather-icon');
        const iconRect = weatherIcon.getBoundingClientRect();
        const weatherInfo = document.getElementById('weather-info');
        weatherInfo.style.top = `${iconRect.bottom + 5}px`;
    }

    async function fetchAndInitialize() {
        try {
            const response = await fetchDataFromApi();
            createElements(response);
            positionWeatherIcon();
            positionWeatherInfo();
            saveDataOnLocalStorage(response);
            saveLastApiCallTimestampOnLocalStorage();
        } catch (error) {
            console.error('fetchAndInitialize() failed:', error);
        }
    }

    async function fetchAndUpdate() {
        try {
            const response = await fetchDataFromApi();
            updateElements(response);
            saveDataOnLocalStorage(response);
            saveLastApiCallTimestampOnLocalStorage();
        } catch (error) {
            console.error('fetchAndUpdate() failed:', error);
        }
    }

    function initializeFromLocalStorage() {
        try {
            const response = getDataFromLocalStorage();
            createElements(response);
            positionWeatherIcon();
            positionWeatherInfo();
        } catch (error) {
            console.error('initializeFromLocalStorage() failed:', error);
        }
    }

    function getTimeDifferenceFromLastApiCall() {
        const lastApiCallTimestamp = getLastApiCallTimestampFromLocalStorage();
        const currentTime = Date.now();
        return currentTime - lastApiCallTimestamp;
    }

    async function scheduleNextApiCall() {
        const waitTimeApiCall = 10 * 1000; // X seconds
        let interval = waitTimeApiCall - getTimeDifferenceFromLastApiCall();
        if (interval <= 0) {
            interval = 0;
        }
        setTimeout(async () => {
            await fetchAndUpdate();
            scheduleNextApiCall();
        }, interval);
    }

    function getCurrentTime() {
        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const seconds = now.getSeconds().toString().padStart(2, '0');
        return `${hours}:${minutes}:${seconds}`;
    }

    async function init() {
        const storedLastConfigChange = getLastConfigChangeFromLocalStorage();
        // The 'lastConfigChange' variable is injected from a PHTML file
        // Source: CristianScheid_WeatherWidget::weather_widget.phtml
        switch (true) {
            
            case (lastConfigChange !== storedLastConfigChange):
                await fetchAndInitialize();
                saveLastConfigChangeOnLocalStorage(lastConfigChange);
                break;
            
            case (lastConfigChange === storedLastConfigChange):
                if (getDataFromLocalStorage()) {
                    initializeFromLocalStorage();
                } else {
                    await fetchAndInitialize();
                }
                break;
        }
        
        await scheduleNextApiCall();

        window.addEventListener('resize', () => {
            positionWeatherIcon();
            positionWeatherInfo();
        });
        
        const weatherIcon = document.getElementById('weather-icon');
        weatherIcon.addEventListener('click', function() {
            const weatherIcon = document.getElementById('weather-icon');
            const weatherInfo = document.getElementById('weather-info');
            if (weatherInfo.style.display === 'none' || weatherInfo.style.display === '') {
                positionWeatherInfo(weatherIcon, weatherInfo);
                weatherInfo.style.display = 'block';
            } else {
                weatherInfo.style.display = 'none';
            }
        });
    }
    
    init();
});