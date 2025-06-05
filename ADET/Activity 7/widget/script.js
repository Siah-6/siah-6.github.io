class WeatherWidget {
    constructor() {
        this.apiKey = '2571699c3999493394a94833250506'; // Your WeatherAPI key
        this.apiUrl = 'https://api.weatherapi.com/v1/current.json';
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadDefaultWeather();
    }

    bindEvents() {
        const searchBtn = document.getElementById('searchBtn');
        const locationInput = document.getElementById('locationInput');
        const citySelect = document.getElementById('citySelect');

        searchBtn.addEventListener('click', () => this.searchWeather());
        locationInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.searchWeather();
            }
        });

        citySelect.addEventListener('change', (e) => {
            if (e.target.value) {
                locationInput.value = e.target.value;
                this.searchWeather();
            }
        });
    }

    async searchWeather() {
        const location = document.getElementById('locationInput').value.trim();
        if (!location) {
            this.showError('Please select or enter a Philippine city');
            return;
        }

        this.showLoading();

        if (!this.apiKey) {
            this.showError('Weather API key required. Please configure a valid WeatherAPI key.');
            return;
        }

        try {
            const response = await fetch(`${this.apiUrl}?key=${this.apiKey}&q=${encodeURIComponent(location)}, Philippines&aqi=no`);

            if (!response.ok) {
                throw new Error(`Weather API error: ${response.status}`);
            }

            const data = await response.json();
            this.displayRealWeatherData(data);
        } catch (error) {
            console.error('Weather API Error:', error);
            this.showError('Unable to fetch weather data. Please try again.');
        }
    }

    async loadDefaultWeather() {
        this.showLoading();

        if (!this.apiKey) {
            this.showError('Weather API key required. Please configure a valid WeatherAPI key.');
            return;
        }

        try {
            const response = await fetch(`${this.apiUrl}?key=${this.apiKey}&q=Manila, Philippines&aqi=no`);

            if (!response.ok) {
                throw new Error(`Weather API error: ${response.status}`);
            }

            const data = await response.json();
            this.displayRealWeatherData(data);
        } catch (error) {
            console.error('Weather API Error:', error);
            this.showError('Unable to fetch weather data. Please provide valid API key.');
        }
    }

    displayRealWeatherData(data) {
        document.getElementById('cityName').textContent = data.location.name;
        document.getElementById('country').textContent = data.location.country;
        document.getElementById('temp').textContent = `${Math.round(data.current.temp_c)}°C`;
        document.getElementById('description').textContent = data.current.condition.text;
        document.getElementById('feelsLike').textContent = `${Math.round(data.current.feelslike_c)}°C`;
        document.getElementById('humidity').textContent = `${data.current.humidity}%`;
        document.getElementById('windSpeed').textContent = `${Math.round(data.current.wind_kph)} km/h`;

        const weatherIcon = document.getElementById('weatherIcon');
        weatherIcon.className = this.getWeatherIcon(data.current.condition.text);

        this.hideLoading();
        this.hideError();
        document.getElementById('weatherInfo').style.display = 'block';
    }

    getWeatherIcon(conditionText) {
        const condition = conditionText.toLowerCase();

        if (condition.includes('sunny') || condition.includes('clear')) {
            return 'fas fa-sun';
        } else if (condition.includes('cloud')) {
            return 'fas fa-cloud';
        } else if (condition.includes('rain') || condition.includes('drizzle')) {
            return 'fas fa-cloud-rain';
        } else if (condition.includes('snow')) {
            return 'fas fa-snowflake';
        } else if (condition.includes('storm') || condition.includes('thunder')) {
            return 'fas fa-bolt';
        } else if (condition.includes('mist') || condition.includes('fog')) {
            return 'fas fa-smog';
        } else {
            return 'fas fa-cloud-sun';
        }
    }

    showLoading() {
        document.getElementById('loading').style.display = 'block';
        document.getElementById('weatherInfo').style.display = 'none';
        document.getElementById('errorMessage').style.display = 'none';
    }

    hideLoading() {
        document.getElementById('loading').style.display = 'none';
    }

    showError(message = 'Unable to fetch weather data. Please try again.') {
        document.getElementById('errorMessage').querySelector('p').textContent = message;
        document.getElementById('errorMessage').style.display = 'block';
        document.getElementById('weatherInfo').style.display = 'none';
        this.hideLoading();
    }

    hideError() {
        document.getElementById('errorMessage').style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new WeatherWidget();
});

function adjustForIframe() {
    if (window.self !== window.top) {
        document.body.style.margin = '0';
        document.body.style.padding = '10px';
        document.body.style.minHeight = 'auto';
    }
}

adjustForIframe();
