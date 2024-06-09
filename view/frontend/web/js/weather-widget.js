document.addEventListener("DOMContentLoaded", function () {

    const imagesBaseUrl = require.toUrl('CristianScheid_WeatherWidget/images');
    let weatherIcon;
    let weatherInfo;

    async function checkApiService() {
        const storedResponse = localStorage.getItem('weather-widget-api-response');
        if (storedResponse) {
            responseObject = JSON.parse(storedResponse);
            createUpdateElements(responseObject);
        } else {
            await fetchDataAndUpdateStorage();
        }
    }

    async function fetchDataAndUpdateStorage() {
        try {
            console.log('fetchDataAndUpdateStorage()');
            const response = await fetch('/rest/V1/weatherwidget/data');
            const responseJson = await response.json();
            localStorage.setItem('weather-widget-api-response', responseJson);
            responseObject = JSON.parse(responseJson);
            createUpdateElements(responseObject);
        } catch (error) {
            console.error('Error fetching weather data:', error);
        }
    }

    function createUpdateElements(response) {

        let selectedParameters = response.data.selectedParameters;
        let weatherData = response.data.weatherData;
    
        let weatherWidget = document.getElementById('weather-widget');
        if (!weatherWidget) {
            weatherWidget = document.createElement('div');
            weatherWidget.id = 'weather-widget';
            document.body.appendChild(weatherWidget);
        }
        
        weatherIcon = document.getElementById('weather-icon');
        let weatherIconImg = document.getElementById('weather-icon-img');;
        if (!weatherIcon) {
            weatherIcon = document.createElement('div');
            weatherIcon.id = 'weather-icon';
            weatherWidget.appendChild(weatherIcon);
            weatherIconImg = document.createElement('img');
            weatherIconImg.id = 'weather-icon-img';
            weatherIcon.appendChild(weatherIconImg);
        }
        weatherIconImg.src = imagesBaseUrl + '/weather-icon/' + weatherData['weather_icon'];

        weatherInfo = document.getElementById('weather-info');
        if (!weatherInfo) {
            weatherInfo = document.createElement('div');
            weatherInfo.id = 'weather-info';
            weatherWidget.appendChild(weatherInfo);
        }

        while (weatherInfo.firstChild) {
            weatherInfo.removeChild(weatherInfo.firstChild);
        }

        let lastElement;
        for (const parameter of selectedParameters) {

            const weatherInfoItem = document.createElement('div');
            weatherInfoItem.className = 'weather-info-item';
    
            const weatherInfoItemImg = document.createElement('img');
            weatherInfoItemImg.src = imagesBaseUrl + '/weather-info/' + parameter + '.png';
            weatherInfoItemImg.className = 'weather-info-item-img';
    
            const weatherInfoItemText = document.createElement('span');
            weatherInfoItemText.textContent = weatherData[parameter];
            weatherInfoItemText.className = '.weather-info-item-text';

            weatherInfoItem.appendChild(weatherInfoItemImg);
            weatherInfoItem.appendChild(weatherInfoItemText);
            if (parameter === 'apparent_temperature') {
                const weatherInfoItemAsterisk = document.createElement('span');
                weatherInfoItemAsterisk.textContent = '*';
                weatherInfoItemAsterisk.className = 'weather-info-item-asterisk';
                weatherInfoItem.appendChild(weatherInfoItemAsterisk);
            }
            weatherInfo.appendChild(weatherInfoItem);
            lastElement = weatherInfoItem;
        }
        if (lastElement) {
            lastElement.classList.add('last-weather-info-item');
        }
        if (weatherData.hasOwnProperty('apparent_temperature')) {
            const weatherInfoItemNote = document.createElement('div');
            weatherInfoItemNote.textContent = '* Apparent Temperature';
            weatherInfoItemNote.className = 'weather-info-item-note';
            weatherInfo.appendChild(weatherInfoItemNote);
        }
    }

    checkApiService();
    positionWeatherIcon();
    positionWeatherInfo();

    function positionWeatherIcon() {
        const pageHeader = document.querySelector('.page-header');
        const headerHeight = pageHeader.offsetHeight;
        let topPosition;
        topPosition = headerHeight + 5;
        weatherIcon.style.top = `${topPosition}px`;
    }

    function positionWeatherInfo() {
        const iconRect = weatherIcon.getBoundingClientRect();
        const topPosition = iconRect.bottom + 5;
        weatherInfo.style.top = `${topPosition}px`;
    }

    window.addEventListener('resize', () => {
        positionWeatherIcon();
        positionWeatherInfo();
    });

    weatherIcon.addEventListener('click', function() {
        if (weatherInfo.style.display === 'none' || weatherInfo.style.display === '') {
            positionWeatherInfo();
            weatherInfo.style.display = 'block';
        } else {
            weatherInfo.style.display = 'none';
        }
    });

    const lastApiCallTimestamp = localStorage.getItem('weather-widget-api-last-call-timestamp') || 0;
    const currentTime = Date.now();
    const elapsedTime = currentTime - lastApiCallTimestamp;
    const remainingTime = Math.max(0, 30 * 1000 - elapsedTime);

    setTimeout(() => {
        fetchDataAndUpdateStorage();
        setInterval(fetchDataAndUpdateStorage, 15 * 1000);
        localStorage.setItem('weather-widget-api-last-call-timestamp', Date.now());
    }, remainingTime);
});