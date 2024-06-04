document.addEventListener("DOMContentLoaded", function() {
    const weatherIcon = document.getElementById('weather-icon');
    const weatherInfo = document.getElementById('weather-info');
    const pageHeader = document.querySelector('.page-header');

    function positionWeatherIcon() {
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

    positionWeatherIcon();
    positionWeatherInfo();

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

    weatherIcon.querySelector('img').src = imagesBaseUrl + '/weather-icon/' + weatherData['weather_icon'];

    let lastElement;
    
    for (const parameter of selectedParameters) {
        if (weatherData.hasOwnProperty(parameter)) {
            const element = document.createElement('div');
            element.className = 'weather-info-item';
    
            const icon = document.createElement('img');
            icon.src = imagesBaseUrl + '/weather-info/' + parameter + '.png';
            icon.className = 'weather-info-item-icon';
    
            const value = document.createElement('span');
            value.textContent = weatherData[parameter];
            value.className = 'weather-info-item-value';

            element.appendChild(icon);
            element.appendChild(value);

            if (parameter === 'apparent_temperature') {
                const note = document.createElement('span');
                note.textContent = '*';
                note.className = 'weather-info-item-asterisk';
                element.appendChild(note);
            }
            weatherInfo.appendChild(element);
    
            lastElement = element;
        }
    }

    if (lastElement) {
        lastElement.classList.add('last-weather-info-item');
    }
    
    if (weatherData.hasOwnProperty('apparent_temperature')) {
        const note = document.createElement('div');
        note.textContent = '* Apparent Temperature';
        note.className = 'weather-info-item-note';
        weatherInfo.appendChild(note);
    }
});
