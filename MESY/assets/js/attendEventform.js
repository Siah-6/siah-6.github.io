



let allProvinces = []
async function loadProvinces() {
    try {
        const response = await fetch('https://psgc.gitlab.io/api/provinces/');
        const data = await response.json();

        data.sort((a, b) => a.name.localeCompare(b.name));

        allProvinces = data;

        const provinceSelect = document.getElementById('provinceSelect')

        allProvinces.forEach(province => {
            const option = document.createElement('option');
            option.innerText = province.name
            option.value = province.name
            provinceSelect.appendChild(option);

        });
        console.log("Provinces", allProvinces.slice(0, 5));

    } catch (error) {
        console.error("Error fetching provinces:", error)
    }
}

let allCities = [];

async function loadCities() {
    try {
        const response = await fetch('https://psgc.gitlab.io/api/cities-municipalities/')
        const data = await response.json();

        allCities = data;

        const citySelect = document.getElementById('citySelect')



        console.log('Cities:', allCities.slice(0, 5));
    } catch (error) {
        console.error("Error fetching cities:", error)

    }
}


function checkifProvinceSelected() {
    const provinceSelect = document.getElementById('provinceSelect');
    provinceSelect.addEventListener("change", filterCities);
}

function filterCities() {


    const citySelect = document.getElementById('citySelect');






    citySelect.disabled = false;

    const provinceSelect = document.getElementById('provinceSelect');
    const provinceValue = provinceSelect.value;

    const selectedProvince = allProvinces.find(p => p.name === provinceValue);

    if (!selectedProvince) {
        console.warn("No matching province found for value:", provinceValue);
        citySelect.disabled = true;
        citySelect.innerHTML = "";
        const defaultOption = document.createElement('option');
        defaultOption.text = "Select a City";
        defaultOption.disabled = true;
        defaultOption.selected = true;
        citySelect.appendChild(defaultOption);

        return;
    }

    const selectedProvinceCode = selectedProvince.code;


    const filteredCities = allCities.filter(city => city.provinceCode === selectedProvinceCode)

    filteredCities.forEach(city => {
        const option = document.createElement('option');
        option.innerText = city.name;
        option.value = city.name;
        citySelect.appendChild(option)


    });





}
