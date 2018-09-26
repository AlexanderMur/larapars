<template>
    <div>
        <div id="tab" class="btn-group btn-group-justified" data-toggle="buttons">
            <a
                @click.prevent="tab = 'new'"
                :class="{'btn btn-primary':true, 'active': tab === 'new'}"
            >
                Новые
            </a>
            <a
                @click.prevent="tab = 'archive'"
                :class="{'btn btn-primary':true, 'active': tab === 'archive'}"
            >
                Архив
            </a>
        </div>
        <div v-if="loading">Loading...</div>
        <div v-if="!loading">
            <review
                v-for="review in reviews.data"
                :key="review.id"
                :review="review"
            ></review>
            <pagination
                :pagination="reviews"
                :offset="5"
                @paginate="fetchPosts()"
            ></pagination>
        </div>
    </div>
</template>

<script>

    import Review from './Review';
    import Pagination from './Pagination';
    import route from 'ziggy';

    window.route = route;

    export default {
        name: "reviews",
        components: {
            Review,
            Pagination
        },
        data() {
            return {
                loading: false,
                tab: 'new',
                allReviews: this.reviews,
                reviews: [],
            };
        },
        watch: {
            tab() {
                this.fetchPosts(1);
            },
        },
        mounted() {
            this.fetchPosts();
        },
        methods: {
            log(...args) {
                console.log(...args);
            },
            fetchPosts(page = null) {
                this.loading = true;
                axios.get(route('reviews.data', {
                    orderBy: 'donors.title',
                    tab: this.tab,
                    page: page || this.reviews.current_page,
                }))
                    .then(response => {
                        this.reviews = response.data;
                        this.loading = false;
                    });
            },
        },
    };
</script>

<style scoped>

</style>