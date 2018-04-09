<template>
    <div id="hello">
        <h2>{{ message }}</h2>
    </div>
</template>

<script>
    export default {
        name: "app",
        data() {
            return {
                message: "Test message!"
            }
        }
    }
</script>

<style>
    #hello {
        font-family: Verdana sans-serif;
        color: navy;
    }
</style>