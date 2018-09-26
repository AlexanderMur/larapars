<template>
    <div>
        <div id="tab" class="btn-group btn-group-justified" data-toggle="buttons">
            <a @click.prevent="curTab = 'first'"
               :class="{'btn btn-primary':true, 'active': curTab === 'first'}"
            >
                first
            </a>
            <a @click.prevent="curTab = 'second'"
               :class="{'btn btn-primary':true, 'active': curTab === 'second'}"
            >
                second
            </a>
        </div>
        <div v-if="curTab === 'first'">
            <div class="row"
                 v-for="review in allReviews"
                 v-if="review.good === null"
            >
                {{log(review)}}
                <div class="col-lg-8">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row mb-3">
                                <div class="col-lg-6">
                                    <h3 class="my-0 d-inline">
                                        {{review.title}}
                                    </h3>
                                </div>
                                <div class="col-lg-6 text-right">
                                    <i class="fa fa-user fa-fw"></i>
                                    {{review.name}}
                                    /
                                    <i class="fa fa-clock-o fa-fw"></i>
                                    <time>{{review.created_at}}</time>
                                </div>
                            </div>

                            <div v-html="review.text"></div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Донор (название)</th>
                                        <th>Донор (ссылка)</th>
                                        <th>Донор (ссылка на страницу)</th>
                                        <th>Дата парсинга</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{review.donor.title}}</td>
                                        <td><a :href="review.donor_link">{{review.donor_link}}</a></td>
                                        <td><a :href="review.donor.link">{{review.donor.link}}</a></td>
                                        <td>{{review.donor.created_at}}</td>
                                    </tr>
                                </tbody>

                            </table>


                            <div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <i class="fa fa-2x fa-thumbs-down" @click="dislike(review)"></i>
                    <i class="fa fa-2x fa-thumbs-up" @click="like(review)"></i>
                </div>
            </div>
        </div>
        <div v-if="curTab === 'second'">
            <div class="row"
                 v-for="review in orderReviews"
                 v-if="review.good !== null"
            >
                {{log(review)}}
                <div class="col-lg-8">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row mb-3">
                                <div class="col-lg-6">
                                    <h3 class="my-0 d-inline">
                                        {{review.title}}
                                    </h3>
                                    <i v-if="review.good === true"
                                       class="fa fa-thumbs-up text-success fa-fw fa-2x"
                                    >
                                    </i>

                                    <i v-if="review.good === false"
                                       class="fa fa-thumbs-down text-danger fa-fw fa-2x"
                                    ></i>
                                </div>
                                <div class="col-lg-6 text-right">
                                    <i class="fa fa-user fa-fw"></i>
                                    {{review.name}}
                                    /
                                    <i class="fa fa-clock-o fa-fw"></i>
                                    <time>{{review.created_at}}</time>
                                </div>
                            </div>

                            <div v-html="review.text"></div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Донор (название)</th>
                                        <th>Донор (ссылка)</th>
                                        <th>Донор (ссылка на страницу)</th>
                                        <th>Дата парсинга</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{review.donor.title}}</td>
                                        <td><a :href="review.donor_link">{{review.donor_link}}</a></td>
                                        <td><a :href="review.donor.link">{{review.donor.link}}</a></td>
                                        <td>{{review.donor.created_at}}</td>
                                    </tr>
                                </tbody>

                            </table>


                            <div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <i class="fa fa-2x fa-thumbs-down" @click="dislike(review)"></i>
                    <i class="fa fa-2x fa-thumbs-up" @click="like(review)"></i>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        name: "reviews",
        props: ['reviews'],
        data() {
            return {
                curTab: 'first',
                allReviews: this.reviews
            };
        },
        computed: {
            orderReviews() {
               return this.reviews;
            }
        },
        mounted() {

        },
        methods: {
            log(...args) {
                console.log(...args);
            },
            like(review) {
                review.good = true;
            },
            dislike(review) {
                review.good = false;
            }
        },
    };
</script>

<style scoped>

</style>