<template>
    <nav class="pagination" role="navigation" aria-label="pagination">

        <ul class="pagination">
            <li :class="{disabled:pagination.current_page <= 1}">
                <a href="#"
                   aria-label="Previous"
                   @click.prevent="changePage(pagination.current_page - 1)"
                >
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li v-for="page in pages" :class="{active:isCurrentPage(page)}">
                <a
                   @click.prevent="changePage(page)"
                >{{ page }}</a>
            </li>
            <li  :class="{disabled:pagination.current_page >= pagination.last_page}">
                <a href="#"
                   aria-label="Next"
                   @click.prevent="changePage(pagination.current_page + 1)"
                >
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</template>

<script>
    export default {
        props: ['pagination', 'offset'],
        methods: {
            isCurrentPage(page) {
                return this.pagination.current_page === page;
            },
            changePage(page) {
                if (page > this.pagination.last_page) {
                    page = this.pagination.last_page;
                }
                this.pagination.current_page = page;
                this.$emit('paginate');
            }
        },
        mounted(){
          console.log(this.pagination)
        },
        computed: {
            pages() {
                let pages = [];
                let from = this.pagination.current_page - Math.floor(this.offset / 2);
                if (from < 1) {
                    from = 1;
                }
                let to = from + this.offset - 1;
                if (to > this.pagination.last_page) {
                    to = this.pagination.last_page;
                }
                while (from <= to) {
                    pages.push(from);
                    from++;
                }
                console.log(from)
                return pages;
            }
        }
    };
</script>