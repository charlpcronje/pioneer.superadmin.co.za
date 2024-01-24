export default {
    data() {
        return {
            toast: null
        }

    },
    mounted(){
        this.toast = this.$swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1000
        });
    }
}
