<template>
  <div id="app">
    <h1>DCKYIV QR scanner</h1>
    <p class="error">Error: {{ error }}</p>
    <div class="attendee">
      <h3 class="name">{{ first_name }} {{ last_name }}</h3>
      <h3 class="t-shirt">{{ t_shirt_size }}</h3>
      <h1 class="green" v-if="status == ''">OK</h1>
      <h1 class="red" v-if="status != ''">BAD</h1>
    </div>
    <p class="decode-result">Last result: <b>{{ result }}</b></p>

    <qrcode-stream @decode="onDecode" @init="onInit" />


  </div>
</template>

<script>

    import axios from 'axios';

    export default {
        data () {
            return {
                result: '',
                error: '',
                first_name: '',
                last_name: '',
                t_shirt_size: '',
                status: '',
            }
        },

        methods: {
            onDecode (result) {
                let items = result.split(':');
                console.log(items);
                this.result = result;
                axios.get('/admin/dc/attendee-data/' + items[0] + '/' + items[1] +'/' + items[2])
                    .then(response => {
                    // JSON responses are automatically parsed.
                    console.log(response);
                    this.first_name = response.data.first_name;
                    this.last_name = response.data.last_name;
                    this.status = response.data.status;
                })
            },

            async onInit (promise) {

                try {
                    await promise
                } catch (error) {

                    if (error.name === 'NotAllowedError') {
                        this.error = "ERROR: you need to grant camera access permisson"
                    } else if (error.name === 'NotFoundError') {
                        this.error = "ERROR: no camera on this device"
                    } else if (error.name === 'NotSupportedError') {
                        this.error = "ERROR: secure context required (HTTPS, localhost)"
                    } else if (error.name === 'NotReadableError') {
                        this.error = "ERROR: is the camera already in use?"
                    } else if (error.name === 'OverconstrainedError') {
                        this.error = "ERROR: installed cameras are not suitable"
                    } else if (error.name === 'StreamApiNotSupportedError') {
                        this.error = "ERROR: Stream API is not supported in this browser"
                    }
                }
            }
        }
    }
</script>

<style>
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
