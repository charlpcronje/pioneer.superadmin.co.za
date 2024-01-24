<template>
    <div  ref="roleSelection"
        v-if="menuVisible"
        v-bind:style="menuStyle"

        class="fm-context-menu"
          tabindex="-1">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" @click="closeMenu">&times;</button>
            <br />
        </div>


        <b-form class="form" role="form" @submit="setRole">
        <ul class="list-unstyled">
            <li v-for="(role, index) in roles"
                v-bind:key="`r-${index}`">

                <b-checkbox   :id="role.name" :value="role.id" v-model="aclForm.selectedRoles">{{role.name}}
                </b-checkbox>

            </li>
        </ul>

            <input type="hidden" v-model="aclForm.path" />

        <b-button class="btn btn-success btn-block" type="submit"> SAVE <i class="fa fa-save"> </i> </b-button>
        </b-form>

    </div>
</template>

<script>
    import EventBus from "../../eventBus";
    import axios from "axios";




    export default {
        name: "RoleSelection",

        data() {
            return {
                menuVisible: false,
                menuStyle: {
                    top: 0,
                    left: 0,
                },
                roles: [],
                aclForm : new Form( {
                    path: '',
                    selectedRoles: []
                }),
                toast :  this.$swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                }),

            };
        },
        created() {

            axios.get('/roles')
                .then( res => {
                    res.data.forEach(item => {
                        if(item.id != 1) {
                            this.roles.push(item);
                        }
                    });

                });
        },
        mounted() {
            /**
             * Listen events
             * 'roleSelection'
             */
            EventBus.$on('roleSelection', (event) => this.showMenu(event));
        },
        methods: {
            showMenu(event) {

                this.aclForm.path = this.$store.getters['fm/selectedItems'][0].path;
                this.aclForm.selectedRoles = [];

                axios.get(`acl/roles?path=${this.aclForm.path}`).then( res => {
                   if(res.data.length > 0){
                       res.data.forEach(item => {
                           this.aclForm.selectedRoles.push(item.role.id);
                       });
                   }
                    this.menuVisible = true;
                    this.$nextTick(() => {
                        this.$refs.roleSelection.focus();
                        // set menu params
                        this.setMenu(event.pageY, event.pageX);
                    });
                });





            },
            setRole($event){
                $event.preventDefault();
                if(this.aclForm.selectedRoles.length === 0){
                    this.toast.fire({
                       icon : 'error',
                       title : 'Select at least one permission'
                    });
                } else {

                    axios.post('acl/roles', this.aclForm.data())
                    .then(() => {
                        this.toast.fire({
                            icon : 'success',
                            title : 'Permissions saved'
                        }).then(() => this.closeMenu());
                    });
                }
            },
            setMenu(top, left) {
                const el = this.$refs.roleSelection.parentNode;

                // get parent el size
                const elSize = el.getBoundingClientRect();

                // actual coordinates of the block
                const elY = window.pageYOffset + elSize.top;
                const elX = window.pageXOffset + elSize.left;

                // calculate the preliminary coordinates
                let menuY = top - elY;
                let menuX = left - elX;

                // calculate max X and Y coordinates
                const maxY = elY + (el.offsetHeight - this.$refs.roleSelection.offsetHeight - 25);
             //   const maxX = elX + (el.offsetWidth - this.$refs.roleSelection.offsetWidth - 25);
                const maxX = elX + (el.offsetWidth - this.$refs.roleSelection.offsetWidth - 25);

                if (top > maxY) menuY = maxY - elY;
                if (left > maxX) menuX = maxX - elX;

                // set coordinates
                this.menuStyle.top = `${menuY}px`;
                this.menuStyle.left = `${menuX}px`;
            },

            closeMenu() {
                this.menuVisible = false;
            },
        }
    };

</script>

<style scoped>

</style>
