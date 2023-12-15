<template>
    <div>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <div class="navbar-nav">
                    <router-link class="nav-item nav-link" :to="{name: 'home'}">Main</router-link>
                    <router-link class="nav-item nav-link" :to="{name: 'desks'}">Desks</router-link>
                </div>
            </div>
        </nav>
        <div>
            <router-view></router-view>
        </div>
    </div>
</template>

<script>

</script>


<style scoped>

</style>
