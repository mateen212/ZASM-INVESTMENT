<div id="app" class="container mt-5">
    <h2>Vue Form Handling</h2>

    <form @submit.prevent="handleSubmit">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" v-model="form.name" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" v-model="form.email" required>
        </div>
        <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea class="form-control" id="message" v-model="form.message" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <!-- Success Alert -->
    <div v-if="submitted" class="alert alert-success mt-4" role="alert">
        <strong>Success!</strong> Your message has been submitted.
    </div>
</div>

<!-- Vue.js -->
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    new Vue({
        el: '#app',
        data() {
            return {
                form: {
                    name: '',
                    email: '',
                    message: ''
                },
                submitted: false
            };
        },
        methods: {
            async handleSubmit() {
                // Prepare the payload
                const payload = {
                    name: this.form.name,
                    email: this.form.email,
                    message: this.form.message
                };

                // Log the payload to the console (this would be an API call in a real app)
                console.log('Payload:', payload);

                try {
                    // Simulate sending data to the server (e.g., using fetch or axios)
                    // In this case, just a mock delay to simulate an API request
                    await this.sendDataToServer(payload);

                    // Show success message
                    this.submitted = true;

                    // Clear the form after submission (optional)
                    this.form.name = '';
                    this.form.email = '';
                    this.form.message = '';
                } catch (error) {
                    // Handle error (e.g., display error message)
                    console.error('Error:', error);
                }
            },

            sendDataToServer(payload) {
                // Mocking an API request with a delay (simulate server request)
                return new Promise((resolve, reject) => {
                    setTimeout(() => {
                        if (Math.random() > 0.2) {
                            resolve('Data sent successfully');
                        } else {
                            reject('Failed to send data');
                        }
                    }, 1000); // Simulate 1 second delay
                });
            }
        }
    });
</script>
