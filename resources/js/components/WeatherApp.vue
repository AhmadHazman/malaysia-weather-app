<template>
    <div class="text-white mb-8">
        <div class="places-input text-gray-800">
            <input type="search" id="address" class="form-control w-full" placeholder="City" />

            <p class="text-white mt-2">Selected: <strong id="address-value">none</strong></p></div>
        <div class="weather-container font-sans w-128 max-w-lg rounded-lg overflow-hidden bg-gray-900 shadow-lg mt-4">
            <div class="current-weather flex items-center justify-between px-6 py-8">
                <div class="flex items-center">
                    <div>
                        <div class="text-6xl font-semibold">{{ currentTemperature.actual }}°C</div>
                        <div>Feels like {{ currentTemperature.feels }}°C</div>
                    </div>
                    <div class="mx-5">
                        <div class="font-semibold">{{ currentTemperature.description }}</div>
                        <div v-if="location.state == ''">{{ location.city }}, {{ location.country }}</div>
                        <div v-else>{{ location.city }}, {{ location.state }}</div>
                    </div>
                </div>
                <div><img :src="currentTemperature.icon" class="w-12 h-12"></div>
            </div>  <!-- end current weather -->

            <div class="future-weather text-sm bg-gray-800 px-6 pt-2 pb-8 overflow-hidden">
                <div v-for="(day, index) in daily" :key="day.valid_date">
                    <div v-if="index != 0" class="flex items-center" :class="{ 'mt-8' : index > 0 }">
                        <div class="w-1/6 text-lg text-gray-200">{{ toDayOfWeek(day.valid_date) }}</div>
                        <div class="w-4/6 px-4 flex items-center">
                            <div><img :src="`../storage/icons/${day.weather.icon}.png`" class="w-8 h-8"></div>
                            <!-- <div><img :src="combineIconLink(day.weather.icon)" class="w-8 h-8"></div> -->
                            <div class="ml-3">{{ day.weather.description }}</div>
                        </div>
                        <div class="w-1/6 text-right">
                            <div>{{ Math.round(day.high_temp) }}°C</div>
                            <div>{{ Math.round(day.low_temp) }}°C</div>
                        </div>
                    </div>
                </div>

            </div>  <!-- end future weather -->
        </div>  <!-- end weather container -->
    </div>
</template>

<script>
    export default {
        mounted() {
            this.fetchData()

            var placesAutocomplete = places({
                appId: 'plHBGLJAI3ID',
                apiKey: '467bc77f18ab7a38be493b24f7c9b63d',
                container: document.querySelector('#address')
            }).configure({
                type: 'city',
                aroundLatLngViaIP: false,
            });

            var $address = document.querySelector('#address-value')
            placesAutocomplete.on('change', (e) => {
                $address.textContent = e.suggestion.value
                
                this.location.city = e.suggestion.name

                if (e.suggestion.administrative) {
                    this.location.state = e.suggestion.administrative
                } else {
                    this.location.state = ''
                }
            });

            placesAutocomplete.on('clear', function() {
                $address.textContent = 'none';
            });
        },
        watch: {
            daily: [],
            location: {
                handler(newValue, oldValue) {
                    this.fetchData()
                },
                deep: true
            }
        },
        data() {
            return {
                currentTemperature: {
                    actual: '',
                    feels: '',
                    description: '',
                    icon: '',
                },
                daily: [],
                location: {
                    'city': 'Klang',
                    'state': 'Selangor',
                    'country': 'Malaysia'
                }
            }
        },
        methods: {
            fetchData() {
                var iconLink = '../storage/icons/'

                fetch(`/api/city/weather?city=${this.location.city}&state=${this.location.state}`)
                    .then(response => response.json())
                    .then(data => {
                        // console.log(data.data[0].description)
                        this.currentTemperature.actual = Math.round(data.data[0].temp)
                        this.currentTemperature.feels = Math.round(data.data[0].app_temp)
                        if(data.data[0].description != undefined) {
                            this.currentTemperature.description = data.data[0].description
                        } else {
                            this.currentTemperature.description = data.data[0].weather.description
                        }
                        iconLink = iconLink + data.data[0].weather.icon + '.png'
                        // console.log('iconlink', iconLink)
                        this.currentTemperature.icon = iconLink
                    })

                fetch(`/api/city/forecast?city=${this.location.city}&state=${this.location.state}`)
                    .then(response => response.json())
                    .then(data => {
                        this.daily = data.data
                    })
            },
            toDayOfWeek(timestamp) {
                const newDate = new Date(timestamp)
                const days = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT']
                
                return days[newDate.getDay()]
            },
            combineIconLink(iconCode) {
                var iconLink = '../storage/icons/' + iconCode + '.png'

                return iconLink
            }
        }
    }
</script>
