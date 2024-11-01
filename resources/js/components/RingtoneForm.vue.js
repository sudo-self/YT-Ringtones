<template>
    <div>
        <form @submit.prevent="submitForm">
            <input v-model="url" type="text" placeholder="Enter YouTube URL" required />
            <button type="submit">Create Ringtone</button>
        </form>

        <div v-if="errorMessage" class="error">{{ errorMessage }}</div>
        <div v-if="downloadLinks">
            <h2>Download your ringtones:</h2>
            <a :href="downloadLinks.m4r" download class="download-button">Apple (M4R)</a>
            <a :href="downloadLinks.mp3" download class="download-button">Android (MP3)</a>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            url: '',
            downloadLinks: null,
            errorMessage: '',
        };
    },
    methods: {
        async submitForm() {
            this.errorMessage = ''; // Reset the error message
            this.downloadLinks = null; // Reset the download links

            try {
                const response = await fetch('/create-ringtone', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), // CSRF token
                    },
                    body: JSON.stringify({ url: this.url }),
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Network response was not ok');
                }

                const data = await response.json();
                // Assuming data.downloadLinks contains the links for the ringtones
                this.downloadLinks = data.downloadLinks;

            } catch (error) {
                this.errorMessage = 'Error: ' + error.message;
                console.error('Error:', error);
            }
        },
    },
};
</script>

<style>
.error {
    color: red;
    margin-top: 10px;
}
.download-button {
    display: inline-block;
    margin: 10px 0;
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    text-decoration: none;
    border-radius: 5px;
}
</style>



